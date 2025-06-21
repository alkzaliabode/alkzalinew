<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\GeneralCleaningTask;
use App\Models\SanitationFacilityTask;
use App\Models\Unit;
use App\Models\UnitGoal;

class TaskSeeder extends Seeder
{
    public function run(): void
    {
        // 📌 وحدة النظافة العامة
        $cleaningUnit = Unit::where('name', 'النظافة العامة')->first();
        // هنا نبحث عن هدف وحدة لأي تاريخ، لأن TaskSeeder لا يحدد تاريخاً للهدف بشكل مباشر
        $cleaningGoal = null;
        if ($cleaningUnit) {
            $cleaningGoal = UnitGoal::where('unit_id', $cleaningUnit->id)->first();
        }


        // ---DEBUGGING START---
        if (!$cleaningUnit) {
            echo "DEBUG: Cleaning Unit 'النظافة العامة' not found in TaskSeeder.\n";
        } else {
            echo "DEBUG: Cleaning Unit found. ID: " . $cleaningUnit->id . "\n";
        }

        if (!$cleaningGoal) {
            echo "DEBUG: Cleaning Goal not found for Unit ID: " . ($cleaningUnit ? $cleaningUnit->id : 'N/A') . " in TaskSeeder.\n";
        } else {
            echo "DEBUG: Cleaning Goal found. ID: " . $cleaningGoal->id . "\n";
        }
        // uncomment the line below to stop the seeder here and inspect
        // dd($cleaningUnit, $cleaningGoal);
        // ---DEBUGGING END---


        if ($cleaningUnit && $cleaningGoal) {
            GeneralCleaningTask::create([
                'unit_id' => $cleaningUnit->id,
                'related_goal_id' => $cleaningGoal->id,
                'date' => now(),
                'shift' => 'صباحي',
                'task_type' => 'تنظيف قاعة',
                'location' => 'الطابق الأرضي',
                'quantity' => 1,
                'status' => 'قيد التنفيذ',
                'working_hours' => 5,
                'mats_count' => 2,
                'pillows_count' => 4,
                'fans_count' => 3,
                'windows_count' => 6,
                'carpets_count' => 1,
                'blankets_count' => 2,
                'beds_count' => 0,
                'beneficiaries_count' => 25,
                'filled_trams_count' => 1,
                'carpets_laid_count' => 1,
                'large_containers_count' => 2,
                'small_containers_count' => 3,
                'notes' => 'تنظيف سريع واستبدال السجاد.',
            ]);
        }

        // 📌 وحدة المنشآت الصحية
        $healthUnit = Unit::where('name', 'المنشآت الصحية')->first();
        $healthGoal = null;
        if ($healthUnit) {
            $healthGoal = UnitGoal::where('unit_id', $healthUnit->id)->first();
        }

        // ---DEBUGGING START---
        if (!$healthUnit) {
            echo "DEBUG: Health Unit 'المنشآت الصحية' not found in TaskSeeder.\n";
        } else {
            echo "DEBUG: Health Unit found. ID: " . $healthUnit->id . "\n";
        }

        if (!$healthGoal) {
            echo "DEBUG: Health Goal not found for Unit ID: " . ($healthUnit ? $healthUnit->id : 'N/A') . " in TaskSeeder.\n";
        } else {
            echo "DEBUG: Health Goal found. ID: " . $healthGoal->id . "\n";
        }
        // ---DEBUGGING END---

        if ($healthUnit && $healthGoal) {
            SanitationFacilityTask::create([
                'unit_id' => $healthUnit->id,
                'related_goal_id' => $healthGoal->id,
                'date' => now(),
                'shift' => 'مسائي',
                'task_type' => 'تعقيم حمام',
                'facility_name' => 'دورة مياه الطابق العلوي',
                'details' => 'تعقيم شامل مع استبدال المعقمات.',
                'status' => 'مكتمل',
                'working_hours' => 3,
                'seats_count' => 2,
                'sinks_count' => 2,
                'mixers_count' => 2,
                'mirrors_count' => 1,
                'doors_count' => 2,
                'toilets_count' => 2,
                'notes' => 'تم الانتهاء من العمل بنجاح.',
            ]);
        }
    }
}
