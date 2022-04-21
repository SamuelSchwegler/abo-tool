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
                <div class="text-md font-semibold text-slate-500">
                    CHF {{ bundle.formatted_price }}<br>
                    {{bundle.deliveries}} Lieferungen à CHF {{bundle.price_per_delivery}}<br>
                    <span v-if="delivery_cost !== null" class="whitespace-nowrap">
                        zzgl. Lieferkosten CHF {{ delivery_cost }}
                    </span>
                    <span v-else>
                        exkl. Lieferkosten
                    </span>
                </div>
            </div>
            <div class="flex space-x-4 my-6 text-sm font-medium" v-if="allowOrder">
                <div class="flex-auto flex space-x-4">
                    <router-link :to="{name: 'bundle.buy', params: { id: bundle.id }}"  class="inline-flex items-center justify-center rounded-md border border-transparent bg-violet px-4 py-2 text-sm font-medium text-white shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 sm:w-auto">
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
