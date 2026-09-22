<x-layout title="Daftar Mata Kuliah" active-nav="courses">

    {{-- WRAPPER ALPINE JS UTAMA --}}
    <div x-data="{ 
        openCreateModal: {{ $errors->any() && !old('_method') ? 'true' : 'false' }},
        openEditModal: {{ $errors->any() && old('_method') === 'PUT' ? 'true' : 'false' }},
        editCourse: {
            id: '{{ old('edit_id', '') }}',
            code: '{{ old('code', '') }}',
            name: '{{ old('name', '') }}',
            description: '{{ old('description', '') }}',
            sks: '{{ old('sks', 3) }}',
            status: '{{ old('status', 'active') }}',
            lecturer_id: '{{ old('lecturer_id', '') }}'
        },
        setEditCourse(course) {
            this.editCourse = {
                id: course.id,
                code: course.code,
                name: course.name,
                description: course.description || '',
                sks: course.sks,
                status: course.status,
                lecturer_id: course.lecturer_id || ''
            };
            this.openEditModal = true;
        }
    }">

        {{-- HERO HEADER --}}
        <div class="flex flex-col lg:flex-row items-start lg:items-center justify-between gap-y-space-sm mb-space-lg">

            <div class="flex flex-col">
                <div class="flex items-center gap-space-xs mb-space-2xs">
                    <span class="px-space-xs py-0.5 rounded-full bg-secondary-container text-on-secondary-container font-label-sm text-label-sm tracking-wide uppercase">
                        Semester Aktif
                    </span>
                    <span class="w-1 h-1 rounded-full bg-outline"></span>
                    <span class="font-label-sm text-label-sm text-on-surface-variant">
                        {{ $courses->total() }} Mata Kuliah Terdaftar
                    </span>
                </div>

                <h1 class="font-headline-lg text-headline-lg tracking-tight">
                    Daftar Mata Kuliah
                </h1>

                <p class="font-body-md text-body-md text-on-surface-variant mt-0.5">
                    Kelola dan pantau seluruh mata kuliah aktif beserta dosen pengampu semester ini.
                </p>
            </div>

            {{-- TOMBOL TRIGGER MODAL TAMBAH --}}
            <button 
                type="button"
                @click="openCreateModal = true"
                class="flex items-center justify-center gap-space-2xs px-space-md py-2.5 rounded-xl bg-primary text-on-primary font-label-lg text-label-lg hover:bg-primary-container transition-all duration-200 shadow-[0_0_20px_rgba(173,210,134,0.3)]"
            >
                <span class="material-symbols-outlined text-headline-sm">
                    add_circle
                </span>
                <span>
                    Tambah Mata Kuliah
                </span>
            </button>

        </div>


        {{-- SUCCESS MESSAGE --}}
        @if (session('success'))
            <div class="mb-space-md p-space-sm rounded-xl bg-secondary-container text-on-secondary-container">
                {{ session('success') }}
            </div>
        @endif


        {{-- METRIK RINGKASAN --}}
        <div class="grid grid-cols-1 md:grid-cols-3 gap-bento-gap-desktop mb-space-lg">
            {{-- Total Course --}}
            <div class="bg-surface-container-low p-space-lg rounded-2xl shadow-md relative overflow-hidden">
                <div class="absolute -right-6 -bottom-6 w-28 h-28 rounded-full bg-primary/5 blur-2xl pointer-events-none"></div>
                <div class="flex items-center gap-space-xs">
                    <div class="w-9 h-9 rounded-xl bg-surface-container flex items-center justify-center text-primary">
                        <span class="material-symbols-outlined text-headline-sm">library_books</span>
                    </div>
                    <span class="font-label-lg text-label-lg text-on-surface-variant">Total Mata Kuliah</span>
                </div>
                <div class="mt-space-md flex items-baseline gap-space-xs">
                    <span class="font-display-lg text-display-lg tracking-tight font-bold leading-none">{{ $courses->total() }}</span>
                    <span class="font-headline-sm text-headline-sm text-on-surface-variant font-medium">Kelas</span>
                </div>
            </div>

            {{-- Course Aktif --}}
            <div class="bg-surface-container-low p-space-lg rounded-2xl shadow-md relative overflow-hidden">
                <div class="absolute -right-6 -bottom-6 w-28 h-28 rounded-full bg-secondary/5 blur-2xl pointer-events-none"></div>
                <div class="flex items-center gap-space-xs">
                    <div class="w-9 h-9 rounded-xl bg-surface-container flex items-center justify-center text-secondary">
                        <span class="material-symbols-outlined text-headline-sm">school</span>
                    </div>
                    <span class="font-label-lg text-label-lg text-on-surface-variant">Status</span>
                </div>
                <div class="mt-space-md flex items-baseline gap-space-xs">
                    <span class="font-display-lg text-display-lg tracking-tight font-bold leading-none">{{ $courses->where('status', 'active')->count() }}</span>
                    <span class="font-headline-sm text-headline-sm text-on-surface-variant font-medium">Aktif di Halaman Ini</span>
                </div>
            </div>

            {{-- Dosen Pengampu --}}
            <div class="bg-surface-container-low p-space-lg rounded-2xl shadow-md relative overflow-hidden">
                <div class="absolute -right-6 -bottom-6 w-28 h-28 rounded-full bg-primary/10 blur-2xl pointer-events-none"></div>
                <div class="flex items-center gap-space-xs">
                    <div class="w-9 h-9 rounded-xl bg-surface-container flex items-center justify-center text-primary">
                        <span class="material-symbols-outlined text-headline-sm">how_to_reg</span>
                    </div>
                    <span class="font-label-lg text-label-lg text-on-surface-variant">Dosen Pengampu</span>
                </div>
                <div class="mt-space-md flex items-baseline gap-space-xs">
                    <span class="font-display-lg text-display-lg text-primary tracking-tight font-bold leading-none">{{ $courses->pluck('lecturer_id')->unique()->count() }}</span>
                    <span class="font-headline-sm text-headline-sm text-on-surface-variant font-medium">Dosen</span>
                </div>
            </div>
        </div>


        {{-- TOOLBAR PENCARIAN & FILTER (SERVER-SIDE) --}}
        <form method="GET" action="{{ route('courses.index') }}" class="bg-surface-container-low p-space-sm rounded-2xl flex flex-col md:flex-row items-stretch md:items-center gap-space-sm shadow-sm mb-bento-gap-desktop">
            
            {{-- Input Search --}}
            <div class="flex items-center gap-space-xs bg-surface-container px-space-md py-2 rounded-xl flex-1">
                <span class="material-symbols-outlined text-outline text-headline-sm">search</span>
                <input 
                    name="search" 
                    value="{{ request('search') }}" 
                    class="bg-transparent border-none outline-none font-body-md text-body-md text-on-surface placeholder-outline flex-1 focus:ring-0" 
                    placeholder="Cari kode atau nama mata kuliah..." 
                    type="text" 
                />
            </div>

            {{-- Filter Status --}}
            <div class="flex items-center gap-space-xs">
                <select name="status" onchange="this.form.submit()" class="bg-surface-container border-none outline-none font-body-md text-body-md text-on-surface rounded-xl px-space-md py-2 focus:ring-0">
                    <option value="">Semua Status</option>
                    <option value="active" @selected(request('status') === 'active')>Active</option>
                    <option value="draft" @selected(request('status') === 'draft')>Draft</option>
                    <option value="archived" @selected(request('status') === 'archived')>Archived</option>
                </select>

                {{-- Tombol Submit --}}
                <button type="submit" class="px-space-md py-2 rounded-xl bg-primary text-on-primary font-label-md hover:bg-primary-container transition-colors flex items-center gap-1">
                    <span class="material-symbols-outlined text-sm">search</span>
                    <span>Cari</span>
                </button>

                {{-- Tombol Reset jika ada pencarian/filter --}}
                @if(request()->filled('search') || request()->filled('status'))
                    <a href="{{ route('courses.index') }}" class="px-space-xs py-2 text-on-surface-variant hover:text-error font-label-md flex items-center" title="Reset Filter">
                        <span class="material-symbols-outlined text-sm">close</span>
                    </a>
                @endif
            </div>

            {{-- Total Ditemukan --}}
            <div class="flex items-center gap-space-xs ml-auto pt-2 md:pt-0 border-t md:border-t-0 border-outline/10">
                <span class="font-label-sm text-label-sm text-on-surface-variant">Total Ditemukan:</span>
                <span class="font-label-lg text-label-lg text-primary">{{ $courses->total() }}</span>
            </div>
        </form>


        {{-- GRID MATA KULIAH --}}
        <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-bento-gap-desktop">
            @forelse ($courses as $course)
                <div class="bg-surface-container-low hover:bg-surface-container p-space-lg rounded-2xl shadow-md transition-all duration-300 flex flex-col justify-between group relative overflow-hidden">
                    <div class="flex flex-col space-y-space-xs">
                        {{-- Kode & Status --}}
                        <div class="flex items-center justify-between mb-1">
                            <span class="px-2.5 py-1 rounded-md bg-tertiary-container/30 text-tertiary font-label-md text-label-md font-bold tracking-wide">
                                {{ $course->code }}
                            </span>
                            <span class="px-2 py-0.5 rounded-full bg-surface-container-high text-on-surface-variant font-label-sm text-label-sm">
                                {{ ucfirst($course->status) }}
                            </span>
                        </div>

                        {{-- Nama Mata Kuliah --}}
                        <h2 class="font-headline-sm text-headline-sm group-hover:text-primary transition-colors line-clamp-2">
                            {{ $course->name }}
                        </h2>

                        {{-- Box Dosen Pengampu --}}
                        <div class="p-space-xs rounded-xl bg-surface-container flex items-center gap-space-xs">
                            @php
                                $lecturerName = $course->lecturer?->name ?? 'Belum ditentukan';
                                $initials = collect(explode(' ', $lecturerName))
                                    ->map(fn ($word) => strtoupper($word[0] ?? ''))
                                    ->take(2)
                                    ->implode('');
                            @endphp

                            <div class="w-8 h-8 rounded-full bg-secondary-container text-on-secondary-container flex items-center justify-center font-label-md text-label-md font-bold shrink-0">
                                {{ $initials }}
                            </div>

                            <div class="flex flex-col min-w-0 flex-1">
                                <span class="font-label-md text-label-md truncate">{{ $lecturerName }}</span>
                                <span class="font-label-sm text-label-sm text-outline">Dosen Pengampu</span>
                            </div>

                            <span class="material-symbols-outlined text-sm text-primary shrink-0">
                                verified
                            </span>
                        </div>

                        {{-- Tampilan Deskripsi di bawah Dosen --}}
                        <div class="pt-2 border-t border-outline/10 mt-1">
                            <span class="block text-[10px] font-bold uppercase tracking-wider text-on-surface-variant mb-0.5">
                                Deskripsi
                            </span>
                            <p class="text-xs text-on-surface-variant line-clamp-2 leading-relaxed">
                                {{ $course->description ?? 'Tidak ada deskripsi.' }}
                            </p>
                        </div>
                    </div>

                    {{-- Tombol Aksi --}}
                    <div class="mt-space-md pt-space-sm flex items-center gap-space-xs">
                        <a href="{{ route('courses.show', $course) }}" class="flex-1 py-2 rounded-xl bg-surface-container-high hover:bg-primary hover:text-on-primary text-on-surface font-label-md text-label-md text-center transition-all flex items-center justify-center gap-1">
                            <span>Lihat Detail</span>
                            <span class="material-symbols-outlined text-sm">arrow_forward</span>
                        </a>

                        {{-- TOMBOL PENSIL (TRIGGER POP-UP EDIT) --}}
                        <button 
                            type="button" 
                            @click="setEditCourse({{ json_encode($course) }})" 
                            class="w-10 py-2 rounded-xl bg-surface-container-high hover:bg-secondary-container text-on-surface font-label-md text-label-md transition-all flex items-center justify-center" 
                            title="Edit"
                        >
                            <span class="material-symbols-outlined text-sm">edit</span>
                        </button>
                    </div>
                </div>
            @empty
                <div class="col-span-full py-space-2xl text-center">
                    <span class="material-symbols-outlined text-headline-lg text-outline">search_off</span>
                    <p class="font-body-md text-body-md text-on-surface-variant mt-space-sm">
                        @if(request()->filled('search') || request()->filled('status'))
                            Tidak ada mata kuliah yang cocok dengan kata kunci atau filter tersebut.
                        @else
                            Belum ada mata kuliah.
                        @endif
                    </p>
                    @if(request()->filled('search') || request()->filled('status'))
                        <a href="{{ route('courses.index') }}" class="inline-flex items-center gap-space-xs mt-space-md px-space-md py-2.5 rounded-xl bg-surface-container-high text-on-surface">
                            Reset Pencarian
                        </a>
                    @else
                        <button @click="openCreateModal = true" class="inline-flex items-center gap-space-xs mt-space-md px-space-md py-2.5 rounded-xl bg-primary text-on-primary">
                            <span class="material-symbols-outlined">add_circle</span>
                            Tambah Mata Kuliah
                        </button>
                    @endif
                </div>
            @endforelse
        </div>


        {{-- NAVIGASI PAGINATION --}}
        <div class="mt-space-lg flex justify-center">
            {{ $courses->links('vendor.pagination.custom-pagination') }}
        </div>


        {{-- POP-UP / MODAL FORM CREATE --}}
        <div 
            x-show="openCreateModal" 
            x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/60 backdrop-blur-sm overflow-y-auto"
            style="display: none;"
        >
            <div 
                @click.away="openCreateModal = false"
                x-transition:enter="transition ease-out duration-300"
                x-transition:enter-start="opacity-0 scale-95"
                x-transition:enter-end="opacity-100 scale-100"
                x-transition:leave="transition ease-in duration-200"
                x-transition:leave-start="opacity-100 scale-100"
                x-transition:leave-end="opacity-0 scale-95"
                class="bg-surface-container-low p-space-lg rounded-2xl shadow-2xl w-full max-w-2xl max-h-[90vh] overflow-y-auto my-auto relative border border-outline/10"
            >
                <div class="flex items-center justify-between mb-space-md pb-space-xs border-b border-outline/10">
                    <div>
                        <h2 class="font-headline-md text-headline-md font-bold tracking-tight">
                            Tambah Mata Kuliah
                        </h2>
                        <p class="font-body-sm text-body-sm text-on-surface-variant">
                            Isi formulir berikut untuk membuat mata kuliah baru.
                        </p>
                    </div>
                    <button @click="openCreateModal = false" class="p-2 rounded-xl text-on-surface-variant hover:bg-surface-container transition-colors">
                        <span class="material-symbols-outlined">close</span>
                    </button>
                </div>

                <form action="{{ route('courses.store') }}" method="POST" class="space-y-space-md">
                    @csrf

                    <div>
                        <label for="code" class="block font-label-md text-label-md mb-1">Kode Mata Kuliah</label>
                        <input id="code" name="code" type="text" value="{{ old('code') }}" placeholder="Contoh: SI251406" class="w-full bg-surface-container border-none rounded-xl px-space-md py-3 focus:ring-2 focus:ring-primary">
                        @if(!$errors->has('edit_id'))
                            @error('code') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                        @endif
                    </div>

                    <div>
                        <label for="name" class="block font-label-md text-label-md mb-1">Nama Mata Kuliah</label>
                        <input id="name" name="name" type="text" value="{{ old('name') }}" placeholder="Contoh: Pemrograman Web" class="w-full bg-surface-container border-none rounded-xl px-space-md py-3 focus:ring-2 focus:ring-primary">
                        @if(!$errors->has('edit_id'))
                            @error('name') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                        @endif
                    </div>

                    <div>
                        <label for="description" class="block font-label-md text-label-md mb-1">Deskripsi</label>
                        <textarea id="description" name="description" rows="3" placeholder="Deskripsi mata kuliah..." class="w-full bg-surface-container border-none rounded-xl px-space-md py-3 focus:ring-2 focus:ring-primary">{{ old('description') }}</textarea>
                        @if(!$errors->has('edit_id'))
                            @error('description') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                        @endif
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-space-md">
                        <div>
                            <label for="sks" class="block font-label-md text-label-md mb-1">SKS</label>
                            <input id="sks" name="sks" type="number" min="1" value="{{ old('sks', 3) }}" class="w-full bg-surface-container border-none rounded-xl px-space-md py-3 focus:ring-2 focus:ring-primary">
                            @if(!$errors->has('edit_id'))
                                @error('sks') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                            @endif
                        </div>

                        <div>
                            <label for="status" class="block font-label-md text-label-md mb-1">Status</label>
                            <select id="status" name="status" class="w-full bg-surface-container border-none rounded-xl px-space-md py-3 focus:ring-2 focus:ring-primary">
                                <option value="draft">Draft</option>
                                <option value="active">Active</option>
                                <option value="archived">Archived</option>
                            </select>
                            @if(!$errors->has('edit_id'))
                                @error('status') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                            @endif
                        </div>
                    </div>

                    <div>
                        <label for="lecturer_id" class="block font-label-md text-label-md mb-1">Dosen Pengampu</label>
                        <select id="lecturer_id" name="lecturer_id" class="w-full bg-surface-container border-none rounded-xl px-space-md py-3 focus:ring-2 focus:ring-primary">
                            <option value="">-- Pilih Dosen --</option>
                            @foreach ($lecturers as $lecturer)
                                <option value="{{ $lecturer->id }}" @selected(old('lecturer_id') == $lecturer->id)>
                                    {{ $lecturer->name }}
                                </option>
                            @endforeach
                        </select>
                        @if(!$errors->has('edit_id'))
                            @error('lecturer_id') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                        @endif
                    </div>

                    <div class="flex items-center justify-end gap-space-sm pt-space-sm border-t border-outline/10">
                        <button type="button" @click="openCreateModal = false" class="px-space-md py-2.5 rounded-xl bg-surface-container-high text-on-surface font-label-lg hover:bg-surface-container transition-all">
                            Batal
                        </button>
                        <button type="submit" class="px-space-md py-2.5 rounded-xl bg-primary text-on-primary font-label-lg hover:bg-primary-container transition-all">
                            Simpan Mata Kuliah
                        </button>
                    </div>
                </form>
            </div>
        </div>


        {{-- POP-UP / MODAL FORM EDIT --}}
        <div 
            x-show="openEditModal" 
            x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/60 backdrop-blur-sm overflow-y-auto"
            style="display: none;"
        >
            <div 
                @click.away="openEditModal = false"
                x-transition:enter="transition ease-out duration-300"
                x-transition:enter-start="opacity-0 scale-95"
                x-transition:enter-end="opacity-100 scale-100"
                x-transition:leave="transition ease-in duration-200"
                x-transition:leave-start="opacity-100 scale-100"
                x-transition:leave-end="opacity-0 scale-95"
                class="bg-surface-container-low p-space-lg rounded-2xl shadow-2xl w-full max-w-2xl max-h-[90vh] overflow-y-auto my-auto relative border border-outline/10 text-left"
            >
                <div class="flex items-center justify-between mb-space-md pb-space-xs border-b border-outline/10">
                    <div>
                        <h2 class="font-headline-md text-headline-md font-bold tracking-tight">
                            Edit Mata Kuliah
                        </h2>
                        <p class="font-body-sm text-body-sm text-on-surface-variant">
                            Perbarui informasi data mata kuliah ini.
                        </p>
                    </div>
                    <button @click="openEditModal = false" class="p-2 rounded-xl text-on-surface-variant hover:bg-surface-container transition-colors">
                        <span class="material-symbols-outlined">close</span>
                    </button>
                </div>

                <form :action="'/courses/' + editCourse.id" method="POST" class="space-y-space-md">
                    @csrf
                    @method('PUT')
                    <input type="hidden" name="edit_id" :value="editCourse.id">

                    <div>
                        <label for="edit_code" class="block font-label-md text-label-md mb-1">Kode Mata Kuliah</label>
                        <input id="edit_code" name="code" type="text" x-model="editCourse.code" placeholder="Contoh: SI251406" class="w-full bg-surface-container border-none rounded-xl px-space-md py-3 focus:ring-2 focus:ring-primary">
                        @if($errors->has('edit_id'))
                            @error('code') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                        @endif
                    </div>

                    <div>
                        <label for="edit_name" class="block font-label-md text-label-md mb-1">Nama Mata Kuliah</label>
                        <input id="edit_name" name="name" type="text" x-model="editCourse.name" placeholder="Contoh: Pemrograman Web" class="w-full bg-surface-container border-none rounded-xl px-space-md py-3 focus:ring-2 focus:ring-primary">
                        @if($errors->has('edit_id'))
                            @error('name') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                        @endif
                    </div>

                    <div>
                        <label for="edit_description" class="block font-label-md text-label-md mb-1">Deskripsi</label>
                        <textarea id="edit_description" name="description" rows="3" x-model="editCourse.description" placeholder="Deskripsi mata kuliah..." class="w-full bg-surface-container border-none rounded-xl px-space-md py-3 focus:ring-2 focus:ring-primary"></textarea>
                        @if($errors->has('edit_id'))
                            @error('description') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                        @endif
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-space-md">
                        <div>
                            <label for="edit_sks" class="block font-label-md text-label-md mb-1">SKS</label>
                            <input id="edit_sks" name="sks" type="number" min="1" x-model="editCourse.sks" class="w-full bg-surface-container border-none rounded-xl px-space-md py-3 focus:ring-2 focus:ring-primary">
                            @if($errors->has('edit_id'))
                                @error('sks') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                            @endif
                        </div>

                        <div>
                            <label for="edit_status" class="block font-label-md text-label-md mb-1">Status</label>
                            <select id="edit_status" name="status" x-model="editCourse.status" class="w-full bg-surface-container border-none rounded-xl px-space-md py-3 focus:ring-2 focus:ring-primary">
                                <option value="active">Active</option>
                                <option value="draft">Draft</option>
                                <option value="archived">Archived</option>
                            </select>
                            @if($errors->has('edit_id'))
                                @error('status') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                            @endif
                        </div>
                    </div>

                    <div>
                        <label for="edit_lecturer_id" class="block font-label-md text-label-md mb-1">Dosen Pengampu</label>
                        <select id="edit_lecturer_id" name="lecturer_id" x-model="editCourse.lecturer_id" class="w-full bg-surface-container border-none rounded-xl px-space-md py-3 focus:ring-2 focus:ring-primary">
                            <option value="">-- Pilih Dosen --</option>
                            @foreach ($lecturers as $lecturer)
                                <option value="{{ $lecturer->id }}">
                                    {{ $lecturer->name }}
                                </option>
                            @endforeach
                        </select>
                        @if($errors->has('edit_id'))
                            @error('lecturer_id') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                        @endif
                    </div>

                    <div class="flex items-center justify-end gap-space-sm pt-space-sm border-t border-outline/10">
                        <button type="button" @click="openEditModal = false" class="px-space-md py-2.5 rounded-xl bg-surface-container-high text-on-surface font-label-lg hover:bg-surface-container transition-all">
                            Batal
                        </button>
                        <button type="submit" class="px-space-md py-2.5 rounded-xl bg-primary text-on-primary font-label-lg hover:bg-primary-container transition-all">
                            Simpan Perubahan
                        </button>
                    </div>
                </form>
            </div>
        </div>

    </div>

</x-layout>