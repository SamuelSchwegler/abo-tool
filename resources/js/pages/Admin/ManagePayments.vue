<template>
    <div class="sm:flex sm:items-center mb-4">
        <div class="sm:flex-auto">
            <h1 class="page-title">Rechnungen</h1>
        </div>
        <div class="mt-4 sm:mt-0 sm:ml-16 sm:flex-none">
            <span @click="openCreate" type="button"
                         class="inline-flex items-center justify-center rounded-md border border-transparent bg-green px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 sm:w-auto">
                Neue Rechnung ausstellen
            </span>
        </div>
    </div>
    <div class="box bg-white">
        <table class="border-collapse table-auto w-full text-sm" v-if="buys.length > 0">
            <thead>
            <tr>
                <th class="border-b dark:border-slate-600 font-medium p-4 pt-0 pb-3 text-slate-400 dark:text-slate-200 text-left">
                    Rechnungsnummer
                </th>
                <th class="border-b dark:border-slate-600 font-medium p-4 pt-0 pb-3 text-slate-400 dark:text-slate-200 text-left">
                    Kunde
                </th>
                <th class="border-b dark:border-slate-600 font-medium p-4 pt-0 pb-3 text-slate-400 dark:text-slate-200 text-left"></th>
                <th class="border-b dark:border-slate-600 font-medium p-4 pt-0 pb-3 text-slate-400 dark:text-slate-200 text-left">
                    Produkt
                </th>
                <th class="border-b dark:border-slate-600 font-medium p-4 pt-0 pb-3 text-slate-400 dark:text-slate-200 text-left">
                    Rabatt
                </th>
                <th class="border-b dark:border-slate-600 font-medium p-4 pt-0 pb-3 text-slate-400 dark:text-slate-200 text-right">
                    Betrag
                </th>
                <th class="border-b dark:border-slate-600 font-medium p-4 pt-0 pb-3 text-slate-400 dark:text-slate-200 text-left">
                    Bezahlt
                </th>
                <th class="border-b dark:border-slate-600 font-medium p-4 pt-0 pb-3 text-slate-400 dark:text-slate-200 text-left">
                    Erstellt
                </th>
                <th class="border-b dark:border-slate-600 font-medium p-4 pt-0 pb-3 text-slate-400 dark:text-slate-200 text-right">
                    Alter (d)
                </th>
                <th class="border-b dark:border-slate-600 font-medium p-2 pt-0 pb-3 text-slate-400 dark:text-slate-200 text-left">
                    Download
                </th>
            </tr>
            </thead>
            <tbody class="bg-white dark:bg-slate-800">
            <buy-payment v-for="(buy, index) in buys" :input_buy="buy" :key="buy.id" :show_customer="true"></buy-payment>
            </tbody>
        </table>
    </div>
    <create-buy-modal :show="showCreateBuyModal" :key="createBuyConfirmModalKey"
    ></create-buy-modal>
</template>

<script>

import BuyPayment from "./Parts/BuyPayment";
import CreateBuyModal from "./Parts/CreateBuyModal";

export default {
    name: "ManagePayments",
    components: {
        BuyPayment, CreateBuyModal
    },
    data: function () {
        return {
            buys: [],
            showCreateBuyModal: false,
            createBuyConfirmModalKey: 0
        }
    },
    methods: {
        openCreate() {
            this.showCreateBuyModal = true;
            this.createBuyConfirmModalKey++;
        },
    },
    created() {
        this.$axios.get(`/api/payments/`)
            .then(response => {
                this.buys = response.data.buys;
            })
            .catch(function (error) {
                console.log(error);
            });
    }
}
</script>

<style scoped>

</style>
