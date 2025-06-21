@if (!empty($images) && is_array($images))
    <div style="display: flex; gap: 4px;">
        @foreach ($images as $img)
            <img src="{{ $img }}" alt="صورة" style="width: 40px; height: 40px; object-fit: cover; border-radius: 4px;">
        @endforeach
    </div>
@else
    <span style="font-size: 12px; color: #aaa;">لا توجد صور</span>
@endif
