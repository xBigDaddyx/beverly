<?php

namespace Xbigdaddyx\Beverly\Filament\Widgets;

use App\Models\User;
use Carbon\Carbon;
use Eliseekn\LaravelMetrics\Enums\Aggregate;
use Eliseekn\LaravelMetrics\LaravelMetrics;
use Filament\Facades\Filament;
use Flowframe\Trend\Trend;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use Leandrocfe\FilamentApexCharts\Widgets\ApexChartWidget;
use Xbigdaddyx\Beverly\Models\Buyer;
use Xbigdaddyx\Beverly\Models\CartonBox;
use Xbigdaddyx\Fuse\Domain\Company\Models\Company;

class CartonBoxPoChart extends ApexChartWidget
{
    /**
     * Chart Id
     *
     * @var string
     */
    protected static ?string $chartId = 'CartonBoxPOChart';
    protected int | string | array $columnSpan = 12;
    protected static ?int $sort = 3;
    /**
     * Widget Title
     *
     * @var string|null
     */
    protected static ?string $heading = 'Validated by PO Chart';
    protected function getFormSchema(): array
    {
        return [

            \Filament\Forms\Components\Select::make('buyer_id')
            ->label('Buyer')
            ->options(function (){
                $buyers = Buyer::where('company_id',Filament::getTenant()->id)->get();
                return $buyers->pluck('name','id');
            }),

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
        $buyer = $this->filterFormData['buyer_id'];
        $dateStart = $this->filterFormData['date_start'];
        $dateEnd = $this->filterFormData['date_end'];
        if($buyer){
            $cartonValidated = DB::table('carton_boxes')
            ->join('packing_lists','packing_lists.id','carton_boxes.packing_list_id')
            ->select(DB::raw('count(completed_at) as count, packing_lists.po'))
            ->where('is_completed','!=',null)
            ->where('buyer_id',$buyer)
            ->where('carton_boxes.company_id',Filament::getTenant()->id)
            ->whereBetween('completed_at',[$dateStart,$dateEnd])
            ->groupBy('packing_lists.po')
            ->get();
        }else{
            $cartonValidated = DB::table('carton_boxes')
            ->join('packing_lists','packing_lists.id','carton_boxes.packing_list_id')
            ->select(DB::raw('count(completed_at) as count, packing_lists.po'))
            ->where('is_completed','!=',null)
            ->where('carton_boxes.company_id',Filament::getTenant()->id)
            ->whereBetween('completed_at',[$dateStart,$dateEnd])
            ->groupBy('packing_lists.po')
            ->get();
        }


            return [
                'chart' => [
                    'type' => 'bar',
                    'height' => 300,
                ],
                'series' => [
                    [
                        'name' => 'Carton Boxes',
                        'data' => $cartonValidated->pluck('count'),
                    ],
                ],
                'stroke' => [
                    'width' => [0, 4],
                ],
                'xaxis' => [
                    'categories' =>$cartonValidated->pluck('po'),
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
