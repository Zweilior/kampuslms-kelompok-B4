<x-layout title="Detail Mata Kuliah - {{ $course->name }}" active-nav="courses">

    {{-- BREADCRUMB / BACK --}}
    <a href="{{ route('courses.index') }}"
       class="inline-flex items-center gap-space-2xs font-label-lg text-label-lg text-on-surface-variant hover:text-primary transition-all mb-space-lg">

        <span class="material-symbols-outlined text-sm">arrow_back</span>
        <span>Kembali ke Daftar Mata Kuliah</span>

    </a>

    <div class="grid grid-cols-1 xl:grid-cols-12 gap-bento-gap-desktop items-start">

        {{-- KARTU DETAIL UTAMA --}}
        <div class="xl:col-span-8 bg-surface-container-low p-space-2xl rounded-2xl shadow-md relative overflow-hidden">

            <div class="absolute -right-10 -top-10 w-48 h-48 rounded-full bg-primary/5 blur-3xl pointer-events-none"></div>

            <div class="flex flex-wrap items-center gap-space-xs mb-space-md">

                <span class="px-2.5 py-1 rounded-md bg-tertiary-container/30 text-tertiary font-label-md text-label-md font-bold tracking-wide">
                    {{ $course->code }}
                </span>

                {{-- STATUS --}}
                <span class="px-2 py-0.5 rounded-full bg-surface-container-high text-on-surface-variant font-label-sm text-label-sm">
                    {{ ucfirst($course->status) }}
                </span>

            </div>

            <h1 class="font-display-lg text-display-lg tracking-tight font-bold leading-tight">
                {{ $course->name }}
            </h1>

            {{-- Dosen --}}
            <div class="mt-space-xl p-space-md rounded-xl bg-surface-container flex items-center gap-space-sm">

                @php
                    $lecturerName = $course->lecturer?->name ?? 'Belum ditentukan';

                    $initials = collect(explode(' ', $lecturerName))
                        ->map(fn($w) => strtoupper($w[0] ?? ''))
                        ->take(2)
                        ->implode('');
                @endphp

                <div class="w-12 h-12 rounded-full bg-secondary-container text-on-secondary-container flex items-center justify-center font-bold text-headline-sm shrink-0">
                    {{ $initials }}
                </div>

                <div class="flex flex-col min-w-0 flex-1">

                    <span class="font-headline-sm text-headline-sm">
                        {{ $lecturerName }}
                    </span>

                    <span class="font-body-sm text-body-sm text-outline">
                        Dosen Pengampu Utama
                    </span>

                </div>

                <span class="material-symbols-outlined text-primary">
                    verified
                </span>

            </div>

        </div>


        {{-- PANEL INFO SAMPING --}}
        <div class="xl:col-span-4 flex flex-col gap-bento-gap-desktop">

            {{-- INFORMASI MATA KULIAH --}}
            <div class="bg-surface-container-low p-space-lg rounded-2xl shadow-md">

                <div class="flex items-center gap-space-xs mb-space-md">

                    <div class="w-9 h-9 rounded-xl bg-surface-container flex items-center justify-center text-primary">

                        <span class="material-symbols-outlined text-headline-sm">
                            info
                        </span>

                    </div>

                    <h3 class="font-headline-sm text-headline-sm">
                        Informasi Mata Kuliah
                    </h3>

                </div>


                <div class="flex flex-col gap-space-sm">

                    {{-- KODE --}}
                    <div class="flex items-center justify-between py-space-xs border-b border-surface-container">

                        <span class="font-body-sm text-body-sm text-on-surface-variant">
                            Kode MK
                        </span>

                        <span class="font-label-lg text-label-lg text-primary">
                            {{ $course->code }}
                        </span>

                    </div>


                    {{-- NAMA --}}
                    <div class="flex items-center justify-between py-space-xs border-b border-surface-container">

                        <span class="font-body-sm text-body-sm text-on-surface-variant">
                            Nama MK
                        </span>

                        <span class="font-label-lg text-label-lg text-right max-w-[60%] truncate">
                            {{ $course->name }}
                        </span>

                    </div>


                    {{-- SKS --}}
                    <div class="flex items-center justify-between py-space-xs border-b border-surface-container">

                        <span class="font-body-sm text-body-sm text-on-surface-variant">
                            SKS
                        </span>

                        <span class="font-label-lg text-label-lg">
                            {{ $course->sks }}
                        </span>

                    </div>


                    {{-- STATUS --}}
                    <div class="flex items-center justify-between py-space-xs border-b border-surface-container">

                        <span class="font-body-sm text-body-sm text-on-surface-variant">
                            Status
                        </span>

                        <span class="font-label-lg text-label-lg">
                            {{ ucfirst($course->status) }}
                        </span>

                    </div>


                    {{-- DOSEN --}}
                    <div class="flex items-center justify-between py-space-xs">

                        <span class="font-body-sm text-body-sm text-on-surface-variant">
                            Dosen
                        </span>

                        <span class="font-label-lg text-label-lg text-right max-w-[60%] truncate">
                            {{ $course->lecturer?->name ?? '-' }}
                        </span>

                    </div>

                </div>

            </div>


            {{-- AKSI CEPAT --}}
            <div class="bg-surface-container-low p-space-lg rounded-2xl shadow-md flex flex-col gap-space-sm">

                <div class="flex items-center gap-space-xs">

                    <div class="w-9 h-9 rounded-xl bg-secondary-container/40 text-secondary flex items-center justify-center">

                        <span class="material-symbols-outlined text-headline-sm">
                            event_note
                        </span>

                    </div>

                    <h3 class="font-headline-sm text-headline-sm">
                        Aksi Cepat
                    </h3>

                </div>


                {{-- EDIT --}}
                <a href="{{ route('courses.edit', $course) }}"
                   class="w-full py-2.5 rounded-xl bg-primary text-on-primary font-label-lg text-label-lg hover:bg-primary-container transition-all flex items-center justify-center gap-1 shadow-[0_0_20px_rgba(173,210,134,0.2)]">

                    <span class="material-symbols-outlined text-sm">
                        edit
                    </span>

                    <span>
                        Edit Mata Kuliah
                    </span>

                </a>


                {{-- HAPUS --}}
                <button type="button"
                        onclick="document.getElementById('deleteCourseModal').classList.remove('hidden')"
                        class="w-full py-2.5 rounded-xl bg-surface-container-high text-on-surface hover:bg-surface-container-highest font-label-lg text-label-lg transition-all flex items-center justify-center gap-1">

                    <span class="material-symbols-outlined text-sm">
                        delete
                    </span>

                    <span>
                        Hapus Mata Kuliah
                    </span>

                </button>


                {{-- MASUK KELAS --}}
                {{-- Belum dihubungkan karena fitur enrollment belum dikerjakan --}}
                <button type="button"
                        class="w-full py-2.5 rounded-xl bg-surface-container-high text-on-surface hover:bg-surface-container-highest font-label-lg text-label-lg transition-all flex items-center justify-center gap-1">

                    <span class="material-symbols-outlined text-sm">
                        login
                    </span>

                    <span>
                        Masuk Kelas
                    </span>

                </button>


                {{-- RPS --}}
                {{-- Belum dihubungkan karena fitur material/RPS belum dikerjakan --}}
                <button type="button"
                        class="w-full py-2.5 rounded-xl bg-surface-container-high text-on-surface hover:bg-surface-container-highest font-label-lg text-label-lg transition-all flex items-center justify-center gap-1">

                    <span class="material-symbols-outlined text-sm">
                        description
                    </span>

                    <span>
                        Unduh Silabus (RPS)
                    </span>

                </button>

            </div>

        </div>

    </div>

        {{-- DELETE MODAL --}}  
    <div id="deleteCourseModal"
        class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black/50 px-4">

        <div class="w-full max-w-md bg-surface-container-low rounded-2xl shadow-xl p-space-xl">

            <div class="flex items-center gap-space-sm">

                <div class="w-10 h-10 rounded-xl bg-error-container text-on-error-container flex items-center justify-center">
                    <span class="material-symbols-outlined">
                        delete
                    </span>
                </div>

                <h2 class="font-headline-lg text-headline-lg font-bold">
                    Hapus Mata Kuliah?
                </h2>

            </div>

            <p class="mt-space-md text-on-surface-variant">
                Yakin ingin menghapus mata kuliah
                <strong class="text-on-surface">
                    {{ $course->name }}
                </strong>?
            </p>

            <p class="mt-space-xs text-sm text-on-surface-variant">
                Tindakan ini tidak dapat dibatalkan.
            </p>

            <div class="mt-space-lg flex justify-end gap-space-sm">

                {{-- BATAL --}}
                <button type="button"
                        onclick="document.getElementById('deleteCourseModal').classList.add('hidden')"
                        class="px-4 py-2.5 rounded-xl bg-surface-container-high text-on-surface hover:bg-surface-container-highest font-label-lg text-label-lg transition-all">

                    Batal

                </button>


                {{-- YA, HAPUS --}}
                <form action="{{ route('courses.destroy', $course) }}"
                    method="POST">

                    @csrf
                    @method('DELETE')

                    <button type="submit"
                            class="px-4 py-2.5 rounded-xl bg-error text-on-error font-label-lg text-label-lg hover:opacity-90 transition-all">

                        Ya, Hapus

                    </button>

                </form>

            </div>

        </div>

    </div>

</x-layout>