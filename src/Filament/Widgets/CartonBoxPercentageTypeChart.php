<?php

namespace Xbigdaddyx\Beverly\Filament\Widgets;

use App\Models\User;
use Carbon\Carbon;
use Flowframe\Trend\Trend;
use Leandrocfe\FilamentApexCharts\Widgets\ApexChartWidget;
use Xbigdaddyx\Beverly\Models\CartonBox;
use Xbigdaddyx\Fuse\Domain\Company\Models\Company;
use Eliseekn\LaravelMetrics\LaravelMetrics;
use Filament\Facades\Filament;

class CartonBoxPercentageTypeChart extends ApexChartWidget
{
    /**
     * Chart Id
     *
     * @var string
     */
    protected static ?string $chartId = 'cartonBoxPercentageTypeChart';
    protected int | string | array $columnSpan = 3;
    protected static ?int $sort = 2;
    /**
     * Widget Title
     *
     * @var string|null
     */
    protected static ?string $heading = 'Validated by Type Percentage Chart';
    protected function getFormSchema(): array
    {
        return [



            \Filament\Forms\Components\DatePicker::make('date_start')
            ->format('m')
            ->displayFormat('m')
                ->default(Carbon::now()->firstOfMonth()),

            \Filament\Forms\Components\DatePicker::make('date_end')
            ->format('m')
            ->displayFormat('m')
                ->default(Carbon::now())

        ];
    }
    /**
     * Chart options (series, labels, types, size, animations...)
     * https://apexcharts.com/docs/options
     *
     * @return array
     */
    protected function getOptions(): array
    {
        $dateStart = $this->filterFormData['date_start'];
        $dateEnd = $this->filterFormData['date_end'];

        $trend = LaravelMetrics::query(
            CartonBox::query()
                ->completed()->whereBetween('completed_at',[$dateStart,$dateEnd])->where('company_id',Filament::getTenant()->id)
        )
        ->count()
        ->byMonth(12)
        ->dateColumn('completed_at')
        ->labelColumn('type')
        ->trends();
        // $trend =  Trend::query(
        //     CartonBox::query()
        //     ->completed()
        //     )
        //     ->dateColumn('completed_at')
        //     ->between(
        //         start: now()->startOfYear(),
        //         end: now()->endOfYear(),
        //     )
        //     ->perMonth()
        //     ->count();

        return [
            'chart' => [
                'type' => 'donut',
                'height' => 300,
            ],
            'series' => $trend['data'],
            'labels' => $trend['labels'],
            'legend' => [
                'labels' => [
                    'colors' => '#9ca3af',
                    'fontWeight' => 600,
                ],
            ],
        ];
    }
}
