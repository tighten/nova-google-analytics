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
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;

class AnalyticsQuery
{
    private mixed $headers;
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
        $this->headers = $searchOptions['headers'];
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

    public function getPageData(): Collection
    {
        $data = $this->getQueryResults();

        $dimensionHeaders = $data->getDimensionHeaders();
        $metricHeaders = $data->getMetricHeaders();

        $rows = collect();

        foreach ($data->getRows() as $rowData) {
            $row = collect();

            foreach ($rowData->getDimensionValues() as $key => $dimensionValue) {
                $row->put($dimensionHeaders[$key]->getName(), $dimensionValue->getValue());
            }

            foreach ($rowData->getMetricValues() as $key => $metricValue) {
                $row->put($metricHeaders[$key]->getName(), $metricValue->getValue());
            }

            $rows->push($row);
        }

        return $rows;
    }

    public function totalPages(): int
    {
        $data = $this->getQueryResults();

        return ceil($data->getRowCount() / $this->limit);
    }

    public function hasMore(): bool
    {
        $data = $this->getQueryResults();

        return ($this->offset * $this->limit) < $data->getRowCount();
    }

    private function cacheKey(): string
    {
        return sprintf('pages-%s-%s-%s-%s-%s', $this->searchTerm, $this->sortDirection, $this->sortBy, $this->limit, $this->offset);
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
                'dimensions' => [
                    new Dimension([
                        'name' => 'pageTitle',
                    ]),
                    new Dimension([
                        'name' => 'pagePath',
                    ]),
                    new Dimension([
                        'name' => 'percentScrolled',
                    ]),
                ],
                'metrics' => [
                    new Metric([
                        'name' => 'totalUsers',
                    ]),
                    new Metric([
                        'name' => 'newUsers',
                    ]),
                    new Metric([
                        'name' => 'screenPageViews',
                    ]),
                    new Metric([
                        'name' => 'screenPageViewsPerSession',
                    ]),
                    new Metric([
                        'name' => 'userEngagementDuration',
                    ]),
                    new Metric([
                        'name' => 'eventCount',
                    ]),
                    new Metric([
                        'name' => 'conversions',
                    ]),
                ],
                'orderBys' => [
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
        $orderBy = match ($field) {
            'pageTitle' => [
                'desc' => $direction,
                'dimension' => new DimensionOrderBy([
                    'dimension_name' => 'pageTitle',
                    'order_type' => OrderType::CASE_INSENSITIVE_ALPHANUMERIC,
                ]),
            ],
            'pagePath' => [
                'desc' => $direction,
                'dimension' => new DimensionOrderBy([
                    'dimension_name' => 'pagePath',
                    'order_type' => OrderType::ORDER_TYPE_UNSPECIFIED,
                ]),
            ],
            'percentScrolled' => [
                'desc' => $direction,
                'dimension' => new DimensionOrderBy([
                    'dimension_name' => 'percentScrolled',
                    'order_type' => OrderType::NUMERIC,
                ]),
            ],
            'totalUsers' => [
                'desc' => $direction,
                'metric' => new MetricOrderBy([
                    'metric_name' => 'totalUsers',
                ]),
            ],
            'newUsers' => [
                'desc' => $direction,
                'metric' => new MetricOrderBy([
                    'metric_name' => 'newUsers',
                ]),
            ],
            'screenPageViewsPerSession' => [
                'desc' => $direction,
                'metric' => new MetricOrderBy([
                    'metric_name' => 'screenPageViewsPerSession',
                ]),
            ],
            'userEngagementDuration' => [
                'desc' => $direction,
                'metric' => new MetricOrderBy([
                    'metric_name' => 'userEngagementDuration',
                ]),
            ],
            'eventCount' => [
                'desc' => $direction,
                'metric' => new MetricOrderBy([
                    'metric_name' => 'eventCount',
                ]),
            ],
            'conversions' => [
                'desc' => $direction,
                'metric' => new MetricOrderBy([
                    'metric_name' => 'conversions',
                ]),
            ],
            'itemRevenue' => [
                'desc' => $direction,
                'metric' => new MetricOrderBy([
                    'metric_name' => 'itemRevenue',
                ]),
            ],
            default => [
                'desc' => $direction,
                'metric' => new MetricOrderBy([
                    'metric_name' => 'screenPageViews',
                ]),
            ],
        };

        return new OrderBy($orderBy);
    }
}
