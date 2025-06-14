<?php

namespace App\Http\Responses;

use Filament\Facades\Filament;
use Filament\Http\Responses\Auth\LoginResponse as BaseLoginResponse;
use Illuminate\Http\RedirectResponse;

class LoginResponse extends BaseLoginResponse
{
    public function toResponse($request): RedirectResponse
    {
        // التحقق من دور المستخدم وتوجيهه إلى الصفحة المناسبة
        if (Filament::auth()->user()->hasRole('super_admin')) {
            return redirect()->route('filament.pages.dashboard'); // لوحة التحكم للمشرفين
        }

        // توجيه الموظفين إلى صفحة قائمة مهام المنشآت الصحية
        return redirect()->route('filament.resources.sanitation-facility-tasks.index');
        // أو لمسار الإنشاء مباشرة:
        // return redirect()->route('filament.resources.sanitation-facility-tasks.create');
    }
}