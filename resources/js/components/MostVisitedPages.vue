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
        <div v-if="!pages"
             class="flex items-center"
        >
            <p class="text-80 font-bold">
                No Data
            </p>
        </div>
        <div v-else
             class="flex items-center"
        >
            <ul class="most-visited-pages-list w-full">
                <li v-for="page in pages"
                    class="page-item align-middle"
                >
                   <div class="flex justify-between py-2">
                       <div>
                           <a :href="`https://${page.hostname}${page.path}`"
                              target="_blank"
                              class="flex-1 text-base text-primary no-underline"
                           >
                               {{ page.name }}
                           </a>
                       </div>
                       <div>
                           <span class="number-badge font-bold">
                               {{ page.visits }}
                           </span>
                       </div>
                   </div>
                </li>
            </ul>
        </div>
    </card>
</template>

<script>
export default {
    props: [
        'card'
    ],
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
.card-panel {
    height: auto !important;
}
.most-visited-pages-list {
    list-style: none;
    padding: 0;
    margin: 0;
}
.page-item {
    border-bottom: 1px solid #bacad6;
}
.page-item:last-of-type {
    border-bottom: none;
}
.number-badge {
    background-color: #3c4b5f;
    border-radius: 15px;
    color: white;
    font-size: 0.8rem;
    font-weight: bold;
    padding: 2px 6px;
    margin-left: 5px;
}
</style>
