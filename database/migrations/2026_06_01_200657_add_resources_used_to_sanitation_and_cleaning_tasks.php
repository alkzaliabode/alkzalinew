<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        // ✅ تعديل جدول sanitation_facility_tasks
        Schema::table('sanitation_facility_tasks', function (Blueprint $table) {
            if (!Schema::hasColumn('sanitation_facility_tasks', 'resources_used')) {
                $table->json('resources_used')->nullable()->after('notes');
            }
        });

        // ✅ تعديل جدول general_cleaning_tasks
        Schema::table('general_cleaning_tasks', function (Blueprint $table) {
            if (!Schema::hasColumn('general_cleaning_tasks', 'resources_used')) {
                $table->json('resources_used')->nullable()->after('notes');
            }
        });
    }

    public function down(): void
    {
        // ... (يمكنك ترك دالة down كما هي)
    }
};