<div class="-mx-4 -mt-4 mb-4"> {{-- هذه الكلاسات تلغي هوامش القسم الافتراضية --}}
    @php
        $url = $getState();
        $embedUrl = str_contains($url, 'youtube.com/watch?v=')
            ? str_replace('watch?v=', 'embed/', $url)
            : $url;
    @endphp

    @if($embedUrl)
        <div class="w-full aspect-video shadow-sm overflow-hidden">
            <iframe
                class="w-full h-full border-0"
                src="{{ $embedUrl }}"
                allowfullscreen>
            </iframe>
        </div>
    @endif
</div>
