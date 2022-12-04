<template>
    <tr v-if="show">
        <td class="border-b border-slate-100 whitespace-nowrap px-3 py-4 text-sm text-gray-500">
            <a :href="'mailto:' + user.email" class="text-indigo-600 hover:text-indigo-900">
                {{ user.email }}
            </a>
        </td>
        <td class="border-b border-slate-100 whitespace-nowrap px-3 py-4 text-sm text-gray-500">
            <span v-for="role in user.roles">{{role.name}}</span>
        </td>
        <td class="border-b border-slate-100 whitespace-nowrap px-3 py-4 text-sm text-gray-500 text-right">
            <router-link :to="'/user/' + user.id" class="text-indigo-600 hover:text-indigo-900">
                Bearbeiten
            </router-link>
        </td>
        <td class="border-b border-slate-100 dark:border-slate-700 p-2 text-slate-500 dark:text-slate-400 text-right">
            <button @click="deleteUser"
                    class="btn inline-flex items-center px-4 py-2 bg-red-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest active:bg-gray-900 focus:outline-none focus:border-gray-900 focus:ring ring-gray-300 disabled:opacity-25 transition ease-in-out duration-150">
                <TrashIcon class="h-6 w-6"></TrashIcon>
            </button>
            <delete-modal :key="'delete_modal_' + deleteModalKey" :show="open_delete" :route="`/api/user/` + user.id"
                          v-on:deleted="deleted"></delete-modal>
        </td>
    </tr>
</template>

<script>
import CreateBuyConfirmModal from "./CreateBuyConfirmModal";
import TextInput from "../../../components/form/textInput";
import DeleteModal from "../../parts/DeleteModal";
import {TrashIcon} from "@heroicons/vue/20/solid";

export default {
    name: "UserRow",
    components: {CreateBuyConfirmModal, TextInput, DeleteModal, TrashIcon},
    props: {
        user: Object
    },
    data() {
        return {
            deleteModalKey: 0,
            open_delete: false,
            show: true
        }
    },
    methods: {
        async deleteUser() {
            this.open_delete = true;
            this.deleteModalKey++;
        },
        deleted() {
            this.show = false;
        }
    }
}
</script>

<style scoped>

</style>
