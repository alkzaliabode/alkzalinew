<?php

namespace App\Filament\Widgets;

use App\Models\Survey;
use Filament\Widgets\ChartWidget;
use Flowframe\Trend\Trend;
use Flowframe\Trend\TrendValue;

class SurveyStats extends ChartWidget
{
    protected static ?string $heading = 'إحصائيات استبيان رضا الزائرين';

    protected function getData(): array
    {
        // بيانات الرضا العام
        $overallData = Survey::selectRaw('overall_satisfaction, COUNT(*) as count')
            ->groupBy('overall_satisfaction')
            ->get()
            ->mapWithKeys(function ($item) {
                $label = match($item->overall_satisfaction) {
                    'very_satisfied' => 'راض جدًا',
                    'satisfied' => 'راض',
                    'acceptable' => 'مقبول',
                    'dissatisfied' => 'غير راض',
                };
                return [$label => $item->count];
            });
            
        // بيانات نظافة المرافق
        $toiletData = Survey::selectRaw('toilet_cleanliness, COUNT(*) as count')
            ->groupBy('toilet_cleanliness')
            ->get()
            ->mapWithKeys(function ($item) {
                $label = match($item->toilet_cleanliness) {
                    'excellent' => 'ممتازة',
                    'very_good' => 'جيدة جدًا',
                    'good' => 'جيدة',
                    'acceptable' => 'مقبولة',
                    'poor' => 'سيئة',
                };
                return [$label => $item->count];
            });
            
        // بيانات توزيع الزوار حسب عدد الزيارات
        $visitData = Survey::selectRaw('visit_count, COUNT(*) as count')
            ->groupBy('visit_count')
            ->get()
            ->mapWithKeys(function ($item) {
                $label = match($item->visit_count) {
                    'first_time' => 'أول مرة',
                    '2_5_times' => '2-5 مرات',
                    'over_5_times' => 'أكثر من 5',
                };
                return [$label => $item->count];
            });

        return [
            'datasets' => [
                [
                    'label' => 'الرضا العام',
                    'data' => $overallData->values()->toArray(),
                    'backgroundColor' => ['#4CAF50', '#2196F3', '#FFC107', '#F44336'],
                ],
                [
                    'label' => 'نظافة دورات المياه',
                    'data' => $toiletData->values()->toArray(),
                    'backgroundColor' => ['#4CAF50', '#8BC34A', '#FFC107', '#FF9800', '#F44336'],
                ],
                [
                    'label' => 'عدد الزيارات',
                    'data' => $visitData->values()->toArray(),
                    'backgroundColor' => ['#3F51B5', '#2196F3', '#00BCD4'],
                ],
            ],
            'labels' => [
                'الرضا العام' => $overallData->keys()->toArray(),
                'نظافة دورات المياه' => $toiletData->keys()->toArray(),
                'عدد الزيارات' => $visitData->keys()->toArray(),
            ],
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }
    
    public static function canView(): bool
    {
        return auth()->user()->can('view_survey_stats');
    }
}