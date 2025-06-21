<?php

namespace App\Filament\Pages;

use Filament\Pages\Dashboard as BaseDashboard;
use App\Filament\Widgets\AppStatsOverview; // سنستخدم هذا الـ widget لعرض البطاقات

class Dashboard extends BaseDashboard
{
    protected static ?string $navigationIcon = 'heroicon-o-home'; // أيقونة الصفحة الرئيسية في القائمة الجانبية
    protected static ?int $navigationSort = 1; // ترتيب الصفحة الرئيسية في القائمة الجانبية
    protected static ?string $navigationLabel = 'لوحة التحكم'; // هذا هو الاسم الذي سيظهر في القائمة الجانبية
    protected static ?string $slug = 'dashboard'; // هذا هو slug الذي سيستخدم في URL للوصول إلى الصفحة الرئيسية
    protected static ?string $title = 'لوحة التحكم'; // هذا هو العنوان الذي سيظهر في شريط العنوان
    protected static ?string $modelLabel = 'الصفحة الرئيسية'; // هذا هو الاسم الذي سيظهر في القائمة الجانبية
    protected static ?string $description = 'لوحة التحكم الرئيسية لعرض الإحصائيات والبيانات'; // وصف الصفحة الرئيسية
    protected static ?string $recordTitleAttribute = 'title'; // هذا هو السمة التي ستستخدم كعنوان للسجل في الصفحة الرئيسية
    protected static ?string $icon = 'heroicon-o-cog-6-tooth
'; // أيقونة الصفحة الرئيسية في شريط العنوان
    protected static ?string $logo = 'resources/images/logo.png'; // مسار الشعار الذي سيظهر في الصفحة الرئيسية
    protected static ?string $logoDark = 'resources/images/logo-dark.png'; // مسار الشعار الداكن
    protected static ?string $favicon = 'resources/images/favicon.ico'; // مسار أيقونة الموقع (favicon) التي ستظهر في الصفحة الرئيسية
    protected static ?string $faviconDark = 'resources/images/favicon-dark.ico'; // مسار أيقونة الموقع الداكنة التي ستظهر في الصفحة الرئيسية
    protected static ?string $defaultLocale = 'ar'; // تعيين اللغة الافتراضية للوحة التحكم إلى العربية
    protected static ?string $header = 'لوحة التحكم'; // هذا هو العنوان الذي سيظهر في رأس الصفحة الرئيسية
    protected static ?string $headerIcon = 'heroicon-o-cog-6-tooth
'; // أيقونة الرأس التي ستظهر في الصفحة الرئيسية
    protected static ?string $headerActions = 'actions'; // هذا هو الـ view الذي سيحتوي على الإجراءات في رأس الصفحة الرئيسية
    protected static ?string $headerActionsView = 'filament.pages.dashboard-header-actions'; // هذا هو الـ view الذي سيحتوي على الإجراءات في رأس الصفحة الرئيسية
    protected static ?string $footer = 'footer'; // هذا هو الـ view الذي سيحتوي على تذييل الصفحة الرئيسية
    protected static ?string $footerView = 'filament.pages.dashboard-footer'; // هذا هو الـ view الذي سيحتوي على تذييل الصفحة الرئيسية
    protected static ?string $navigationUrl = 'dashboard'; // URL للوصول إلى الصفحة الرئيسية
    
    
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