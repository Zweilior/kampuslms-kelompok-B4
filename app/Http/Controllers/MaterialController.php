<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\Material;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;

class MaterialController extends Controller
{
    /**
     * Menampilkan daftar materi dari suatu mata kuliah.
     */
    public function index(Course $course)
    {
        Gate::authorize('viewAny', [Material::class, $course]); // BARU

        $materials = $course->materials()
            ->latest()
            ->paginate(15);

        return view('materials.index', compact('course', 'materials'));
    }

    /**
     * [ADMIN] Menampilkan daftar semua materi (Global).
     * CATATAN: Tidak memanggil Gate::authorize('viewAny') karena Policy 
     * membutuhkan argumen Course. Keamanan bergantung pada middleware 'role:admin'.
     */
    public function adminIndex(Request $request)
    {
        $query = Material::with(['course.lecturer', 'uploader']);

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($materialQuery) use ($search) {
                $materialQuery->where('title', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%")
                    ->orWhereHas('course', function ($courseQuery) use ($search) {
                        $courseQuery->where('name', 'like', "%{$search}%")
                            ->orWhere('code', 'like', "%{$search}%");
                    });
            });
        }

        if ($request->filled('course_id')) {
            $query->where('course_id', $request->input('course_id'));
        }

        $materials = $query->latest()->paginate(15)->withQueryString();
        $courses = Course::query()->orderBy('name')->get(['id', 'name']);
        $totalMaterials = Material::count();

