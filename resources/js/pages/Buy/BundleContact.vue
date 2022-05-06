<template>
    <progress-steps :steps="steps"></progress-steps>
    <div class="grid lg:grid-cols-2 grid-cols-1 gap-4" :key="bundleBuyKey">
        <div class="box bg-white">
            <h3 class="title">Ihre Bestellung</h3>
            <bundle :bundle="bundle" v-if="bundle.hasOwnProperty('id')"></bundle>
            <div v-if="!isLoggedIn" class="mt-5">
                <alert
                    :text="'Damit Sie Liefertermine verwalten können (beispielsweise um Ferienabwesenheiten zu\n'+
'melden), benötigen Sie ein persönliches Konto. In diesem Schritt können Sie dies\n'+
'eröffnen.'"></alert>
                <div class="grid gap-4 grid-cols-2 mt-5">
                    <login-credentials :credentials="credentials" :errors="errors"></login-credentials>
                    <div>
                        <button type="button" @click="openLoginModal"
                                class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-violet hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                            Ich habe bereits ein Konto
                        </button>
                    </div>
                    <login-modal v-on:authenticated="authenticated" :show="show_loginModal"
                                 :key="loginModalKey"></login-modal>
                </div>
            </div>
        </div>
        <div class="box bg-white">
            <customer-data :customer="user.customer" :errors="errors"></customer-data>
            <fieldset>
                <legend class="sr-only">Plan</legend>
                <div class="space-y-5">
                    <div v-for="option in delivery_options" :key="option.id" class="relative flex items-start">
                        <div class="flex items-center h-5">
                            <input :id="option.id" :aria-describedby="`${option.id}-description`" name="plan"
                                   type="radio"
                                   :checked="option.id === delivery_option" @change="changeDeliveryOption(option.id)"
                                   class="focus:ring-indigo-500 h-4 w-4 text-violet border-gray-300"
                                   v-bind:class="'delivery-option-' + option.id"/>
                        </div>
                        <div class="ml-3 text-sm">
                            <label :for="option.id" class="font-medium text-gray-700">{{ option.name }}</label>
                            <span :id="`${option.id}-description`" class="text-gray-500">{{ option.description }}</span>
                        </div>
                    </div>
                </div>
            </fieldset>
            <div v-if="delivery_option !== 'pickup'" class="mt-5 delivery-address">
                <h4>Lieferadresse</h4>
                <address-vue v-on:postcodeChanged="getDeliveryService" :address="user.customer.delivery_address ?? {}"
                             @updated="updateAddress('delivery_address', $event)"
                             prefix="delivery_address_"
                             class="mt-5" :errors="delivery_address_errors"></address-vue>
                <alert v-if="!delivery_service.pickup && delivery_service.delivery_cost > 0"
                       :text="'Die Lieferkosten betragen pro Lieferung ' + delivery_service.delivery_cost+ ' CHF'"></alert>
                <alert v-if="delivery_service.pickup" :text="'Für diese PLZ wird keine Lieferung angeboten'"></alert>
            </div>
            <div v-if="delivery_option !== 'match'" class="mt-5 billing-address">
                <h4>Rechnungsadresse</h4>
                <address-vue :address="user.customer.billing_address ?? {}"
                             @updated="updateAddress('billing_address', $event)"
                             prefix="billing_address_"
                             :errors="billing_address_errors"></address-vue>
            </div>
        </div>
    </div>
    <div class="text-center">
        <button type="button" @click="proceed" id="proceed"
                class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-green hover:bg-green-hover focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
            Daten Speichern und Fortfahren
        </button>
    </div>
</template>

<script>
import TextInput from "../../components/form/textInput";
import Alert from "../../components/parts/Alert";
import ProgressSteps from "../parts/ProgressSteps";
import CustomerData from "../parts/CustomerData";
import AddressVue from "../parts/Address";
import Bundle from "../parts/Bundle";
import LoginModal from "./parts/LoginModal";
import LoginCredentials from "./parts/LoginCredentials";

