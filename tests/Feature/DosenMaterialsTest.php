<?php

namespace Tests\Feature;

use App\Models\Course;
use App\Models\Material;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DosenMaterialsTest extends TestCase
{
    use RefreshDatabase;

    public function test_material_index_renders_styled_list_and_confirmation_dialog(): void
    {
        $lecturer = User::factory()->dosen()->create();
        $course = Course::factory()->for($lecturer, 'lecturer')->create();
        $material = new Material([
            'title' => 'Panduan Praktikum',
            'description' => 'Referensi untuk kelas.',
            'type' => 'link',
            'external_url' => 'https://example.test/panduan',
        ]);
        $material->uploaded_by = $lecturer->id;
        $course->materials()->save($material);

        $this->actingAs($lecturer)
            ->get(route('dosen.courses.materials.index', $course))
            ->assertOk()
            ->assertSee('dosen-materials__header', false)
            ->assertSee('Panduan Praktikum')
            ->assertSee('Buka tautan materi')
            ->assertSee('Hapus materi ini?')
            ->assertSee('Ya, hapus materi')
            ->assertDontSee("confirm('Hapus materi?')")
            ->assertSee(route('dosen.courses.materials.edit', [$course, $material]));
    }

    public function test_material_create_and_edit_pages_render_styled_forms_with_existing_values(): void
    {
        $lecturer = User::factory()->dosen()->create();
        $course = Course::factory()->for($lecturer, 'lecturer')->create();
        $material = new Material([
            'title' => 'Catatan Minggu 1',
            'description' => 'Pengantar materi.',
            'type' => 'file',
            'file_path' => 'materials/notes.pdf',
            'original_name' => 'notes.pdf',
        ]);
        $material->uploaded_by = $lecturer->id;
        $course->materials()->save($material);

        $this->actingAs($lecturer)
            ->get(route('dosen.courses.materials.create', $course))
            ->assertOk()
            ->assertSee('Tambah Materi')
            ->assertSee('dosen-material-form__conditional', false)
            ->assertSee('multipart/form-data');

        $this->get(route('dosen.courses.materials.edit', [$course, $material]))
            ->assertOk()
            ->assertSee('Edit Materi')
            ->assertSee('Catatan Minggu 1')
            ->assertSee('notes.pdf')
            ->assertSee('Kosongkan jika tidak ingin mengganti file.');
    }

    public function test_updating_material_without_replacement_file_keeps_existing_file_metadata(): void
    {
        $lecturer = User::factory()->dosen()->create();
        $course = Course::factory()->for($lecturer, 'lecturer')->create();
        $material = new Material([
            'title' => 'Materi Lama',
            'description' => '',
            'type' => 'file',
            'file_path' => 'materials/notes.pdf',
            'original_name' => 'notes.pdf',
            'file_size' => 2048,
            'mime_type' => 'application/pdf',
        ]);
        $material->uploaded_by = $lecturer->id;
        $course->materials()->save($material);

        $this->actingAs($lecturer)
            ->put(route('dosen.courses.materials.update', [$course, $material]), [
                'title' => 'Materi Diperbarui',
                'description' => 'Deskripsi terbaru.',
                'type' => 'file',
            ])
            ->assertRedirect(route('dosen.courses.materials.index', $course));

        $this->assertDatabaseHas('materials', [
            'id' => $material->id,
            'title' => 'Materi Diperbarui',
            'file_path' => 'materials/notes.pdf',
            'original_name' => 'notes.pdf',
            'file_size' => 2048,
            'mime_type' => 'application/pdf',
        ]);
    }
}
