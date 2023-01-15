<template>
    <div class="sm:flex sm:items-center" v-if="can('manage customers')">
        <div class="sm:flex-auto">
            <h1 class="page-title">
                {{ customer.name }}
                <div
                    :class="[customer.active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800', 'inline-flex items-baseline px-2.5 py-0.5 rounded-full text-sm font-medium md:mt-2 lg:mt-0']">
                    <span v-if="customer.active">
                        Aktiv
                    </span>
                    <span v-else="customer.active">
                        Passiv
                    </span>
                </div>
            </h1>
            <p class="mt-2 text-sm text-gray-700">Überblick über Bestellungen.</p>
        </div>
        <div class="mt-4 sm:mt-0 sm:ml-16 sm:flex-none">
            <router-link :to="'/customer/' + customer.id + '/buys'" type="button"
                         class="inline-flex items-center justify-center rounded-md border border-transparent bg-violet mr-3 px-4 py-2 text-sm font-medium text-white shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 sm:w-auto">
                Zur Rechnungsübersicht
            </router-link>
            <router-link :to="'/customer/' + customer.id" type="button"
                         class="inline-flex items-center justify-center rounded-md border border-transparent bg-violet px-4 py-2 text-sm font-medium text-white shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 sm:w-auto">
                Zum Kundenkonto
            </router-link>
        </div>
    </div>
    <div class="mt-4 grid grid-cols-1 gap-4" v-bind:class="{'lg:grid-cols-2': !can('manage customers')}">
        <div class="box bg-white" v-if="orders.length > 0" :key="'orders_key_' + orders_key">
            <h3 class="title">Kommende Lieferungen</h3>
            <table class="border-collapse table-auto w-full text-sm">
                <thead>
                <th class="border-b dark:border-slate-600 font-medium p-4 pt-0 pb-3 text-slate-400 dark:text-slate-200 text-left"></th>
                <th class="border-b dark:border-slate-600 font-medium p-4 pt-0 pb-3 text-slate-400 dark:text-slate-200 text-left">
                    Lieferdatum
                </th>
                <th v-if="multiple_delivery_services" class="border-b dark:border-slate-600 font-medium py-4 px-2 pt-0 pb-3 text-slate-400 dark:text-slate-200 text-left">
                    Lieferart
                </th>
                <th v-if="multiple_products" class="border-b dark:border-slate-600 font-medium py-4 px-2 pt-0 pb-3 text-slate-400 dark:text-slate-200 text-left">
                    Produkt
                </th>
                <th class="border-b dark:border-slate-600 font-medium p-4 pt-0 pb-3 text-slate-400 dark:text-slate-200 text-left">
                    Abstellort
                </th>
                <th class="border-b dark:border-slate-600 font-medium p-4 pt-0 pb-3 text-slate-400 dark:text-slate-200 text-left">
                    Schluss
                </th>
                <th v-if="can('manage customers')"
                    class="border-b dark:border-slate-600 font-medium p-4 pt-0 pb-3 text-slate-400 dark:text-slate-200 text-left">
                    Interner Kommentar
                </th>
                <th v-if="can('manage customers')"
                    class="border-b dark:border-slate-600 font-medium py-4 pr-2 pt-0 pb-3 text-slate-400 dark:text-slate-200 text-left">
                </th>
                <th v-if="can('manage customers')"
                    class="border-b dark:border-slate-600 font-medium py-4 pr-2 pt-0 pb-3 text-slate-400 dark:text-slate-200 text-left">
                </th>
                </thead>
                <tbody class="bg-white dark:bg-slate-800">
                <order v-for="(order, index) in orders" :input_order="order" :multiple_products="multiple_products"
                       :multiple_delivery_services="multiple_delivery_services"
                       :key="'order_key_' + order.id + '_affordable_' + order.affordable" @toggleOrder="toggleOrder"></order>
                </tbody>
            </table>
        </div>
        <div>
            <div class="box bg-white" v-if="!can('manage customers')">
                <h3 class="title">Meine Abos</h3>
                <template v-if="Object.entries(product_balances).length > 0">
                    <table class="border-collapse table-auto w-full text-sm" :key="'balances_key_' + balances_key">
                        <thead>
                        <tr>
                            <th class="border-b dark:border-slate-600 font-medium p-4 pt-0 pb-3 text-slate-400 dark:text-slate-200 text-left">
                                Abo
                            </th>
                            <th class="border-b dark:border-slate-600 font-medium p-4 pt-0 pb-3 text-slate-400 dark:text-slate-200 text-left">
                                Guthaben *
                            </th>
                            <th class="border-b dark:border-slate-600 font-medium p-4 pt-0 pb-3 text-slate-400 dark:text-slate-200 text-left">
                                bereits geplant
                            </th>
                        </tr>
                        </thead>
                        <tbody class="bg-white dark:bg-slate-800">
                        <tr v-for="[index, balance] in Object.entries(product_balances)">
                            <td class="border-b border-slate-100 dark:border-slate-700 p-4 text-slate-500 dark:text-slate-400">
                                {{ balance.name }}
                            </td>
                            <td class="border-b border-slate-100 dark:border-slate-700 p-4 text-slate-500 dark:text-slate-400">
                                {{ balance.balance + balance.planned }}
                            </td>
                            <td class="border-b border-slate-100 dark:border-slate-700 p-4 text-slate-500 dark:text-slate-400">
                                {{ balance.planned }}
                            </td>
                        </tr>
                        </tbody>
                    </table>
                    <p class="p-4 text-slate-500 text-sm">* Guthaben exklusive der kommenden Lieferungen.</p>
                </template>

                <alert v-else :text="'Momentan ist noch kein Guthaben für Bestellungen freigeschaltet.'"></alert>
            </div>
            <div class="box bg-white" v-if="!can('manage customers') && orders.length > 0">
                <h3 class="title">Ferienabwesenheiten</h3>
                <p class="mb-1">
                    Sie haben die Möglichkeit, den Regler neben den entsprechenden Daten auf inaktiv stellen (grün =
                    aktiv, grau = keine Lieferung).
                </p>
                <p>
                    Die nachkommenden Liefertermine werden automatisch nach hinten verschoben.
                </p>
            </div>
            <div class="box bg-white"
                 v-if="!can('manage customers') && customer.hasOwnProperty('delivery_service') && customer.delivery_service.pickup">
                <h3 class="title">Abholung im Bioladen der Gartenbauschule Hünibach</h3>
                <p class="mb-1">
                    Sie haben bei den Lieferoptionen die Abholung gewählt. Ihre Tasche mit Bio-Gemüse wartet zu den
                    hier aufgeführten Daten jeweils ab 8.30 Uhr im Bioladen auf Sie.
                </p>
                <p>
                    Bitte beachten Sie die Öffnungszeiten (MO-FR 8.30-12 Uhr, 13.30-18.30, SA 8.30-16 Uhr).
                </p>
            </div>
        </div>
    </div>
    <div class="box bg-white" v-if="can('manage customers')">
        <h3 class="title">Bestellhistorie</h3>
        <template v-if="Object.entries(product_balances).length > 0">
            <table class="border-collapse table-auto w-full text-sm" :key="'balances_key_' + balances_key">
                <thead>
                <tr>
                    <th class="border-b dark:border-slate-600 font-medium p-4 pt-0 pb-3 text-slate-400 dark:text-slate-200 text-left">
                        Abo
                    </th>
                    <th class="border-b dark:border-slate-600 font-medium p-4 pt-0 pb-3 text-slate-400 dark:text-slate-200 text-left">
                        gekauft
                    </th>
                    <th class="border-b dark:border-slate-600 font-medium p-4 pt-0 pb-3 text-slate-400 dark:text-slate-200 text-left">
                        geordert
                    </th>
                    <th class="border-b dark:border-slate-600 font-medium p-4 pt-0 pb-3 text-slate-400 dark:text-slate-200 text-left">
                        davon geplant
                    </th>
                    <th class="border-b dark:border-slate-600 font-medium p-4 pt-0 pb-3 text-slate-400 dark:text-slate-200 text-left">
                        davon manuell korrigiert
                    </th>
                    <th class="border-b dark:border-slate-600 font-medium p-4 pt-0 pb-3 text-slate-400 dark:text-slate-200 text-left">
                        Guthaben*
                    </th>
                </tr>
                </thead>
                <tbody class="bg-white dark:bg-slate-800">
                <tr v-for="[index, balance] in Object.entries(product_balances)">
                    <td class="border-b border-slate-100 dark:border-slate-700 p-4 text-slate-500 dark:text-slate-400">
                        {{ balance.name }}
                    </td>
                    <td class="border-b border-slate-100 dark:border-slate-700 p-4 text-slate-500 dark:text-slate-400">
                        {{ balance.total_deliveries }}
                    </td>
                    <td class="border-b border-slate-100 dark:border-slate-700 p-4 text-slate-500 dark:text-slate-400">
                        {{ balance.ordered }}
                    </td>
                    <td class="border-b border-slate-100 dark:border-slate-700 p-4 text-slate-500 dark:text-slate-400">
                        {{ balance.planned }}
                    </td>
                    <td class="border-b border-slate-100 dark:border-slate-700 p-4 text-slate-500 dark:text-slate-400">
                        <text-input :value="balance.ordered_before" name="used_orders" class="w-32" :error="errors['used_orders']"
                                    @change="updateUsedOrders(balance.product_id, $event.target.value)">
                        </text-input>
                    </td>
                    <td class="border-b border-slate-100 dark:border-slate-700 p-4 text-slate-500 dark:text-slate-400">
                        {{ balance.balance + balance.planned }}
                    </td>
                </tr>
                </tbody>
            </table>
            <p class="p-4 text-slate-500 text-sm">* beinhaltet nur definitiv bezogene Lieferungen.</p>
        </template>
        <p v-else class="text-slate-500 text-sm">Es gibt keine Bestellungen durch {{ customer.name }}</p>
    </div>
