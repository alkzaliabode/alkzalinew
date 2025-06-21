<?php

namespace App\Filament\Pages;

use App\Models\TaskImageReport;
use Filament\Pages\Page;
use Illuminate\Contracts\View\View;

class PrintImageReport extends Page
{
    protected static string $view = 'filament.pages.print-image-report';

    public TaskImageReport $record;

    public function mount(TaskImageReport $record): void
    {
        $this->record = $record;
    }

    // إلغاء ظهور الصفحة في قائمة التنقل
    public static function shouldRegisterNavigation(array $parameters = []): bool
    {
        return false;
    }

    // عنوان الصفحة في المتصفح (اختياري)
    public function getTitle(): string
    {
        return 'تقرير المهمة - ' . ($this->record->location ?? 'غير محدد');
    }

    // إرسال البيانات إلى الـ Blade بشكل يدوي
    public function render():  \Illuminate\Contracts\View\View
    {
        return view(static::$view, [
            'record' => $this->record,
        ]);
    }
}
