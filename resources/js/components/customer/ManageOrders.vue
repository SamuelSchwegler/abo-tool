<template>
    <div class="box">
        <h3 class="title">Kommende Lieferungen</h3>
        <table class="border-collapse table-auto w-full text-sm">
            <thead>
            <th class="border-b dark:border-slate-600 font-medium p-4 pl-8 pt-0 pb-3 text-slate-400 dark:text-slate-200 text-left"></th>
            <th class="border-b dark:border-slate-600 font-medium p-4 pl-8 pt-0 pb-3 text-slate-400 dark:text-slate-200 text-left">Lieferdatum</th>
            <th class="border-b dark:border-slate-600 font-medium p-4 pl-8 pt-0 pb-3 text-slate-400 dark:text-slate-200 text-left">Schluss</th>
            </thead>
            <tbody class="bg-white dark:bg-slate-800">
            <tr class="order" v-for="(order, index) in orders">
                <td class="border-b border-slate-100 dark:border-slate-700 p-4 pl-8 text-slate-500 dark:text-slate-400">
                    <CheckIcon class="h-5 w-5" v-if="!order.canceled"></CheckIcon>
                    <BanIcon class="h-5 w-5" v-if="order.canceled"></BanIcon>
                </td>
                <td class="border-b border-slate-100 dark:border-slate-700 p-4 pl-8 text-slate-500 dark:text-slate-400">{{ order.delivery.date['d.m.Y'] }}</td>
                <td class="border-b border-slate-100 dark:border-slate-700 p-4 pl-8 text-slate-500 dark:text-slate-400">{{ order.delivery.deadline['d.m.Y'] }}</td>
            </tr>
            </tbody>
        </table>
    </div>
</template>

<script>
import { CheckIcon, BanIcon } from '@heroicons/vue/solid';
export default {
    props: ["orders"],
    components: {
        CheckIcon, BanIcon
    },
    name: "ManageOrders"
}
</script>

<style scoped>

</style>
