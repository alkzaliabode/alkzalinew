<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ServiceTask;

class ServiceTaskSeeder extends Seeder
{
    public function run(): void
    {
        ServiceTask::create([
            'title' => 'تنظيف شامل لساحة الألعاب',
            'description' => 'تنظيف الحشائش والقمامة وتنظيم المكان.',
            'unit' => 'النظافة العامة',
            'status' => 'pending',
            'priority' => 'medium',
            'due_date' => now()->addDays(3),
            'assigned_to' => 1, // تأكد أن لديك موظف بـ id = 1
        ]);
    }
}
