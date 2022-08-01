<?php

namespace Tightenco\NovaGoogleAnalytics;

use Carbon\Carbon;
use Google\Analytics\Data\V1beta\DateRange;
use Google\Analytics\Data\V1beta\Dimension;
use Google\Analytics\Data\V1beta\Filter;
use Google\Analytics\Data\V1beta\Filter\StringFilter;
use Google\Analytics\Data\V1beta\Filter\StringFilter\MatchType;
use Google\Analytics\Data\V1beta\FilterExpression;
use Google\Analytics\Data\V1beta\FilterExpressionList;
use Google\Analytics\Data\V1beta\Metric;
use Google\Analytics\Data\V1beta\OrderBy;
use Google\Analytics\Data\V1beta\OrderBy\DimensionOrderBy;
use Google\Analytics\Data\V1beta\OrderBy\DimensionOrderBy\OrderType;
use Google\Analytics\Data\V1beta\OrderBy\MetricOrderBy;
use Google\Analytics\Data\V1beta\RunReportResponse;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Cache;
use Spatie\Analytics\Analytics;
use Spatie\Analytics\Period;

class AnalyticsQuery
{
    private array $dimensions;
    private array $metrics;
    private int $limit;
    private int $offset;
    private mixed $searchTerm;
    private string $sortDirection;
    private string $sortBy;
    private string $duration;
    private mixed $queryResults;
    private string $property;

    public function __construct(array $searchOptions)
    {
        $this->dimensions = $searchOptions['dimensions'];
        $this->metrics = $searchOptions['metrics'];
        $this->limit = $searchOptions['limit'];
        $this->offset = $searchOptions['offset'];
        $this->searchTerm = $searchOptions['searchTerm'];
        $this->sortDirection = $searchOptions['sortDirection'];
        $this->sortBy = $searchOptions['sortBy'];
        $this->duration = $searchOptions['duration'];
        $this->property = $searchOptions['property'];
        $this->setQueryResults($this->getAnalyticsData());
    }

    public function getDuration(): DateRange
    {
        return $this->getDateRangeForDuration($this->duration);
    }

    public function setDuration(string $duration): void
    {
        $this->duration = $duration;
    }

    public function getQueryResults(): object
    {
        return $this->queryResults;
    }

    public function setQueryResults($results): void
    {
        $this->queryResults = $results;
    }

    public function getPageData(): array
    {
        $data = $this->getQueryResults();
        dd($data->getDimensionHeaders());
        foreach ($data->getRows() as $row) {
            dd($row);
        }
        return array_map(
            function ($row) {
                return array_combine($this->headers, $row);
            },
            array_slice($data->getRows(), $this->offset, $this->limit) ?? []);
    }

    public function totalPages(): int
    {
        $data = $this->getQueryResults();

        return ceil(count($data->getRows()) / $this->limit);
    }

    public function hasMore(): bool
    {
        $data = $this->getQueryResults();

        return ($this->offset + $this->limit) < count($data->getRows());
    }

    private function cacheKey(): string
    {
        return sprintf('pages-%s-%s-%s', $this->searchTerm, $this->sortDirection, $this->sortBy);
    }

    private function getAnalyticsData(): RunReportResponse
    {
        $client = app(GoogleAnalytics::class)->getClientForProperty($this->property);

        return Cache::remember($this->cacheKey(), now()->addMinutes(30), function () use ($client) {
            return $client->runReport([
                'property' => 'properties/' . $this->property,
                'dateRanges' => [
                    $this->getDuration(),
                ],
                'dimensions' => $this->getDimensions(),
                'metrics' => $this->getMetrics(),
                'orderBy' => [
                    $this->getOrderBy($this->sortDirection === 'desc', $this->sortBy),
                ],
                'dimensionFilter' => $this->searchTerm
                    ? new FilterExpression([
                        'or_group' => new FilterExpressionList([
                            'expressions' => [
                                new FilterExpression([
                                    'filter' => new Filter([
                                        'field_name' => 'pageTitle',
                                        'string_filter' => new StringFilter([
                                            'match_type' => MatchType::CONTAINS,
                                            'value' => $this->searchTerm,
                                            'case_sensitive' => false,
                                        ]),
                                    ]),
                                ]),
                                new FilterExpression([
                                    'filter' => new Filter([
                                        'field_name' => 'pagePath',
                                        'string_filter' => new StringFilter([
                                            'match_type' => MatchType::CONTAINS,
                                            'value' => $this->searchTerm,
                                            'case_sensitive' => false,
                                        ]),
                                    ]),
                                ]),
                            ],
                        ]),
                    ])
                    : null,
                'offset' => strval($this->offset),
                'limit' => strval($this->limit),
            ]);
        });
    }

