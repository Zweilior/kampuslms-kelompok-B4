<x-layout title="Dashboard Admin - KampusLMS" active-nav="dashboard">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <header class="mb-space-lg">
            <div class="flex items-center gap-space-xs mb-space-2xs">
                <span class="px-space-xs py-0.5 rounded-full bg-secondary-container text-on-secondary-container font-label-sm text-label-sm uppercase">
                    Administrasi
                </span>
                <span class="w-1 h-1 rounded-full bg-outline"></span>
                <span class="font-label-sm text-label-sm text-on-surface-variant">Ringkasan sistem</span>
            </div>
            <h1 class="font-headline-lg text-headline-lg text-on-surface">Dashboard Admin</h1>
            <p class="font-body-md text-body-md text-on-surface-variant mt-0.5">
                Pantau pengguna dan mata kuliah yang tersimpan di KampusLMS.
            </p>
        </header>

        <section aria-label="Statistik sistem" class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-bento-gap-desktop mb-space-lg">
            <article class="bg-surface-container-low p-space-lg rounded-2xl shadow-md border border-outline/10">
                <div class="flex items-center gap-space-xs">
                    <div class="w-9 h-9 rounded-xl bg-primary/10 flex items-center justify-center text-primary">
                        <span class="material-symbols-outlined text-headline-sm">group</span>
                    </div>
                    <span class="font-label-lg text-label-lg text-on-surface-variant">Total User</span>
                </div>
                <p class="mt-space-md font-display-lg text-display-lg text-on-surface font-bold leading-none">{{ number_format($totalUsers) }}</p>
                <p class="mt-space-xs font-label-sm text-label-sm text-outline">Seluruh akun terdaftar</p>
            </article>

            <article class="bg-surface-container-low p-space-lg rounded-2xl shadow-md border border-outline/10">
                <div class="flex items-center gap-space-xs">
                    <div class="w-9 h-9 rounded-xl bg-secondary-container flex items-center justify-center text-on-secondary-container">
                        <span class="material-symbols-outlined text-headline-sm">school</span>
                    </div>
                    <span class="font-label-lg text-label-lg text-on-surface-variant">Total Dosen</span>
                </div>
                <p class="mt-space-md font-display-lg text-display-lg text-on-surface font-bold leading-none">{{ number_format($totalLecturers) }}</p>
                <p class="mt-space-xs font-label-sm text-label-sm text-outline">Akun dengan role dosen</p>
            </article>

            <article class="bg-surface-container-low p-space-lg rounded-2xl shadow-md border border-outline/10">
                <div class="flex items-center gap-space-xs">
                    <div class="w-9 h-9 rounded-xl bg-tertiary-container/30 flex items-center justify-center text-tertiary">
                        <span class="material-symbols-outlined text-headline-sm">groups</span>
                    </div>
                    <span class="font-label-lg text-label-lg text-on-surface-variant">Total Mahasiswa</span>
                </div>
                <p class="mt-space-md font-display-lg text-display-lg text-on-surface font-bold leading-none">{{ number_format($totalStudents) }}</p>
                <p class="mt-space-xs font-label-sm text-label-sm text-outline">Akun dengan role mahasiswa</p>
            </article>

            <article class="bg-surface-container-low p-space-lg rounded-2xl shadow-md border border-outline/10">
                <div class="flex items-center gap-space-xs">
                    <div class="w-9 h-9 rounded-xl bg-primary/10 flex items-center justify-center text-primary">
                        <span class="material-symbols-outlined text-headline-sm">library_books</span>
                    </div>
                    <span class="font-label-lg text-label-lg text-on-surface-variant">Mata Kuliah</span>
                </div>
                <p class="mt-space-md font-display-lg text-display-lg text-on-surface font-bold leading-none">{{ number_format($totalCourses) }}</p>
                <p class="mt-space-xs font-label-sm text-label-sm text-outline">Total mata kuliah terdaftar</p>
            </article>
        </section>
    </div>
</x-layout>