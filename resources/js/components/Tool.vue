<template>
  <loading-view :loading="initialLoading">
    <heading class="mb-6">{{ title }}</heading>
    <div class="flex">
      <div class="relative h-9 flex-no-shrink mb-6">
        <icon type="search" class="absolute search-icon-center ml-3 text-70" />

        <input
            data-testid="search-input"
            dusk="search"
            class="appearance-none form-search w-search pl-search shadow"
            :placeholder="__('Search')"
            type="search"
            v-model="search"
            @keydown.stop="performSearch"
            @search="performSearch"
            spellcheck="false"
        />
      </div>
    </div>
    <loading-card :loading="loading" class="card relative">
      <div>
        <div class="flex items-center py-3 border-b border-50">
          <div class="flex items-center ml-auto px-3">
            <filter-menu :viaResource="false"
                         :perPage="limit"
                         :perPageOptions="[10, 25, 50, 100]"
                         @per-page-changed="updateLimit"
            ></filter-menu>
          </div>
        </div>
        <table v-if="pages.length > 0"
               class="table w-full table-default"
               cellpadding="0"
               cellspacing="0">
          <thead>
          <tr>
            <th class="text-left">
              <sortable-icon
                  @sort="sortByChange"
                  @reset="resetOrderBy"
                  resource-name="Pages"
                  uri-key="ga:pageTitle"
                  :direction="direction"
              >
              <span class="inline-flex items-center">
                {{ __('Name') }}
              </span>
              </sortable-icon>
            </th>

            <th class="text-left">
              <sortable-icon
                  @sort="sortByChange"
                  @reset="resetOrderBy"
                  resource-name="Paths"
                  uri-key="ga:pagePath"
                  :direction="direction"
              >
              <span class="inline-flex items-center">
                {{ __('Path') }}
              </span>
              </sortable-icon>
            </th>
            <th class="text-left">
              <sortable-icon
                  @sort="sortByChange"
                  @reset="resetOrderBy"
                  resource-name="Pages"
                  uri-key="ga:pageviews"
                  :direction="direction"
              >
              <span class="inline-flex items-center">
                {{ __('Visits') }}
              </span>
              </sortable-icon>
            </th>
            <th class="text-left">
              <sortable-icon
                  @sort="sortByChange"
                  @reset="resetOrderBy"
                  resource-name="Pages"
                  uri-key="ga:uniquePageviews"
                  :direction="direction"
              >
              <span class="inline-flex items-center">
                {{ __('Unique Visits') }}
              </span>
              </sortable-icon>
            </th>
            <th class="text-left">
              <sortable-icon
                  @sort="sortByChange"
                  @reset="resetOrderBy"
                  resource-name="Pages"
                  uri-key="ga:avgTimeOnPage"
                  :direction="direction"
              >
              <span class="inline-flex items-center">
                {{ __('Avg. Time on Page') }}
              </span>
              </sortable-icon>
            </th>
            <th class="text-left">
              <sortable-icon
                  @sort="sortByChange"
                  @reset="resetOrderBy"
                  resource-name="Pages"
                  uri-key="ga:entrances"
                  :direction="direction"
              >
              <span class="inline-flex items-center">
                {{ __('Entrances') }}
              </span>
              </sortable-icon>
            </th>
            <th class="text-left">
              <sortable-icon
                  @sort="sortByChange"
                  @reset="resetOrderBy"
                  resource-name="Pages"
                  uri-key="ga:bounceRate"
                  :direction="direction"
              >
              <span class="inline-flex items-center">
                {{ __('Bounce Rate') }}
              </span>
              </sortable-icon>
            </th>
            <th class="text-left">
              <sortable-icon
                  @sort="sortByChange"
                  @reset="resetOrderBy"
                  resource-name="Pages"
                  uri-key="ga:exitRate"
                  :direction="direction"
              >
              <span class="inline-flex items-center">
                {{ __('Exit Rate') }}
              </span>
              </sortable-icon>
            </th>
            <th class="text-left">
              <sortable-icon
                  @sort="sortByChange"
                  @reset="resetOrderBy"
                  resource-name="Pages"
                  uri-key="ga:pageValue"
                  :direction="direction"
              >
              <span class="inline-flex items-center">
                {{ __('Page Value') }}
              </span>
              </sortable-icon>
            </th>
          </tr>
          </thead>
          <tbody>
          <tr v-for="page in pages">
            <td>{{ page.name }}</td>
            <td>{{ page.path }}</td>
            <td>{{ page.visits }}</td>
            <td>{{ page.unique_visits }}</td>
            <td>{{ getFormattedTime(page.avg_page_time) }}</td>
            <td>{{ page.entrances }}</td>
            <td>{{ getFormattedPercent(page.bounce_rate) }}</td>
            <td>{{ getFormattedPercent(page.exit_rate) }}</td>
            <td>{{ getFormattedCurrency(page.page_value) }}</td>
            <td class="td-fit text-right pr-6 align-middle">
              <div class="inline-flex items-center">
                <span class="inline-flex">
                  <a :href="'/nova/nova-google-analytics/page?url='+encodeURI(page.path)" class="cursor-pointer text-70 hover:text-primary mr-3 inline-flex items-center has-tooltip" data-testid="users-items-0-view-button" dusk="1-view-button" data-original-title="null">
                    <svg xmlns="http://www.w3.org/2000/svg" width="22" height="18" viewBox="0 0 22 16" aria-labelledby="view" role="presentation" class="fill-current"><path d="M16.56 13.66a8 8 0 0 1-11.32 0L.3 8.7a1 1 0 0 1 0-1.42l4.95-4.95a8 8 0 0 1 11.32 0l4.95 4.95a1 1 0 0 1 0 1.42l-4.95 4.95-.01.01zm-9.9-1.42a6 6 0 0 0 8.48 0L19.38 8l-4.24-4.24a6 6 0 0 0-8.48 0L2.4 8l4.25 4.24h.01zM10.9 12a4 4 0 1 1 0-8 4 4 0 0 1 0 8zm0-2a2 2 0 1 0 0-4 2 2 0 0 0 0 4z"></path></svg>
                  </a>
                </span>
              </div>
            </td>
          </tr>
          </tbody>
        </table>
      </div>

      <pagination-links
          :data="pages"
          :hasMore="hasMore"
          :hasPrevious="hasPrevious"
          :current-page="page"
          :total-pages="totalPages"
          @previous="previousPage"
          @next="nextPage"
      ></pagination-links>
    </loading-card>
  </loading-view>
