<?php

namespace App\Providers\Filament;

use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\Widgets; // استيراد كلاس Widgets لاستخدامه في تحديد الـ widgets
use Filament\Navigation\NavigationItem; // إضافة استيراد NavigationItem
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;

use Illuminate\View\Middleware\ShareErrorsFromSession;
use BezhanSalleh\FilamentShield\FilamentShieldPlugin; // استيراد FilamentShieldPlugin لتكامل الصلاحيات

class AdminPanelProvider extends PanelProvider
{
    protected static ?string $navigationLabel = 'الصفحة'; // Navigation label in Arabic
    protected static ?string $navigationGroup = 'إدارة الأداء'; // Navigation group in Arabic
    protected static ?string $navigationIcon = 'heroicon-o-cog-6-tooth'; // أيقونة لوحة الإدارة
    protected static ?int $navigationSort = 1; // ترتيب لوحة الإدارة في القائمة الجانبية
    protected static ?string $recordTitleAttribute = 'name'; // استخدام الاسم كعنوان السجل
    protected static ?string $navigationUrl = 'admin'; // URL للوصول إلى لوحة الإدارة
    protected static ?string $slug = 'admin'; // تعيين slug للوحة الإدارة
    protected static ?string $title = 'لوحة الإدارة'; // عنوان لوحة الإدارة
    protected static ?string $description = 'لوحة الإدارة لإدارة الأداء والنتائج'; // وصف لوحة الإدارة
    protected static ?string $icon = 'heroicon-o-cog-6-tooth'; // أيقونة لوحة الإدارة
    protected static ?string $logo = 'images/logo.png'; // المسار يجب أن يكون نسبة إلى مجلد 'public'
    protected static ?string $logoDark = 'resources/images/logo-dark.png'; // مسار الشعار الداكن
    protected static ?string $defaultLocale = 'ar'; // تعيين اللغة الافتراضية للوحة الإدارة إلى العربية

    /**
     * تكوين لوحة إدارة Filament.
     *
     * هذه الدالة تقوم بتعريف خصائص لوحة الإدارة مثل المسار، الألوان،
     * الموارد، الصفحات، الـ widgets، والـ middlewares.
     *
     * @param Panel $panel كائن لوحة الإدارة.
     * @return Panel كائن لوحة الإدارة المهيأ.
     */
    public function panel(Panel $panel): Panel
    {
        return $panel
            // تعيين هذه اللوحة كلوحة الإدارة الافتراضية للتطبيق.
            ->default()
            // تعيين معرف فريد للوحة الإدارة (يستخدم في الروابط والخدمات).
            ->id('admin')
            // تحديد المسار الأساسي (URL segment) للوحة الإدارة.
            ->path('admin')
            // تمكين صفحة تسجيل الدخول المدمجة في Filament.
            ->login()
            // تحديد لوحة الألوان المخصصة التي ستستخدمها لوحة الإدارة.
           ->colors([
                'danger'  => Color::Rose,
                'gray'    => Color::Gray,
                'info'    => Color::Blue,
                'primary' => Color::Orange,  // ✅ تم تغيير اللون الأساسي إلى البرتقالي
                'success' => Color::Emerald,
                'warning' => Color::Yellow,  // ✅ (اختياري) يمكنك تغيير لون التحذير لتمييزه عن الأساسي
            ])
            // تطبيق الثيم المخصص لـ Filament من ملف CSS.
            
            ->viteTheme('resources/css/filament/admin/theme.css')
            // اكتشاف وتسجيل جميع الموارد (Resources) تلقائيًا.
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\\Filament\\Resources')
            // اكتشاف وتسجيل جميع الصفحات (Pages) تلقائيًا.
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\\Filament\\Pages')
            // تسجيل الصفحات المحددة يدويًا، مثل لوحة القيادة المخصصة.
            ->pages([
                
                \App\Filament\Pages\Dashboard::class, // صفحة لوحة القيادة التي ستعرض البطاقات.
                    \App\Filament\Pages\ProfessionalImageReports::class,  // إضافة صفحة التقارير المصورة
                \App\Filament\Pages\ServiceTasksBoardPage::class, // صفحة لوحة مهام الخدمة.

            ])
            // اكتشاف وتسجيل جميع الـ Widgets تلقائيًا.
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\\Filament\\Widgets')
            // تحديد الـ Widgets التي ستظهر في لوحة القيادة الافتراضية.
            // إذا كنت تستخدم لوحة قيادة مخصصة بالكامل (مثل تلك التي تعرض البطاقات في الـ view الخاص بها)،
            // قد ترغب في تعطيل هذه الـ widgets الافتراضية لتجنب الازدواجية أو التداخل.
            ->widgets([
                Widgets\AccountWidget::class,     // Widget لعرض معلومات حساب المستخدم الحالي.
                Widgets\FilamentInfoWidget::class, // Widget لعرض معلومات حول إصدار Filament.
            ])
            // تحديد الـ middlewares التي سيتم تطبيقها على جميع طلبات لوحة الإدارة.
            ->middleware([
                EncryptCookies::class,             // تشفير الكوكيز لحماية البيانات الحساسة.
                AddQueuedCookiesToResponse::class, // إضافة الكوكيز المنتظرة إلى استجابة HTTP.
                StartSession::class,               // بدء جلسة HTTP للمستخدم.
                AuthenticateSession::class,        // التحقق من صحة جلسة المصادقة.
                ShareErrorsFromSession::class,     // مشاركة رسائل الخطأ من الجلسة مع الـ views.
                VerifyCsrfToken::class,            // التحقق من توكن CSRF لمنع هجمات تزوير الطلبات عبر المواقع.
                SubstituteBindings::class,         // ربط نماذج Eloquent بمعرفات المسارات تلقائيًا.
                DisableBladeIconComponents::class, // تعطيل مكونات Blade للأيقونات (يمكن إعادة تمكينها إذا لزم الأمر).
                DispatchServingFilamentEvent::class, // إطلاق حدث بعد تهيئة وخدمة Filament.
            ])
            // تسجيل إضافات (Plugins) Filament.
            ->plugins([
                FilamentShieldPlugin::make(), // إضافة Filament Shield لإدارة الأدوار والصلاحيات.
            ])
            // تحديد الـ middlewares الخاصة بالمصادقة (Authentication).
            ->authMiddleware([
                Authenticate::class, // Middleware للمصادقة على المستخدم قبل الوصول إلى لوحة الإدارة.
            ]);
    }

    /**
     * تخصيص العناصر في شريط التنقل (Navigation).
     *
     * @return array
     */
    public function navigation(): array
    {
        return [
            NavigationItem::make('الصفحة الرئيسية')  // تغيير التسمية هنا إلى اللغة العربية
                ->url(route('filament.pages.dashboard')) // تعيين الرابط لصفحة الـ Dashboard
                ->icon('heroicon-o-home'),  // أيقونة الصفحة الرئيسية
        ];
    }
}
