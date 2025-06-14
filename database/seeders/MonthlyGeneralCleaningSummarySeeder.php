<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MonthlyGeneralCleaningSummarySeeder extends Seeder
{
    public function run(): void
    {
        $summary = DB::table('general_cleaning_tasks')
            ->selectRaw('
                DATE_FORMAT(date, "%Y-%m") as month,
                location,
                task_type,
                SUM(mats_count) as total_mats,
                SUM(pillows_count) as total_pillows,
                SUM(fans_count) as total_fans,
                SUM(windows_count) as total_windows,
                SUM(carpets_count) as total_carpets,
                SUM(blankets_count) as total_blankets,
                SUM(beds_count) as total_beds,
                SUM(beneficiaries_count) as total_beneficiaries,
                SUM(filled_trams_count) as total_trams,
                SUM(carpets_laid_count) as total_laid_carpets,
                SUM(large_containers_count) as total_large_containers,
                SUM(small_containers_count) as total_small_containers
            ')
            ->groupBy('month', 'location', 'task_type')
            ->get();

        foreach ($summary as $row) {
            DB::table('monthly_general_cleaning_summary')->insert([
                'id' => md5($row->month . $row->location . $row->task_type),
                'month' => $row->month,
                'location' => $row->location,
                'task_type' => $row->task_type,
                'total_mats' => $row->total_mats ?? 0,
                'total_pillows' => $row->total_pillows ?? 0,
                'total_fans' => $row->total_fans ?? 0,
                'total_windows' => $row->total_windows ?? 0,
                'total_carpets' => $row->total_carpets ?? 0,
                'total_blankets' => $row->total_blankets ?? 0,
                'total_beds' => $row->total_beds ?? 0,
                'total_beneficiaries' => $row->total_beneficiaries ?? 0,
                'total_trams' => $row->total_trams ?? 0,
                'total_laid_carpets' => $row->total_laid_carpets ?? 0,
                'total_large_containers' => $row->total_large_containers ?? 0,
                'total_small_containers' => $row->total_small_containers ?? 0,
            ]);
        }
    }
}