<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\MainGoal;

class MainGoalSeeder extends Seeder
{
    public function run(): void
    {
        MainGoal::create([
            'goal_text' => 'تقديم أفضل الخدمات كماً ونوعاً للزائرين الكرام في مدينة الإمام الحسين.',
        ]);
    }
}