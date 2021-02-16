<template>
    <card class="px-4 py-4">
        <div class="flex mb-4">
            <h3 class="mr-3 text-base text-80 font-bold">Most Visited Pages</h3>
            <select class="select-box-sm ml-auto min-w-24 h-6 text-xs appearance-none bg-40 pl-2 pr-6 active:outline-none active:shadow-outline focus:outline-none focus:shadow-outline"
                    v-model="duration"
                    @change="updateDuration"
            >
                <option value="week">
                    This Week
                </option>
                <option value="month">
                    This Month
                </option>
                <option value="year">
                    This Year
                </option>
            </select>
        </div>
        <div v-if="!pages" class="flex items-center">
            <p class="text-80 font-bold">No Data</p>
        </div>
        <ul v-else class="most-visited-pages-list mb-4 mt-2 overflow-y-scroll">
            <li v-for="page in pages">
                <a :href="`https://${page.hostname}${page.path}`" target="_blank">{{ page.name }}</a>: {{ page.visits }}
            </li>
        </ul>
    </card>
</template>

<script>
export default {
    props: ['card'],

    data: function() {
        return {
            pages: [],
            duration: 'week'
        }
    },
    methods: {
        updateDuration(event) {
            this.duration = event.target.value;
            this.getPages();
        },
        getPages() {
            Nova.request()
                .get('/nova-vendor/nova-google-analytics/most-visited-pages?duration='+this.duration)
                .then(response => {
                    this.pages = response.data;
                });
        }
    },
    mounted() {
        this.getPages();
    },
}
</script>

<style scoped>
.most-visited-pages-list {
    height: 4.6rem;
}
</style>
