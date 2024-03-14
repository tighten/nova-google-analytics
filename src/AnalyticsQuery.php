<?php

namespace Tightenco\NovaGoogleAnalytics;

use Google\Analytics\Data\V1beta\Filter;
use Google\Analytics\Data\V1beta\Filter\StringFilter;
use Google\Analytics\Data\V1beta\Filter\StringFilter\MatchType;
use Google\Analytics\Data\V1beta\FilterExpression;
use Google\Analytics\Data\V1beta\FilterExpressionList;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Cache;
use Spatie\Analytics\Facades\Analytics;
use Spatie\Analytics\OrderBy;
use Spatie\Analytics\Period;

class AnalyticsQuery
{
    private int $limit;
    private int $offset;
    private mixed $searchTerm;
    private string $sortDirection;
    private string $sortBy;
    private string $duration;
    private mixed $queryResults;
    private array $queryMetrics = [
        'totalUsers', 'screenPageViews', 'bounceRate'
    ];

    private array $queryDimensions = [
        'pageTitle', 'pagePath'
    ];

    private array $headers = [
        'name', 'path', 'unique_visits', 'visits', 'bounce_rate'
    ];

    public function __construct($limit, $offset, $searchTerm, $sortDirection, $sortBy, $duration)
    {
        $this->limit = $limit;
        $this->offset = $offset;
        $this->searchTerm = $searchTerm;
        $this->sortDirection = $sortDirection;
        $this->sortBy = $sortBy;
        $this->duration = $duration;
        $this->setQueryResults($this->getAnalyticsData());
    }

    public function getDuration(): Period
    {
        return $this->getPeriodForDuration($this->duration);
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
        $headers = collect($this->headers);

        return $this->getQueryResults()->slice($this->offset, $this->limit)
            ->map(fn ($data) => $headers->combine($data))
            ->toArray();
    }

    public function totalPages(): int
    {
        $data = $this->getQueryResults();

        return ceil($data->count()/$this->limit);
    }

    public function hasMore(): bool
    {
        $data = $this->getQueryResults();

        return ($this->offset+$this->limit) < $data->count();
    }

    private function cacheKey(): string
    {
        return sprintf('pages-%s-%s-%s', $this->searchTerm, $this->sortDirection, $this->sortBy);
    }

    private function getAnalyticsData(): object
    {
        return Cache::remember($this->cacheKey(), now()->addMinutes(30), function() {
            $dimensionFilter = new FilterExpression([
                'or_group' => new FilterExpressionList([
                    'expressions' => [
                        new FilterExpression([
                            'filter' => new Filter([
                                'field_name' => 'pageTitle',
                                'string_filter' => new StringFilter([
                                    'match_type' => MatchType::CONTAINS,
                                    'value' => $this->searchTerm,
                                ]),
                            ]),
                        ]),
                        new FilterExpression([
                            'filter' => new Filter([
                                'field_name' => 'pagePath',
                                'string_filter' => new StringFilter([
                                    'match_type' => MatchType::CONTAINS,
                                    'value' => $this->searchTerm,
                                ]),
                            ]),
                        ]),
                    ],
                ]),
            ]);

            return Analytics::get(
                $this->getDuration(),
                $this->queryMetrics,
                $this->queryDimensions,
                100,
                [OrderBy::metric($this->sortBy, $this->sortDirection)],
                0,
                $dimensionFilter
            );
        });
    }

    private function getPeriodForDuration($duration): Period
    {
        $map = [
            'week' => Period::days(7),
            'month' => Period::months(1),
            'year' => Period::years(1),
        ];

        return Arr::get($map, $duration, Period::days(7));
    }
}
