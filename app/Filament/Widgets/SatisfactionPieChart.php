<?php

namespace App\Filament\Widgets;

use App\Models\Survey;
use Filament\Widgets\ChartWidget;

class SatisfactionPieChart extends ChartWidget
{
    protected static ?string $heading = 'توزيع الرضا العام';

    protected function getData(): array
    {
        $data = Survey::selectRaw('overall_satisfaction, COUNT(*) as count')
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

        return [
            'datasets' => [
                [
                    'data' => $data->values()->toArray(),
                    'backgroundColor' => ['#4CAF50', '#8BC34A', '#FFC107', '#F44336'],
                ],
            ],
            'labels' => $data->keys()->toArray(),
        ];
    }

    protected function getType(): string
    {
        return 'pie';
    }
}