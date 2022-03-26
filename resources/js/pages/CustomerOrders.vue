<template>
    <div class="mt-4 grid grid-cols-2 gap-4">
        <div class="box">
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
        <div class="box">
            <h3 class="title">Meine Abos</h3>
            <table class="border-collapse table-auto w-full text-sm" :key="'balances_key_' + balances_key" v-if="product_balances.length > 0">
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
                <tr v-for="(balance, index) in product_balances">
                    <td class="border-b border-slate-100 dark:border-slate-700 p-4 text-slate-500 dark:text-slate-400">
                        {{ balance.name }}
                    </td>
                    <td class="border-b border-slate-100 dark:border-slate-700 p-4 text-slate-500 dark:text-slate-400">
                        {{balance.balance}}
                    </td>
                    <td class="border-b border-slate-100 dark:border-slate-700 p-4 text-slate-500 dark:text-slate-400">
                        {{balance.planned}}
                    </td>
                </tr>
                </tbody>
            </table>
            <alert :text="'Momentan ist noch kein Guthaben für Bestellungen freigeschaltet.'"></alert>
        </div>
    </div>
</template>

<script>

import order from "../components/parts/Order";
import Alert from "../components/parts/Alert";

export default {
    name: "CustomerOrders",
    components: {order, Alert},
    data: function(){
        return {
            product_balances: [],
            balances_key: 0,
            orders: [],
        }
    },
    methods: {
        toggleOrder(action) {
            let index = this.product_balances.findIndex(balance => balance.product_id === action.product_id);
            this.product_balances[index].balance += action.running ? -1 : 1;
            this.product_balances[index].planned += action.running ? 1 : -1;

            this.balances_key++;
        }
    },
    created() {
        this.$axios.get(`/api/orders/`)
            .then(response => {
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
