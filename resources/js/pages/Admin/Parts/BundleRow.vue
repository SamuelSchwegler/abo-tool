<template>
    <tr v-if="show" :key="bundleKey">
        <td class="border-b border-slate-100 whitespace-nowrap px-3 py-4 text-sm text-gray-500">
            {{ b.name }}
        </td>
        <td class="border-b border-slate-100 whitespace-nowrap px-3 py-4 text-sm text-gray-500">
            <text-input v-model="b.title" :value="b.title" name="title" @change="update"></text-input>
        </td>
        <td class="border-b border-slate-100 whitespace-nowrap px-3 py-4 text-sm text-gray-500">
            {{ b.product.name }}
        </td>
        <td class="border-b border-slate-100 whitespace-nowrap px-3 py-4 text-sm text-gray-500">
            {{ b.trial ? 'Ja' : 'Nein' }}
        </td>
        <td class="border-b border-slate-100 whitespace-nowrap px-3 py-4 text-sm text-gray-500 text-right">
            {{ b.formatted_price }}
        </td>
        <td class="border-b border-slate-100 whitespace-nowrap px-3 py-4 text-sm text-gray-500">
            {{ b.deliveries }}
        </td>
        <td class="border-b border-slate-100 whitespace-nowrap px-3 py-4 text-sm text-gray-500 text-right">
            {{ b.price_per_delivery }}
        </td>
        <td class="border-b border-slate-100 whitespace-nowrap px-3 py-4 text-sm text-gray-500">
            <text-input v-model="b.short_description" :value="b.short_description" name="title"
                        @change="update"></text-input>
        </td>
        <td class="border-b border-slate-100 dark:border-slate-700 p-2 text-slate-500 dark:text-slate-400 text-right">
            <button @click="orderDown" disabled title="Diese Funktion kommt in der Zukunft"
                    class="btn inline-flex items-center px-4 py-2 bg-violet border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest active:bg-gray-900 focus:outline-none focus:border-gray-900 focus:ring ring-gray-300 disabled:opacity-25 transition ease-in-out duration-150">
                <ChevronDownIcon class="h-6 w-6"></ChevronDownIcon>
            </button>
        </td>
        <td class="border-b border-slate-100 dark:border-slate-700 p-2 text-slate-500 dark:text-slate-400 text-right">
            <button @click="orderUp" disabled title="Diese Funktion kommt in der Zukunft"
                    class="btn inline-flex items-center px-4 py-2 bg-violet border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest active:bg-gray-900 focus:outline-none focus:border-gray-900 focus:ring ring-gray-300 disabled:opacity-25 transition ease-in-out duration-150">
                <ChevronUpIcon class="h-6 w-6"></ChevronUpIcon>
            </button>
        </td>
        <td class="border-b border-slate-100 dark:border-slate-700 p-2 text-slate-500 dark:text-slate-400 text-right">
            <button @click="deleteBundle" disabled title="Diese Funktion kommt in der Zukunft"
                    class="btn inline-flex items-center px-4 py-2 bg-red-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest active:bg-gray-900 focus:outline-none focus:border-gray-900 focus:ring ring-gray-300 disabled:opacity-25 transition ease-in-out duration-150">
                <TrashIcon class="h-6 w-6"></TrashIcon>
            </button>
            <delete-modal :key="'delete_modal_' + deleteModalKey" :show="open_delete"
                          :route="`/api/bundle/` + b.id"
                          v-on:deleted="deleted"></delete-modal>
        </td>
    </tr>
</template>

<script>
import CreateBuyConfirmModal from "./CreateBuyConfirmModal.vue";
import TextInput from "../../../components/form/textInput.vue";
import DeleteModal from "../../parts/DeleteModal.vue";
import {ChevronDownIcon, ChevronUpIcon, TrashIcon} from "@heroicons/vue/20/solid";

export default {
    name: "BundleRow",
    components: {CreateBuyConfirmModal, TextInput, DeleteModal, TrashIcon, ChevronDownIcon, ChevronUpIcon},
    props: {
        bundle: Object
    },
    data() {
        return {
            bundleKey: 0,
            deleteModalKey: 0,
            open_delete: false,
            show: true,
            b: this.bundle
        }
    },
    methods: {
        async update() {
            axios.patch('/api/bundle/' + this.b.id, {
                ...this.b
            }).then(response => {
                this.b = response.data.bundle;
                this.$notify({type: "success", text: 'Bearbeiten erfolgreich'});
            }).catch(errors => {
                this.$notify({type: "danger", text: 'Fehler beim Bearbeiten'});
            }).finally(() => {
                this.bundleKey++;
            });
        },
        async deleteBundle() {
            this.open_delete = true;
            this.deleteModalKey++;
        },
        async orderDown() {

        },
        async orderUp() {

        },
        deleted() {
            this.show = false;
        }
    }
}
</script>

<style scoped>

</style>
