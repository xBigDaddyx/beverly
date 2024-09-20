<?php

namespace Xbigdaddyx\Beverly\Filament\Widgets;

use App\Models\User;
use Filament\Facades\Filament;
use Flowframe\Trend\Trend;
use Leandrocfe\FilamentApexCharts\Widgets\ApexChartWidget;
use Xbigdaddyx\Beverly\Models\CartonBox;
use Xbigdaddyx\Fuse\Domain\Company\Models\Company;

class CartonBoxSummaryChart extends ApexChartWidget
{
    /**
     * Chart Id
     *
     * @var string
     */
    protected static ?string $chartId = 'cartonBoxSummaryChart';
    protected int | string | array $columnSpan = 6;
    protected static ?int $sort = 1;
    /**
     * Widget Title
     *
     * @var string|null
     */
    protected static ?string $heading = 'Yearly Validated Chart';

    /**
     * Chart options (series, labels, types, size, animations...)
     * https://apexcharts.com/docs/options
     *
     * @return array
     */
    protected function getOptions(): array
    {
        $trend =  Trend::query(
            CartonBox::query()

            ->completed()->where('company_id',Filament::getTenant()->id ?? auth()->user()->company_id)
            )
            ->dateColumn('completed_at')
            ->between(
                start: now()->startOfYear(),
                end: now()->endOfYear(),
            )
            ->perMonth()
            ->count();

        return [
            'chart' => [
                'type' => 'bar',
                'height' => 300,
            ],
            'series' => [
                [
                    'name' => 'Carton Boxes',
                    'data' => $trend->pluck('aggregate'),
                ],
            ],
            'xaxis' => [
                'categories' =>$trend->pluck('date'),
                'labels' => [
                    'style' => [
                        'fontFamily' => 'inherit',
                    ],
                ],
            ],
            'yaxis' => [
                'labels' => [
                    'style' => [
                        'fontFamily' => 'inherit',
                    ],
                ],
            ],
            'colors' => ['#45dfb1'],
            'fill' => [
                'type' => 'gradient',
                'gradient' => [
                    'shade' => 'dark',
                    'type' => 'vertical',
                    'shadeIntensity' => 0.5,
                    'gradientToColors' => ['#99e890'],
                    'inverseColors' => true,
                    'opacityFrom' => 1,
                    'opacityTo' => 1,
                    'stops' => [0, 100],
                ],
            ],
        ];
    }
}
