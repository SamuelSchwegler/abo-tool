<template v-if="customer.hasOwnProperty('name')" :key="key">
    <div class="sm:flex sm:items-center mb-4">
        <div class="sm:flex-auto">
            <h1 class="page-title">{{ customer.name }}</h1>
        </div>
        <div class="mt-4 sm:mt-0 sm:ml-16 sm:flex-none">
            <router-link to="/customers" type="button"
                         class="inline-flex items-center justify-center rounded-md border border-transparent bg-green px-4 py-2 text-sm font-medium text-white shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 sm:w-auto">
                Zu allen Kunden
            </router-link>
        </div>
    </div>
    <div class="grid grid-cols-2 gap-4" v-if="key > 0">
        <div class="box bg-white">
            <customer-data v-on:updated="updated" :customer="customer" :errors="[]" :editable="true"></customer-data>
        </div>
        <div class="box bg-white">
            <fieldset>
                <legend class="sr-only">Plan</legend>
                <div class="space-y-5">
                    <div v-for="option in delivery_options" :key="option.id" class="relative flex items-start">
                        <div class="flex items-center h-5">
                            <input :id="option.id" :aria-describedby="`${option.id}-description`" name="plan"
                                   type="radio"
                                   :checked="option.id === customer.delivery_option" @change="changeDeliveryOption(option.id)"
                                   class="focus:ring-indigo-500 h-4 w-4 text-violet border-gray-300"/>
                        </div>
                        <div class="ml-3 text-sm">
                            <label :for="option.id" class="font-medium text-gray-700">{{ option.name }}</label>
                            <span :id="`${option.id}-description`" class="text-gray-500">{{ option.description }}</span>
                        </div>
                    </div>
                </div>
            </fieldset>
            <div v-if="customer.delivery_option !== 'pickup'" class="mt-5">
                <h4>Lieferadresse</h4>
                <address-vue :address="customer.delivery_address" class="mt-5"
                             :errors="[]"></address-vue>
            </div>
            <div v-if="customer.delivery_option !== 'match'" class="mt-5">
                <h4>Rechnungsadresse</h4>
                <address-vue :address="customer.billing_address" :errors="[]"></address-vue>
            </div>
        </div>
    </div>
</template>

<script>

import CustomerData from "../parts/CustomerData";
import AddressVue from "../parts/Address";

export default {
    name: "Customer",
    components: {CustomerData, AddressVue},
    data() {
        const delivery_options = [
            {id: 'match', name: 'Liefer und Rechnungsadresse stimmen überein', description: ''},
            {id: 'split', name: 'Unterschiedliche Rechnungs und Lieferadresse', description: ''},
            {id: 'pickup', name: 'Lieferung wird direkt vor Ort in Hünibach abgeholt', description: ''},
        ]

        return {
            customer: {},
            key: 0,
            delivery_options: delivery_options,
            errors: []
        }
    },
    methods: {
        async load() {
            await axios.get('/api/customer/' + this.$route.params.id).then(response => {
                this.customer = response.data.customer;
            });
            this.key++;
        },
        updated(customer) {
            this.customer = customer;
            this.key++;
        },
        changeDeliveryOption: function (id) {
            this.customer.delivery_option = id;
        },
    },
    created() {
        this.load();
    }
}
</script>

<style scoped>

</style>