</template>

<script>

import order from "../components/parts/Order.vue";
import Alert from "../components/parts/Alert.vue";
import TextInput from "../components/form/textInput.vue";

export default {
    name: "CustomerOrders",
    components: {order, Alert, TextInput},
    data: function () {
        return {
            product_balances: {},
            balances_key: 0,
            orders_key: 0,
            orders: [],
            customer_id: 0,
            customer: {},
            errors: [],
            multiple_products: false,
            multiple_delivery_services: false
        }
    },
    methods: {
        toggleOrder(action) {
            let index = this.product_balances.hasOwnProperty(action.product_id);
            if (index > -1) { // nur falls index gefunden worden ist
                this.product_balances[action.product_id].balance += action.running ? -1 : 1;
                this.product_balances[action.product_id].planned += action.running ? 1 : -1;
                this.product_balances[action.product_id].ordered += action.running ? 1 : -1;
            }

            this.loadData();

            this.balances_key++;
        },
        async updateUsedOrders(product_id, value) {
            let vm = this;
            await axios.patch('/api/customer/' + this.customer.id + '/used-orders', {
                'product_id': product_id,
                'used_orders': parseInt(value)
            }).then(response => {
                this.product_balances = response.data.product_balances;
                this.balances_key++;
                vm.errors = [];
            }).catch(function (error) {
                vm.errors = error.response.data.errors;
            }).then(function () {
                vm.loadData();
            });
        },
        async loadData() {
            let route = '';
            if (this.can('manage customers') && this.$route.params.hasOwnProperty('id')) {
                route = `/api/orders/${this.$route.params.id}`;
            } else {
                route = `/api/orders/`;
            }
            await this.$axios.get(route)
                .then(response => {
                    this.customer = response.data.customer;
                    this.orders = response.data.orders;
                    this.product_balances = response.data.product_balances;

                    this.multiple_products = (Object.keys(this.product_balances).length > 1);
                    this.multiple_delivery_services = (response.data.distinct_delivery_services > 1);
                })
                .catch(function (error) {
                    console.log(error);
                });
        }
    },
    created() {
       this.loadData();
    }
}
</script>

<style scoped>

</style>
