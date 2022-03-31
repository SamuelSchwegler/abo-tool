<template>
    <div class="box">
        <h3 class="title">Zahlungen</h3>
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
            </tr>
            </thead>
            <tbody class="bg-white dark:bg-slate-800">
            <tr v-for="delivery in deliveries" v-bind:class="{'bg-slate-200': delivery.date.passed}">
                <td class="border-b border-slate-100 dark:border-slate-700 p-4 text-slate-500 dark:text-slate-400">
                    {{ delivery.date['weekday'] }}
                </td>
                <td class="border-b border-slate-100 dark:border-slate-700 p-4 text-slate-500 dark:text-slate-400">
                    {{ delivery.date['d.m.Y'] }}
                </td>
                <td class="border-b border-slate-100 dark:border-slate-700 p-4 text-slate-500 dark:text-slate-400">
                    {{ delivery.delivery_service.name }}
                </td>
                <td class="border-b border-slate-100 dark:border-slate-700 p-4 text-slate-500 dark:text-slate-400">
                    {{ delivery.orders.length }}
                </td>
                <td class="border-b border-slate-100 dark:border-slate-700 p-4 text-slate-500 dark:text-slate-400">
                    {{ delivery.deadline['d.m.Y'] }}
                </td>
            </tr>
            </tbody>
        </table>
    </div>
</template>

<script>
import formats from "../../formats";
import DeliveryFilter from "./Parts/DeliveryFilter";

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
                delivery_service_ids: []
            }
        }
    },
    methods: {
        async load() {
            await axios.get('/api/deliveries/', {
                params: {
                    start: formats.getDateString(this.show_until, 'Y-m-d'),
                    delivery_service_ids: this.filter.delivery_service_ids.length > 0 ? this.filter.delivery_service_ids : null
                }
            }).then(response => {
                this.deliveries = response.data.deliveries;
                this.delivery_services = response.data.delivery_services;
                this.filterKey++;
                this.deliveriesKey++;
            }).catch(function (error) {
                console.log(error);
            });
        },
        filterChange(params) {
            this.filter[params.section] = this.toggleValueInArray(this.filter[params.section], params.value);
            this.load();
        },
        toggleValueInArray(array, value) {
            if(array.includes(value)) {
                return array.filter(function (ele) { return ele !== value;});
            } else {
               array.push(value);
               return array;
            }
        }
    },
    created() {
        this.load();
    }
}
</script>

<style scoped>

</style>
