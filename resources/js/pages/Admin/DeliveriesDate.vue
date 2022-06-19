<template>
    <div class="sm:flex sm:items-center mb-4">
        <div class="sm:flex-auto">
            <h1 class="page-title">Lieferungen am {{ date_format['d.m.Y'] }}</h1>
            <p class="mt-2 text-sm text-gray-700">Zusammenfassung aller Lieferungen für den {{ date_format['d.m.Y'] }}.</p>
        </div>
        <div class="mt-4 sm:mt-0 sm:ml-16 sm:flex-none">
            <router-link type="button" to="/deliveries"
                         class="inline-flex items-center justify-center rounded-md border border-transparent bg-green px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 sm:w-auto">
                Alle Lieferungen
            </router-link>
        </div>
    </div>
    <div class="box bg-white">
        <table class="border-collapse table-auto w-full text-sm" v-if="deliveries.length > 0">
            <thead>
            <tr>
                <th class="border-b dark:border-slate-600 font-medium p-4 pt-0 pb-3 text-slate-400 dark:text-slate-200 text-left">
                    Lieferant
                </th>
                <th class="border-b dark:border-slate-600 font-medium p-4 pt-0 pb-3 text-slate-400 dark:text-slate-200 text-left">
                    # Bestellungen
                </th>
                <th class="border-b dark:border-slate-600 font-medium p-4 pt-0 pb-3 text-slate-400 dark:text-slate-200 text-left">
                    Deadline
                </th>
                <th class="border-b dark:border-slate-600 font-medium p-4 pt-0 pb-3 text-slate-400 dark:text-slate-200 text-left">
                    Bestätigt
                </th>
                <th class="border-b dark:border-slate-600 font-medium p-4 pt-0 pb-3 text-slate-400 dark:text-slate-200 text-left">
                    Details
                </th>
            </tr>
            </thead>
            <tbody class="bg-white dark:bg-slate-800">
            <tr v-for="delivery in deliveries" v-bind:class="{'bg-slate-200': delivery.date.passed}">
                <td class="border-b border-slate-100 dark:border-slate-700 p-4 text-slate-500 dark:text-slate-400">
                    <router-link :to="'/delivery-service/' + delivery.delivery_service.id"
                                 class="text-indigo-600 hover:text-indigo-900">{{ delivery.delivery_service.name }}
                    </router-link>
                </td>
                <td class="border-b border-slate-100 dark:border-slate-700 p-4 text-slate-500 dark:text-slate-400">
                    {{ delivery.orders.length }}
                </td>
                <td class="border-b border-slate-100 dark:border-slate-700 p-4 text-slate-500 dark:text-slate-400">
                    {{ delivery.deadline['d.m.Y'] }}
                </td>
                <td class="border-b border-slate-100 dark:border-slate-700 p-4 text-slate-500 dark:text-slate-400">
                    {{ delivery.approved ? 'ja' : 'nein' }}
                </td>
                <td class="border-b border-slate-100 dark:border-slate-700 p-4 text-slate-500 dark:text-slate-400">
                    <router-link :to="'/delivery/' + delivery.id" class="text-indigo-600 hover:text-indigo-900">mehr
                    </router-link>
                </td>
            </tr>
            </tbody>
        </table>
    </div>
    <div class="box bg-white">
        <delivery-product-items v-for="item in items" :delivery-items="item" :date-format="date_format"></delivery-product-items>
    </div>
</template>

<script>
import formats from "../../formats";
import DeliveryProductItems from "./DeliveryProductItems";

export default {
    name: "DeliveriesDate",
    components: {
        DeliveryProductItems
    },
    data: function () {
        let date = new Date(this.$route.params.date);
        return {
            date: date,
            deliveries: [],
            delivery_services: [],
            items: [],
            date_format: {
                'Y-m-d': formats.getDateString(date, 'Y-m-d'),
                'd.m.Y': formats.getDateString(date, 'd.m.Y'),
            }
        }
    },
    methods: {
        async loadData() {
            await axios.get('/api/deliveries/' + this.date_format['Y-m-d']).then(response => {
                this.deliveries = response.data.deliveries;
                this.delivery_services = response.data.delivery_services;
                this.items = response.data.items;
            }).catch(function (error) {
                console.log(error);
            });
        }
    },
    mounted() {
        this.loadData();
    }
}
</script>

<style scoped>

</style>
