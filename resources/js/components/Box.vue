<template>
    <div class="box bg-white">
        <h3 class="title" v-if="title.length > 0">{{ title }}</h3>
        <slot />
    </div>
</template>

<script>
export default {
    name: "Box",
    props: {
        title: {
            type: String,
            default: ''
        }
    }
}
</script>

<style scoped>

</style>
