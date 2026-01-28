@if ($paginator->hasPages())
    <div class="pagination-container">
        {{-- Results info section - separated from navigation --}}
        <div class="pagination-info">
            <p class="pagination-info-text">
                {{ __('courses.showing') }}
                @if ($paginator->firstItem())
                    <span>{{ $paginator->firstItem() }}</span>
                    {{ __('courses.to') }}
                    <span>{{ $paginator->lastItem() }}</span>
                @else
                    {{ $paginator->count() }}
                @endif
                {{ __('courses.of') }}
                <span>{{ $paginator->total() }}</span>
                {{ __('courses.results') }}
            </p>
        </div>

        {{-- Navigation section with proper list structure --}}
        <nav role="navigation" aria-label="{{ __('Pagination Navigation') }}" class="pagination-nav">
            <ul class="pagination-list">
                {{-- Previous Page Link --}}
                @if ($paginator->onFirstPage())
                    <li class="pagination-item">
                        <span class="pagination-link pagination-disabled" aria-disabled="true" aria-label="{{ __('pagination.previous') }}">
                            <svg class="pagination-icon" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z" clip-rule="evenodd" />
                            </svg>
                        </span>
                    </li>
                @else
                    <li class="pagination-item">
                        <a href="{{ $paginator->previousPageUrl() }}" rel="prev" class="pagination-link" aria-label="{{ __('pagination.previous') }}">
                            <svg class="pagination-icon" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z" clip-rule="evenodd" />
                            </svg>
                        </a>
                    </li>
                @endif

                {{-- Pagination Elements --}}
                @foreach ($elements as $element)
                    {{-- "Three Dots" Separator --}}
                    @if (is_string($element))
                        <li class="pagination-item">
                            <span class="pagination-link pagination-separator" aria-disabled="true">{{ $element }}</span>
                        </li>
                    @endif

                    {{-- Array Of Links --}}
                    @if (is_array($element))
                        @foreach ($element as $page => $url)
                            @if ($page == $paginator->currentPage())
                                <li class="pagination-item">
                                    <span class="pagination-link pagination-current" aria-current="page">{{ $page }}</span>
                                </li>
                            @else
                                <li class="pagination-item">
                                    <a href="{{ $url }}" class="pagination-link" aria-label="{{ __('Go to page :page', ['page' => $page]) }}">{{ $page }}</a>
                                </li>
                            @endif
                        @endforeach
                    @endif
                @endforeach

                {{-- Next Page Link --}}
                @if ($paginator->hasMorePages())
                    <li class="pagination-item">
                        <a href="{{ $paginator->nextPageUrl() }}" rel="next" class="pagination-link" aria-label="{{ __('pagination.next') }}">
                            <svg class="pagination-icon" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" />
                            </svg>
                        </a>
                    </li>
                @else
                    <li class="pagination-item">
                        <span class="pagination-link pagination-disabled" aria-disabled="true" aria-label="{{ __('pagination.next') }}">
                            <svg class="pagination-icon" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" />
                            </svg>
                        </span>
                    </li>
                @endif
            </ul>
        </nav>
    </div>
@endif