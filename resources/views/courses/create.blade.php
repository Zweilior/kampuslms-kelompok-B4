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
            id="createCourseForm"
            action="{{ route('courses.store') }}"
            method="POST"
            class="space-y-space-md"
            novalidate
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

                <p id="js-error-code" class="text-red-500 text-sm mt-1 hidden"></p>

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

                <p id="js-error-name" class="text-red-500 text-sm mt-1 hidden"></p>

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
                    value="{{ old('sks', 3) }}"
                    class="w-full bg-surface-container border-none rounded-xl px-space-md py-3 focus:ring-2 focus:ring-primary"
                >

                <p id="js-error-sks" class="text-red-500 text-sm mt-1 hidden"></p>

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

                <p id="js-error-lecturer_id" class="text-red-500 text-sm mt-1 hidden"></p>

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
                    <option value="">
                        -- Pilih Status --
                    </option>

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

                <p id="js-error-status" class="text-red-500 text-sm mt-1 hidden"></p>

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

    {{-- Script Client-Side JavaScript Validation --}}
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const form = document.getElementById('createCourseForm');

            form.addEventListener('submit', function (e) {
                let isValid = true;

                // Reset error messages
                const errorElements = document.querySelectorAll('[id^="js-error-"]');
                errorElements.forEach(el => {
                    el.classList.add('hidden');
                    el.textContent = '';
                });

                // Validasi Code (Wajib diisi)
                const codeInput = document.getElementById('code');
                if (!codeInput.value.trim()) {
                    showJsError('js-error-code', '[JS Validation] Kode mata kuliah wajib diisi.');
                    isValid = false;
                }

                // Validasi Name (Wajib diisi)
                const nameInput = document.getElementById('name');
                if (!nameInput.value.trim()) {
                    showJsError('js-error-name', '[JS Validation] Nama mata kuliah wajib diisi.');
                    isValid = false;
                }

                // Validasi SKS (Wajib diisi & 1–6)
                const sksInput = document.getElementById('sks');
                const sksVal = parseInt(sksInput.value, 10);
                if (!sksInput.value.trim()) {
                    showJsError('js-error-sks', '[JS Validation] Jumlah SKS wajib diisi.');
                    isValid = false;
                } else if (isNaN(sksVal) || sksVal < 1 || sksVal > 6) {
                    showJsError('js-error-sks', '[JS Validation] Jumlah SKS harus antara 1 sampai 6.');
                    isValid = false;
                }

                // Validasi Lecturer ID (Wajib dipilih)
                const lecturerSelect = document.getElementById('lecturer_id');
                if (!lecturerSelect.value) {
                    showJsError('js-error-lecturer_id', '[JS Validation] Dosen pengampu wajib dipilih.');
                    isValid = false;
                }

                // Validasi Status (Wajib dipilih)
                const statusSelect = document.getElementById('status');
                if (!statusSelect.value) {
                    showJsError('js-error-status', '[JS Validation] Status mata kuliah wajib dipilih.');
                    isValid = false;
                }

                // Hentikan pengiriman form jika ada input tidak valid
                if (!isValid) {
                    e.preventDefault();
                }
            });

            function showJsError(elementId, message) {
                const el = document.getElementById(elementId);
                if (el) {
                    el.textContent = message;
                    el.classList.remove('hidden');
                }
            }
        });
    </script>

</x-layout>