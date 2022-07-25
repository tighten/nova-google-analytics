<!-- From vendor/laravel/nova/resources/js/components/Pagination/PaginationLinks.vue -->
<template>
    <div class='bg-20 rounded-b-lg border-t border-gray-200 dark:border-gray-700'>
        <nav v-if='data.length > 0' class='flex justify-between items-center'>
            <!-- Previous Link -->
            <div>
                <button
                    :class="{
                        'text-primary-500 hover:text-primary-400 active:text-primary-600': hasPrevious,
                        'text-gray-300 dark:text-gray-600': !hasPrevious,
                    }"
                    :disabled='!hasPrevious'
                    class='text-xs font-bold py-3 px-4 focus:outline-none rounded-bl-lg focus:ring focus:ring-inset'
                    rel='prev'
                    @click.prevent='previousPage'
                >
                    {{ __('Previous') }}
                </button>
            </div>

            <!-- Page Definition -->
            <div v-if='currentPage && totalPages' class='text-xs px-4'>{{ currentPage }} {{ __('of') }}
                {{ totalPages }}
            </div>

            <!-- Next Link -->
            <div>
                <button
                    :class="{
                        'text-primary-500 hover:text-primary-400 active:text-primary-600': hasMore,
                        'text-gray-300 dark:text-gray-600': !hasMore,
                    }"
                    :disabled='!hasMore'
                    class='text-xs font-bold py-3 px-4 focus:outline-none rounded-br-lg focus:ring focus:ring-inset'
                    rel='next'
                    @click.prevent='nextPage'
                >
                    {{ __('Next') }}
                </button>
            </div>
        </nav>
    </div>
</template>

<script>
export default {
    props: [
        'data',
        'hasMore',
        'hasPrevious',
        'currentPage',
        'totalPages',
    ],
    methods: {
        previousPage() {
            this.$emit('previous');
        },

        nextPage() {
            this.$emit('next');
        },
    },
};
</script>
