<?php

namespace Tests\Feature;

use App\Models\Assignment;
use App\Models\Course;
use App\Models\Material;
use App\Models\Submission;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

/**
 * Minggu 9 (B7): validasi tipe berkas materi dan penghapusan berkas fisik.
 *
 *   php artisan test --filter=MaterialFileCleanupTest
 */
class MaterialFileCleanupTest extends TestCase
{
    use RefreshDatabase;

    private const DOCX = 'application/vnd.openxmlformats-officedocument.wordprocessingml.document';
    private const PPTX = 'application/vnd.openxmlformats-officedocument.presentationml.presentation';

    private User $admin;
    private User $lecturer;
    private Course $course;

    protected function setUp(): void
    {
        parent::setUp();

        Storage::fake('local');
        Storage::fake('public');

        $this->admin = User::factory()->admin()->create();
        $this->lecturer = User::factory()->dosen()->create();
        $this->course = Course::factory()->create(['lecturer_id' => $this->lecturer->id]);
    }

    private function makeFileMaterial(string $path = 'materials/lama.pdf', ?Course $course = null): Material
    {
        Storage::disk('local')->put($path, 'isi berkas');

        $material = new Material([
            'course_id' => ($course ?? $this->course)->id,
            'title' => 'Materi '.$path,
            'description' => 'Deskripsi',
            'type' => 'file',
            'file_path' => $path,
            'original_name' => basename($path),
            'file_size' => 10,
            'mime_type' => 'application/pdf',
        ]);
        $material->uploaded_by = $this->lecturer->id; // tidak ada di $fillable
        $material->save();

        return $material;
    }

    // ------------------------------------------------------------------
    // Validasi mimes + max (B3)
    // ------------------------------------------------------------------

    public function test_dosen_can_upload_pdf_docx_and_pptx(): void
    {
        $files = [
            ['materi.pdf', 'application/pdf'],
            ['materi.docx', self::DOCX],
            ['materi.pptx', self::PPTX],
        ];

        foreach ($files as $i => [$name, $mime]) {
            $this->actingAs($this->lecturer)
                ->post(route('dosen.courses.materials.store', $this->course), [
                    'title' => "Materi {$i}",
                    'description' => 'Deskripsi',
                    'type' => 'file',
                    'file' => UploadedFile::fake()->create($name, 10, $mime),
                ])
                ->assertSessionHasNoErrors();
        }

        $this->assertSame(3, Material::count());
    }

    public function test_dosen_upload_rejects_disallowed_types(): void
    {
        $files = [
            ['skrip.php', 'application/x-php'],
            ['gambar.png', 'image/png'],
            ['program.exe', 'application/x-msdownload'],
        ];

        foreach ($files as [$name, $mime]) {
            $this->actingAs($this->lecturer)
                ->post(route('dosen.courses.materials.store', $this->course), [
                    'title' => 'Materi terlarang',
                    'description' => 'Deskripsi',
                    'type' => 'file',
                    'file' => UploadedFile::fake()->create($name, 10, $mime),
                ])
                ->assertSessionHasErrors('file');
        }

        $this->assertSame(0, Material::count());
    }

    public function test_dosen_upload_rejects_file_over_20_mb(): void
    {
        $this->actingAs($this->lecturer)
            ->post(route('dosen.courses.materials.store', $this->course), [
                'title' => 'Terlalu besar',
                'description' => 'Deskripsi',
                'type' => 'file',
                'file' => UploadedFile::fake()->create('besar.pdf', 20_481, 'application/pdf'),
            ])
            ->assertSessionHasErrors('file');

        $this->assertSame(0, Material::count());
    }

    public function test_admin_upload_rejects_disallowed_type(): void
    {
        $this->actingAs($this->admin)
            ->post(route('admin.materials.store'), [
                'course_id' => $this->course->id,
                'title' => 'Materi terlarang',
                'description' => 'Deskripsi',
                'type' => 'file',
                'file' => UploadedFile::fake()->create('skrip.php', 10, 'application/x-php'),
            ])
            ->assertSessionHasErrors('file');

        $this->assertSame(0, Material::count());
    }

    // ------------------------------------------------------------------
    // Penghapusan berkas fisik (B4)
    // ------------------------------------------------------------------

    public function test_dosen_destroy_removes_physical_file(): void
    {
        $material = $this->makeFileMaterial();

        $this->actingAs($this->lecturer)
            ->delete(route('dosen.courses.materials.destroy', [$this->course, $material]))
            ->assertRedirect();

        $this->assertModelMissing($material);
        Storage::disk('local')->assertMissing('materials/lama.pdf');
    }

    public function test_admin_destroy_removes_physical_file(): void
    {
        $material = $this->makeFileMaterial();

        $this->actingAs($this->admin)
            ->delete(route('admin.materials.destroy', $material))
            ->assertRedirect();

        $this->assertModelMissing($material);
        Storage::disk('local')->assertMissing('materials/lama.pdf');
    }

    public function test_deleting_material_model_removes_file_via_hook(): void
    {
        $material = $this->makeFileMaterial();

        $material->delete();

        Storage::disk('local')->assertMissing('materials/lama.pdf');
    }

    public function test_deleting_link_material_does_not_fail(): void
    {
        $material = new Material([
            'course_id' => $this->course->id,
            'title' => 'Tautan',
            'description' => 'Deskripsi',
            'type' => 'link',
            'external_url' => 'https://example.test/materi',
        ]);
        $material->uploaded_by = $this->lecturer->id;
        $material->save();

        $material->delete();

        $this->assertModelMissing($material);
    }

