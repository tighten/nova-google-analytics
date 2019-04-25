<template>
    <card class="px-2 py-4">
        <div class="mb-4">
            <h3 class="mx-3 text-base text-80 font-bold">Top Referrers </h3>
        </div>
        <div v-if="!list" class="flex items-center">
            <p class="text-80 font-bold">No Data</p>
        </div>
        <ul v-else class="most-visited-pages-list mb-4 mt-2 pl-1 overflow-y-scroll">
            <li v-for="referrer in list" class="list-reset mx-3 my-1">
                <div class="text-base">
                    <a :href="`https://${referrer.url}`" target="_blank">{{ referrer.url}}</a> : {{ referrer.pageViews }}
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
            }
        },

        mounted() {
            Nova.request().get('/nova-vendor/nova-google-analytics/referrer-list').then(response => {
                this.list = response.data;
                console.log(this.list);
            });
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
