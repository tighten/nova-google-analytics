<?php

namespace Tightenco\NovaGoogleAnalytics;

use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Cache;
use Spatie\Analytics\Analytics;
use Spatie\Analytics\Period;

class AnalyticsQuery
{
    private $headers;
    private $limit;
    private $offset;
    private $searchTerm;
    private $sortDirection;
    private $sortBy;
    private $duration;
    private $queryResults;

    public function __construct($headers, $limit, $offset, $searchTerm, $sortDirection, $sortBy, $duration)
    {
        $this->headers = $headers;
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
        $data = $this->getQueryResults();

        return array_map(
            function ($row) {
                return array_combine($this->headers, $row);
            },
            array_slice($data->rows, $this->offset, $this->limit) ?? []);
    }

    public function totalPages(): int
    {
        $data = $this->getQueryResults();

        return ceil(count($data->rows)/$this->limit);
    }

    public function hasMore(): bool
    {
        $data = $this->getQueryResults();

        return ($this->offset+$this->limit) < count($data->rows);
    }

    private function cacheKey(): string
    {
        return sprintf('pages-%s-%s-%s', $this->searchTerm, $this->sortDirection, $this->sortBy);
    }

    private function getAnalyticsData(): object
    {
        return Cache::remember($this->cacheKey(), now()->addMinutes(30), function() {
            return app(Analytics::class)->performQuery(
                $this->getDuration(),
                'ga:users',
                [
                    'metrics' => 'ga:pageviews,ga:uniquePageviews,ga:avgTimeOnPage,ga:entrances,ga:bounceRate,ga:exitRate,ga:pageValue',
                    'dimensions' => 'ga:pageTitle,ga:pagePath',
                    'sort' => ($this->sortDirection . $this->sortBy),
                    'filters' => $this->searchTerm ? sprintf('ga:pageTitle=@%s,ga:pagePath=@%s', strval($this->searchTerm), strval($this->searchTerm)) : null,
                ]
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
