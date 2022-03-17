<template>
    <div class="box">
        <h3 class="title">Kommende Lieferungen</h3>
        <table class="border-collapse table-auto w-full text-sm">
            <thead>
            <th></th>
            <th class="border-b dark:border-slate-600 font-medium p-4 pt-0 pb-3 text-slate-400 dark:text-slate-200 text-left">Lieferdatum</th>
            <th class="border-b dark:border-slate-600 font-medium p-4 pt-0 pb-3 text-slate-400 dark:text-slate-200 text-left">Abstellort</th>
            <th class="border-b dark:border-slate-600 font-medium p-4 pt-0 pb-3 text-slate-400 dark:text-slate-200 text-left">Schluss</th>
            </thead>
            <tbody class="bg-white dark:bg-slate-800">
                <order v-for="(order, index) in orders" :input_order="order"></order>
            </tbody>
        </table>
    </div>
</template>

<script>
import { CheckIcon, BanIcon } from '@heroicons/vue/solid';
import order from "./Order";

export default {
    props: ["orders"],
    components: {
        CheckIcon, BanIcon, order
    },
    name: "ManageOrders"
}
</script>
<style scoped>

</style>
