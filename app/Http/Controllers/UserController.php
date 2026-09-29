<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    /**
     * Menampilkan daftar user.
     */
    public function index(Request $request)
    {
        $query = User::query();

        // Pencarian berdasarkan nama atau email
        if ($request->filled('search')) {
            $search = $request->input('search');

            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('nim_nip', 'like', "%{$search}%");
            });
        }

        // Filter berdasarkan role
        if ($request->filled('role')) {
            $query->where('role', $request->input('role'));
        }

        // Pagination 15 data per halaman dan mempertahankan filter
        $users = $query
            ->orderBy('id')
            ->paginate(15)
            ->withQueryString();

        $userRoutePrefix = $request->routeIs('admin.users.*') ? 'admin.users' : 'users';

        return view('courses.user', compact('users', 'userRoutePrefix'));
    }

    /**
     * Menampilkan form tambah user.
     */
    public function create()
    {
        return view('users.create');
    }

    /**
     * Menyimpan user baru.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8'],
            'nim_nip' => ['required', 'string', 'max:50', 'unique:users,nim_nip'],
            'role' => ['required', 'in:admin,dosen,mahasiswa'],
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'nim_nip' => $validated['nim_nip'],
        ]);

        // Role tetap diberikan secara eksplisit.
        $user->role = $validated['role'];
        $user->save();

        $userRoutePrefix = $request->routeIs('admin.users.*') ? 'admin.users' : 'users';

        return redirect()
            ->route($userRoutePrefix . '.index')
            ->with('success', 'User berhasil ditambahkan.');
    }

    /**
     * Menampilkan detail user.
     */
    public function show(User $user)
    {
        return view('users.show', compact('user'));
    }

    /**
     * Menampilkan form edit user.
     */
    public function edit(User $user)
    {
        return view('users.edit', compact('user'));
    }

    /**
     * Memperbarui user.
     */
    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => [
                'required',
                'email',
                'max:255',
                Rule::unique('users', 'email')->ignore($user->id),
            ],
            'password' => ['nullable', 'string', 'min:8'],
            'nim_nip' => [
                'required',
                'string',
                'max:50',
                Rule::unique('users', 'nim_nip')->ignore($user->id),
            ],
            'role' => ['required', 'in:admin,dosen,mahasiswa'],
        ]);

        $user->update([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'nim_nip' => $validated['nim_nip'],
        ]);

        // Role tetap diberikan secara eksplisit.
        $user->role = $validated['role'];

        // Password hanya diubah kalau diisi.
        if (!empty($validated['password'])) {
            $user->password = Hash::make($validated['password']);
        }

        $user->save();

        $userRoutePrefix = $request->routeIs('admin.users.*') ? 'admin.users' : 'users';

        return redirect()
            ->route($userRoutePrefix . '.show', $user)
            ->with('success', 'User berhasil diperbarui.');
    }

    /**
     * Menghapus user.
     */
    public function destroy(Request $request, User $user)
    {
        $user->delete();
        $userRoutePrefix = $request->routeIs('admin.users.*') ? 'admin.users' : 'users';

        return redirect()
            ->route($userRoutePrefix . '.index')
            ->with('success', 'User berhasil dihapus.');
    }
}