<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Unit;

class UnitSeeder extends Seeder
{
    public function run(): void
    {
        $units = [
            ['name' => 'النظافة العامة', 'code' => 'CLN001', 'description' => 'الوحدة تهتم بنظافة المدينة'],
            ['name' => 'المنشآت الصحية', 'code' => 'HLT001', 'description' => 'الوحدة المسؤولة عن المرافق الصحية'],
        ];

        foreach ($units as $unit) {
            Unit::updateOrCreate(
                ['name' => $unit['name']],
                $unit
            );
        }
    }
}
