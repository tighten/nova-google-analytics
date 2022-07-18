<template>
    <Head :title='title' />
    <LoadingView :loading='initialLoading'>
        <Heading class='mb-6'>
            {{ title }}
        </Heading>
        <div class='flex'>
            <div class='relative h-9 flex-no-shrink mb-6'>
                <Icon
                    :style="{ top: '4px' }"
                    class='absolute ml-2 text-gray-400'
                    type='search'
                    width='20'
                />
                <RoundInput
                    v-model='search'
                    :placeholder="__('Search')"
                    class='appearance-none bg-white dark:bg-gray-800 shadow rounded-full h-8 w-full dark:focus:bg-gray-800'
                    data-testid='search-input'
                    dusk='search'
                    spellcheck='false'
                    type='search'
                    @search='performSearch'
                    @keydown.stop='performSearch'
                />
            </div>
        </div>
        <LoadingCard :loading='loading' class='card relative'>
            <div>
                <div class='flex items-center py-3 border-b border-50'>
                    <div class='flex items-center ml-auto px-3'>
                        <filter-menu
                            :perPage='limit'
                            :perPageOptions='[
                                {label: 10, value: 10},
                                {label: 25, value: 25},
                                {label: 50, value: 50},
                                {label: 100, value: 100}
                            ]'
                            :viaResource='false'
                            @per-page-changed='updateLimit'
                        ></filter-menu>
                    </div>
                </div>
                <table
                    aria-describedby='Page Table'
                    v-if='data.length > 0'
                    cellpadding='0'
                    cellspacing='0'
                    class='table w-full table-default'
                >
                    <thead class='bg-gray-50 dark:bg-gray-800'>
                    <tr>
                        <th class='text-left uppercase text-gray-500 text-xxs tracking-wide py-2 px-4'>
                            <SortableIcon
                                :direction='direction'
                                resource-name='Pages'
                                uri-key='ga:pageTitle'
                                @reset='resetOrderBy'
                                @sort='sortByChange'
                            >
                                {{ __('Name') }}
                            </SortableIcon>
                        </th>

                        <th class='text-left uppercase text-gray-500 text-xxs tracking-wide py-2 px-4'>
                            <SortableIcon
                                :direction='direction'
                                resource-name='Paths'
                                uri-key='ga:pagePath'
                                @reset='resetOrderBy'
                                @sort='sortByChange'
                            >
                                {{ __('Path') }}
                            </SortableIcon>
                        </th>

                        <th class='text-left uppercase text-gray-500 text-xxs tracking-wide py-2 px-4'>
                            <SortableIcon
                                :direction='direction'
                                resource-name='Pages'
                                uri-key='ga:pageviews'
                                @reset='resetOrderBy'
                                @sort='sortByChange'
                            >
                                {{ __('Visits') }}
                            </SortableIcon>
                        </th>

                        <th class='text-left uppercase text-gray-500 text-xxs tracking-wide py-2 px-4'>
                            <SortableIcon
                                :direction='direction'
                                resource-name='Pages'
                                uri-key='ga:uniquePageviews'
                                @reset='resetOrderBy'
                                @sort='sortByChange'
                            >
                                {{ __('Unique Visits') }}
                            </SortableIcon>
                        </th>

                        <th class='text-left uppercase text-gray-500 text-xxs tracking-wide py-2 px-4'>
                            <SortableIcon
                                :direction='direction'
                                resource-name='Pages'
                                uri-key='ga:avgTimeOnPage'
                                @reset='resetOrderBy'
                                @sort='sortByChange'
                            >
                                {{ __('Avg. Time on Page') }}
                            </SortableIcon>
                        </th>

                        <th class='text-left uppercase text-gray-500 text-xxs tracking-wide py-2 px-4'>
                            <SortableIcon
                                :direction='direction'
                                resource-name='Pages'
                                uri-key='ga:entrances'
                                @reset='resetOrderBy'
                                @sort='sortByChange'
                            >
                                {{ __('Entrances') }}
                            </SortableIcon>
                        </th>

                        <th class='text-left uppercase text-gray-500 text-xxs tracking-wide py-2 px-4'>
                            <SortableIcon
                                :direction='direction'
                                resource-name='Pages'
                                uri-key='ga:bounceRate'
                                @reset='resetOrderBy'
                                @sort='sortByChange'
                            >
                                {{ __('Bounce Rate') }}
                            </SortableIcon>
                        </th>

                        <th class='text-left uppercase text-gray-500 text-xxs tracking-wide py-2 px-4'>
                            <SortableIcon
                                :direction='direction'
                                resource-name='Pages'
                                uri-key='ga:exitRate'
                                @reset='resetOrderBy'
                                @sort='sortByChange'
                            >
                                {{ __('Exit Rate') }}
                            </SortableIcon>
                        </th>

                        <th class='text-left uppercase text-gray-500 text-xxs tracking-wide py-2 px-4'>
                            <SortableIcon
                                :direction='direction'
                                resource-name='Pages'
                                uri-key='ga:pageValue'
                                @reset='resetOrderBy'
                                @sort='sortByChange'
                            >
                                {{ __('Page Value') }}
                            </SortableIcon>
                        </th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr v-for='row in data'>
                        <td>{{ row.name }}</td>
                        <td>{{ row.path }}</td>
                        <td>{{ row.visits }}</td>
                        <td>{{ row.unique_visits }}</td>
                        <td>{{ getFormattedTime(row.avg_page_time) }}</td>
                        <td>{{ row.entrances }}</td>
                        <td>{{ getFormattedPercent(row.bounce_rate) }}</td>
                        <td>{{ getFormattedPercent(row.exit_rate) }}</td>
                        <td>{{ getFormattedCurrency(row.page_value) }}</td>
                    </tr>
                    </tbody>
                </table>
            </div>

            <pagination-links
                :current-page='page'
                :data='data'
                :hasMore='hasMore'
                :hasPrevious='hasPrevious'
                :total-pages='totalPages'
                @next='nextPage'
                @previous='previousPage'
            ></pagination-links>
        </LoadingCard>
    </LoadingView>
