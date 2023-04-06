<template>
    <div class="min-h-full flex flex-col justify-center py-12 sm:px-6 lg:px-8">
        <div class="sm:mx-auto sm:w-full sm:max-w-md">
            <img class="mx-auto h-24 w-auto" :src="signet_path" alt="Workflow"/>
            <h2 class="mt-6 text-center text-3xl font-extrabold text-gray-900">{{title}}</h2>
        </div>
        <div class="mt-8 sm:mx-auto sm:w-full sm:max-w-md">
            <div class="bg-white py-8 px-4 shadow sm:rounded-lg sm:px-10">
                <slot />
            </div>
        </div>
    </div>
</template>

<script>

export default {
    name: "AuthLayout",
    props: {
        title: String
    },
    data() {
        return {
            signet_path: new URL('/img/gsh-signet.png', import.meta.url).href
        }
    }
}
</script>


<style scoped>

</style>
