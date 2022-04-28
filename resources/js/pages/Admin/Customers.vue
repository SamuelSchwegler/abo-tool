<!-- This example requires Tailwind CSS v2.0+ -->
<template>
    <div class="sm:flex sm:items-center">
        <div class="sm:flex-auto">
            <h1 class="page-title">Kunden</h1>
            <p class="mt-2 text-sm text-gray-700">Zusammenfassung aller Kunden.</p>
        </div>
        <div class="mt-4 sm:mt-0 sm:ml-16 sm:flex-none" v-if="false">
            <button type="button"
                    class="inline-flex items-center justify-center rounded-md border border-transparent bg-green px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 sm:w-auto">
                Neuer Kunde erstellen
            </button>
        </div>
    </div>
    <div class="mt-8 flex flex-col">
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
                            <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Kontostand</th>
                            <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900"></th>
                            <th scope="col" class="relative py-3.5 pl-3 pr-4 sm:pr-6">
                                <span class="sr-only">Edit</span>
                            </th>
                        </tr>
                        </thead>
                        <tbody class="bg-white">
                        <tr v-for="(person, personIdx) in customers" :key="person.email"
                            :class="personIdx % 2 === 0 ? undefined : 'bg-gray-50'">
                            <td class="whitespace-nowrap py-4 pl-4 pr-3 text-sm font-medium text-gray-900 sm:pl-6">
                                <router-link :to="'/customer/' + person.id"
                                             class="text-indigo-600 hover:text-indigo-900">
                                    {{ person.name }}
                                </router-link>
                            </td>
                            <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">
                                <a :href="'mailto:' + person.mail" class="text-indigo-600 hover:text-indigo-900">
                                    {{ person.email }}
                                </a>
                            </td>
                            <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">
                                <span v-for="balance in person.balances.filter(b => b.balance > 0)" class="whitespace-nowrap">
                                    {{balance.name}}: {{balance.balance}};
                                </span>
                            </td>
                            <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">
                                <span v-if="person.buys.filter(buy => !buy.paid).length > 0">
                                    Offene Rechnung
                                </span>
                                <span v-else>
                                    <button v-for="balance in person.balances.filter(b => b.balance < 5)" @click="issue(person.id, balance.product_id)"
                                            class="text-indigo-600 hover:text-indigo-900 whitespace-nowrap">
                                    {{balance.name}} verlängern
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
    </div>
</template>

<script>

export default {
    name: "Customers",
    data() {
        return {
            customers: []
        }
    },
    methods: {
        async load() {
            await axios.get('/api/customers').then(response => {
                this.customers = response.data.customers;
            }).catch(error => {
                console.log(error);
            })
        },
        async issue(customer_id, product_id) {
            await axios.post('/api/buy', {
                product_id: product_id,
                customer_id: customer_id
            }).then(response => {
                let customer = response.data.customer;
                this.customers = this.customers.map(c => {
                   return (c.id === customer.id ? customer : c);
                });
            }).catch(error => {
                console.log(error);
            })
        }
    },
    created() {
        this.load();
    }
}
</script>
