<template>
    <div class="flex bg-white rounded-md shadow-md">
        <div class="flex-none w-64 relative">
            <img src="/tasche.jpg" alt="" class="absolute inset-0 w-full h-full object-cover rounded-l-md"/>
        </div>
        <form class="flex-auto p-6">
            <div class="flex flex-wrap">
                <h1 class="flex-auto text-lg font-semibold text-slate-900">
                    {{ bundle.name }}
                </h1>
                <div class="text-lg font-semibold text-slate-500">
                    {{ bundle.formatted_price }} CHF
                    <span v-if="delivery_cost !== null" class="whitespace-nowrap">
                        + Lieferkosten {{ delivery_cost }} CHF
                    </span>
                </div>
            </div>
            <div class="flex space-x-4 my-6 text-sm font-medium" v-if="allowOrder">
                <div class="flex-auto flex space-x-4">
                    <router-link :to="{name: 'bundle.buy', params: { id: bundle.id }}" class='btn inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest  active:bg-gray-900 focus:outline-none focus:border-gray-900 focus:ring ring-gray-300 disabled:opacity-25 transition ease-in-out duration-150'>
                        Bestellen
                    </router-link>
                </div>
            </div>
        </form>
    </div>
</template>

<script>
export default {
    name: "Bundle",
    props: {
        bundle: {
            type: Object
        },
        allowOrder: {
            type: Boolean
        },
        delivery_cost: {
            type: Number,
            default: null
        }
    }
}
</script>

<style scoped>

</style>
