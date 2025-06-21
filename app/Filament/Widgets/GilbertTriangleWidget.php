<?php

    namespace App\Filament\Widgets;

    use Filament\Widgets\ChartWidget;
    use App\Models\ResourceTracking;
    use App\Models\ActualResult;
    use App\Models\UnitGoal;
    use Carbon\Carbon;
    use Illuminate\Support\Facades\Log; // تأكد من استيراد Log facade

    class GilbertTriangleWidget extends ChartWidget
    {
        protected static ?string $heading = 'مثلث جلبرت - الأداء والكفاءة والفاعلية';
        protected static ?string $maxHeight = '480px';
        protected static ?int $sort = 1;
        protected static ?string $pollingInterval = null;

        protected function getType(): string
        {
            return 'radar';
        }

        protected function getData(): array
        {
            $today = Carbon::today()->toDateString();
            $yesterday = Carbon::yesterday()->toDateString();

            // حساب مقاييس جيلبرت لليوم
            $todayMetrics = $this->calculateGilbertMetrics($today);
            // حساب مقاييس جيلبرت للأمس
            $yesterdayMetrics = $this->calculateGilbertMetrics($yesterday);

            // ---DEBUGGING---
            Log::info("Gilbert Widget Data for Today ({$today}): " . json_encode($todayMetrics));
            Log::info("Gilbert Widget Data for Yesterday ({$yesterday}): " . json_encode($yesterdayMetrics));
            // إذا أردت إيقاف التنفيذ ورؤية المتغيرات مباشرةً في المتصفح
            // dd($todayMetrics, $yesterdayMetrics);
            // ---END DEBUGGING---

            return [
                'datasets' => [
                    [
                        'label' => 'اليوم',
                        'data' => [
                            $todayMetrics['performance'],
                            $todayMetrics['effectiveness'],
                            $todayMetrics['efficiency'],
                        ],
                        'backgroundColor' => 'rgba(59, 130, 246, 0.18)',
                        'borderColor' => 'rgba(59, 130, 246, 1)',
                        'pointBackgroundColor' => 'rgba(59, 130, 246, 1)',
                        'pointBorderColor' => '#fff',
                        'pointHoverBackgroundColor' => '#fff',
                        'pointHoverBorderColor' => 'rgba(59, 130, 246, 1)',
                    ],
                    [
                        'label' => 'الأمس',
                        'data' => [
                            $yesterdayMetrics['performance'],
                            $yesterdayMetrics['effectiveness'],
                            $yesterdayMetrics['efficiency'],
                        ],
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

        private function calculateGilbertMetrics(string $date): array
        {
            $goal = UnitGoal::where('date', $date)->first();
            $actualResult = ActualResult::where('date', $date)->first();
            $resourceTracking = ResourceTracking::where('date', $date)->first();

            $completedTasks = $actualResult?->completed_tasks ?? 0;
            $targetGoal = $goal?->target_tasks ?? 0; // أو حقل آخر يمثل الهدف الكلي
            $totalWorkingHours = $resourceTracking?->working_hours ?? 0;
            $qualityRating = $actualResult?->quality_rating ?? 0;

            // --- حساب الأداء (Performance - للنتائج) ---
            $performance = $qualityRating;

            // --- حساب الفعالية (Effectiveness: النتائج / الأهداف) ---
            $effectiveness = ($targetGoal > 0) ? min(100, round(($completedTasks / $targetGoal) * 100, 2)) : 0;

            // --- حساب الكفاءة (Efficiency: النتائج / الموارد) ---
            $efficiency = ($totalWorkingHours > 0) ? min(100, round(($completedTasks / $totalWorkingHours) * 20, 2)) : 0;

            // --- حساب الملاءمة (Relevance: الموارد / الأهداف) ---
            $relevance = 0;
            if ($targetGoal > 0 && $totalWorkingHours > 0) {
                 $relevance = round(($totalWorkingHours / $targetGoal) * 10, 2);
                 $relevance = min(100, $relevance);
            } else if ($targetGoal == 0 && $totalWorkingHours == 0) {
                $relevance = 100;
            } else if ($targetGoal == 0 && $totalWorkingHours > 0) {
                $relevance = 0; // موارد بلا أهداف
            } else if ($targetGoal > 0 && $totalWorkingHours == 0) {
                $relevance = 0; // أهداف بلا موارد
            }

            // ---DEBUGGING---
            Log::info("Calculated metrics for date {$date}: Performance={$performance}%, Effectiveness={$effectiveness}%, Efficiency={$efficiency}%, Relevance={$relevance}%");
            // ---END DEBUGGING---

            return [
                'performance' => $performance,
                'effectiveness' => $effectiveness,
                'efficiency' => $efficiency,
                'relevance' => $relevance,
            ];
        }

        protected function getOptions(): array
        {
            return [
                'responsive' => true,
                'maintainAspectRatio' => true,
                'layout' => [
                    'padding' => 40,
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
                            'showLabelBackdrop' => false,
                            'callback' => 'function(value) { return value + "%"; }'
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
    