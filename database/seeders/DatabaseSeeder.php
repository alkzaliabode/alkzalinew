<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role; // تأكد من استيرادها
// database/seeders/DatabaseSeeder.php


class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            SuperAdminSeeder::class, // استدعه هنا
            // ... Seeders الأخرى
            MainGoalSeeder::class,
            DepartmentGoalSeeder::class,
            UnitSeeder::class,
            UnitGoalSeeder::class,
            MonthlyGeneralCleaningSummarySeeder::class,
            MonthlySanitationSummarySeeder::class,
            EmployeeSeeder::class,
            TaskSeeder::class, // تأكد من
            ServiceTaskSeeder::class, // تأكد من استدعاء ServiceTaskSeeder
        ]);

        // إذا كنت تستدعي المستخدم Rawan هنا أيضًا، فقم بإزالته
        // لأن SuperAdminSeeder سيهتم به.
        // $user = User::factory()->create([...]); // احذف هذا الجزء إذا كان SuperAdminSeeder ينشئ المستخدم
        // $user->assignRole('Super Admin'); // احذف هذا الجزء أيضًا
    }
}