        return view('admin.materials.index', compact('materials', 'courses', 'totalMaterials'));
    }

    /**
     * [ADMIN] Form tambah materi.
     * CATATAN: Keamanan bergantung pada middleware 'role:admin'.
     */
    public function adminCreate()
    {
        $courses = Course::query()->orderBy('name')->get(['id', 'name']);
        return view('admin.materials.form', ['material' => null, 'courses' => $courses]);
    }

    /**
     * [ADMIN] Simpan materi baru.
     */
    public function adminStore(Request $request)
    {
        // 1. AMBIL COURSE TUJUAN UNTUK OTORISASI
        $course = Course::findOrFail($request->input('course_id'));
        
        // 2. CEK HAK AKSES (Apakah user boleh create di course ini?)
        Gate::authorize('create', [Material::class, $course]); // BARU

        // 3. Validasi & Simpan
        $validated = $request->validate($this->adminMaterialRules($request, true));
        
        $uploaderId = Auth::id() ?? User::where('role', 'admin')->value('id');
        if (!$uploaderId) {
            return back()->withErrors(['uploaded_by' => 'Akun admin tidak ditemukan.'])->withInput();
        }

        $file = $validated['file'] ?? null;
        unset($validated['file']);

        $material = new Material($validated);
        $material->uploaded_by = $uploaderId;
        $material->external_url = $material->type === 'link' ? $validated['external_url'] : null;

        if ($file) {
            $material->file_path = $file->store('materials', 'public');
            $material->original_name = $file->getClientOriginalName();
            $material->file_size = $file->getSize();
            $material->mime_type = $file->getMimeType();
        }

        $material->save();

        return redirect()->route('admin.materials.index')->with('success', 'Materi berhasil ditambahkan.');
    }

    /**
     * [ADMIN] Edit materi.
     */
    public function adminEdit(Material $material)
    {
        Gate::authorize('update', $material); // BARU

        $courses = Course::query()->orderBy('name')->get(['id', 'name']);
        return view('admin.materials.form', compact('material', 'courses'));
    }

    /**
     * [ADMIN] Update materi.
     */
    public function adminUpdate(Request $request, Material $material)
    {
        Gate::authorize('update', $material); // BARU

        $validated = $request->validate($this->adminMaterialRules($request, !$material->file_path));
        
        $file = $validated['file'] ?? null;
        unset($validated['file']);

        $oldFilePath = $material->file_path;
        $material->fill($validated);
        $material->external_url = $material->type === 'link' ? $validated['external_url'] : null;

        if ($material->type === 'link') {
            $material->file_path = null;
            $material->original_name = null;
            $material->file_size = null;
            $material->mime_type = null;
        } elseif ($file) {
            $material->file_path = $file->store('materials', 'public');
            $material->original_name = $file->getClientOriginalName();
            $material->file_size = $file->getSize();
            $material->mime_type = $file->getMimeType();
        }

        $material->save();

        if ($oldFilePath && $oldFilePath !== $material->file_path) {
            Storage::disk('public')->delete($oldFilePath);
        }

        return redirect()->route('admin.materials.index')->with('success', 'Materi berhasil diperbarui.');
    }

    /**
     * [ADMIN] Hapus materi.
     */
    public function adminDestroy(Material $material)
    {
        Gate::authorize('delete', $material); // BARU

        $filePath = $material->file_path;
        $material->delete();

        if ($filePath) {
            Storage::disk('public')->delete($filePath);
        }

        return redirect()->route('admin.materials.index')->with('success', 'Materi berhasil dihapus.');
    }

    private function adminMaterialRules(Request $request, bool $requireFile): array
    {
        $fileRules = ['nullable', 'file', 'max:20480'];
        if ($requireFile && $request->input('type') === 'file') {
            $fileRules = ['required', 'file', 'max:20480'];
        }

        return [
            'course_id' => ['required', 'integer', 'exists:courses,id'],
            'title' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string'],
            'type' => ['required', 'in:file,link'],
            'external_url' => ['nullable', 'required_if:type,link', 'url', 'max:2048'],
            'file' => $fileRules,
        ];
    }

    // --------------------------------------------------------------------------
    // DOSEN METHODS
    // --------------------------------------------------------------------------

    public function dosenIndex(Course $course)
    {
        Gate::authorize('viewAny', [Material::class, $course]); // BARU

        $materials = $course->materials()->latest()->get();
        return view('dosen.materials.index', compact('course', 'materials'));
    }

    public function dosenCreate(Course $course)
    {
        Gate::authorize('create', [Material::class, $course]); // BARU
        return view('dosen.materials.create', compact('course'));
    }

    public function dosenStore(Request $request, Course $course)
    {
        Gate::authorize('create', [Material::class, $course]); // BARU

        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'type' => ['required', 'in:file,link'],
            'external_url' => ['nullable', 'required_if:type,link', 'url', 'max:2048'],
            'file' => ['nullable', 'file', 'max:20480'],
        ]);

        $material = $course->materials()->make($validated);
        $material->uploaded_by = Auth::id() ?? User::where('role', 'dosen')->value('id');

        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $material->file_path = $file->store('materials', 'public');
            $material->original_name = $file->getClientOriginalName();
            $material->file_size = $file->getSize();
            $material->mime_type = $file->getMimeType();
        }

        $material->save();

        return redirect()->route('dosen.courses.materials.index', $course)
            ->with('success', 'Materi berhasil ditambahkan.');
    }

    public function dosenEdit(Course $course, Material $material)
    {
        Gate::authorize('update', $material); // BARU
        return view('dosen.materials.edit', compact('course', 'material'));
    }

    public function dosenUpdate(Request $request, Course $course, Material $material)
    {
        Gate::authorize('update', $material); // BARU

        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'type' => ['required', 'in:file,link'],
            'external_url' => ['nullable', 'required_if:type,link', 'url', 'max:2048'],
            'file' => ['nullable', 'file', 'max:20480'],
        ]);

        $oldFile = $material->file_path;
        $material->fill($validated);

        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $material->file_path = $file->store('materials', 'public');
            $material->original_name = $file->getClientOriginalName();
            $material->file_size = $file->getSize();
            $material->mime_type = $file->getMimeType();
        }

        $material->save();

        if ($oldFile && $oldFile !== $material->file_path) {
            Storage::disk('public')->delete($oldFile);
        }

        return redirect()->route('dosen.courses.materials.index', $course)
            ->with('success', 'Materi berhasil diperbarui.');
    }

    public function dosenDestroy(Course $course, Material $material)
    {
        Gate::authorize('delete', $material); // BARU

        $file = $material->file_path;
        $material->delete();

        if ($file) {
            Storage::disk('public')->delete($file);
        }

        return redirect()->route('dosen.courses.materials.index', $course)
            ->with('success', 'Materi berhasil dihapus.');
    }

    // --------------------------------------------------------------------------
    // STANDARD METHODS (Legacy/General)
    // --------------------------------------------------------------------------

    public function create(Course $course)
    {
        Gate::authorize('create', [Material::class, $course]); // BARU
        return view('materials.create', compact('course'));
    }

    public function store(Request $request, Course $course)
    {
        Gate::authorize('create', [Material::class, $course]); // BARU

        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'type' => ['required', 'string', 'max:50'],
            'external_url' => ['nullable', 'url', 'max:2048'],
        ]);

        $course->materials()->create($validated);

        return redirect()
            ->route('courses.materials.index', $course)
            ->with('success', 'Materi berhasil ditambahkan.');
    }

    public function show(Course $course, Material $material)
    {
        Gate::authorize('view', $material); // BARU
        return view('materials.show', compact('course', 'material'));
    }

    public function edit(Course $course, Material $material)
    {
        Gate::authorize('update', $material); // BARU
        return view('materials.edit', compact('course', 'material'));
    }

    public function update(Request $request, Course $course, Material $material)
    {
        Gate::authorize('update', $material); // BARU

        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'type' => ['required', 'string', 'max:50'],
            'external_url' => ['nullable', 'url', 'max:2048'],
        ]);

        $material->update($validated);

        return redirect()
            ->route('courses.materials.show', [$course, $material])
            ->with('success', 'Materi berhasil diperbarui.');
    }

    public function destroy(Course $course, Material $material)
    {
        Gate::authorize('delete', $material); // BARU
        $material->delete();

        return redirect()
            ->route('courses.materials.index', $course)
            ->with('success', 'Materi berhasil dihapus.');
    }
}