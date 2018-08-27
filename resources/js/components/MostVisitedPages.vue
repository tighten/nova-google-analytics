<template>
    <card class="flex flex-col items-center justify-center">
        <div class="px-3 py-3">
            <h1 class="text-center text-3xl text-80 font-light">Most Visited Card!</h1>
        </div>
    </card>
</template>

<script>
export default {
    props: ['card'],

    mounted() {
        console.log("Mounted");
    },
}
</script>
