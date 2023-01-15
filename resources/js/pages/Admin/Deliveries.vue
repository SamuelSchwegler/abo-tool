<template>
    <div class="sm:flex sm:items-center mb-4">
        <div class="sm:flex-auto">
            <h1 class="page-title">Lieferungen</h1>
        </div>
    </div>
    <div class="box bg-white">
        <delivery-filter v-on:filter="filterChange" :delivery_services="delivery_services" :filter="filter" class="mb-4" :key="'delivery_filter_key_' + filterKey"></delivery-filter>
        <table class="border-collapse table-auto w-full text-sm" v-if="deliveries.length > 0" :key="'deliveries_key_' + deliveriesKey">
            <thead>
            <tr>
                <th class="border-b dark:border-slate-600 font-medium p-4 pt-0 pb-3 text-slate-400 dark:text-slate-200 text-left">
                    Wochentag
                </th>
                <th class="border-b dark:border-slate-600 font-medium p-4 pt-0 pb-3 text-slate-400 dark:text-slate-200 text-left">
                    Datum
                </th>
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
                    {{ delivery.date['weekday'] }}
                </td>
                <td class="border-b border-slate-100 dark:border-slate-700 p-4 text-slate-500 dark:text-slate-400">
                    <router-link :to="'/deliveries/' + delivery.date['Y-m-d']" class="text-indigo-600 hover:text-indigo-900">
                        {{ delivery.date['d.m.Y'] }}
                    </router-link>
                </td>
                <td class="border-b border-slate-100 dark:border-slate-700 p-4 text-slate-500 dark:text-slate-400">
                    <router-link :to="'/delivery-service/' + delivery.delivery_service.id" class="text-indigo-600 hover:text-indigo-900">{{ delivery.delivery_service.name }}</router-link>
                </td>
                <td class="border-b border-slate-100 dark:border-slate-700 p-4 text-slate-500 dark:text-slate-400">
                    {{ delivery.orders.length }}
                </td>
                <td class="border-b border-slate-100 dark:border-slate-700 p-4 text-slate-500 dark:text-slate-400">
                    {{ delivery.deadline['d.m.Y'] }}
                </td>
                <td class="border-b border-slate-100 dark:border-slate-700 p-4 text-slate-500 dark:text-slate-400">
                    {{ delivery.approved ? 'ja' : 'nein'}}
                </td>
                <td class="border-b border-slate-100 dark:border-slate-700 p-4 text-slate-500 dark:text-slate-400">
                    <router-link :to="'/delivery/' + delivery.id" class="text-indigo-600 hover:text-indigo-900">mehr</router-link>
                </td>
            </tr>
            </tbody>
        </table>
    </div>
</template>

<script>
import formats from "../../formats";
import DeliveryFilter from "./Parts/DeliveryFilter.vue";
import helpers from "../../helpers";

export default {
    name: "Deliveries",
    components: {DeliveryFilter},
    data: function () {
        let until = new Date();
        until.setDate(until.getDate() - 5);
        return {
            deliveries: [],
            delivery_services: [],
            show_until: until,
            filterKey: 0,
            deliveriesKey: 0,
            filter: {
                delivery_service_ids: [],
                order_by: "date"
            }
        }
    },
    methods: {
        async load() {
            await axios.get('/api/deliveries/', {
                params: {
                    start: formats.getDateString(this.show_until, 'Y-m-d'),
                    delivery_service_ids: this.filter.delivery_service_ids.length > 0 ? this.filter.delivery_service_ids : null,
                    order_by: this.filter.order_by
                }
            }).then(response => {
                this.deliveries = response.data.deliveries;
                this.delivery_services = response.data.delivery_services;
                if(this.filter.delivery_service_ids.length === 0) { // falls kein Service ausgewählt ist, dann soll man einen neuen nehmen
                    this.filter.delivery_service_ids = this.delivery_services.map(d => d.id);
                }

                this.filterKey++;
                this.deliveriesKey++;
            }).catch(function (error) {
                console.log(error);
            });
        },
        filterChange(params) {
            if(Array.isArray(this.filter[params.section])) {
                this.filter[params.section] = this.toggleValueInArray(this.filter[params.section], params.value);
            } else {
                this.filter[params.section] = params.value;
            }
            this.load();
        },
        toggleValueInArray(array, value) {
            return helpers.toggleValueInArray(array, value)
        }
    },
    created() {
        this.load();
    }
}
</script>

<style scoped>

</style>
