<template>
  <loading-view :loading="initialLoading">
    <heading class="mb-6">{{ title }}</heading>
    <div>
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
      <table v-if="pages.length > 0"
             class="table w-full table-default"
             cellpadding="0"
             cellspacing="0">
        <thead>
          <th class="text-left">
            <span class="inline-flex items-center">
              {{ __('Name') }}
            </span>
          </th>

          <th class="text-left">
            <span class="inline-flex items-center">
              {{ __('Path') }}
            </span>
          </th>
          <th class="text-left">
            <span class="inline-flex items-center">
              {{ __('Visits') }}
            </span>
          </th>
        </thead>
        <tbody>
          <tr v-for="page in pages">
            <td>{{ page.name }}</td>
            <td>{{ page.path }}</td>
            <td>{{ page.visits }}</td>
          </tr>
        </tbody>
      </table>

      <pagination-links
          :data="pages"
          :hasMore="hasMore"
          :hasPrevious="hasPrevious"
          @previous="previousPage"
          @next="nextPage"
      ></pagination-links>
    </loading-card>
  </loading-view>
</template>

<script>
import PaginationLinks from "./PaginationLinks.vue";

    export default {
      components: {
        'pagination-links': PaginationLinks
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
          search: '',
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

        getPages() {
          Nova.request()
              .get(`/nova-vendor/nova-google-analytics/pages?duration=${this.duration}&page=${this.page}&s=${this.search}`)
              .then(response => {
                this.pages = response.data.pages;
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
          if (event.which != 9) {
            this.page = 1
            this.getPages()
          }
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
