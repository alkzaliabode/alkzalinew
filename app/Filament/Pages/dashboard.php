<?php

namespace App\Filament\Pages;

use Filament\Pages\Dashboard as BaseDashboard;
use App\Filament\Widgets\AppStatsOverview; // سنستخدم هذا الـ widget لعرض البطاقات

class Dashboard extends BaseDashboard
{
    
    // يمكنك تعديل هذه الخصائص إذا أردت
    protected static string $view = 'filament.pages.dashboard'; // هذا هو ملف الـ view الذي سنصمم فيه البطاقات

    // يمكنك إزالة getHeaderWidgets() هنا أو تركها إذا كنت تريد Widgets أخرى غير البطاقات
    // ولكن لغرض تصميم صفحة كاملة بالبطاقات، سنركز على الـ view
    protected function getHeaderWidgets(): array
    {
        return [
            // يمكنك إضافة widgets هنا إذا كنت تريدها فوق البطاقات
            // على سبيل المثال: AccountWidget::class,
            // أو لا شيء إذا أردت لوحة القيادة لتكون بطاقات فقط
        ];
    }

    protected function getFooterWidgets(): array
    {
        return [
            // يمكنك إضافة widgets هنا إذا كنت تريدها أسفل البطاقات
        ];
    }
}