<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\UnitGoal;
use App\Models\DepartmentGoal;
use App\Models\Unit; // استيراد نموذج الوحدة (Unit Model)

class UnitGoalSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(): void
    {
        // الحصول على معرف أول هدف قسم (department goal ID)
        // إذا لم يتم العثور على هدف قسم، لن يتم تنفيذ تلقيم أهداف الوحدة.
        $departmentGoal = DepartmentGoal::first();

        if (!$departmentGoal) {
            $this->command->info('No DepartmentGoal found. Skipping UnitGoal seeding.');
            return;
        }

        $departmentGoalId = $departmentGoal->id;

        // تعريف أهداف الوحدات مع أسماء الوحدات
        $unitGoalsData = [
            // أهداف النظافة العامة
            'النظافة العامة' => [
                'تنظيف قاعة واحدة يوميًا مع إدامة عميقة للطابقين العلوي والسفلي حسب الخطة الأسبوعية.',
                'إدامة يومية لقاعات المبيت بعد مغادرة الزوار.',
                'التعقيم الكامل للقاعات باستخدام المعقمات المعتمدة وفق جدول دوري.',
                'كنس وغسل الساحات العامة ثلاث مرات يوميًا.',
                'إزالة الأوساخ والمخلفات بشكل دوري كل ساعتين.',
                'فرش السجاد في القاعات والساحات.',
                'التأكد من نظافة وتعقيم السجاد قبل وبعد الاستخدام.',
                'تعبئة الترامز كل 4 ساعات أو حسب الحاجة.',
                'التأكد من نظافتها وصلاحية المياه داخلها.',
                'رفع الحاويات من جميع النقاط كل 6 ساعات.',
                'غسل وتعقيم الحاويات كل نهاية يوم.',
            ],
            // أهداف المنشآت الصحية
            'المنشآت الصحية' => [
                'تنظيف وتعقيم الحمامات كل ساعتين.',
                'إعادة تعبئة المواد الصحية (صابون، الزاهي للغسالات) بشكل دوري.',
                'صيانة دورية للسيفونات، المغاسل، والمرايا.',
                'إصلاح الأعطال في وقت قصير من الإبلاغ.',
            ],
            // أضف المزيد من مجموعات الأهداف للوحدات الأخرى إذا لزم الأمر
        ];

        // حلقة التكرار لإنشاء أهداف الوحدات
        foreach ($unitGoalsData as $unitName => $goals) {
            // البحث عن الوحدة بناءً على اسمها
            $unit = Unit::where('name', $unitName)->first();

            // إذا لم يتم العثور على الوحدة، قم بتخطيط هذه المجموعة من الأهداف وأبلغ المستخدم
            if (!$unit) {
                $this->command->warn("Unit with name '{$unitName}' not found. Skipping goals for this unit.");
                continue; // الانتقال إلى الوحدة التالية
            }

            foreach ($goals as $goalText) {
                UnitGoal::create([
                    'department_goal_id' => $departmentGoalId,
                    'unit_id' => $unit->id, // <--- تم إضافة معرف الوحدة هنا
                    'unit_name' => $unitName, // يمكنك استخدام اسم الوحدة من الوحدة التي تم جلبها أو التي في البيانات
                    'goal_text' => $goalText,
                    'date' => now()->toDateString(),
                ]);
            }
        }
    }
}

