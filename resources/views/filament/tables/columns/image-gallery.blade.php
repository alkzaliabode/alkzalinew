{{-- resources/views/filament/tables/columns/image-gallery.blade.php --}}
@php
    $images = $getRecord()->{$type . '_images_urls'}; // $type ستكون 'before' أو 'after'
@endphp

@if (!empty($images))
    <div class="flex -space-x-2 overflow-hidden">
        @foreach ($images as $image)
            @if ($image['url'])
                <img class="inline-block h-10 w-10 rounded-full ring-2 ring-white" src="{{ $image['url'] }}" alt="صورة">
            @endif
        @endforeach
    </div>
@else
    <span>لا توجد صور</span>
@endif