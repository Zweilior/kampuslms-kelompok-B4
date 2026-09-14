<x-layout title="Detail Mata Kuliah" active-nav="courses">

    {{-- WRAPPER ALPINE JS UNTUK KONTROL MODAL --}}
    <div x-data="{ openEditModal: {{ $errors->any() ? 'true' : 'false' }} }">

        {{-- KEMBALI KE DAFTAR --}}
        <div class="mb-space-md">
            <a href="{{ route('courses.index') }}" class="inline-flex items-center gap-space-xs text-on-surface-variant hover:text-primary transition-colors text-sm font-medium">
                <span class="material-symbols-outlined text-sm">arrow_back</span>
                Kembali ke Daftar Mata Kuliah
            </a>
        </div>

        {{-- GRID UTAMA 2 KOLOM --}}
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-bento-gap-desktop items-start">

            {{-- KOLOM KIRI (CARD UTAMA) --}}
            <div class="lg:col-span-2 space-y-space-md">
                <div class="bg-surface-container-low p-space-xl rounded-2xl shadow-sm border border-outline/10 space-y-space-md">
                    
                    {{-- Badge Kode & Status --}}
                    <div class="flex items-center gap-2">
                        <span class="px-2.5 py-1 rounded-md bg-tertiary-container/30 text-tertiary font-label-md text-xs font-bold tracking-wide">
                            {{ $course->code }}
                        </span>
                        <span class="px-2.5 py-0.5 rounded-full bg-surface-container-high text-on-surface-variant text-xs font-medium">
                            {{ ucfirst($course->status) }}
                        </span>
                    </div>

                    {{-- Nama Mata Kuliah --}}
                    <h1 class="font-headline-lg text-3xl font-bold tracking-tight text-on-surface">
                        {{ $course->name }}
                    </h1>

                    {{-- Box Dosen Pengampu --}}
                    @php
                        $lecturerName = $course->lecturer?->name ?? 'Belum ditentukan';
                        $initials = collect(explode(' ', $lecturerName))
                            ->map(fn ($word) => strtoupper($word[0] ?? ''))
                            ->take(2)
                            ->implode('');
                    @endphp

                    <div class="p-space-md rounded-2xl bg-surface-container flex items-center justify-between">
                        <div class="flex items-center gap-space-md">
                            <div class="w-12 h-12 rounded-full bg-secondary-container text-on-secondary-container flex items-center justify-center font-bold text-base">
                                {{ $initials }}
                            </div>
                            <div class="flex flex-col">
                                <span class="font-headline-sm font-bold text-on-surface">
                                    {{ $lecturerName }}
                                </span>
                                <span class="text-xs text-on-surface-variant">
                                    Dosen Pengampu Utama
                                </span>
                            </div>
                        </div>
                        <span class="material-symbols-outlined text-primary text-xl">
                            verified
                        </span>
                    </div>

                    {{-- Deskripsi Mata Kuliah (Ditambahkan di bawah Box Dosen) --}}
                    <div class="pt-space-xs border-t border-outline/10">
                        <span class="block text-xs font-bold uppercase tracking-wider text-on-surface-variant mb-1">
                            Deskripsi Mata Kuliah
                        </span>
                        <p class="text-sm text-on-surface leading-relaxed">
                            {{ $course->description ?? 'Tidak ada deskripsi untuk mata kuliah ini.' }}
                        </p>
                    </div>

                </div>
            </div>

            {{-- KOLOM KANAN (INFORMASI & AKSI) --}}
            <div class="space-y-bento-gap-desktop">

                {{-- CARD INFORMASI MATA KULIAH --}}
                <div class="bg-surface-container-low p-space-lg rounded-2xl shadow-sm border border-outline/10">
                    <div class="flex items-center gap-2 mb-space-md pb-space-xs border-b border-outline/10">
                        <span class="material-symbols-outlined text-primary">info</span>
                        <h2 class="font-headline-sm font-bold text-on-surface">Informasi Mata Kuliah</h2>
                    </div>

                    <div class="space-y-3 text-sm">
                        <div class="flex justify-between items-center py-1 border-b border-outline/5">
                            <span class="text-on-surface-variant">Kode MK</span>
                            <span class="font-bold text-on-surface">{{ $course->code }}</span>
                        </div>
                        <div class="flex justify-between items-center py-1 border-b border-outline/5">
                            <span class="text-on-surface-variant">Nama MK</span>
                            <span class="font-bold text-on-surface text-right">{{ $course->name }}</span>
                        </div>
                        <div class="flex justify-between items-center py-1 border-b border-outline/5">
                            <span class="text-on-surface-variant">SKS</span>
                            <span class="font-bold text-on-surface">{{ $course->sks }}</span>
                        </div>
                        <div class="flex justify-between items-center py-1 border-b border-outline/5">
                            <span class="text-on-surface-variant">Status</span>
                            <span class="font-bold text-on-surface">{{ ucfirst($course->status) }}</span>
                        </div>
                        <div class="flex justify-between items-center py-1">
                            <span class="text-on-surface-variant">Dosen</span>
                            <span class="font-bold text-on-surface text-right">{{ $lecturerName }}</span>
                        </div>
                    </div>
                </div>

                {{-- CARD AKSI CEPAT --}}
                <div class="bg-surface-container-low p-space-lg rounded-2xl shadow-sm border border-outline/10">
                    <div class="flex items-center gap-2 mb-space-md pb-space-xs border-b border-outline/10">
                        <span class="material-symbols-outlined text-primary">edit_calendar</span>
                        <h2 class="font-headline-sm font-bold text-on-surface">Aksi Cepat</h2>
                    </div>

                    <div class="space-y-space-xs">
                        {{-- Tombol Trigger Edit Pop-up --}}
                        <button 
                            type="button" 
                            @click="openEditModal = true"
                            class="w-full py-3 px-space-md rounded-xl bg-primary hover:bg-primary-dark text-on-primary font-semibold text-sm transition-all flex items-center justify-center gap-2 shadow-sm"
                        >
                            <span class="material-symbols-outlined text-sm">edit</span>
                            <span>Edit Mata Kuliah</span>
                        </button>

                        {{-- Tombol Hapus Mata Kuliah --}}
                        <form action="{{ route('courses.destroy', $course) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus mata kuliah ini?');">
                            @csrf
                            @method('DELETE')
                            <button 
                                type="submit" 
                                class="w-full py-3 px-space-md rounded-xl bg-surface-container-high hover:bg-red-500/10 text-red-600 font-semibold text-sm transition-all flex items-center justify-center gap-2"
                            >
                                <span class="material-symbols-outlined text-sm">delete</span>
                                <span>Hapus Mata Kuliah</span>
                            </button>
                        </form>
                    </div>
                </div>

            </div>

        </div>


        {{-- ============================================ --}}
        {{-- MODAL POP-UP EDIT FORM (DI TENGAH LAYAR) --}}
        {{-- ============================================ --}}
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
                {{-- Header Modal --}}
                <div class="flex items-center justify-between mb-space-md pb-space-xs border-b border-outline/10">
                    <div>
                        <h2 class="font-headline-md text-headline-md font-bold tracking-tight">
                            Edit Mata Kuliah
                        </h2>
                        <p class="font-body-sm text-body-sm text-on-surface-variant">
                            Perbarui informasi data mata kuliah ini.
                        </p>
                    </div>
                    <button 
                        @click="openEditModal = false" 
                        class="p-2 rounded-xl text-on-surface-variant hover:bg-surface-container transition-colors"
                    >
                        <span class="material-symbols-outlined">close</span>
                    </button>
                </div>

                {{-- Form Body Edit --}}
                <form action="{{ route('courses.update', $course) }}" method="POST" class="space-y-space-md">
                    @csrf
                    @method('PUT')

                    {{-- Kode --}}
                    <div>
                        <label for="code" class="block font-label-md text-label-md mb-1">
                            Kode Mata Kuliah
                        </label>
                        <input id="code" name="code" type="text" value="{{ old('code', $course->code) }}" placeholder="Contoh: SI251406" class="w-full bg-surface-container border-none rounded-xl px-space-md py-3 focus:ring-2 focus:ring-primary">
                        @error('code')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Nama --}}
                    <div>
                        <label for="name" class="block font-label-md text-label-md mb-1">
                            Nama Mata Kuliah
                        </label>
                        <input id="name" name="name" type="text" value="{{ old('name', $course->name) }}" placeholder="Contoh: Pemrograman Web" class="w-full bg-surface-container border-none rounded-xl px-space-md py-3 focus:ring-2 focus:ring-primary">
                        @error('name')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Deskripsi --}}
                    <div>
                        <label for="description" class="block font-label-md text-label-md mb-1">
                            Deskripsi
                        </label>
                        <textarea id="description" name="description" rows="3" placeholder="Deskripsi mata kuliah..." class="w-full bg-surface-container border-none rounded-xl px-space-md py-3 focus:ring-2 focus:ring-primary">{{ old('description', $course->description) }}</textarea>
                        @error('description')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Grid SKS & Status --}}
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-space-md">
                        <div>
                            <label for="sks" class="block font-label-md text-label-md mb-1">
                                SKS
                            </label>
                            <input id="sks" name="sks" type="number" min="1" value="{{ old('sks', $course->sks) }}" class="w-full bg-surface-container border-none rounded-xl px-space-md py-3 focus:ring-2 focus:ring-primary">
                            @error('sks')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="status" class="block font-label-md text-label-md mb-1">
                                Status
                            </label>
                            <select id="status" name="status" class="w-full bg-surface-container border-none rounded-xl px-space-md py-3 focus:ring-2 focus:ring-primary">
                                <option value="active" @selected(old('status', $course->status) === 'active')>Active</option>
                                <option value="inactive" @selected(old('status', $course->status) === 'inactive')>Inactive</option>
                            </select>
                            @error('status')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    {{-- Dosen --}}
                    <div>
                        <label for="lecturer_id" class="block font-label-md text-label-md mb-1">
                            Dosen Pengampu
                        </label>
                        <select id="lecturer_id" name="lecturer_id" class="w-full bg-surface-container border-none rounded-xl px-space-md py-3 focus:ring-2 focus:ring-primary">
                            <option value="">-- Pilih Dosen --</option>
                            @foreach ($lecturers as $lecturer)
                                <option value="{{ $lecturer->id }}" @selected(old('lecturer_id', $course->lecturer_id) == $lecturer->id)>
                                    {{ $lecturer->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('lecturer_id')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Button Footer --}}
                    <div class="flex items-center justify-end gap-space-sm pt-space-sm border-t border-outline/10">
                        <button 
                            type="button" 
                            @click="openEditModal = false"
                            class="px-space-md py-2.5 rounded-xl bg-surface-container-high text-on-surface font-label-lg hover:bg-surface-container transition-all"
                        >
                            Batal
                        </button>
                        <button 
                            type="submit"
                            class="px-space-md py-2.5 rounded-xl bg-primary text-on-primary font-label-lg hover:bg-primary-container transition-all"
                        >
                            Simpan Perubahan
                        </button>
                    </div>

                </form>
            </div>
        </div>

    </div>

</x-layout>