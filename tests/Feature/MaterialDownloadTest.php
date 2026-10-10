<?php

namespace Tests\Feature;

use App\Models\Course;
use App\Models\Material;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

/**
 * Menguji penutupan IDOR file materi (baris 55 pada docs/keamanan.md).
 *
 *   DB_CONNECTION=sqlite DB_DATABASE=:memory: php artisan test --filter=MaterialDownloadTest
 */
class MaterialDownloadTest extends TestCase
{
    use RefreshDatabase;

    private User $owner;
    private User $otherLecturer;
    private User $admin;
    private User $enrolled;
    private User $outsider;
    private Course $course;
    private Course $otherCourse;
    private Material $fileMaterial;
    private Material $linkMaterial;

    protected function setUp(): void
    {
        parent::setUp();

        Storage::fake('local');
        Storage::fake('public');

        $this->owner = User::factory()->dosen()->create();
        $this->otherLecturer = User::factory()->dosen()->create();
        $this->admin = User::factory()->admin()->create();
        $this->enrolled = User::factory()->mahasiswa()->create();
        $this->outsider = User::factory()->mahasiswa()->create();

        $this->course = Course::factory()->create(['lecturer_id' => $this->owner->id]);
        $this->otherCourse = Course::factory()->create(['lecturer_id' => $this->otherLecturer->id]);
        $this->course->students()->attach($this->enrolled->id, ['enrolled_at' => now()]);

        Storage::disk('local')->put('materials/notes.pdf', 'isi materi');

        $this->fileMaterial = $this->makeMaterial([
            'type' => 'file',
            'file_path' => 'materials/notes.pdf',
            'original_name' => 'catatan.pdf',
        ]);
        $this->linkMaterial = $this->makeMaterial([
            'type' => 'link',
            'external_url' => 'https://example.test/materi',
        ]);
    }

    private function makeMaterial(array $attributes): Material
    {
        $material = new Material(array_merge([
            'course_id' => $this->course->id,
            'title' => 'Materi',
            'description' => 'Deskripsi',
        ], $attributes));
        $material->uploaded_by = $this->owner->id; // tidak ada di $fillable
        $material->save();

        return $material;
    }

    private function url(?Course $course = null, ?Material $material = null): string
    {
        return route('courses.materials.download', [
            $course ?? $this->course,
            $material ?? $this->fileMaterial,
        ]);
    }

    public function test_admin_owner_and_enrolled_student_can_download(): void
    {
        foreach ([$this->admin, $this->owner, $this->enrolled] as $user) {
            $this->actingAs($user)
                ->get($this->url())
                ->assertOk()
                ->assertDownload('catatan.pdf');
        }
    }

    public function test_outsiders_cannot_download(): void
    {
        foreach ([$this->outsider, $this->otherLecturer] as $user) {
            $this->actingAs($user)->get($this->url())->assertForbidden();
        }
    }

    public function test_guest_gets_401(): void
    {
        $this->get($this->url())->assertUnauthorized();
    }

    public function test_material_cannot_be_reached_through_another_course_in_the_url(): void
    {
        // scopeBindings: {material} harus milik {course} pada URL.
        $this->actingAs($this->otherLecturer)
            ->get($this->url($this->otherCourse, $this->fileMaterial))
            ->assertNotFound();
    }

    public function test_link_material_and_missing_file_return_404_for_authorized_user(): void
    {
        $this->actingAs($this->owner)->get($this->url(null, $this->linkMaterial))->assertNotFound();

        Storage::disk('local')->delete('materials/notes.pdf');
        $this->actingAs($this->owner)->get($this->url())->assertNotFound();
    }

    public function test_uploaded_material_is_stored_on_the_private_disk_only(): void
    {
        $this->actingAs($this->owner)
            ->post(route('dosen.courses.materials.store', $this->course), [
                'title' => 'Slide Minggu 1',
                'description' => 'Slide pengantar.',
                'type' => 'file',
                'file' => UploadedFile::fake()->create('slide.pdf', 10, 'application/pdf'),
            ])
            ->assertRedirect(route('dosen.courses.materials.index', $this->course));

        $uploaded = Material::where('title', 'Slide Minggu 1')->firstOrFail();

        Storage::disk('local')->assertExists($uploaded->file_path);
        Storage::disk('public')->assertMissing($uploaded->file_path);
    }
}
