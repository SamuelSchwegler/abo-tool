<template>
    <div class="sm:flex sm:items-center mb-4">
        <div class="sm:flex-auto">
            <h1 class="page-title">Neuer User</h1>
        </div>
        <div class="mt-4 sm:mt-0 sm:ml-16 sm:flex-none">
            <router-link to="/users" type="button"
                         class="inline-flex items-center justify-center rounded-md border border-transparent bg-green px-4 py-2 text-sm font-medium text-white shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 sm:w-auto">
                Zu allen Users
            </router-link>
        </div>
    </div>
    <div class="grid grid-cols-2 gap-4">
        <div class="box bg-white">
            <user-data :user="user" :editable="true" v-on:updated="updated" :errors="errors"></user-data>
            <search-select label="Rolle" :items="[{'name': 'admin'},{'name': 'office'}]" v-model="role" :item="role"
                           @change="changeRole($event)" @selected="selectedRole"></search-select>
            <p class="text-slate-500 text-sm mt-2">Der / die neue NutzerIn kann das sich mittels
                <router-link to="/forgot-password" class="text-indigo-600 hover:text-indigo-900">Passwort zurücksetzen</router-link> ein Passwort setzen.</p>
        </div>
    </div>
    <div class="text-center">
        <button type="button" @click="create"
                class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-green hover:bg-green-hover focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
            Daten speichern
        </button>
    </div>
</template>

<script>

import AddressVue from "../parts/Address";
import UserData from "../parts/UserData";
import SearchSelect from "../parts/SearchSelect";

export default {
    name: "UserCreate",
    components: {AddressVue, UserData, SearchSelect},
    data() {
        return {
            key: 0,
            errors: [],
            user: {
                email: ''
            },
            role: {
                name: 'office'
            }
        }
    },
    methods: {
        updated(user) {
            this.user = user;
            this.key++;
        },
        async create() {
            await axios.post('/api/user/', {
                ...this.user,
                role: this.role.name
            }).then(response => {
                this.user = response.data.user;
                this.errors = [];
                this.$notify({type: "success", text: 'Bearbeiten erfolgreich'});

                this.$router.replace('/users/');
            }).catch(errors => {
                console.log(errors);
                this.errors = errors.response.data.errors;
                this.$notify({type: "error", text: 'Es ist ein Fehler aufgetreten'});
            });
            this.key++;
        },
        selectedRole(role) {
            this.role = role;
        },
        changeRole(event) {
            this.role = event.originalTarget.value;
        },
    }
}
</script>

<style scoped>

</style>
