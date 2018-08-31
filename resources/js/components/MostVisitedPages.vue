<template>
    <card class="px-4 py-4">
        <div class="mb-4">
            <h3 class="mr-3 text-base text-80 font-bold">GA Most-visited pages this week</h3>
        </div>

        <ul class="most-visited-pages-list mb-4 mt-2 overflow-y-scroll">
            <li v-for="page in pages"><a :href="'http://' + page.hostname + page.path">{{ page.name }}</a>: {{ page.visits }}</li>
        </ul>
    </card>
</template>

<script>
export default {
    props: ['card'],

    data: function() {
        return {
            pages: [],
        }
    },

    mounted() {
        Nova.request().get('/nova-vendor/nova-google-analytics/most-visited-pages').then(response => {
            this.pages = response.data;
        });
    },
}
</script>

<style scoped>
.most-visited-pages-list {
    height: 4.6rem;
}
</style>
