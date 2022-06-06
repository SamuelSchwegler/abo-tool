<template v-if="customer.hasOwnProperty('name')" :key="key">
    <div class="sm:flex sm:items-center mb-4">
        <div class="sm:flex-auto">
            <h1 class="page-title">{{ customer.name }}</h1>
        </div>
        <div class="mt-4 sm:mt-0 sm:ml-16 sm:flex-none">
            <div class="inline-flex mr-3">
                <span class="mr-3 text-sm font-medium text-gray-700">
                    <template v-if="customer.active">Aktiv</template>
                    <template v-if="!customer.active">Passiv</template>
                </span>
                <Toggle v-model="customer.active"></Toggle>
            </div>
            <router-link :to="'/customer/' + customer.id + '/orders'" type="button"
                         class="inline-flex items-center justify-center rounded-md border border-transparent bg-violet mr-3 px-4 py-2 text-sm font-medium text-white shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 sm:w-auto">
                Zur Bestellübersicht
            </router-link>
            <router-link :to="'/customer/' + customer.id + '/buys'" type="button"
                         class="inline-flex items-center justify-center rounded-md border border-transparent bg-violet mr-3 px-4 py-2 text-sm font-medium text-white shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 sm:w-auto">
                Zur Rechnungsübersicht
            </router-link>
            <router-link to="/customers" type="button"
                         class="inline-flex items-center justify-center rounded-md border border-transparent bg-green px-4 py-2 text-sm font-medium text-white shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 sm:w-auto">
                Zu allen Kunden
            </router-link>
        </div>
    </div>
    <div class="grid grid-cols-2 gap-4" v-if="key > 0">
        <div class="box bg-white">
            <customer-data v-on:updated="updated" :customer="customer" :errors="errors" :editable="true"></customer-data>
            <user-data v-if="customer.email !== null" :user="user"></user-data>
            <div class="pb-4">
                <label for="comment" class="block text-sm font-medium text-gray-700">Interner Kommentar</label>
                <div class="mt-1">
                    <textarea rows="4" name="comment" id="comment"
                              v-model="customer.internal_comment"
                              class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md" />
                </div>
            </div>
            <div class="grid gap-4 md:grid-cols-2 grid-cols-1 pb-4" :key="key">
                <div>
                    <text-input name="first_name" :value="customer.discount" v-model="customer.discount" label="Rabatt"
                                suffix="%" :error="errors['discount']"></text-input>
                </div>
            </div>
        </div>
        <div class="box bg-white">
            <fieldset>
                <legend class="sr-only">Plan</legend>
                <div class="space-y-5">
                    <div v-for="option in delivery_options" :key="option.id" class="relative flex items-start">
                        <div class="flex items-center h-5">
                            <input :id="option.id" :aria-describedby="`${option.id}-description`" name="plan"
                                   type="radio"
                                   :checked="option.id === customer.delivery_option"
                                   @change="changeDeliveryOption(option.id)"
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
                <address-vue :address="customer.delivery_address ?? {}" class="mt-5"
                             v-on:updated="deliveryAddressUpdated"
                             :errors="delivery_address_errors"></address-vue>
                <text-input v-model="customer.depository"  :value="customer.depository"
                    label="Standard Abstellort"></text-input>
            </div>
            <div v-if="customer.delivery_option !== 'match'" class="mt-5">
                <h4>Rechnungsadresse</h4>
                <address-vue :address="customer.billing_address ?? {}" v-on:updated="billingAddressUpdated"
                             :errors="billing_address_errors"></address-vue>
            </div>
        </div>
    </div>
    <div class="text-center">
        <button type="button" @click="update"
                class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-green hover:bg-green-hover focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
            Daten speichern
        </button>
    </div>
</template>

<script>

import CustomerData from "../parts/CustomerData";
import AddressVue from "../parts/Address";
import UserData from "../parts/UserData";
import customerHelpers from "./Helpers/customerHelpers";
import TextInput from "../../components/form/textInput";
import Toggle from "@vueform/toggle";

export default {
    name: "Customer",
    components: {CustomerData, AddressVue, UserData, TextInput, Toggle},
    data() {
        return {
            customer: {},
            key: 0,
            delivery_options: customerHelpers.getDeliveryOptions(),
            errors: [],
            delivery_address_errors: {},
            billing_address_errors: {},
            user: {}
        }
    },
    methods: {
        async load() {
            await axios.get('/api/customer/' + this.$route.params.id).then(response => {
                this.customer = response.data.customer;
                this.user = response.data.user;
            });
            this.key++;
        },
        updated(customer) {
            this.customer = customer;
            this.key++;
        },
        deliveryAddressUpdated(address) {
            this.customer.delivery_address = address;
        },
        billingAddressUpdated(address) {
            this.customer.billing_address = address;
        },
        changeDeliveryOption: function (id) {
            let old = this.customer.delivery_option;
            this.customer.delivery_option = id;

            if (old === 'pickup' && this.customer.delivery_address === null) {
                this.customer.delivery_address = this.customer.billing_address
            }
        },
        async update() {
            await axios.patch('/api/customer/' + this.$route.params.id, {
                ...this.customer,
            }).then(response => {
                this.customer = response.data.customer;
                this.errors = [];
                this.delivery_address_errors = {};
                this.billing_address_errors = {};
                this.$notify({type: "success", text: 'Bearbeiten erfolgreich'});
            }).catch(errors => {
                this.errors = errors.response.data.errors;

                this.delivery_address_errors = {
                    street: this.errors['delivery_address.street'],
                    postcode: this.errors['delivery_address.postcode'],
                    city: this.errors['delivery_address.city']
                }

                this.billing_address_errors = {
                    street: this.errors['billing_address.street'],
                    postcode: this.errors['billing_address.postcode'],
                    city: this.errors['billing_address.city']
                }

                let text = 'Es ist ein Fehler aufgetreten';
                if(errors.response.status === 422) {
                    text = "Die gesendeten Angaben sind ungültig. Bitte prüfen Sie die Felder."
                }
                this.$notify({type: "error", text: text});
            });
            this.key++;
        }
    },
    created() {
        this.load();
    }
}
</script>

<style scoped>

</style>
