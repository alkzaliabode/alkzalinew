<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\DepartmentGoal;

class DepartmentGoalSeeder extends Seeder
{
    public function run(): void
    {
        $mainGoalId = \App\Models\MainGoal::first()->id;

        $goals = [
            'الحفاظ على نظافة جميع مرافق المدينة ومحيطها الخارجي.',
            'ضمان جاهزية وصيانة جميع المرافق الصحية بشكل مستمر.',
            'توفير بيئة نظيفة وصحية وآمنة للزائرين على مدار الساعة.',
            'الاستجابة السريعة للأعطال الطارئة في وحدات الخدمة.',
            'جدولة وتنفيذ خطط التنظيف والصيانة اليومية والأسبوعية.',
            'دعم المناسبات والزيارات المليونية بخطط تنظيف استثنائية.',
            'إدارة ومتابعة أداء فرق العمل وتوزيع المهام بكفاءة.',
            'تحسين الموارد المستخدمة وتقليل الهدر في الاستهلاك.',
            'تعزيز ثقافة النظافة العامة لدى العاملين والزائرين.',
            'إعداد تقارير مهنية يومية وشهرية لرفعها لإدارة المدينة.',
        ];

        foreach ($goals as $goal) {
            DepartmentGoal::create([
                'main_goal_id' => $mainGoalId,
                'goal_text' => $goal,
            ]);
        }
    }
}
