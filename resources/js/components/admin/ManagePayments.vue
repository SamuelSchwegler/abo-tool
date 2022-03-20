<template>
    <div class="box">
        <h3 class="title">Zahlungen</h3>
        <table class="border-collapse table-auto w-full text-sm">
            <thead>
            <tr>
                <th class="border-b dark:border-slate-600 font-medium p-4 pt-0 pb-3 text-slate-400 dark:text-slate-200 text-left">
                    ID
                </th>
                <th class="border-b dark:border-slate-600 font-medium p-4 pt-0 pb-3 text-slate-400 dark:text-slate-200 text-left">
                    Preis
                </th>
                <th class="border-b dark:border-slate-600 font-medium p-4 pt-0 pb-3 text-slate-400 dark:text-slate-200 text-left">
                    Bezahlt
                </th>
                <th class="border-b dark:border-slate-600 font-medium p-4 pt-0 pb-3 text-slate-400 dark:text-slate-200 text-left">
                    Download
                </th>
            </tr>
            </thead>
            <tbody class="bg-white dark:bg-slate-800">
            <buy-payment v-for="(buy, index) in buys" :input_buy="buy"></buy-payment>
            </tbody>
        </table>
    </div>
</template>

<script>
import BuyPayment from "./BuyPayment";

export default {
    props: ['input_buys'],
    name: "ManagePayments",
    components: {
        BuyPayment
    },
    data: function () {
        return {
            buys: this.input_buys
        }
    }
}
</script>

<style scoped>

</style>
