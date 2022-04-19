<template>
    <div class="sm:flex sm:items-center" v-if="can('manage customers')">
        <div class="sm:flex-auto" >
            <h1 class="page-title">{{ customer.name }}</h1>
            <p class="mt-2 text-sm text-gray-700">Überblick über Bestellungen.</p>
        </div>
        <div class="mt-4 sm:mt-0 sm:ml-16 sm:flex-none">
            <router-link to="/customers" type="button" class="inline-flex items-center justify-center rounded-md border border-transparent bg-violet px-4 py-2 text-sm font-medium text-white shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 sm:w-auto">Zur Kundenübersicht</router-link>
        </div>
    </div>
    <div class="mt-4 grid lg:grid-cols-2 grid-cols-1 gap-4">
        <div class="box bg-white" v-if="orders.length > 0">
            <h3 class="title">Kommende Lieferungen</h3>
            <table class="border-collapse table-auto w-full text-sm">
                <thead>
                <th></th>
                <th class="border-b dark:border-slate-600 font-medium p-4 pt-0 pb-3 text-slate-400 dark:text-slate-200 text-left">
                    Lieferdatum
                </th>
                <th class="border-b dark:border-slate-600 font-medium p-4 pt-0 pb-3 text-slate-400 dark:text-slate-200 text-left">
                    Abstellort
                </th>
                <th class="border-b dark:border-slate-600 font-medium p-4 pt-0 pb-3 text-slate-400 dark:text-slate-200 text-left">
                    Schluss
                </th>
                </thead>
                <tbody class="bg-white dark:bg-slate-800">
                <order v-for="(order, index) in orders" :input_order="order" @toggleOrder="toggleOrder"></order>
                </tbody>
            </table>
        </div>
        <div class="box bg-white">
            <h3 class="title">Meine Abos</h3>
            <table class="border-collapse table-auto w-full text-sm" :key="'balances_key_' + balances_key"
                   v-if="Object.entries(product_balances).length > 0">
                <thead>
                <tr>
                    <th class="border-b dark:border-slate-600 font-medium p-4 pt-0 pb-3 text-slate-400 dark:text-slate-200 text-left">
                        Abo
                    </th>
                    <th class="border-b dark:border-slate-600 font-medium p-4 pt-0 pb-3 text-slate-400 dark:text-slate-200 text-left">
                        Guthaben
                    </th>
                    <th class="border-b dark:border-slate-600 font-medium p-4 pt-0 pb-3 text-slate-400 dark:text-slate-200 text-left">
                        davon geplant
                    </th>
                </tr>
                </thead>
                <tbody class="bg-white dark:bg-slate-800">
                <tr v-for="[index, balance] in Object.entries(product_balances)">
                    <td class="border-b border-slate-100 dark:border-slate-700 p-4 text-slate-500 dark:text-slate-400">
                        {{ balance.name }}
                    </td>
                    <td class="border-b border-slate-100 dark:border-slate-700 p-4 text-slate-500 dark:text-slate-400">
                        {{ balance.balance }}
                    </td>
                    <td class="border-b border-slate-100 dark:border-slate-700 p-4 text-slate-500 dark:text-slate-400">
                        {{ balance.planned }}
                    </td>
                </tr>
                </tbody>
            </table>
            <alert v-else :text="'Momentan ist noch kein Guthaben für Bestellungen freigeschaltet.'"></alert>
        </div>
    </div>
</template>

<script>

import order from "../components/parts/Order";
import Alert from "../components/parts/Alert";

export default {
    name: "CustomerOrders",
    components: {order, Alert},
    data: function () {
        return {
            product_balances: {},
            balances_key: 0,
            orders: [],
            customer_id: 0,
            customer: {}
        }
    },
    methods: {
        toggleOrder(action) {
            let index = this.product_balances.hasOwnProperty(action.product_id);
            if (index > -1) { // nur falls index gefunden worden ist
                this.product_balances[action.product_id].balance += action.running ? -1 : 1;
                this.product_balances[action.product_id].planned += action.running ? 1 : -1;
            }

            this.balances_key++;
        }
    },
    created() {
        let route = '';
        if(this.can('manage customers') && this.$route.params.hasOwnProperty('id')) {
            route = `/api/orders/${this.$route.params.id}`;
        } else {
            route = `/api/orders/`;
        }
        this.$axios.get(route)
            .then(response => {
                this.customer = response.data.customer;
                this.orders = response.data.orders;
                this.product_balances = response.data.product_balances;
            })
            .catch(function (error) {
                console.log(error);
            });
    },
    beforeRouteEnter(to, from, next) {
        if (!window.Laravel.isLoggedIn) {
            window.location.href = "/";
        }
        next();
    }
}
</script>

<style scoped>

</style>
