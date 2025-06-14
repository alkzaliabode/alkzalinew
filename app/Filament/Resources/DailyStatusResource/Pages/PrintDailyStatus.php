<?php

namespace App\Filament\Resources\DailyStatusResource\Pages;

use App\Filament\Resources\DailyStatusResource;
use App\Models\DailyStatus;
use Filament\Resources\Pages\Page;

class PrintDailyStatus extends Page
{
    protected static string $resource = DailyStatusResource::class;

    protected static string $view = 'filament.resources.daily-status-resource.pages.print-daily-status';

    public DailyStatus $record;

    public function mount(DailyStatus $record): void
    {
        $this->record = $record;
    }

    // ✅ هذا ضروري لتجنب خطأ التوافق في بعض الإصدارات
    public static function shouldRegisterNavigation(array $parameters = []): bool
    {
        return false;
    }

    // ✅ عنوان الصفحة إذا أردت تغييره داخل Filament
    public function getTitle(): string
    {
        return 'طباعة الموقف اليومي';
    }
}
