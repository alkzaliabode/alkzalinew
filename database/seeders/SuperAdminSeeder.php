<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Filament\Facades\Filament; // استيراد Filament Facade
use Illuminate\Support\Str; // لاستخدام Str::plural

class SuperAdminSeeder extends Seeder
{
    public function run(): void
    {
        // 1. إنشاء/جلب دور Super Admin
        $superAdminRole = Role::firstOrCreate(
            ['name' => 'super_admin', 'guard_name' => 'web'],
            // يمكنك إضافة ['team_id' => null] إذا كنت تستخدم فرق العمل
        );

        // 2. توليد الصلاحيات لجميع موارد Filament (Resources, Pages, Widgets)
        // هذا الجزء يحاكي ما يفعله php artisan shield:generate --all
        $permissionsToAssign = [];
        $permissionPrefixes = config('filament-shield.permission_prefixes');

        // صلاحيات الموارد (Resources)
        if (config('filament-shield.entities.resources')) {
            foreach (Filament::getResources() as $resource) {
                $resourceName = Str::lower(Str::replaceLast('Resource', '', class_basename($resource)));
                foreach ($permissionPrefixes['resource'] as $prefix) {
                    $permissionsToAssign[] = "{$prefix}_{$resourceName}";
                }
            }
        }

        // صلاحيات الصفحات (Pages)
        if (config('filament-shield.entities.pages')) {
            foreach (Filament::getPages() as $page) {
                $pageName = Str::kebab(class_basename($page));
                $permissionsToAssign[] = "{$permissionPrefixes['page']}_{$pageName}";
            }
        }

        // صلاحيات الأدوات (Widgets)
        if (config('filament-shield.entities.widgets')) {
            foreach (Filament::getWidgets() as $widget) {
                $widgetName = Str::kebab(class_basename($widget));
                $permissionsToAssign[] = "{$permissionPrefixes['widget']}_{$widgetName}";
            }
        }

        // إنشاء الصلاحيات فعليًا في قاعدة البيانات
        foreach (array_unique($permissionsToAssign) as $permissionName) {
            Permission::firstOrCreate(['name' => $permissionName, 'guard_name' => 'web']);
        }

        // 3. ربط جميع الصلاحيات المولدة (الآن هي الصلاحيات الصحيحة) بالدور
        $superAdminRole->syncPermissions(Permission::all());

        // 4. إنشاء أو جلب المستخدم Rawan
        $user = User::firstOrCreate(
            ['email' => 'roan1@admin.com'],
            [
                'name' => 'Rawan',
                'password' => Hash::make('1234'),
            ]
        );

        // 5. تعيين دور Super Admin للمستخدم
        if (!$user->hasRole('super_admin')) {
            $user->assignRole($superAdminRole);
        }
    }
}