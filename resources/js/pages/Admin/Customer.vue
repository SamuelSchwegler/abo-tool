<template v-if="customer.hasOwnProperty('name')" :key="key">
    <div class="sm:flex sm:items-center mb-4">
        <div class="sm:flex-auto">
            <h1 class="page-title">{{ customer.name }}</h1>
        </div>
        <div class="mt-4 sm:mt-0 sm:ml-16 sm:flex-none">
            <div class="inline-flex mr-3">
                <span class="mr-3 text-sm font-medium text-gray-700">
                    <template v-if="active">Aktiv</template>
                    <template v-if="!active">Passiv</template>
                </span>
                <Toggle v-model="active"></Toggle>
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
            <customer-data v-on:updated="updated" :customer="customer" :errors="errors"
                           :editable="true"></customer-data>
            <user-data v-if="customer.email !== null" :user="user" :editable="can('manage customers')"></user-data>
            <div class="pb-4">
                <label for="comment" class="block text-sm font-medium text-gray-700">Interner Kommentar</label>
                <div class="mt-1">
                    <textarea rows="4" name="comment" id="comment"
                              v-model="customer.internal_comment"
                              class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md"/>
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
                                   @change="changeDeliveryOption(option.type)"
                                   class="focus:ring-indigo-500 h-4 w-4 text-violet border-gray-300"/>
                        </div>
                        <div class="ml-3 text-sm">
                            <label :for="option.id" class="font-medium text-gray-700">{{ option.name }}</label>
                            <span :id="`${option.id}-description`" class="text-gray-500">{{ option.description }}</span>
                        </div>
                    </div>
                </div>
                <div class="mt-2">
                    <label class="text-base font-medium text-gray-900">Abholung</label>
                    <fieldset class="mt-1">
                        <legend class="sr-only">Abholungsort</legend>
                        <div class="space-y-3">
                            <div v-for="option in pickup_options" :key="option.id"
                                 class="relative flex items-start">
                                <div class="flex items-center h-5">
                                    <input :id="option.id" :aria-describedby="`${option.id}-description`" name="plan"
                                           type="radio"
                                           :checked="option.type === 'pickup' && customer.hasOwnProperty('delivery_service') && customer.delivery_service !== null && customer.delivery_service.id === option.id"
                                           @change="changeDeliveryOption(option.type, option)"
                                           class="focus:ring-indigo-500 h-4 w-4 text-violet border-gray-300"
                                           v-bind:class="'delivery-option-' + option.id"/>
                                </div>
                                <div class="ml-3 text-sm">
                                    <label :for="option.id" class="font-medium text-gray-700">{{ option.name }}</label>
                                    <span :id="`${option.id}-description`" class="text-gray-500">{{
                                            option.description
                                        }}</span>
                                </div>
                            </div>
                        </div>
                    </fieldset>
                </div>
            </fieldset>
            <div v-if="customer.delivery_option !== 'pickup'" class="mt-5">
                <h4>Lieferadresse</h4>
                <address-vue :address="customer.delivery_address ?? {}" class="mt-5"
                             v-on:postcodeChanged="updateDeliveryService"
                             v-on:updated="deliveryAddressUpdated" :key="'delivery_address'"
                             :errors="delivery_address_errors"></address-vue>
                <div class="grid gap-4 grid-cols-4 pb-4" :key="key">
                    <div class="col-span-1" v-if="customer.hasOwnProperty('delivery_service') && customer.delivery_service !== null">
                        <label class="block text-sm font-medium text-gray-700">Lieferdienst</label>
                        <router-link :to="'/delivery-service/' + customer.delivery_service.id " type="button"
                                     class="mt-1 w-full inline-flex items-center justify-center rounded-md border border-transparent bg-violet px-4 py-2 text-sm font-medium text-white shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                            {{ customer.delivery_service.name }}
                        </router-link>
                    </div>
                    <div class="col-span-3">
                        <text-input v-model="customer.depository" :value="customer.depository"
                                    label="Standard Abstellort"></text-input>
                    </div>
                </div>

            </div>
            <div v-if="customer.delivery_option !== 'match'" class="mt-5">
                <h4>Rechnungsadresse</h4>
                <address-vue :address="customer.billing_address ?? {}" v-on:updated="billingAddressUpdated"
                             :key="'billing_address'"
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
    <audits class="mt-4" :audits="customer.audits"></audits>
</template>

<script>

import CustomerData from "../parts/CustomerData.vue";
import AddressVue from "../parts/Address.vue";
import UserData from "../parts/UserData.vue";
import customerHelpers from "./Helpers/customerHelpers";
import TextInput from "../../components/form/textInput.vue";
import Toggle from "@vueform/toggle";
import Audits from "../parts/Audits.vue";

export default {
    name: "Customer",
    components: {Audits, CustomerData, AddressVue, UserData, TextInput, Toggle},
    emits: ['authentication'],
    data() {
        return {
            customer: {},
            key: 0,
            delivery_options: customerHelpers.getDeliveryOptions(),
            pickup_options: [],
            errors: [],
            delivery_address_errors: {},
            billing_address_errors: {},
            user: {},
            active: true
        }
    },
    methods: {
        async load() {
            await axios.get('/api/customer/' + this.$route.params.id).then(response => {
                this.customer = response.data.customer;
                this.user = response.data.user;
                this.active = this.customer.active;
            });

            this.$axios.get('/api/delivery-services').then(response => {
                this.delivery_services = response.data.services;

                this.delivery_services.filter(s => s.pickup).forEach(s => {
                    this.pickup_options.push({
                        id: s.id,
                        type: 'pickup',
                        name: s.option_description,
                        description: ''
                    });
                });
            }).catch(function (error) {
                console.log(error);
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
        changeDeliveryOption: function (type, service = null) {
            this.delivery_option = type;
            if(service !== null) {
                this.customer.delivery_service = service;
            }

            let old = this.customer.delivery_option;
            this.customer.delivery_option = type;

            if (old === 'pickup' && this.customer.delivery_address === null) {
                this.customer.delivery_address = this.customer.billing_address
            }
        },
        async updateDeliveryService() {
            if (this.customer.delivery_address !== null && this.customer.delivery_address.hasOwnProperty('postcode')) {
                let postcode = this.customer.delivery_address.postcode;
                if (postcode.length > 0) {
                    await axios.get('/api/postcode-delivery-service', {
                        params: {
                            postcode: this.customer.delivery_address.postcode,
                            strict: 1
                        }
                    }).then(response => {
                        this.customer.delivery_service = response.data.service;
                    });
                }
            }
        },
        async update() {
            await axios.patch('/api/customer/' + this.$route.params.id, {
                ...this.customer,
                email: this.user.email,
                active: this.active
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
                if (errors.response.status === 422) {
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
