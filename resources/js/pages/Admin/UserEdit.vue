<template v-if="user.hasOwnProperty('roles')">
    <div class="sm:flex sm:items-center mb-4">
        <div class="sm:flex-auto">
            <h1 class="page-title">User bearbeiten</h1>
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
            <user-data :user="user" :editable="true" v-on:updated="updated" :errors="errors"
                       :key="'data_' + user.id"></user-data>
            <search-select label="Rolle" :items="[{'name': 'admin'},{'name': 'office'}]" v-model="role"
                           v-if="user.hasOwnProperty('roles') && user.roles.length > 0"
                           :item="role" :key="'role_' + user.id"
                           @change="changeRole($event)" @selected="selectedRole"></search-select>
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
import UserData from "../parts/UserData.vue";
import SearchSelect from "../parts/SearchSelect.vue";

export default {
    name: "UserEdit",
    data() {
        return {
            user: {},
            role: {}
        }
    },
    components: {
        UserData, SearchSelect
    },
    methods: {
        async load() {
            await this.$axios.get(`/api/user/${this.$route.params.id}`)
                .then(response => {
                    this.user = response.data.user;
                    this.role = { ...this.user.roles[0] };
                })
                .catch(function (error) {
                    console.log(error);
                });
        },
        updated(user) {
            this.user = user;
            this.key++;
        },
        async update() {
            await axios.patch('/api/user/' + this.user.id, {
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
        }
    },
    created() {
        this.load();
    }
}
</script>

<style scoped>

</style>
