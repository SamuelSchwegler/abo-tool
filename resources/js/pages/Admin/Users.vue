<!-- This example requires Tailwind CSS v2.0+ -->
<template>
    <div class="sm:flex sm:items-center">
        <div class="sm:flex-auto">
            <h1 class="page-title">User</h1>
            <p class="mt-2 text-sm text-gray-700">
                Zusammenfassung aller Users.
                <span v-if="search.length > 0">({{users.length}} von {{total_count}} werden Angezeigt)</span>
            </p>
        </div>
        <div class="mt-4 sm:mt-0 sm:ml-16 sm:flex-none">
            <router-link type="button" to="/user"
                         class="inline-flex items-center justify-center rounded-md border border-transparent bg-green px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 sm:w-auto">
                Neuer User erstellen
            </router-link>
        </div>
    </div>
    <div class="mt-8 flex flex-col">
        <div class="box bg-white">
            <div class="mt-1">
                <text-input name="search" :value="search" v-model="search" label="Suche"></text-input>
            </div>
        </div>
        <div class="-my-2 -mx-4 overflow-x-auto sm:-mx-6 lg:-mx-8">
            <div class="inline-block min-w-full py-2 align-middle md:px-6 lg:px-8">
                <div class="overflow-hidden shadow ring-1 ring-black ring-opacity-5 md:rounded-lg">
                    <table class="min-w-full divide-y divide-gray-300">
                        <thead class="bg-gray-50">
                        <tr>
                            <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Email</th>
                            <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Rolle</th>
                            <th></th>
                            <th></th>
                        </tr>
                        </thead>
                        <tbody class="bg-white">
                        <user-row v-for="user in users" :key="users.email" :user="user" />
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</template>

<script>

import CreateBuyConfirmModal from "./Parts/CreateBuyConfirmModal.vue";
import TextInput from "../../components/form/textInput.vue";
import UserRow from "./Parts/UserRow.vue";

export default {
    name: "Users",
    components: {CreateBuyConfirmModal, TextInput, UserRow},

    data() {
        return {
            users: [],
            showCreateBuyConfirmModal: false,
            createBuyConfirmModalCustomer: {},
            createBuyConfirmModalProduct: {},
            createBuyConfirmModalKey: 0,
            search: '',
            total_count: 0,
            deleteModalKey: 0,
            open_delete: false,
            loadIndex: 0
        }
    },
    emits: ['authentication'],
    methods: {
        async load() {
            this.loadIndex++;
            const startIndex = this.loadIndex;

            let params = {
                roles: ['admin', 'office']
            };

            if(this.search.length > 0) {
                params.search = this.search;
            }

            await axios.get('/api/users', {
                params: params
            }).then((response) => {
                if(startIndex === this.loadIndex) {
                    this.users = response.data.users;
                    this.total_count = response.data.total_count;
                }
            }).catch((error) => {
                console.log(error);
            }).finally(() => {

            })
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
