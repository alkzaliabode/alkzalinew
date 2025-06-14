<?php

namespace App\Filament\Widgets;

use Filament\Widgets\ChartWidget;
use App\Models\ResourceTracking;
use App\Models\ActualResult;
use App\Models\UnitGoal;
use Carbon\Carbon;

class GilbertTriangleWidget extends ChartWidget
{
    protected static ?string $heading = 'مثلث جلبرت - الأداء والكفاءة والفاعلية';
    protected static ?string $maxHeight = '480px'; // حجم أكثر توازنًا
    protected static ?int $sort = 1;
    protected static ?string $pollingInterval = null;

    public $effectiveness = 0;
    public $efficiency = 0;

    protected function getType(): string
    {
        return 'radar';
    }

    protected function getData(): array
    {
        $today = Carbon::today()->toDateString();
        $yesterday = Carbon::yesterday()->toDateString();

        [$todayEfficiency, $todayEffectiveness] = $this->getRates($today);

        $this->efficiency = $todayEfficiency;
        $this->effectiveness = $todayEffectiveness;

        return [
            'datasets' => [
                [
                    'label' => 'اليوم',
                    'data' => $this->prepareTriangleData($today),
                    'backgroundColor' => 'rgba(59, 130, 246, 0.18)',
                    'borderColor' => 'rgba(59, 130, 246, 1)',
                    'pointBackgroundColor' => 'rgba(59, 130, 246, 1)',
                    'pointBorderColor' => '#fff',
                    'pointHoverBackgroundColor' => '#fff',
                    'pointHoverBorderColor' => 'rgba(59, 130, 246, 1)',
                ],
                [
                    'label' => 'الأمس',
                    'data' => $this->prepareTriangleData($yesterday),
                    'backgroundColor' => 'rgba(239, 68, 68, 0.14)',
                    'borderColor' => 'rgba(239, 68, 68, 1)',
                    'pointBackgroundColor' => 'rgba(239, 68, 68, 1)',
                    'pointBorderColor' => '#fff',
                    'pointHoverBackgroundColor' => '#fff',
                    'pointHoverBorderColor' => 'rgba(239, 68, 68, 1)',
                ],
            ],
            'labels' => ['النتائج', 'الأهداف', 'الموارد'],
        ];
    }

    private function prepareTriangleData(string $date): array
    {
        $goal = UnitGoal::where('date', $date)->first();
        $result = ActualResult::where('date', $date)->first();
        $resource = ResourceTracking::where('date', $date)->first();

        $performance = $result?->quality_rating ?? 0;
        $completedTasks = $result?->completed_tasks ?? 0;
        $goalValue = $goal?->total_goal ?? 0;
        $effectiveness = ($goalValue > 0) ? min(100, round(($completedTasks / $goalValue) * 100, 2)) : 0;
        $workingHours = $resource?->total_working_hours ?? 0;
        $efficiency = ($workingHours > 0) ? min(100, round(($completedTasks / $workingHours) * 20, 2)) : 0;

        return [
            $performance,
            $effectiveness,
            $efficiency,
        ];
    }

    private function getRates(string $date): array
    {
        $goal = UnitGoal::where('date', $date)->first();
        $result = ActualResult::where('date', $date)->first();
        $resource = ResourceTracking::where('date', $date)->first();

        $completedTasks = $result?->completed_tasks ?? 0;
        $workingHours = $resource?->total_working_hours ?? 0;
        $goalValue = $goal?->total_goal ?? 0;

        $efficiency = ($workingHours > 0) ? round(($completedTasks / $workingHours), 2) : 0;
        $effectiveness = ($goalValue > 0) ? round(($completedTasks / $goalValue) * 100, 2) : 0;

        return [$efficiency, $effectiveness];
    }

    protected function getOptions(): array
    {
        return [
            'responsive' => true,
            'maintainAspectRatio' => true, // مهم لتناسق الشكل
            'layout' => [
                'padding' => 40, // تقليل الحشو الخارجي
            ],
            'scales' => [
                'r' => [
                    'angleLines' => [
                        'display' => true,
                        'color' => 'rgba(0, 0, 0, 0.1)'
                    ],
                    'suggestedMin' => 0,
                    'suggestedMax' => 100,
                    'ticks' => [
                        'stepSize' => 20,
                        'backdropColor' => 'transparent',
                        'showLabelBackdrop' => false
                    ],
                    'pointLabels' => [
                        'font' => [
                            'family' => 'Tajawal, sans-serif',
                            'size' => 22,
                            'weight' => 'bold'
                        ]
                    ]
                ]
            ],
            'elements' => [
                'line' => [
                    'tension' => 0.1,
                    'borderWidth' => 4
                ],
                'point' => [
                    'radius' => 8,
                    'hoverRadius' => 12,
                    'borderWidth' => 3
                ]
            ],
            'plugins' => [
                'legend' => [
                    'position' => 'top',
                    'rtl' => true,
                    'labels' => [
                        'font' => [
                            'family' => 'Tajawal, sans-serif',
                            'size' => 18
                        ],
                        'usePointStyle' => true
                    ]
                ],
                'tooltip' => [
                    'rtl' => true,
                    'bodyFont' => [
                        'family' => 'Tajawal, sans-serif',
                        'size' => 16
                    ],
                    'titleFont' => [
                        'family' => 'Tajawal, sans-serif',
                        'size' => 20,
                        'weight' => 'bold'
                    ],
                    'callbacks' => [
                        'label' => 'function(context) {
                            return " " + context.dataset.label + ": " + context.raw + "%";
                        }'
                    ]
                ]
            ]
        ];
    }
}
