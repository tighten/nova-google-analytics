<?php

namespace Tightenco\NovaGoogleAnalytics;

use Carbon\Carbon;
use Illuminate\Support\Collection;
use Spatie\Analytics\Analytics;
use Spatie\Analytics\Period;

trait MetricDiffTrait
{
    private function performQuery(string $metric, string $dimensions, Period $period): Collection
    {
        $analyticsData = app(Analytics::class)
            ->performQuery(
                $period,
                $metric,
                [
                    'metrics' => $metric,
                    'dimensions' => $dimensions,
                    'samplingLevel' => 'HIGHER_PRECISION',
                ]
            );

        $results = collect($analyticsData->getRows());

        return collect($results ?? [])->map(function (array $dateRow) {
            return [
                'date' => $dateRow[0],
                'value' => $dateRow[1],
            ];
        });
    }

    private function getLastWeek(): array
    {
        $current = collect(
            [
                'startDate' => Carbon::today()
                    ->clone()
                    ->startOfWeek(Carbon::SUNDAY)
                    ->subWeek(),

                'endDate' => Carbon::today()
                    ->clone()
                    ->subWeek()
                    ->endOfWeek(Carbon::SATURDAY),
            ],
        );

        $previous = Period::create(
            Carbon::today()
                ->clone()
                ->startOfWeek(Carbon::SUNDAY)
                ->subWeeks(2),
            Carbon::today()
                ->clone()
                ->subWeeks(2)
                ->endOfWeek(Carbon::SATURDAY)
        );

        return [
            'current' => $current,
            'previous' => $previous,
        ];
    }

    private function getLastMonth(): array
    {
        $current = Period::create(
            Carbon::today()
                ->clone()
                ->startOfMonth()
                ->subMonth(),
            Carbon::today()
                ->clone()
                ->startOfMonth()
                ->subMonth()
                ->endOfMonth()
        );

        $previous = Period::create(
            Carbon::today()
                ->clone()
                ->startOfMonth()
                ->subMonths(2),
            Carbon::today()
                ->clone()
                ->startOfMonth()
                ->subMonths(2)
                ->endOfMonth()
        );

        return [
            'current' => $current,
            'previous' => $previous,
        ];
    }

    private function getLastSevenDays(): array
    {
        $current = Period::create(
            Carbon::yesterday()
                ->clone()
                ->subDays(6),
            Carbon::yesterday()
        );

        $previous = Period::create(
            Carbon::yesterday()
                ->clone()
                ->subDays(13),
            Carbon::yesterday()
                ->clone()
                ->subDays(7)
        );

        return [
            'current' => $current,
            'previous' => $previous,
        ];
    }

    private function getLastThirtyDays(): array
    {
        $current = Period::create(
            Carbon::yesterday()
                ->clone()
                ->subDays(29),
            Carbon::yesterday()
        );

        $previous = Period::create(
            Carbon::yesterday()
                ->clone()
                ->subDays(59),
            Carbon::yesterday()
                ->clone()
                ->subDays(30)
        );

        return [
            'current' => $current,
            'previous' => $previous,
        ];
    }
}
