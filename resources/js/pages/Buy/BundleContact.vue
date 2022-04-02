<template>
    <progress-steps :steps="steps"></progress-steps>
    <div class="grid grid-cols-2 gap-4" :key="bundleBuyKey">
        <div class="box">
            <h3 class="title">Ihre Bestellung</h3>
            <bundle :bundle="bundle" v-if="bundle.hasOwnProperty('id')"></bundle>
            <div v-if="!isLoggedIn" class="mt-5">
                <alert
                    :text="'Um die Liefertermine zu verwalten brauchen Sie ein Kundenkonto. In diesem Schritt können Sie gleich ein Konto erstellen.'"></alert>
                <div class="grid gap-4 grid-cols-2 mt-5">
                    <login-credentials :credentials="credentials" :errors="errors"></login-credentials>
                    <div>
                        <button type="button" @click="openLoginModal"
                                class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-green hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                            Ich habe bereits ein Konto
                        </button>
                    </div>
                    <login-modal v-on:authenticated="authenticated" :show="show_loginModal"
                                 :key="loginModalKey"></login-modal>
                </div>
            </div>
        </div>
        <div class="box">
            <customer :customer="user.customer" :errors="errors"></customer>
            <fieldset>
                <legend class="sr-only">Plan</legend>
                <div class="space-y-5">
                    <div v-for="option in delivery_options" :key="option.id" class="relative flex items-start">
                        <div class="flex items-center h-5">
                            <input :id="option.id" :aria-describedby="`${option.id}-description`" name="plan"
                                   type="radio"
                                   :checked="option.id === delivery_option" @change="changeDeliveryOption(option.id)"
                                   class="focus:ring-indigo-500 h-4 w-4 text-violet border-gray-300"/>
                        </div>
                        <div class="ml-3 text-sm">
                            <label :for="option.id" class="font-medium text-gray-700">{{ option.name }}</label>
                            <span :id="`${option.id}-description`" class="text-gray-500">{{ option.description }}</span>
                        </div>
                    </div>
                </div>
            </fieldset>
            <div v-if="delivery_option !== 'pickup'" class="mt-5">
                <h4>Lieferadresse</h4>
                <address-vue :address="user.customer.delivery_address" class="mt-5"
                             :errors="delivery_address_errors"></address-vue>
            </div>
            <div v-if="delivery_option !== 'match'" class="mt-5">
                <h4>Rechnungsadresse</h4>
                <address-vue :address="user.customer.billing_address" :errors="billing_address_errors"></address-vue>
            </div>
        </div>
    </div>
    <div class="text-center">
        <button type="button" @click="proceed"
                class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-green hover:bg-green-hover focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
            Daten Speichern und Fortfahren
        </button>
    </div>
</template>

<script>
import Bundle from "../../components/Bundle";
import Customer from "../../components/parts/Customer";
import AddressVue from "../../components/parts/Address";
import ProgressSteps from "../../components/parts/ProgressSteps";
import TextInput from "../../components/form/textInput";
import Alert from "../../components/parts/Alert";
import LoginModal from "../../components/parts/LoginModal";
import LoginCredentials from "../../components/parts/LoginCredentials";

export default {
    name: "BundleBuy",
    components: {
        ProgressSteps, Alert, LoginModal, LoginCredentials,
        Bundle, Customer, AddressVue, TextInput
    },

    data: function () {
        const delivery_options = [
            {id: 'match', name: 'Liefer und Rechnungsadresse stimmen überein', description: ''},
            {id: 'split', name: 'Unterschiedliche Rechnungs und Lieferadresse', description: ''},
            {id: 'pickup', name: 'Lieferung wird direkt vor Ort in Hünibach abgeholt', description: ''},
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
                    billing_address: {}
                }
            }
        }

        return {
            bundle: {},
            delivery_options: delivery_options,
            steps: steps,
            delivery_option: 'match',
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
            billing_address_errors: []
        }
    },
    methods: {
        proceed: function () {
            if (this.delivery_option === "match") {
                this.user.customer.billing_address = this.user.customer.delivery_address;
            } else if (this.delivery_option === "pickup") {
                this.user.customer.delivery_address = {}
            }

            this.$axios.post(`/api/bundle/${this.$route.params.id}/buy`, {
                pickup: this.delivery_option === "pickup",
                delivery_option: this.delivery_option,
                ...this.user.customer,
                ...this.credentials
            }).then(response => {
                let buy = response.data.buy;
                this.$router.push({name: 'buy.bill', params: {id: buy.id}})
            }).catch(error => {
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
        },
        openLoginModal: function () {
            this.show_loginModal = true;
            this.loginModalKey++;
        },
        authenticated: function (user) {
            this.show_loginModal = false;
            this.user = user;
            this.isLoggedIn = true;
            this.bundleBuyKey++;
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
    }
}
</script>

<style scoped>

</style>