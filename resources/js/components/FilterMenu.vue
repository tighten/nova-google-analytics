<!-- From vendor/laravel/nova/resources/js/components/FilterMenu.vue -->
<template>
    <div v-on:click.prevent='toggleDropDown'>
        <div class='filter-menu-dropdown'>
            <dropdown
                :autoHide='false'
                :show='showDropDown'
                dusk='filter-selector'
                trigger='manual'
            >
                <dropdown-trigger
                    :active='filtersAreApplied'
                    :class="{ 'bg-primary border-primary': filtersAreApplied }"
                    class='bg-30 px-3 border-2 border-30 rounded'
                >
                    <icon
                        :class="filtersAreApplied ? 'text-white' : 'text-80'"
                        type='filter'
                    />

                    <span
                        v-if='filtersAreApplied'
                        class='ml-2 font-bold text-white text-80'
                    >
                        {{ activeFilterCount }}
                    </span>
                </dropdown-trigger>

                <dropdown-menu
                    slot='menu'
                    :dark='true'
                    direction='rtl'
                    width='290'
                >
                    <scroll-wrap :height='350'>
                        <div
                            v-if='filtersAreApplied'
                            class='bg-30 border-b border-60'
                        >
                            <button
                                class='py-2 w-full block text-xs uppercase tracking-wide text-center text-80 dim font-bold focus:outline-none'
                                @click="$emit('clear-selected-filters')"
                            >
                                {{ __('Reset Filters') }}
                            </button>
                        </div>

                        <!-- Custom Filters -->
                        <component
                            :is='filter.component'
                            v-for='filter in filters'
                            :key='filter.name'
                            :filter-key='filter.class'
                            :lens='lens'
                            @change="$emit('filter-changed')"
                            @input="$emit('filter-changed')"
                        />

                        <!-- Soft Deletes -->
                        <div
                            v-if='softDeletes'
                            dusk='filter-soft-deletes'
                        >
                            <h3
                                slot='default'
                                class='text-sm uppercase tracking-wide text-80 bg-30 p-3'
                            >
                                {{ __('Trashed') }}
                            </h3>

                            <div class='p-2'>
                                <select
                                    slot='select'
                                    :value='trashed'
                                    class='block w-full form-control-sm form-select'
                                    dusk='trashed-select'
                                    @change='trashedChanged'
                                >
                                    <option selected value=''>&mdash;</option>
                                    <option value='with'>{{ __('With Trashed') }}</option>
                                    <option value='only'>{{ __('Only Trashed') }}</option>
                                </select>
                            </div>
                        </div>

                        <!-- Per Page -->
                        <div dusk='filter-per-page'>
                            <h3
                                slot='default'
                                class='text-sm uppercase tracking-wide text-80 bg-30 p-3'
                            >
                                {{ __('Per Page') }}
                            </h3>

                            <div class='p-2'>
                                <select
                                    slot='select'
                                    :value='perPage'
                                    class='block w-full form-control-sm form-select'
                                    dusk='per-page-select'
                                    @change='perPageChanged'
                                >
                                    <option
                                        v-for='option in perPageOptions'
                                        :key='option'
                                    >
                                        {{ option }}
                                    </option>
                                </select>
                            </div>
                        </div>
                    </scroll-wrap>
                </dropdown-menu>
            </dropdown>
        </div>
    </div>
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

    data: () => ({
        showDropDown: false,
    }),

    methods: {
        trashedChanged(event) {
            this.$emit('trashed-changed', event.target.value);
        },

        perPageChanged(event) {
            this.$emit('per-page-changed', event.target.value);
        },

        toggleDropDown() {
            this.showDropDown = !this.showDropDown;
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
