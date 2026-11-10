@if ($paginator->hasPages())
    <nav role="navigation" aria-label="Navigasi Halaman" class="custom-pagination-nav flex justify-center my-4 w-full px-2">
        {{-- Capsule/Pill Container (< X of Y >) --}}
        <div class="custom-pagination__pill-container inline-flex items-center justify-between gap-3 sm:gap-4 p-1.5 rounded-full backdrop-blur-md shadow-sm">

            {{-- Tombol Previous (<) --}}
            @if ($paginator->onFirstPage())
                <span
                    class="custom-pagination__circle-btn custom-pagination__circle-btn--disabled"
                    aria-disabled="true" aria-label="Halaman sebelumnya">
                    <span class="material-symbols-outlined text-[1.25rem]" aria-hidden="true">chevron_left</span>
                </span>
            @else
                <a href="{{ $paginator->previousPageUrl() }}"
                    rel="prev"
                    class="custom-pagination__circle-btn custom-pagination__circle-btn--prev"
                    aria-label="Halaman sebelumnya">
                    <span class="material-symbols-outlined text-[1.25rem]" aria-hidden="true">chevron_left</span>
                </a>
            @endif

            {{-- Status Halaman (X of Y) --}}
            <div class="custom-pagination__info flex items-center gap-1.5 px-3 select-none">
                <span class="custom-pagination__current-page font-bold text-sm sm:text-base text-on-surface">
                    {{ $paginator->currentPage() }}
                </span>
                <span class="custom-pagination__separator text-xs sm:text-sm font-medium text-on-surface-variant/70 uppercase tracking-wider">
                    of
                </span>
                <span class="custom-pagination__total-pages font-bold text-sm sm:text-base text-on-surface">
                    {{ $paginator->lastPage() }}
                </span>
            </div>

            {{-- Tombol Next (>) --}}
            @if ($paginator->hasMorePages())
                <a href="{{ $paginator->nextPageUrl() }}"
                    rel="next"
                    class="custom-pagination__circle-btn custom-pagination__circle-btn--next custom-pagination__circle-btn--primary"
                    aria-label="Halaman berikutnya">
                    <span class="material-symbols-outlined text-[1.25rem]" aria-hidden="true">chevron_right</span>
                </a>
            @else
                <span
                    class="custom-pagination__circle-btn custom-pagination__circle-btn--disabled"
                    aria-disabled="true" aria-label="Halaman berikutnya">
                    <span class="material-symbols-outlined text-[1.25rem]" aria-hidden="true">chevron_right</span>
                </span>
            @endif

        </div>
    </nav>
@endif