</template>

<script>
import PaginationLinks from "./PaginationLinks.vue";
import dayjs from 'dayjs';
import duration from 'dayjs/plugin/duration';
import utc from 'dayjs/plugin/utc';
import FilterMenu from "./FilterMenu";

dayjs.extend(duration);
dayjs.extend(utc);

    export default {
      components: {
        'pagination-links': PaginationLinks,
        'dayjs': dayjs,
        'filter-menu': FilterMenu
      },
      data: function () {
        return {
          title: 'Google Analytics',
          pages: [],
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
        }
      },
      metaInfo() {
        return {
          title: this.title,
        }
      },
      methods: {
        updateDuration(event) {
          this.duration = event.target.value;
          this.getPages();
        },

        updateLimit(value) {
          this.limit = value;
          this.getPages();
        },

        getPages() {
          Nova.request()
              .get(`/nova-vendor/nova-google-analytics/pages?limit=${this.limit}&duration=${this.duration}&page=${this.page}&s=${this.search}&sortBy=${this.sortBy}&sortDirection=${this.sortDirection}`)
              .then(response => {
                this.pages = response.data.pages;
                this.totalPages = response.data.totalPages;
                this.hasMore = response.data.hasMore;
                this.loading = false;
              });
        },

        nextPage() {
          this.loading = true
          this.page++
          this.getPages()
        },

        previousPage() {
          this.loading = true
          if (this.hasPrevious) {
            this.page--
          }
          this.getPages()
        },

        performSearch(event) {
          if (event.which !== 9) {
            this.page = 1
            this.getPages()
          }
        },

        sortByChange(event) {
          let direction = this.sortDirection === 'asc' ? 'desc' : 'asc'

          if (this.sortBy !== event.key) {
            direction = 'asc'
          }

          this.sortBy = event.key
          this.sortDirection = direction
          this.getPages()
        },

        resetOrderBy(event) {
          this.sortBy = 'ga:pageviews'
          this.sortDirection = 'desc'
          this.getPages()
        },

        getFormattedTime(timeString) {
          return dayjs.utc(dayjs.duration({seconds: timeString}).asMilliseconds()).format('HH:mm:ss')
        },

        getFormattedPercent(percentString) {
          return parseFloat(percentString).toFixed(2)+'%'
        },

        getFormattedCurrency(percentString) {
          //return '$'+parseFloat(percentString).toFixed(2)
          return new Intl.NumberFormat('en-US', {style: 'currency', currency: 'USD'}).format(percentString)
        }
      },
      computed: {
        hasPrevious() {
          return this.page > 1
        }
      },
      mounted() {
        this.getPages();
        this.initialLoading = false;
      },
    }
</script>

<style>
    /* Scoped Styles */
</style>
