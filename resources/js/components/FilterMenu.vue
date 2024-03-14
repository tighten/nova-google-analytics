<!-- From vendor/laravel/nova/resources/js/components/FilterMenu.vue -->
<template>
    <Dropdown
        :class="{
            'bg-primary-500 hover:bg-primary-600 border-primary-500':
            filtersAreApplied,
            'dark:bg-primary-500 dark:hover:bg-primary-600 dark:border-primary-500':
            filtersAreApplied,
        }"
        :handle-internal-clicks='false'
        :should-close-on-blur="false"
        class='flex h-9 hover:bg-gray-100 dark:hover:bg-gray-700 rounded'
    >
        <DropdownTrigger
            :class="{'text-white hover:text-white dark:text-gray-800 dark:hover:text-gray-800': filtersAreApplied}"
            class='toolbar-button px-2'
        >
            <Icon type='filter' />

            <span
                v-if='filtersAreApplied'
                :class="{
                    'text-white dark:text-gray-800': filtersAreApplied,
                }"
                class='ml-2 font-bold'
            >
                {{ activeFilterCount }}
            </span>
        </DropdownTrigger>

        <template #menu>
            <DropdownMenu width='260'>
                <ScrollWrap :height='350'>
                    <div class='divide-y divide-gray-200 dark:divide-gray-800 divide-solid'>
                        <div
                            v-if='filtersAreApplied'
                            class='bg-30 border-b border-60'
                        >
                            <button
                                class='py-2 w-full block text-xs uppercase tracking-wide text-center text-80 dim font-bold focus:outline-none'
                                @click="Nova.$emit('clear-filter-values')"
                            >
                                {{ __('Reset Filters') }}
                            </button>
                        </div>

                        <!-- Per Page -->
                        <div class='pt-2 pb-3'>
                            <h3 class='px-3 text-xs uppercase font-bold tracking-wide'>
                                {{ __('Per Page') }}
                            </h3>

                            <div class='mt-1 px-3'>
                                <SelectControl
                                    v-bind:selected='perPage'
                                    v-on:change='perPageChanged'
                                    :options='perPageOptions'
                                    size='sm'
                                />
                            </div>
                        </div>
                    </div>
                </ScrollWrap>
            </DropdownMenu>
        </template>
    </Dropdown>
</template>

<script>
export default {
    props: {
        trashed: {
            type: String,
            validator: value => [
                '',
                'with',
                'only',
            ].indexOf(value) !== -1,
        },
        perPage: [String, Number],
        perPageOptions: Array,
    },

    methods: {
        trashedChanged(event) {
            this.$emit('trashed-changed', event.target.value);
        },

        perPageChanged(event) {
            let value = event?.target?.value || event

            this.$emit('per-page-changed', value);
        },
    },

    computed: {
        filters() {
            return this.$store.getters[`${this.resourceName}/filters`];
        },

        filtersAreApplied() {
            return this.$store.getters[`${this.resourceName}/filtersAreApplied`];
        },

        activeFilterCount() {
            return this.$store.getters[`${this.resourceName}/activeFilterCount`];
        },
    },
};
</script>
