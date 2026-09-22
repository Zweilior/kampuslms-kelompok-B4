@if ($paginator->hasPages())
    <nav role="navigation" aria-label="Pagination Navigation" class="flex justify-center my-4">
        {{-- Capsule/Pill Container Light Mode --}}
        <div class="inline-flex items-center gap-1.5 p-1.5 rounded-full bg-surface-container-high/60 backdrop-blur-md border border-outline/10 shadow-sm">
            
            {{-- Tombol Previous --}}
            @if ($paginator->onFirstPage())
                <span class="px-5 py-2 rounded-full text-body-sm font-label-md text-on-surface-variant/40 cursor-not-allowed select-none">
                    Previous
                </span>
            @else
                <a href="{{ $paginator->previousPageUrl() }}" class="px-5 py-2 rounded-full bg-surface hover:bg-surface-container text-on-surface text-body-sm font-label-md transition-all shadow-sm">
                    Previous
                </a>
            @endif

            {{-- Elemen Angka Halaman --}}
            @foreach ($elements as $element)
                {{-- "Three Dots" Separator --}}
                @if (is_string($element))
                    <span class="px-3 py-2 text-on-surface-variant font-label-md text-body-sm select-none">
                        {{ $element }}
                    </span>
                @endif

                {{-- Array Of Links --}}
                @if (is_array($element))
                    @foreach ($element as $page => $url)
                        @if ($page == $paginator->currentPage())
                            {{-- Halaman Aktif (Lingkaran Putih/Surface dengan Shadow) --}}
                            <span class="w-9 h-9 flex items-center justify-center rounded-full bg-surface text-primary font-bold text-body-sm shadow-md border border-outline/10 select-none">
                                {{ $page }}
                            </span>
                        @else
                            {{-- Halaman Tidak Aktif --}}
                            <a href="{{ $url }}" class="w-9 h-9 flex items-center justify-center rounded-full text-on-surface-variant hover:text-on-surface hover:bg-surface/50 font-label-md text-body-sm transition-all">
                                {{ $page }}
                            </a>
                        @endif
                    @endforeach
                @endif
            @endforeach

            {{-- Tombol Next (Warna Primary sama seperti tombol 'Tambah Mata Kuliah') --}}
            @if ($paginator->hasMorePages())
                <a href="{{ $paginator->nextPageUrl() }}" class="px-5 py-2 rounded-full bg-primary hover:bg-primary-container text-on-primary font-label-md text-body-sm transition-all shadow-[0_0_15px_rgba(173,210,134,0.3)]">
                    Next
                </a>
            @else
                <span class="px-5 py-2 rounded-full bg-surface-container text-on-surface-variant/40 text-body-sm font-label-md cursor-not-allowed select-none">
                    Next
                </span>
            @endif

        </div>
    </nav>
@endif