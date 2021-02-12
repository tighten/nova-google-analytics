<template>
    <card class="px-6 py-4">
        <div class="mb-4">
            <h3 class="mr-3 text-base text-80 font-bold">
                Most Visited Pages - This Week
            </h3>
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
            pages: []
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
