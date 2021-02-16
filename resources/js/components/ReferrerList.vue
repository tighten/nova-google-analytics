<template>
    <card class="px-2 py-4">
        <div class="flex mb-4">
            <h3 class="mx-3 text-base text-80 font-bold">Top Referrers</h3>
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
        <div v-if="!list" class="flex items-center">
            <p class="text-80 font-bold">No Data</p>
        </div>
        <ul v-else class="most-visited-pages-list mb-4 mt-2 pl-1 overflow-y-scroll">
            <li v-for="referrer in list" class="list-reset mx-3 my-1">
                <div class="text-base">
                    <a :href="`http://${referrer.url}`" target="_blank">{{ referrer.url}}</a> : {{ referrer.pageViews }}
                </div>
            </li>
        </ul>
    </card>
</template>

<script>
    export default {
        props: ['card'],

        data: function() {
            return {
                list: [],
                duration: 'week'
            }
        },
        methods: {
            updateDuration(event) {
                this.duration = event.target.value;
                this.getList();
            },
            getList() {
                Nova.request()
                    .get('/nova-vendor/nova-google-analytics/referrer-list?duration='+this.duration)
                    .then(response => {
                        this.list = response.data;
                    });
            }
        },
        mounted() {
            this.getList();
        },
    }
</script>

<style scoped>
    .most-visited-pages-list {
        height: 10.4rem;
    }
    .card-panel {
        height: 255px;
    }
    a {
        color: #00427A;
        text-decoration: none;
    }
    a:hover {
        color: #424D5C
    }
</style>
