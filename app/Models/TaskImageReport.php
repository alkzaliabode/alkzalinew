<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Support\Facades\Storage;

class TaskImageReport extends Model
{
    protected $fillable = [
        'report_title',
        'date',
        'unit_type',
        'location',
        'task_type',
        'task_id', // <--- أضف هذا السط
        'before_images',
        'after_images',
        'status',
        'notes'
    ];

    protected $casts = [
        'date' => 'date',
        'before_images' => 'array',
        'after_images' => 'array',
    ];

    protected $appends = [
        'before_images_urls',
        'after_images_urls',
        'before_images_count',
        'after_images_count'
    ];

    // Accessor للصور قبل التنفيذ
    public function getBeforeImagesUrlsAttribute()
    {
        return $this->processImages($this->before_images);
    }

    // Accessor للصور بعد التنفيذ
    public function getAfterImagesUrlsAttribute()
    {
        return $this->processImages($this->after_images);
    }

    // Accessor لعدد الصور قبل التنفيذ
    public function getBeforeImagesCountAttribute()
    {
        return count($this->before_images_urls);
    }

    // Accessor لعدد الصور بعد التنفيذ
    public function getAfterImagesCountAttribute()
    {
        return count($this->after_images_urls);
    }

    // معالجة الصور
    private function processImages($images)
    {
        if (empty($images)) {
            return [];
        }

        return collect($images)->map(function ($image) {
            // تنظيف المسار من أي إشارة إلى storage/public
            $cleanPath = str_replace(['storage/', 'public/'], '', $image);
            
            // التحقق من وجود الصورة
            $exists = Storage::disk('public')->exists($cleanPath);
            
            return [
                'url' => $exists ? Storage::disk('public')->url($cleanPath) : null,
                'path' => $cleanPath,
                'exists' => $exists,
                'absolute_path' => $exists ? Storage::disk('public')->path($cleanPath) : null
            ];
        })->toArray();
    }

    // الحصول على أول 3 صور قبل التنفيذ للجدول
    public function getBeforeImagesForTableAttribute()
    {
        return collect($this->before_images_urls)
            ->take(3)
            ->pluck('url')
            ->filter()
            ->toArray();
    }

    // الحصول على أول 3 صور بعد التنفيذ للجدول
    public function getAfterImagesForTableAttribute()
    {
        return collect($this->after_images_urls)
            ->take(3)
            ->pluck('url')
            ->filter()
            ->toArray();
    }
}