export default {
    name: "BundleBuy",
    components: {
        ProgressSteps, Alert, LoginModal, LoginCredentials,
        Bundle, CustomerData, AddressVue, TextInput
    },
    emits: ['authentication'],
    data: function () {
        const delivery_options = [
            {id: 'match', name: 'Liefer- und Rechnungsadresse stimmen überein', description: ''},
            {id: 'split', name: 'Unterschiedliche Liefer- und Rechnungsadresse', description: ''},
            {id: 'pickup', name: 'Ich hole mein Gemüse im Bioladen der Gartenbauschule Hünibach ab', description: ''},
        ]

        const steps = [
            {
                id: '01',
                name: 'Produktauswahl',
                description: 'Wählen Sie ihr Gemüsepaket aus.',
                href: '#',
                status: 'complete'
            },
            {
                id: '02',
                name: 'Angabe Lieferdaten',
                description: 'Geben Sie ihre Liefer- und Rechnungsadresse an.',
                href: '#',
                status: 'current'
            },
            {id: '03', name: 'Bezahlung', description: 'Zustellung Rechnung.', href: '#', status: 'upcoming'},
        ]

        let user = window.Laravel.user;
        if (user === undefined || !user.hasOwnProperty('id')) {
            user = {
                customer: {
                    delivery_address: {},
                    billing_address: {},
                    delivery_options: 'match'
                }
            }
        }

        return {
            bundle: {},
            delivery_options: delivery_options,
            steps: steps,
            delivery_option: user.customer.delivery_option,
            isLoggedIn: window.Laravel.isLoggedIn,
            user: user,
            show_loginModal: false,
            loginModalKey: 0,
            bundleBuyKey: 0,
            errors: [],
            credentials: {
                email: '',
                password: ''
            },
            delivery_address_errors: [],
            billing_address_errors: [],
            delivery_service: {}
        }
    },
    methods: {
        proceed: function () {
            if (this.delivery_option === "match") {
                this.user.customer.billing_address = this.user.customer.delivery_address;
            } else if (this.delivery_option === "pickup") {
                this.user.customer.delivery_address = {}
            }

            this.user.customer.delivery_option = this.delivery_option;

            this.$axios.post(`/api/bundle/${this.$route.params.id}/buy`, {
                pickup: (this.delivery_option === "pickup"),
                ...this.user.customer,
                ...this.credentials
            }).then(response => {
                let buy = response.data.buy;
                this.$router.push({name: 'buy.bill', params: {id: buy.id}})
            }).catch(error => {
                if (error.response.data.authenticated && !this.isLoggedIn) {
                    let user = error.response.data.user;
                    this.user.email = user.email;

                    this.isLoggedIn = true;
                    this.$emit('authentication', user);
                    this.$notify({type: "success", text: 'Es fehlen noch Angaben, aber ein Konto wurde erstellt.'});
                }
                this.errors = error.response.data.errors;

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

                this.bundleBuyKey++;
            });
        },
        changeDeliveryOption: function (id) {
            this.delivery_option = id;
            if ((id === 'match' || id === 'split') && this.user.customer.delivery_address === null) {
                this.user.customer.delivery_address = {
                    street: '',
                    city: '',
                    postcode: ''
                }
            } else if ((id === 'pickup' || id === 'split') && this.user.customer.billing_address === null) {
                this.user.customer.billing_address = {
                    street: '',
                    city: '',
                    postcode: ''
                }
            }
            this.getDeliveryService();
        },
        openLoginModal: function () {
            this.show_loginModal = true;
            this.loginModalKey++;
        },
        authenticated: function (user) {
            this.show_loginModal = false;
            this.user = user;
            this.isLoggedIn = true;
            this.delivery_option = user.customer.delivery_option;
            this.$emit('authentication', user);

            this.bundleBuyKey++;
        },
        async getDeliveryService() {
            if (this.user.customer.delivery_address !== null && this.user.customer.delivery_address.hasOwnProperty('postcode')) {
                let postcode = this.user.customer.delivery_address.postcode;
                if (postcode.length > 0) {
                    await axios.get('/api/postcode-delivery-service', {
                        params: {
                            postcode: this.user.customer.delivery_address.postcode
                        }
                    }).then(response => {
                        this.delivery_service = response.data.service;
                    });
                }
            }
        },
        updateAddress(kind, address) {
            this.user.customer[kind] = address;
        }
    },
    created() {
        this.$axios.get(`/api/bundle/${this.$route.params.id}`)
            .then(response => {
                this.bundle = response.data.data;
            })
            .catch(function (error) {
                console.log(error);
            });

        this.getDeliveryService();
    }
}
</script>

<style scoped>

</style>
