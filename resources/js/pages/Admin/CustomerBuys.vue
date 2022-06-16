<template>
    <div class="sm:flex sm:items-center mb-4">
        <div class="sm:flex-auto">
            <h1 class="page-title">{{ customer.name }}
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
            <p class="mt-2 text-sm text-gray-700">Überblick über die Rechnungen.</p>
        </div>
        <div class="mt-4 sm:mt-0 sm:ml-16 sm:flex-none">
            <router-link :to="'/customer/' + customer.id + '/orders'" type="button"
                         class="inline-flex items-center justify-center rounded-md border border-transparent bg-violet mr-3 px-4 py-2 text-sm font-medium text-white shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 sm:w-auto">
                Zur Bestellübersicht
            </router-link>
            <router-link :to="'/customer/' + customer.id" type="button"
                         class="inline-flex items-center justify-center rounded-md border border-transparent bg-violet px-4 py-2 text-sm font-medium text-white shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 sm:w-auto">
                Zum Kundenkonto
            </router-link>
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
                <th class="border-b dark:border-slate-600 font-medium p-2 pt-0 pb-3 text-slate-400 dark:text-slate-200 text-left">
                </th>
            </tr>
            </thead>
            <tbody class="bg-white dark:bg-slate-800">
            <buy-payment :show_customer="false" :allow_delete="true" v-for="(buy, index) in buys" :input_buy="buy"
                         :key="buy.id"></buy-payment>
            </tbody>
        </table>
        <p v-else class="text-slate-500 text-sm">Es gibt keine Rechnungen für {{ customer.name }}</p>
    </div>
</template>

<script>
import BuyPayment from "./Parts/BuyPayment";

export default {
    name: "CustomerBuys",
    components: {BuyPayment},
    data: function () {
        return {
            buys: [],
            customer: {}
        }
    },
    created() {
        let route = `/api/buys/${this.$route.params.id}`;

        this.$axios.get(route)
            .then(response => {
                this.customer = response.data.customer;
                this.buys = response.data.buys;
            })
            .catch(function (error) {
                console.log(error);
            });
    },
}
</script>

<style scoped>

</style>