    public function test_deleting_submission_model_removes_file_via_hook(): void
    {
        Storage::disk('local')->put('submissions/1/tugas.pdf', 'jawaban');

        $assignment = Assignment::factory()->create([
            'course_id' => $this->course->id,
            'created_by' => $this->lecturer->id,
        ]);
        $submission = Submission::factory()->create([
            'assignment_id' => $assignment->id,
            'file_path' => 'submissions/1/tugas.pdf',
        ]);

        $submission->delete();

        Storage::disk('local')->assertMissing('submissions/1/tugas.pdf');
    }

    // ------------------------------------------------------------------
    // dosenUpdate: ganti berkas / ubah tipe (B4)
    // ------------------------------------------------------------------

    public function test_dosen_update_to_link_removes_old_file_and_clears_columns(): void
    {
        $material = $this->makeFileMaterial();

        $this->actingAs($this->lecturer)
            ->put(route('dosen.courses.materials.update', [$this->course, $material]), [
                'title' => $material->title,
                'description' => 'Deskripsi',
                'type' => 'link',
                'external_url' => 'https://example.test/materi-baru',
            ])
            ->assertSessionHasNoErrors();

        $material->refresh();

        Storage::disk('local')->assertMissing('materials/lama.pdf');
        $this->assertNull($material->file_path);
        $this->assertNull($material->original_name);
        $this->assertNull($material->file_size);
        $this->assertNull($material->mime_type);
        $this->assertSame('https://example.test/materi-baru', $material->external_url);
    }

    public function test_dosen_update_with_new_file_replaces_old_file(): void
    {
        $material = $this->makeFileMaterial();

        $this->actingAs($this->lecturer)
            ->put(route('dosen.courses.materials.update', [$this->course, $material]), [
                'title' => $material->title,
                'description' => 'Deskripsi',
                'type' => 'file',
                'file' => UploadedFile::fake()->create('baru.pdf', 10, 'application/pdf'),
            ])
            ->assertSessionHasNoErrors();

        $material->refresh();

        Storage::disk('local')->assertMissing('materials/lama.pdf');
        $this->assertNotSame('materials/lama.pdf', $material->file_path);
        Storage::disk('local')->assertExists($material->file_path);
    }

    public function test_dosen_update_without_new_file_keeps_old_file(): void
    {
        $material = $this->makeFileMaterial();

        $this->actingAs($this->lecturer)
            ->put(route('dosen.courses.materials.update', [$this->course, $material]), [
                'title' => 'Judul diubah',
                'description' => 'Deskripsi',
                'type' => 'file',
            ])
            ->assertSessionHasNoErrors();

        $material->refresh();

        $this->assertSame('materials/lama.pdf', $material->file_path);
        Storage::disk('local')->assertExists('materials/lama.pdf');
    }

    // ------------------------------------------------------------------
    // Hapus course: kasus cascade (B4)
    // ------------------------------------------------------------------

    public function test_destroying_course_removes_material_files(): void
    {
        $this->makeFileMaterial('materials/satu.pdf');
        $this->makeFileMaterial('materials/dua.pdf');

        $this->actingAs($this->admin)
            ->delete(route('admin.courses.destroy', $this->course))
            ->assertSessionHas('success');

        $this->assertModelMissing($this->course);
        $this->assertSame(0, Material::count());
        Storage::disk('local')->assertMissing('materials/satu.pdf');
        Storage::disk('local')->assertMissing('materials/dua.pdf');
    }

    public function test_course_with_submissions_cannot_be_destroyed_and_keeps_files(): void
    {
        $this->makeFileMaterial('materials/satu.pdf');
        Storage::disk('local')->put('submissions/1/tugas.pdf', 'jawaban');

        $assignment = Assignment::factory()->create([
            'course_id' => $this->course->id,
            'created_by' => $this->lecturer->id,
        ]);
        Submission::factory()->create([
            'assignment_id' => $assignment->id,
            'file_path' => 'submissions/1/tugas.pdf',
        ]);

        $this->actingAs($this->admin)
            ->delete(route('admin.courses.destroy', $this->course))
            ->assertSessionHas('error');

        $this->assertModelExists($this->course);
        Storage::disk('local')->assertExists('materials/satu.pdf');
        Storage::disk('local')->assertExists('submissions/1/tugas.pdf');
    }

    // ------------------------------------------------------------------
    // Accessor ukuran berkas (B6)
    // ------------------------------------------------------------------

    public function test_file_size_for_humans_accessor_on_models(): void
    {
        $this->assertSame('1,5 MB', (new Material(['file_size' => 1_536_000]))->file_size_for_humans);
        $this->assertSame('2,0 KB', (new Submission(['file_size' => 2048]))->file_size_for_humans);
        $this->assertNull((new Material())->file_size_for_humans);
    }

    public function test_dosen_index_lists_enrolled_students_who_have_not_submitted(): void
    {
        $lecturer = User::factory()->dosen()->create();
        $course = Course::factory()->create(['lecturer_id' => $lecturer->id]);
        $assignment = Assignment::factory()->create([
            'course_id' => $course->id,
            'created_by' => $lecturer->id,
        ]);

        $done = User::factory()->mahasiswa()->create();
        $pending = User::factory()->mahasiswa()->create();
        $course->students()->attach([
            $done->id => ['enrolled_at' => now()],
            $pending->id => ['enrolled_at' => now()],
        ]);
        Submission::factory()->create([
            'assignment_id' => $assignment->id,
            'user_id' => $done->id,
        ]);

        $this->actingAs($lecturer)
            ->get(route('dosen.courses.assignments.submissions.index', [$course, $assignment]))
            ->assertOk()
            ->assertViewHas('notSubmitted')
            ->assertViewHas('notSubmitted', fn ($list) => $list->pluck('id')->all() === [$pending->id]);
    }
}