    private function getDateRangeForDuration($duration): DateRange
    {
        $map = [
            'week' => new DateRange([
                'start_date' => Carbon::today()->subDays(7)->format('Y-m-d'),
                'end_date' => Carbon::today()->format('Y-m-d'),
            ]),
            'month' => new DateRange([
                'start_date' => Carbon::today()->subMonth()->format('Y-m-d'),
                'end_date' => Carbon::today()->format('Y-m-d'),
            ]),
            'year' => new DateRange([
                'start_date' => Carbon::today()->subYear()->format('Y-m-d'),
                'end_date' => Carbon::today()->format('Y-m-d'),
            ]),
        ];

        return Arr::get($map, $duration, new DateRange([
            'start_date' => Carbon::today()->subDays(7)->format('Y-m-d'),
            'end_date' => Carbon::today()->format('Y-m-d'),
        ]));
    }

    private function getOrderBy(bool $direction, string $field): OrderBy
    {
        // @TODO - Make this more dynamic by providing field & order type.
        $orderBy = match ($field) {
            'pagePath' => [
                'desc' => $direction,
                'dimension' => new DimensionOrderBy([
                    'dimensionName' => 'pagePath',
                    'orderType' => OrderType::ORDER_TYPE_UNSPECIFIED,
                ]),
            ],
            'percentScrolled' => [
                'desc' => $direction,
                'dimension' => new DimensionOrderBy([
                    'dimensionName' => 'percentScrolled',
                    'orderType' => OrderType::NUMERIC,
                ]),
            ],
            'screenPageViews' => [
                'desc' => $direction,
                'metric' => new MetricOrderBy([
                    'metricName' => 'screenPageViews',
                ]),
            ],
            'totalUsers' => [
                'desc' => $direction,
                'metric' => new MetricOrderBy([
                    'metricName' => 'totalUsers',
                ]),
            ],
            'newUsers' => [
                'desc' => $direction,
                'metric' => new MetricOrderBy([
                    'metricName' => 'newUsers',
                ]),
            ],
            'screenPageViewsPerSession' => [
                'desc' => $direction,
                'metric' => new MetricOrderBy([
                    'metricName' => 'screenPageViewsPerSession',
                ]),
            ],
            'userEngagementDuration' => [
                'desc' => $direction,
                'metric' => new MetricOrderBy([
                    'metricName' => 'userEngagementDuration',
                ]),
            ],
            'eventCount' => [
                'desc' => $direction,
                'metric' => new MetricOrderBy([
                    'metricName' => 'eventCount',
                ]),
            ],
            'conversions' => [
                'desc' => $direction,
                'metric' => new MetricOrderBy([
                    'metricName' => 'conversions',
                ]),
            ],
            'itemRevenue' => [
                'desc' => $direction,
                'metric' => new MetricOrderBy([
                    'metricName' => 'itemRevenue',
                ]),
            ],
            default => [
                'desc' => $direction,
                'dimension' => new DimensionOrderBy([
                    'dimension_name' => 'pageTitle',
                    'order_type' => OrderType::CASE_INSENSITIVE_ALPHANUMERIC,
                ]),
            ],
        };

        return new OrderBy($orderBy);
    }

    private function getDimensions(): array
    {
        $dimensionsRequest = [];

        foreach ($this->dimensions as $dimension) {
            array_push($dimensionsRequest, [
                new Dimension([
                    'name' => $dimension,
                ]),
            ]);
        }

        return $dimensionsRequest;
    }

    private function getMetrics(): array
    {
        $metricsRequest = [];

        foreach ($this->metrics as $metric) {
            array_push($metricsRequest, [
                new Metric([
                    'name' => $metric,
                ]),
            ]);
        }

        return $metricsRequest;
    }
}
