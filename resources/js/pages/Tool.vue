<template>
    <Head :title='title' />
    <LoadingView :loading='initialLoading'>
        <Heading class='mb-6'>
            {{ title }}
        </Heading>
        <div class='flex'>
            <div class='relative h-9 w-full md:w-1/3 md:flex-shrink-0 mb-6'>
                <Icon
                    :style="{ top: '4px' }"
                    class='absolute ml-2 text-gray-400'
                    type='search'
                    width='20'
                />
                <RoundInput
                    :value='search'
                    class='appearance-none bg-white dark:bg-gray-800 shadow rounded-full h-8 w-full dark:focus:bg-gray-800'
                    :placeholder="__('Search')"
                    spellcheck='false'
                    type='search'
                    @input='performSearch'
                />
            </div>
        </div>
        <LoadingCard :loading='loading'>
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
                                uri-key='pageTitle'
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
                                uri-key='pagePath'
                                @reset='resetOrderBy'
                                @sort='sortByChange'
                            >
                                {{ __('Path') }}
                            </SortableIcon>
                        </th>

                        <th class='text-center uppercase text-gray-500 text-xxs tracking-wide py-2 px-4'>
                            <SortableIcon
                                :direction='direction'
                                resource-name='Pages'
                                uri-key='percentScrolled'
                                @reset='resetOrderBy'
                                @sort='sortByChange'
                            >
                                {{ __('Scrolled') }}
                            </SortableIcon>
                        </th>

                        <th class='text-center uppercase text-gray-500 text-xxs tracking-wide py-2 px-4'>
                            <SortableIcon
                                :direction='direction'
                                resource-name='Pages'
                                uri-key='totalUsers'
                                @reset='resetOrderBy'
                                @sort='sortByChange'
                            >
                                {{ __('Total Users') }}
                            </SortableIcon>
                        </th>

                        <th class='text-center uppercase text-gray-500 text-xxs tracking-wide py-2 px-4'>
                            <SortableIcon
                                :direction='direction'
                                resource-name='Pages'
                                uri-key='newUsers'
                                @reset='resetOrderBy'
                                @sort='sortByChange'
                            >
                                {{ __('New Users') }}
                            </SortableIcon>
                        </th>

                        <th class='text-center uppercase text-gray-500 text-xxs tracking-wide py-2 px-4'>
                            <SortableIcon
                                :direction='direction'
                                resource-name='Pages'
                                uri-key='screenPageViews'
                                @reset='resetOrderBy'
                                @sort='sortByChange'
                            >
                                {{ __('Total Views') }}
                            </SortableIcon>
                        </th>

                        <th class='text-center uppercase text-gray-500 text-xxs tracking-wide py-2 px-4'>
                            <SortableIcon
                                :direction='direction'
                                resource-name='Pages'
                                uri-key='screenPageViewsPerSession'
                                @reset='resetOrderBy'
                                @sort='sortByChange'
                            >
                                {{ __('Views per Session') }}
                            </SortableIcon>
                        </th>

                        <th class='text-center uppercase text-gray-500 text-xxs tracking-wide py-2 px-4'>
                            <SortableIcon
                                :direction='direction'
                                resource-name='Pages'
                                uri-key='userEngagementDuration'
                                @reset='resetOrderBy'
                                @sort='sortByChange'
                            >
                                {{ __('Engagement Duration') }}
                            </SortableIcon>
                        </th>

                        <th class='text-center uppercase text-gray-500 text-xxs tracking-wide py-2 px-4'>
                            <SortableIcon
                                :direction='direction'
                                resource-name='Pages'
                                uri-key='eventCount'
                                @reset='resetOrderBy'
                                @sort='sortByChange'
                            >
                                {{ __('Event Count') }}
                            </SortableIcon>
                        </th>

                        <th class='text-center uppercase text-gray-500 text-xxs tracking-wide py-2 px-4'>
                            <SortableIcon
                                :direction='direction'
                                resource-name='Pages'
                                uri-key='conversions'
                                @reset='resetOrderBy'
                                @sort='sortByChange'
                            >
                                {{ __('Conversions') }}
                            </SortableIcon>
                        </th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr v-for='row in data'>
                        <td class='px-4 py-2 border-t border-gray-100 dark:border-gray-700 dark:bg-gray-800 group-hover:bg-gray-50 dark:group-hover:bg-gray-900'>
                            {{ row.pageTitle }}
                        </td>
                        <td class='px-4 py-2 border-t border-gray-100 dark:border-gray-700 dark:bg-gray-800 group-hover:bg-gray-50 dark:group-hover:bg-gray-900'>
                            {{ row.pagePath }}
                        </td>
                        <td class='text-center px-4 py-2 border-t border-gray-100 dark:border-gray-700 dark:bg-gray-800 group-hover:bg-gray-50 dark:group-hover:bg-gray-900'>
                            {{ getFormattedPercent(row.percentScrolled) }}
                        </td>
                        <td class='text-center px-4 py-2 border-t border-gray-100 dark:border-gray-700 dark:bg-gray-800 group-hover:bg-gray-50 dark:group-hover:bg-gray-900'>
                            {{ row.totalUsers }}
                        </td>
                        <td class='text-center px-4 py-2 border-t border-gray-100 dark:border-gray-700 dark:bg-gray-800 group-hover:bg-gray-50 dark:group-hover:bg-gray-900'>
                            {{ row.newUsers }}
                        </td>
                        <td class='text-center px-4 py-2 border-t border-gray-100 dark:border-gray-700 dark:bg-gray-800 group-hover:bg-gray-50 dark:group-hover:bg-gray-900'>
                            {{ row.screenPageViews }}
                        </td>
                        <td class='text-center px-4 py-2 border-t border-gray-100 dark:border-gray-700 dark:bg-gray-800 group-hover:bg-gray-50 dark:group-hover:bg-gray-900'>
                            {{ parseFloat(row.screenPageViewsPerSession).toFixed(2) }}
                        </td>
                        <td class='text-center px-4 py-2 border-t border-gray-100 dark:border-gray-700 dark:bg-gray-800 group-hover:bg-gray-50 dark:group-hover:bg-gray-900'>
                            {{ getFormattedTime(row.userEngagementDuration) }}
                        </td>
                        <td class='text-center px-4 py-2 border-t border-gray-100 dark:border-gray-700 dark:bg-gray-800 group-hover:bg-gray-50 dark:group-hover:bg-gray-900'>
                            {{ row.eventCount }}
                        </td>
                        <td class='text-center px-4 py-2 border-t border-gray-100 dark:border-gray-700 dark:bg-gray-800 group-hover:bg-gray-50 dark:group-hover:bg-gray-900'>
                            {{ getFormattedCurrency(row.conversions) }}
                        </td>
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
    props: [
        'property',
    ],
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
            sortBy: 'screenPageViews',
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
                .get(`/nova-vendor/nova-google-analytics/pages?property=${this.property}&limit=${this.limit}&duration=${this.duration}&page=${this.page}&s=${this.search}&sortBy=${this.sortBy}&sortDirection=${this.sortDirection}`)
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
            this.loading = true;
            this.page = 1;
            this.search = event?.target?.value || '';
            this.getData();
        },

        sortByChange(event) {
            this.loading = true;

            let direction = this.sortDirection == 'asc' ? 'desc' : 'asc';
            if (this.sortBy != event.key) {
                direction = 'asc';
            }

            this.sortBy = event.key;
            this.sortDirection = direction;
            this.getData();
        },

        resetOrderBy() {
            this.sortBy = 'ga:pageviews';
            this.sortDirection = 'desc';
            this.getData();
        },

        getFormattedTime(timeString) {
            return dayjs.utc(dayjs.duration({ seconds: timeString }).asMilliseconds()).format('HH:mm:ss');
        },

        getFormattedPercent(percentString) {
            const parsed = parseFloat(percentString);

            if (Number.isNaN(parsed)) {
                return '0.00%';
            }

            return parseFloat(percentString ?? '0.00').toFixed(2) + '%';
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
