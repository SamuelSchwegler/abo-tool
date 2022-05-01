<template v-if="customer.hasOwnProperty('name')" :key="key">
    <div class="sm:flex sm:items-center mb-4">
        <div class="sm:flex-auto">
            <h1 class="page-title">Neuer Kunde</h1>
        </div>
        <div class="mt-4 sm:mt-0 sm:ml-16 sm:flex-none">
            <router-link to="/customers" type="button"
                         class="inline-flex items-center justify-center rounded-md border border-transparent bg-green px-4 py-2 text-sm font-medium text-white shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 sm:w-auto">
                Zu allen Kunden
            </router-link>
        </div>
    </div>
    <div class="grid grid-cols-2 gap-4">
        <div class="box bg-white">
            <customer-data v-on:updated="updated" :customer="customer" :errors="errors" :editable="true"></customer-data>
            <!--<user-data :user="user"></user-data>-->
        </div>
    </div>
    <div class="text-center">
        <button type="button" @click="create"
                class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-green hover:bg-green-hover focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
            Daten Speichern
        </button>
    </div>
</template>

<script>

import CustomerData from "../parts/CustomerData";
import AddressVue from "../parts/Address";
import UserData from "../parts/UserData";
import customerHelpers from "./Helpers/customerHelpers";

export default {
    name: "Customer",
    components: {CustomerData, AddressVue, UserData},
    data() {
        return {
            customer: {
                first_name: '',
                last_name: '',
                company: '',
                phone: ''
            },
            key: 0,
            errors: [],
            user: {}
        }
    },
    methods: {
        updated(customer) {
            this.customer = customer;
            this.key++;
        },
        async create() {
            await axios.post('/api/customer/', {
                ...this.customer,
            }).then(response => {
                this.customer = response.data.customer;
                this.errors = [];
                this.$notify({type: "success", text: 'Bearbeiten erfolgreich'});

                this.$router.replace('/customer/' + this.customer.id);
            }).catch(errors => {
                console.log(errors);
                this.errors = errors.response.data.errors;
                this.$notify({type: "danger", text: 'Es ist ein Fehler aufgetreten'});
            });
            this.key++;
        }
    }
}
</script>

<style scoped>

</style>
