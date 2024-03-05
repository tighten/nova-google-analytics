<template>
    <card class='px-6 py-4'>
        <div class='h-6 flex items-center mb-4'>
            <h3 class='mr-3 leading-tight text-sm font-bold'>
                {{ __('Top Referrers') }}
            </h3>
            <div class='flex relative ml-auto w-[6rem] shrink-0'>
                <select
                    v-model='duration'
                    class='w-full block form-control form-control-bordered form-input h-6 text-xs'
                    @change='updateDuration'
                >
                    <option value='week'>
                        {{ __('This Week') }}
                    </option>
                    <option value='month'>
                        {{ __('This Month') }}
                    </option>
                    <option value='year'>
                        {{ __('This Year') }}
                    </option>
                </select>
                <IconArrow class='shrink-0 text-gray-700 pointer-events-none absolute right-[11px] top-[9px]' />
            </div>
        </div>

        <div
            v-if='!list'
            class='flex items-center font-bold text-sm'
        >
            <p class='text-gray-400 font-semibold'>
                {{ __('No Data') }}
            </p>
        </div>

        <div
            v-else
            class='flex items-center'
        >
            <ul class='most-visited-pages-list w-full'>
                <li
                    v-for='referrer in list'
                    class='page-item align-middle'
                >
                    <div class='flex justify-between py-2'>
                        <div>
                            <a
                                class='flex-1 text-90 leading-normal'
                                :href='`http://${referrer.url}`'
                                target='_blank'
                            >
                                {{ referrer.url }}
                            </a>
                        </div>
                        <div>
                            <CircleBadge>
                                {{ referrer.pageViews }}
                            </CircleBadge>
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
        'card',
    ],
    data: function() {
        return {
            list: [],
            duration: 'week',
        };
    },
    methods: {
        updateDuration(event) {
            this.duration = event.target.value;
            this.getList();
        },
        getList() {
            Nova.request()
                .get('/nova-vendor/nova-google-analytics/referrer-list?duration=' + this.duration)
                .then(response => {
                    this.list = response.data;
                });
        },
    },
    mounted() {
        this.getList();
    },
};
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
