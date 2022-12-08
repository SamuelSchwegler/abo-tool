<template>
    <div class="flex flex-col sm:flex-row bg-white rounded-md shadow-md">
        <div class=" sm:flex-none flex-auto h-64 sm:w-64 w-full relative">
            <img :src="'/media/img/bundle/' + bundle.id" alt="" class="absolute inset-0 w-full h-full object-cover rounded-l-md"/>
        </div>
        <form class="flex-auto p-6">
            <div class="flex flex-wrap">
                <h1 class="flex-auto text-lg font-semibold text-slate-900">
                    {{ bundle.title }}
                </h1>
                <div class="text-md font-semibold text-slate-500">
                    {{ bundle.deliveries }} Lieferungen à CHF {{ bundle.price_per_delivery }}<br>(total CHF {{ bundle.formatted_price }})
                    <span v-if="delivery_cost !== null" class="whitespace-nowrap">
                        zzgl. Lieferkosten CHF {{ delivery_cost }}
                    </span>
                    <span v-else>
                        exkl. Lieferkosten
                    </span>
                    <span v-if="bundle.short_description !== null">
                        <br>{{ bundle.short_description }}
                    </span>
                </div>
            </div>
            <div class="flex space-x-4 my-6 text-sm font-medium" v-if="allowOrder">
                <div class="flex-auto flex space-x-4">
                    <router-link :to="{name: 'bundle.buy', params: { id: bundle.id }}"
                                 class="order-button inline-flex items-center justify-center rounded-md border border-transparent bg-violet px-4 py-2 text-sm font-medium text-white shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 sm:w-auto">
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
