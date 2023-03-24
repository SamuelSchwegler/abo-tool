<template>
    <div class="sm:flex sm:items-center">
        <div class="sm:flex-auto">
            <h1 class="page-title">{{ pageTitle}}</h1>
            <p class="mt-2 text-sm text-gray-700">
                <slot name="description" />
            </p>
        </div>
        <div class="mt-4 sm:mt-0 sm:ml-16 sm:flex-none">
            <slot name="links" />
        </div>
    </div>
</template>

<script>
export default {
    name: "PageHead",
    props: {
        pageTitle: String
    }
}
</script>

<style scoped>

</style>
