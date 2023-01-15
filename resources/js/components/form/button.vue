<template>
    <button type="button"
            class="inline-flex duration-100 items-center justify-center rounded-md border border-transparent px-4 py-2 text-sm font-medium text-white shadow-sm focus:outline-none focus:ring-2 focus:ring-offset-2 sm:w-auto'"
            :class="colorVariants[this.color]"
    >
        <slot />
    </button>
</template>

<script>
const colorVariants = {
    green: 'bg-green',
    red: 'bg-red-600 hover:bg-red-700 focus:ring-red-600',
    violet: 'bg-violet'
}
export default {
    name: "vue-button",
    props: {
        color: {
            type: String,
            default: 'violet'
        }
    },
    data() {
        return {
            colorVariants
        }
    }
}
</script>

<style scoped>

</style>
