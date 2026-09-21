<x-layout title="Tambah Mata Kuliah" active-nav="courses">

    <div class="mb-space-lg">

        <a
            href="{{ route('courses.index') }}"
            class="inline-flex items-center gap-space-xs text-on-surface-variant hover:text-primary transition-colors"
        >
            <span class="material-symbols-outlined text-sm">
                arrow_back
            </span>

            Kembali ke daftar mata kuliah
        </a>

        <h1 class="font-headline-lg text-headline-lg tracking-tight mt-space-md">
            Tambah Mata Kuliah
        </h1>

        <p class="font-body-md text-body-md text-on-surface-variant mt-1">
            Tambahkan mata kuliah baru ke dalam sistem.
        </p>

    </div>


    <div class="bg-surface-container-low p-space-lg rounded-2xl shadow-md max-w-3xl">

        <form
            action="{{ route('courses.store') }}"
            method="POST"
            class="space-y-space-md"
        >

            @csrf

            {{-- Kode --}}
            <div>

                <label
                    for="code"
                    class="block font-label-md text-label-md mb-1"
                >
                    Kode Mata Kuliah
                </label>

                <input
                    id="code"
                    name="code"
                    type="text"
                    value="{{ old('code') }}"
                    placeholder="Contoh: SI251406"
                    class="w-full bg-surface-container border-none rounded-xl px-space-md py-3 focus:ring-2 focus:ring-primary"
                >

                @error('code')
                    <p class="text-red-500 text-sm mt-1">
                        {{ $message }}
                    </p>
                @enderror

            </div>


            {{-- Nama --}}
            <div>

                <label
                    for="name"
                    class="block font-label-md text-label-md mb-1"
                >
                    Nama Mata Kuliah
                </label>

                <input
                    id="name"
                    name="name"
                    type="text"
                    value="{{ old('name') }}"
                    placeholder="Contoh: Pemrograman Web"
                    class="w-full bg-surface-container border-none rounded-xl px-space-md py-3 focus:ring-2 focus:ring-primary"
                >

                @error('name')
                    <p class="text-red-500 text-sm mt-1">
                        {{ $message }}
                    </p>
                @enderror

            </div>


            {{-- Deskripsi --}}
            <div>

                <label
                    for="description"
                    class="block font-label-md text-label-md mb-1"
                >
                    Deskripsi
                </label>

                <textarea
                    id="description"
                    name="description"
                    rows="5"
                    placeholder="Deskripsi mata kuliah..."
                    class="w-full bg-surface-container border-none rounded-xl px-space-md py-3 focus:ring-2 focus:ring-primary"
                >{{ old('description') }}</textarea>

                @error('description')
                    <p class="text-red-500 text-sm mt-1">
                        {{ $message }}
                    </p>
                @enderror

            </div>


            {{-- SKS --}}
            <div>

                <label
                    for="sks"
                    class="block font-label-md text-label-md mb-1"
                >
                    SKS
                </label>

                <input
                    id="sks"
                    name="sks"
                    type="number"
                    min="1"
                    value="{{ old('sks', 3) }}"
                    class="w-full bg-surface-container border-none rounded-xl px-space-md py-3 focus:ring-2 focus:ring-primary"
                >

                @error('sks')
                    <p class="text-red-500 text-sm mt-1">
                        {{ $message }}
                    </p>
                @enderror

            </div>


            {{-- Dosen --}}
            <div>

                <label
                    for="lecturer_id"
                    class="block font-label-md text-label-md mb-1"
                >
                    Dosen Pengampu
                </label>

                <select
                    id="lecturer_id"
                    name="lecturer_id"
                    class="w-full bg-surface-container border-none rounded-xl px-space-md py-3 focus:ring-2 focus:ring-primary"
                >

                    <option value="">
                        -- Pilih Dosen --
                    </option>

                    @foreach ($lecturers as $lecturer)

                        <option
                            value="{{ $lecturer->id }}"
                            @selected(old('lecturer_id') == $lecturer->id)
                        >
                            {{ $lecturer->name }}
                        </option>

                    @endforeach

                </select>

                @error('lecturer_id')
                    <p class="text-red-500 text-sm mt-1">
                        {{ $message }}
                    </p>
                @enderror

            </div>


            {{-- Status --}}
            <div>
                <label
                    for="status"
                    class="block font-label-md text-label-md mb-1"
                >
                    Status
                </label>

                <select
                    id="status"
                    name="status"
                    class="w-full bg-surface-container border-none rounded-xl px-space-md py-3 focus:ring-2 focus:ring-primary"
                >
                    <option
                        value="draft"
                        @selected(old('status', 'draft') === 'draft')
                    >
                        Draft
                    </option>

                    <option
                        value="active"
                        @selected(old('status') === 'active')
                    >
                        Active
                    </option>

                    <option
                        value="archived"
                        @selected(old('status') === 'archived')
                    >
                        Archived
                    </option>
                </select>

                @error('status')
                    <p class="text-red-500 text-sm mt-1">
                        {{ $message }}
                    </p>
                @enderror
            </div>


            {{-- Button --}}
            <div class="flex items-center gap-space-sm pt-space-sm">

                <a
                    href="{{ route('courses.index') }}"
                    class="px-space-md py-2.5 rounded-xl bg-surface-container-high text-on-surface font-label-lg"
                >
                    Batal
                </a>

                <button
                    type="submit"
                    class="px-space-md py-2.5 rounded-xl bg-primary text-on-primary font-label-lg hover:bg-primary-container transition-all"
                >
                    Simpan Mata Kuliah
                </button>

            </div>

        </form>

    </div>

</x-layout>