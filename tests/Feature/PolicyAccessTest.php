<?php

namespace Tests\Feature;

use App\Models\Assignment;
use App\Models\Course;
use App\Models\Grade;
use App\Models\Material;
use App\Models\Submission;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Gate;
use Tests\TestCase;

/**
 * Menguji Policy terhadap matriks "Bagian 3. Peran & Hak Akses".
 *
 * PERHATIAN: test ini memakai RefreshDatabase. phpunit.xml di repo ini tidak
 * mengunci koneksi DB (barisnya di-comment), jadi jalankan dengan SQLite
 * in-memory agar database dev TIDAK terhapus:
 *
 *   DB_CONNECTION=sqlite DB_DATABASE=:memory: php artisan test --filter=PolicyAccessTest
 */
class PolicyAccessTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;
    private User $dosenA;
    private User $dosenB;
    private User $mhsA1;   // terdaftar di MK A
    private User $mhsA2;   // terdaftar di MK A
    private User $mhsB;    // terdaftar di MK B saja
    private Course $courseA;
    private Course $courseB;
    private Material $materialA;
    private Material $materialB;
    private Assignment $assignmentA;
    private Assignment $draftA;
    private Assignment $assignmentB;
    private Assignment $futureA;   // tenggat belum lewat
    private Submission $subFuture;
    private Submission $subA1;
    private Submission $subA2;
    private Submission $subB;
    private Grade $gradeA1;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->admin()->create();
        $this->dosenA = User::factory()->dosen()->create();
        $this->dosenB = User::factory()->dosen()->create();
        $this->mhsA1 = User::factory()->mahasiswa()->create();
        $this->mhsA2 = User::factory()->mahasiswa()->create();
        $this->mhsB = User::factory()->mahasiswa()->create();

        $this->courseA = Course::factory()->create(['lecturer_id' => $this->dosenA->id]);
        $this->courseB = Course::factory()->create(['lecturer_id' => $this->dosenB->id]);

        $this->courseA->students()->attach([
            $this->mhsA1->id => ['enrolled_at' => now()],
            $this->mhsA2->id => ['enrolled_at' => now()],
        ]);
        $this->courseB->students()->attach([$this->mhsB->id => ['enrolled_at' => now()]]);

        $this->materialA = $this->makeMaterial($this->courseA, $this->dosenA);
        $this->materialB = $this->makeMaterial($this->courseB, $this->dosenB);

        // Tenggat sudah lewat: nilai boleh diberikan (keputusan Q5).
        $this->assignmentA = Assignment::factory()->create([
            'course_id' => $this->courseA->id, 'created_by' => $this->dosenA->id,
            'due_at' => now()->subDay(),
        ]);
        $this->futureA = Assignment::factory()->create([
            'course_id' => $this->courseA->id, 'created_by' => $this->dosenA->id,
            'due_at' => now()->addDays(3),
        ]);
        $this->draftA = Assignment::factory()->create([
            'course_id' => $this->courseA->id, 'created_by' => $this->dosenA->id, 'status' => 'draft',
        ]);
        $this->assignmentB = Assignment::factory()->create([
            'course_id' => $this->courseB->id, 'created_by' => $this->dosenB->id,
            'due_at' => now()->subDay(),
        ]);

        $this->subA1 = Submission::factory()->create([
            'assignment_id' => $this->assignmentA->id, 'user_id' => $this->mhsA1->id,
        ]);
        $this->subA2 = Submission::factory()->create([
            'assignment_id' => $this->assignmentA->id, 'user_id' => $this->mhsA2->id,
        ]);
        $this->subFuture = Submission::factory()->create([
            'assignment_id' => $this->futureA->id, 'user_id' => $this->mhsA2->id,
        ]);
        $this->subB = Submission::factory()->create([
            'assignment_id' => $this->assignmentB->id, 'user_id' => $this->mhsB->id,
        ]);

        $this->gradeA1 = Grade::factory()->create([
            'submission_id' => $this->subA1->id, 'graded_by' => $this->dosenA->id,
        ]);
    }

    private function makeMaterial(Course $course, User $uploader): Material
    {
        // uploaded_by tidak ada di $fillable (controller mengisinya langsung),
        // jadi atribut ini diset manual agar model repo tidak perlu diubah.
        $material = new Material([
            'course_id' => $course->id,
            'title' => 'Materi',
            'description' => 'Deskripsi',
            'type' => 'file',
            'file_path' => 'materials/x.pdf',
            'original_name' => 'x.pdf',
        ]);
        $material->uploaded_by = $uploader->id;
        $material->save();

        return $material;
    }

    public function test_policies_are_auto_discovered(): void
    {
        foreach ([Course::class, Material::class, Assignment::class, Submission::class, Grade::class] as $model) {
            $this->assertNotNull(Gate::getPolicyFor($model), "Policy untuk {$model} tidak ditemukan.");
        }
    }

    // ---------- Course ----------

    public function test_only_admin_can_create_or_delete_course_but_owner_can_update_it(): void
    {
        $this->assertTrue($this->admin->can('create', Course::class));
        $this->assertTrue($this->admin->can('update', $this->courseA));
        $this->assertTrue($this->admin->can('delete', $this->courseA));

        $this->assertTrue($this->dosenA->can('update', $this->courseA));
        $this->assertFalse($this->dosenB->can('update', $this->courseA));
        $this->assertFalse($this->mhsA1->can('update', $this->courseA));

        foreach ([$this->dosenA, $this->dosenB, $this->mhsA1] as $user) {
            $this->assertFalse($user->can('create', Course::class));
            $this->assertFalse($user->can('delete', $this->courseA));
        }
    }

    public function test_course_view_is_limited_to_admin_owner_and_enrolled(): void
    {
        $this->assertTrue($this->admin->can('view', $this->courseA));
        $this->assertTrue($this->dosenA->can('view', $this->courseA));
        $this->assertTrue($this->mhsA1->can('view', $this->courseA));

        $this->assertFalse($this->dosenB->can('view', $this->courseA));
        $this->assertFalse($this->mhsB->can('view', $this->courseA));
    }

    public function test_enrollment_is_managed_by_admin_or_owning_lecturer_only(): void
    {
        $this->assertTrue($this->admin->can('manageEnrollment', $this->courseA));
        $this->assertTrue($this->dosenA->can('manageEnrollment', $this->courseA));
        $this->assertFalse($this->dosenB->can('manageEnrollment', $this->courseA));
        $this->assertFalse($this->mhsA1->can('manageEnrollment', $this->courseA));
    }

    // ---------- Material ----------

    public function test_material_read_access_follows_course_membership(): void
    {
        foreach (['view', 'download'] as $ability) {
            $this->assertTrue($this->admin->can($ability, $this->materialA));
            $this->assertTrue($this->dosenA->can($ability, $this->materialA));
            $this->assertTrue($this->mhsA1->can($ability, $this->materialA));

            $this->assertFalse($this->dosenB->can($ability, $this->materialA));
            $this->assertFalse($this->mhsB->can($ability, $this->materialA), 'Mahasiswa MK lain tidak boleh mengunduh.');
        }

        $this->assertTrue($this->mhsA1->can('viewAny', [Material::class, $this->courseA]));
        $this->assertFalse($this->mhsB->can('viewAny', [Material::class, $this->courseA]));
    }

    public function test_material_write_access_admin_and_owning_lecturer_only(): void
    {
        $this->assertTrue($this->admin->can('create', [Material::class, $this->courseA]));
        $this->assertTrue($this->dosenA->can('create', [Material::class, $this->courseA]));
        $this->assertFalse($this->dosenB->can('create', [Material::class, $this->courseA]));
        $this->assertFalse($this->mhsA1->can('create', [Material::class, $this->courseA]));

        foreach (['update', 'delete'] as $ability) {
            $this->assertTrue($this->admin->can($ability, $this->materialA));
            $this->assertTrue($this->dosenA->can($ability, $this->materialA));
            $this->assertFalse($this->dosenB->can($ability, $this->materialA));
            $this->assertFalse($this->mhsA1->can($ability, $this->materialA));
        }
    }

    // ---------- Assignment ----------

    public function test_student_sees_published_assignment_but_draft_is_hidden_as_404(): void
    {
        $this->assertTrue($this->mhsA1->can('view', $this->assignmentA));

        $response = Gate::forUser($this->mhsA1)->inspect('view', $this->draftA);
        $this->assertTrue($response->denied());
        $this->assertSame(404, $response->status());

        // Mahasiswa yang tidak terdaftar: 403 biasa, bukan 404.
        $outsider = Gate::forUser($this->mhsB)->inspect('view', $this->assignmentA);
        $this->assertTrue($outsider->denied());
        $this->assertNotSame(404, $outsider->status());

        $this->assertTrue($this->dosenA->can('view', $this->draftA));
        $this->assertTrue($this->admin->can('view', $this->draftA));
    }

    public function test_assignment_write_access_admin_and_owning_lecturer_only(): void
    {
        $this->assertTrue($this->admin->can('create', [Assignment::class, $this->courseA]));
        $this->assertTrue($this->dosenA->can('create', [Assignment::class, $this->courseA]));
        $this->assertFalse($this->dosenB->can('create', [Assignment::class, $this->courseA]));
        $this->assertFalse($this->mhsA1->can('create', [Assignment::class, $this->courseA]));

        foreach (['update', 'delete'] as $ability) {
            $this->assertTrue($this->dosenA->can($ability, $this->assignmentA));
            $this->assertFalse($this->dosenB->can($ability, $this->assignmentA), 'Dosen tidak boleh menyentuh MK orang lain.');
            $this->assertFalse($this->mhsA1->can($ability, $this->assignmentA));
        }
    }

    // ---------- Submission ----------

    public function test_student_cannot_view_or_download_another_students_submission(): void
    {
        foreach (['view', 'download'] as $ability) {
            $this->assertTrue($this->mhsA1->can($ability, $this->subA1));
            $this->assertFalse($this->mhsA1->can($ability, $this->subA2), 'Mahasiswa A tidak boleh mengakses submission B (satu MK).');
            $this->assertFalse($this->mhsA1->can($ability, $this->subB), 'Submission MK lain juga ditolak.');
        }
    }

    public function test_lecturer_reads_submissions_of_own_course_only(): void
    {
        foreach (['view', 'download'] as $ability) {
            $this->assertTrue($this->dosenA->can($ability, $this->subA1));
            $this->assertFalse($this->dosenA->can($ability, $this->subB));
            $this->assertFalse($this->dosenB->can($ability, $this->subA1));
        }

        $this->assertTrue($this->dosenA->can('viewAny', [Submission::class, $this->assignmentA]));
        $this->assertFalse($this->dosenB->can('viewAny', [Submission::class, $this->assignmentA]));
    }

    public function test_admin_can_read_submissions_but_cannot_submit_or_change_them(): void
    {
        // Keputusan Q2: admin boleh membuat tugas, jadi boleh melihat hasilnya.
        $this->assertTrue($this->admin->can('view', $this->subA1));
        $this->assertTrue($this->admin->can('download', $this->subA1));
        $this->assertTrue($this->admin->can('viewAny', [Submission::class, $this->assignmentA]));

        // Matriks: admin tidak mengumpulkan tugas.
        $this->assertFalse($this->admin->can('create', [Submission::class, $this->assignmentA]));
        $this->assertFalse($this->admin->can('update', $this->subA2));
    }

    public function test_only_enrolled_students_can_submit(): void
    {
        $this->assertTrue($this->mhsA1->can('create', [Submission::class, $this->assignmentA]));

        $this->assertFalse($this->mhsB->can('create', [Submission::class, $this->assignmentA]), 'Tidak terdaftar.');
        $this->assertFalse($this->dosenA->can('create', [Submission::class, $this->assignmentA]));
        $this->assertFalse($this->admin->can('create', [Submission::class, $this->assignmentA]));

        $draft = Gate::forUser($this->mhsA1)->inspect('create', [Submission::class, $this->draftA]);
        $this->assertSame(404, $draft->status(), 'Tugas draft disembunyikan (404).');
    }

    // tests/Feature/PolicyAccessTest.php line 263-270
    public function test_resubmit_allowed_only_for_owner_until_graded(): void
    {
        $this->assertFalse($this->mhsA1->can('update', $this->subA1), 'Sudah dinilai -> terkunci.');
        $this->assertTrue($this->mhsA2->can('update', $this->subA2), 'Belum dinilai -> boleh kirim ulang.');
        $this->assertFalse($this->mhsA1->can('update', $this->subA2), 'Bukan pemilik.');
        $this->assertFalse($this->dosenA->can('update', $this->subA2));
        $this->assertFalse($this->admin->can('update', $this->subA2)); // <-- Ini akan jadi FALSE, sehingga test admin PASS!
    }

    public function test_nobody_can_delete_a_submission(): void
    {
        foreach ([$this->admin, $this->dosenA, $this->mhsA2] as $user) {
            $this->assertFalse($user->can('delete', $this->subA2));
        }
    }

    // ---------- Grade ----------

    public function test_only_owning_lecturer_can_give_or_change_grades(): void
    {
        $this->assertTrue($this->dosenA->can('create', [Grade::class, $this->subA2]));
        $this->assertTrue($this->dosenA->can('update', $this->gradeA1));

        $this->assertFalse($this->dosenB->can('create', [Grade::class, $this->subA2]), 'Dosen MK lain.');
        $this->assertFalse($this->dosenB->can('update', $this->gradeA1));
        $this->assertFalse($this->admin->can('create', [Grade::class, $this->subA2]), 'Admin tidak memberi nilai.');
        $this->assertFalse($this->admin->can('update', $this->gradeA1));
        $this->assertFalse($this->mhsA1->can('create', [Grade::class, $this->subA2]));
        $this->assertFalse($this->mhsA1->can('update', $this->gradeA1));
    }

    public function test_grade_visibility_matches_matrix(): void
    {
        $this->assertTrue($this->admin->can('view', $this->gradeA1));
        $this->assertTrue($this->dosenA->can('view', $this->gradeA1));
        $this->assertTrue($this->mhsA1->can('view', $this->gradeA1), 'Mahasiswa melihat nilainya sendiri.');

        $this->assertFalse($this->mhsA2->can('view', $this->gradeA1), 'Mahasiswa tidak boleh melihat nilai orang lain.');
        $this->assertFalse($this->dosenB->can('view', $this->gradeA1));
        $this->assertFalse($this->mhsB->can('view', $this->gradeA1));
    }

    public function test_grading_is_only_allowed_after_the_deadline(): void
    {
        $denied = Gate::forUser($this->dosenA)->inspect('create', [Grade::class, $this->subFuture]);
        $this->assertTrue($denied->denied());
        $this->assertStringContainsString('tenggat', (string) $denied->message());

        $this->assertTrue($this->dosenA->can('create', [Grade::class, $this->subA2]), 'Tenggat sudah lewat.');

        // Revisi nilai juga terkunci sebelum tenggat.
        $early = Grade::factory()->create([
            'submission_id' => $this->subFuture->id, 'graded_by' => $this->dosenA->id,
        ]);
        $this->assertFalse($this->dosenA->can('update', $early));
    }

    public function test_nobody_can_delete_a_grade(): void
    {
        foreach ([$this->admin, $this->dosenA, $this->mhsA1] as $user) {
            $this->assertFalse($user->can('delete', $this->gradeA1));
        }
    }
}
