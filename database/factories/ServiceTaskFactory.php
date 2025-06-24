<?php

namespace Database\Factories;

use App\Models\ServiceTask; // استيراد نموذج ServiceTask
use App\Models\Employee;
use Illuminate\Database\Eloquent\Factories\Factory;
// use App\Models\Employee; // تم إزالة هذا الاستيراد لأنه غير مستخدم في Factory

class ServiceTaskFactory extends Factory
{
    /**
     * النموذج المرتبط بالـ factory.
     *
     * @var string
     */
    protected $model = ServiceTask::class;

    /**
     * تعريف الحالة الافتراضية للنموذج.
     *
     * @return array<string, mixed>
     */
    public function definition(): array // تم إضافة تلميح النوع (type hint) للحفاظ على التناسق
    {
        return [
            'title' => $this->faker->sentence(), // عنوان مهمة عشوائي
            'description' => $this->faker->paragraph(), // وصف عشوائي
            'unit' => $this->faker->randomElement(['النظافة العامة', 'المنشأت الصحية']), // اختيار وحدة عشوائية (قيم ثابتة)
            'status' => $this->faker->randomElement(['pending', 'in_progress', 'completed', 'rejected']), // اختيار حالة عشوائية (قيم ثابتة)
            'order_column' => $this->faker->unique()->numberBetween(1, 100), // رقم ترتيب فريد
            'due_date' => $this->faker->dateTimeBetween('now', '+1 month'), // تاريخ استحقاق عشوائي
            // تعيين المهمة لمستخدم عشوائي موجود. يتطلب وجود مستخدمين في قاعدة البيانات.
            'assigned_to' => User::inRandomOrder()->first()->id,
            'priority' => $this->faker->randomElement(['قليلة', 'متوسطة', 'عالية']), // اختيار أولوية عشوائية
            // إضافة created_at و updated_at للحفاظ على التناسق في المصنع (إذا لم يتم تعيينهما بواسطة Laravel تلقائياً)
            'created_at' => $this->faker->dateTimeBetween('-1 year', 'now'),
            'updated_at' => $this->faker->dateTimeBetween('-1 year', 'now'),
        ];
    }
}
