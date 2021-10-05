<template>
  <loading-view :loading="initialLoading">
    <heading class="mb-6">{{ title }}</heading>
  </loading-view>
</template>

<script>
export default {
  props: [
    'url'
  ],
  data: function () {
    return {
      title: 'Page Data',
      initialLoading: true,
      loading: true,
    }
  },
  methods: {
    getPages() {
      Nova.request()
          .get(`/nova-vendor/nova-google-analytics/pages/page?url=${encodeURI(this.url)}`)
          .then(response => {
            console.log(response);
            // this.pages = response.data.pages;
            // this.totalPages = response.data.totalPages;
            // this.hasMore = response.data.hasMore;
            // this.loading = false;
          });
    },
  },
  computed: {},
  mounted() {
    this.getPages();
    this.initialLoading = false;
  },
}
</script>