</template>

<script>
import PaginationLinks from '../components/PaginationLinks.vue';
import dayjs from 'dayjs';
import duration from 'dayjs/plugin/duration';
import utc from 'dayjs/plugin/utc';
import FilterMenu from '../components/FilterMenu';

dayjs.extend(duration);
dayjs.extend(utc);

export default {
    components: {
        'pagination-links': PaginationLinks,
        'dayjs': dayjs,
        'filter-menu': FilterMenu,
    },
    data: function() {
        return {
            title: 'Google Analytics',
            data: [],
            duration: 'week',
            initialLoading: true,
            loading: true,
            hasMore: true,
            page: 1,
            totalPages: 1,
            search: '',
            sortBy: 'ga:pageviews',
            sortDirection: 'desc',
            limit: 10,
        };
    },
    metaInfo() {
        return {
            title: this.title,
        };
    },
    methods: {
        updateDuration(event) {
            this.duration = event.target.value;
            this.getData();
        },

        updateLimit(value) {
            this.limit = value;
            this.getData();
        },

        getData() {
            Nova.request()
                .get(`/nova-vendor/nova-google-analytics/pages?limit=${this.limit}&duration=${this.duration}&page=${this.page}&s=${this.search}&sortBy=${this.sortBy}&sortDirection=${this.sortDirection}`)
                .then(response => {
                    this.data = response.data.pageData;
                    this.totalPages = response.data.totalPages;
                    this.hasMore = response.data.hasMore;
                    this.loading = false;
                });
        },

        nextPage() {
            this.loading = true;
            this.page++;
            this.getData();
        },

        previousPage() {
            this.loading = true;
            if (this.hasPrevious) {
                this.page--;
            }
            this.getData();
        },

        performSearch(event) {
            if (event.which != 9) {
                this.page = 1;
                this.getData();
            }
        },

        sortByChange(event) {
            let direction = this.sortDirection == 'asc' ? 'desc' : 'asc';

            if (this.sortBy != event.key) {
                direction = 'asc';
            }

            this.sortBy = event.key;
            this.sortDirection = direction;
            this.getData();
        },

        resetOrderBy(event) {
            this.sortBy = 'ga:pageviews';
            this.sortDirection = 'desc';
            this.getData();
        },

        getFormattedTime(timeString) {
            return dayjs.utc(dayjs.duration({ seconds: timeString }).asMilliseconds()).format('HH:mm:ss');
        },

        getFormattedPercent(percentString) {
            return parseFloat(percentString).toFixed(2) + '%';
        },

        getFormattedCurrency(percentString) {
            return new Intl.NumberFormat('en-US', { style: 'currency', currency: 'USD' }).format(percentString);
        },
    },
    computed: {
        hasPrevious() {
            return this.page > 1;
        },
    },
    mounted() {
        this.getData();
        this.initialLoading = false;
    },
};
</script>
