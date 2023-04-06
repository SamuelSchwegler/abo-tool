<!-- This example requires Tailwind CSS v2.0+ -->
<template>
    <PageHead page-title="Kunden">
        <template #description>
            Zusammenfassung aller Kunden.
            <span v-if="search.length > 0">({{customers.length}} von {{total_count}} werden Angezeigt)</span>
        </template>
        <template #links>
            <router-link type="button" to="/customer"
                         class="inline-flex items-center justify-center rounded-md border border-transparent bg-green px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 sm:w-auto">
                Neuer Kunde erstellen
            </router-link>
        </template>
    </PageHead>
    <PageContent>
        <Box>
            <div class="mt-1">
                <text-input name="search" :value="search" v-model="search" label="Suche"></text-input>
            </div>
        </Box>
        <div class="-my-2 -mx-4 overflow-x-auto sm:-mx-6 lg:-mx-8">
            <div class="inline-block min-w-full py-2 align-middle md:px-6 lg:px-8">
                <div class="overflow-hidden shadow ring-1 ring-black ring-opacity-5 md:rounded-lg">
                    <table class="min-w-full divide-y divide-gray-300">
                        <thead class="bg-gray-50">
                        <tr>
                            <th scope="col"
                                class="py-3.5 pl-4 pr-3 text-left text-sm font-semibold text-gray-900 sm:pl-6">Name
                            </th>
                            <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Email</th>
                            <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">
                                Kontostand
                            </th>
                            <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900"></th>
                            <th scope="col" class="relative py-3.5 pl-3 pr-4 sm:pr-6">
                                <span class="sr-only">Edit</span>
                            </th>
                        </tr>
                        </thead>
                        <tbody class="bg-white">
                        <tr v-for="(person, personIdx) in customers" :key="person.email"
                            v-bind:class="{'bg-gray-50': (person.active && personIdx % 2 === 0), 'bg-gray-200': !person.active}">
                            <td class="whitespace-nowrap py-4 pl-4 pr-3 text-sm font-medium text-gray-900 sm:pl-6">
                                <router-link :to="'/customer/' + person.id"
                                             class="text-indigo-600 hover:text-indigo-900">
                                    {{ person.name }}
                                </router-link>
                            </td>
                            <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">
                                <a :href="'mailto:' + person.email" class="text-indigo-600 hover:text-indigo-900">
                                    {{ person.email }}
                                </a>
                            </td>
                            <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">
                                <span v-for="balance in person.balances.filter(b => b.balance > 0)"
                                      class="whitespace-nowrap">
                                    {{ balance.name }}: {{ balance.balance + balance.planned }};
                                </span>
                            </td>
                            <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">
                                <span v-if="person.buys.filter(buy => !buy.paid).length > 0">
                                    Offene Rechnung
                                </span>
                                <span v-else-if="person.active">
                                    <button
                                        v-for="balance in person.balances.filter(b => b.balance + b.planned < 4 && b.planned > 0)"
                                        @click="issue(person.id, balance.product_id, balance.name)"
                                        class="text-indigo-600 hover:text-indigo-900 whitespace-nowrap">
                                    {{ balance.name }} verlängern
                                </button>
                                </span>
                            </td>
                            <td class="relative whitespace-nowrap py-4 pl-3 pr-4 text-right text-sm font-medium sm:pr-6">
                                <router-link :to="'/customer/' + person.id + '/orders'"
                                             class="text-indigo-600 hover:text-indigo-900">
                                    Bestellübersicht
                                    <span class="sr-only">, {{ person.name }}</span>
                                </router-link>
                            </td>
                        </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </PageContent>
    <create-buy-confirm-modal :show="showCreateBuyConfirmModal" :customer="createBuyConfirmModalCustomer"
                              :product="createBuyConfirmModalProduct" :key="createBuyConfirmModalKey"
                              v-on:issued="issued"
    ></create-buy-confirm-modal>
</template>

<script>

import CreateBuyConfirmModal from "./Parts/CreateBuyConfirmModal.vue";
import TextInput from "../../components/form/textInput.vue";
import Box from "../../components/Box.vue";
import PageContent from "../../layout/PageContent.vue";
import PageHead from "../../layout/PageHead.vue";

export default {
    name: "Customers",
    components: {PageHead, PageContent, Box, CreateBuyConfirmModal, TextInput},

    data() {
        return {
            customers: [],
            showCreateBuyConfirmModal: false,
            createBuyConfirmModalCustomer: {},
            createBuyConfirmModalProduct: {},
            createBuyConfirmModalKey: 0,
            search: '',
            total_count: 0
        }
    },
    emits: ['authentication'],
    methods: {
        async load() {
            let params = {};

            if(this.search.length > 0) {
                params.search = this.search;
            }

            await axios.get('/api/customers', {
                params: params
            }).then(response => {
                this.customers = response.data.customers;
                this.total_count = response.data.total_count;
            }).catch(error => {
                console.log(error);
            })
        },
        issue(customer_id, product_id, product_name) {
            this.showCreateBuyConfirmModal = true;
            this.createBuyConfirmModalCustomer = this.customers.filter(c => c.id === customer_id)[0];
            this.createBuyConfirmModalProduct = {id: product_id, name: product_name}
            this.createBuyConfirmModalKey++;
        },
        issued(customer) {
            this.customers = this.customers.map(c => {
                return (c.id === customer.id ? customer : c);
            });
        }
    },
    watch: {
        search: function (newValue) {
            this.load();
        }
    },
    created() {
        this.load();
    }
}
</script>
