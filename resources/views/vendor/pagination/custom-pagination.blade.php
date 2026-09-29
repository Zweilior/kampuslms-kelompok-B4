@if ($paginator->hasPages())
    <nav role="navigation" aria-label="Pagination Navigation" class="flex justify-center my-4">
        {{-- Capsule/Pill Container Light Mode --}}
        <div class="custom-pagination__container inline-flex items-center gap-1.5 p-1.5 rounded-full backdrop-blur-md">

            {{-- Tombol Previous --}}
            @if ($paginator->onFirstPage())
                <span
                    class="custom-pagination__previous-disabled px-5 py-2 rounded-full text-body-sm font-label-md cursor-not-allowed select-none">
                    Previous
                </span>
            @else
                <a href="{{ $paginator->previousPageUrl() }}"
                    class="custom-pagination__previous px-5 py-2 rounded-full text-body-sm font-label-md transition-all shadow-sm">
                    Previous
                </a>
            @endif

            {{-- Elemen Angka Halaman --}}
            @foreach ($elements as $element)
                {{-- "Three Dots" Separator --}}
                @if (is_string($element))
                    <span class="custom-pagination__ellipsis px-3 py-2 font-label-md text-body-sm select-none">
                        {{ $element }}
                    </span>
                @endif

                {{-- Array Of Links --}}
                @if (is_array($element))
                    @foreach ($element as $page => $url)
                        @if ($page == $paginator->currentPage())
                            {{-- Halaman Aktif (Lingkaran Putih/Surface dengan Shadow) --}}
                            <span
                                class="custom-pagination__page-active w-9 h-9 flex items-center justify-center rounded-full font-bold text-body-sm shadow-md select-none">
                                {{ $page }}
                            </span>
                        @else
                            {{-- Halaman Tidak Aktif --}}
                            <a href="{{ $url }}"
                                class="custom-pagination__page w-9 h-9 flex items-center justify-center rounded-full font-label-md text-body-sm transition-all">
                                {{ $page }}
                            </a>
                        @endif
                    @endforeach
                @endif
            @endforeach

            {{-- Tombol Next (Warna Primary sama seperti tombol 'Tambah Mata Kuliah') --}}
            @if ($paginator->hasMorePages())
                <a href="{{ $paginator->nextPageUrl() }}"
                    class="custom-pagination__next px-5 py-2 rounded-full font-label-md text-body-sm transition-all">
                    Next
                </a>
            @else
                <span
                    class="custom-pagination__next-disabled px-5 py-2 rounded-full text-body-sm font-label-md cursor-not-allowed select-none">
                    Next
                </span>
            @endif

        </div>
    </nav>
@endif
