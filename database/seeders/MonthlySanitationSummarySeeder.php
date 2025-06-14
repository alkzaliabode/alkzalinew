<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class MonthlySanitationSummarySeeder extends Seeder
{
    public function run(): void
    {
        $summary = DB::table('sanitation_facility_tasks')
            ->selectRaw('
                DATE_FORMAT(date, "%Y-%m") as month,
                facility_name,
                task_type,
                SUM(seats_count) as total_seats,
                SUM(mirrors_count) as total_mirrors,
                SUM(mixers_count) as total_mixers,
                SUM(doors_count) as total_doors,
                SUM(sinks_count) as total_sinks,
                SUM(toilets_count) as total_toilets,
                COUNT(*) as total_tasks
            ')
            ->groupBy('month', 'facility_name', 'task_type')
            ->get();

        foreach ($summary as $row) {
            DB::table('monthly_sanitation_summary')->insert([
                'id' => md5($row->month . $row->facility_name . $row->task_type),
                'month' => $row->month,
                'facility_name' => $row->facility_name,
                'task_type' => $row->task_type,
                'total_seats' => $row->total_seats,
                'total_mirrors' => $row->total_mirrors,
                'total_mixers' => $row->total_mixers,
                'total_doors' => $row->total_doors,
                'total_sinks' => $row->total_sinks,
                'total_toilets' => $row->total_toilets,
                'total_tasks' => $row->total_tasks,
            ]);
        }
    }
}
