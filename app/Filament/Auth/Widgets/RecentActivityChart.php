<?php

namespace App\Filament\Auth\Widgets;

use App\Models\RecentActivity;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\Auth;

class RecentActivityChart extends ChartWidget
{
    protected static ?int $sort = 2;

    protected ?string $pollingInterval = '15s';

    protected int|string|array $columnSpan = [
        'default' => 'full',
        'md' => 1,
    ];

    public function getHeading(): string
    {
        return __('general.activity_trends') ?? 'Activity Trends';
    }

    protected function getData(): array
    {
        $data = RecentActivity::query()
            ->where('user_id', Auth::id())
            ->where('activity_date', '>=', now()->subDays(6)->startOfDay())
            ->selectRaw('DATE(activity_date) as date, count(*) as count')
            ->groupBy('date')
            ->pluck('count', 'date')
            ->toArray();

        $results = [];
        $labels = [];

        // Last 7 days including today
        for ($i = 6; $i >= 0; $i--) {
            $date = now()->subDays($i);
            $dateString = $date->format('Y-m-d');

            $results[] = $data[$dateString] ?? 0;
            $labels[] = $date->translatedFormat('D'); // Mon, Tue... localized
        }

        return [
            'datasets' => [
                [
                    'label' => __('general.activities') ?? 'Activities',
                    'data' => $results,
                    'borderColor' => '#2563eb', // Primary Blue
                    'backgroundColor' => 'rgba(37, 99, 235, 0.1)', // Gradient effect (solid with opacity)
                    'fill' => true,
                    'tension' => 0.4,
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }

    protected function getOptions(): array
    {
        return [
            'scales' => [
                'x' => [
                    'grid' => [
                        'display' => false,
                    ],
                ],
                'y' => [
                    'grid' => [
                        'color' => '#374151', // gray-700 for dark mode
                    ],
                    'ticks' => [
                        'stepSize' => 1,
                    ],
                ],
            ],
            'plugins' => [
                'legend' => [
                    'display' => false,
                ],
            ],
        ];
    }
}
