<?php

namespace Tests\Feature;

use App\Models\Assignment;
use App\Models\Course;
use App\Models\Material;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Menguji penutupan IDOR di area /mahasiswa/courses/{course}/...
 * (baris 30, 32-36 pada docs/keamanan.md).
 *
 * Jalankan dengan SQLite in-memory agar DB dev aman:
 *   DB_CONNECTION=sqlite DB_DATABASE=:memory: php artisan test --filter=MahasiswaAccessTest
 */
class MahasiswaAccessTest extends TestCase
{
    use RefreshDatabase;

    private User $enrolled;
    private User $outsider;
    private Course $course;
    private Assignment $published;
    private Assignment $draft;

    protected function setUp(): void
    {
        parent::setUp();

        $lecturer = User::factory()->dosen()->create();
        $this->enrolled = User::factory()->mahasiswa()->create();
        $this->outsider = User::factory()->mahasiswa()->create();

        $this->course = Course::factory()->create(['lecturer_id' => $lecturer->id]);
        $this->course->students()->attach($this->enrolled->id, ['enrolled_at' => now()]);

        $this->published = Assignment::factory()->create([
            'course_id' => $this->course->id,
            'created_by' => $lecturer->id,
            'status' => 'published',
        ]);
        $this->draft = Assignment::factory()->create([
            'course_id' => $this->course->id,
            'created_by' => $lecturer->id,
            'status' => 'draft',
        ]);

        // uploaded_by tidak ada di $fillable, jadi diisi manual.
        $material = new Material([
            'course_id' => $this->course->id,
            'title' => 'Materi',
            'description' => 'Deskripsi',
            'type' => 'link',
            'external_url' => 'https://example.test/materi',
        ]);
        $material->uploaded_by = $lecturer->id;
        $material->save();
    }

    /** @return array<string, string> label => URL */
    private function courseUrls(): array
    {
        return [
            'detail MK (No 30)' => route('mahasiswa.courses.show', $this->course),
            'nilai MK (No 32)' => route('mahasiswa.grades.courses.show', $this->course),
            'daftar tugas (No 33)' => route('mahasiswa.courses.assignments.index', $this->course),
            'daftar materi (No 34)' => route('mahasiswa.courses.materials.index', $this->course),
            'daftar submission (No 35)' => route('mahasiswa.courses.assignments.submissions.index', [$this->course, $this->published]),
            'form submit (No 36)' => route('mahasiswa.courses.assignments.submissions.create', [$this->course, $this->published]),
        ];
    }

    public function test_student_not_enrolled_gets_403_on_every_course_route(): void
    {
        foreach ($this->courseUrls() as $label => $url) {
            $response = $this->actingAs($this->outsider)->get($url);

            $this->assertSame(403, $response->getStatusCode(), "Mahasiswa non-terdaftar seharusnya 403 pada {$label}");
        }
    }

    public function test_enrolled_student_can_open_course_routes(): void
    {
        foreach ($this->courseUrls() as $label => $url) {
            $response = $this->actingAs($this->enrolled)->get($url);

            $this->assertSame(200, $response->getStatusCode(), "Mahasiswa terdaftar seharusnya 200 pada {$label}");
        }
    }

    public function test_draft_assignment_is_blocked_for_enrolled_student(): void
    {
        // SubmissionPolicy@viewAny mengembalikan false untuk draft -> 403.
        $this->actingAs($this->enrolled)
            ->get(route('mahasiswa.courses.assignments.submissions.index', [$this->course, $this->draft]))
            ->assertForbidden();

        // SubmissionPolicy@create memakai denyAsNotFound() untuk draft -> 404.
        $this->actingAs($this->enrolled)
            ->get(route('mahasiswa.courses.assignments.submissions.create', [$this->course, $this->draft]))
            ->assertNotFound();
    }

    public function test_non_published_assignment_is_not_listed_for_student(): void
    {
        $this->actingAs($this->enrolled)
            ->get(route('mahasiswa.courses.assignments.index', $this->course))
            ->assertOk()
            ->assertSee($this->published->title)
            ->assertDontSee($this->draft->instructions);
    }

    public function test_guest_gets_401_and_other_roles_are_forbidden(): void
    {
        $url = route('mahasiswa.courses.materials.index', $this->course);

        // Web: AuthenticationException dirender sebagai halaman 401 (bootstrap/app.php).
        $this->get($url)->assertUnauthorized();

        $this->actingAs(User::factory()->dosen()->create())->get($url)->assertForbidden();
        $this->actingAs(User::factory()->admin()->create())->get($url)->assertForbidden();
    }
}
