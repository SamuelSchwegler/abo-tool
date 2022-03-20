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
            <table class="border-collapse table-auto w-full text-sm" :key="'balances_key_' + balances_key">
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
        </div>
    </div>
</template>

<script>
import order from "./Order";

export default {
    name: "CustomerOrders",
    props: ["orders", "input_product_balances"],
    components: {order},
    data: function(){
        return {
            product_balances: this.input_product_balances,
            balances_key: 0,
        }
    },
    methods: {
        toggleOrder(action) {
            let index = this.product_balances.findIndex(balance => balance.product_id === action.product_id);
            this.product_balances[index].balance += action.running ? -1 : 1;
            this.product_balances[index].planned += action.running ? 1 : -1;
            this.balances_key++;
        }
    }
}
</script>

<style scoped>

</style>
