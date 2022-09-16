<template>
    <tr :key="key" v-if="show">
        <td class="border-b border-slate-100 dark:border-slate-700 p-4 text-slate-500 dark:text-slate-400">
            {{ buy.bill_number }}
        </td>
        <td v-if="show_customer"
            class="border-b border-slate-100 dark:border-slate-700 p-4 text-slate-500 dark:text-slate-400">
            <router-link :to="'/customer/' + buy.customer.id" class="text-indigo-600 hover:text-indigo-900">
                {{ buy.customer.name }}
            </router-link>
        </td>
        <td v-if="show_customer"
            class="border-b border-slate-100 dark:border-slate-700 p-4 text-slate-500 dark:text-slate-400">
            <router-link :to="'/customer/' + buy.customer.id + '/buys'" class="text-indigo-600 hover:text-indigo-900">
                <ArchiveBoxIcon class="h-6 w-6"></ArchiveBoxIcon>
            </router-link>
        </td>
        <td class="border-b border-slate-100 dark:border-slate-700 p-4 text-slate-500 dark:text-slate-400">
            {{ buy.bundle.name }}
        </td>
        <td class="border-b border-slate-100 dark:border-slate-700 p-4 text-slate-500 dark:text-slate-400">
            <text-input v-model="buy.discount" :value="buy.discount" suffix="%" input-class="w-16"
                        @change="updateBuy"></text-input>
        </td>
        <td class="border-b border-slate-100 dark:border-slate-700 p-4 text-slate-500 dark:text-slate-400 text-right">
            CHF {{ buy.total_cost }}
        </td>
        <td class="border-b border-slate-100 dark:border-slate-700 p-4 text-slate-500 dark:text-slate-400">
            <Toggle v-model="paid" @change="updateBuy"></Toggle>
        </td>
        <td class="border-b border-slate-100 dark:border-slate-700 p-4 text-slate-500 dark:text-slate-400">
            {{ buy.created['d.m.Y'] }}
        </td>
        <td class="border-b border-slate-100 dark:border-slate-700 p-4 text-slate-500 dark:text-slate-400 text-right">
            {{ buy.age }}
        </td>
        <td class="border-b border-slate-100 dark:border-slate-700 p-2 text-slate-500 dark:text-slate-400">
            <a target="_blank" :href="'/export/buy/' + buy.id + '/bill'" title="PDF Download"
               class="btn inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest active:bg-gray-900 focus:outline-none focus:border-gray-900 focus:ring ring-gray-300 disabled:opacity-25 transition ease-in-out duration-150">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24"
                     stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round"
                          d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                </svg>
            </a>
        </td>
        <td v-if="allow_delete"
            class="border-b border-slate-100 dark:border-slate-700 p-2 text-slate-500 dark:text-slate-400">
            <button @click="deleteBuy" v-bind:disabled="paid"
                    v-bind:title="paid ? 'Löschen ist nur bei unbezahlten Rechnungen möglich': ''"
                    class="btn inline-flex items-center px-4 py-2 bg-red-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest active:bg-gray-900 focus:outline-none focus:border-gray-900 focus:ring ring-gray-300 disabled:opacity-25 transition ease-in-out duration-150">
                <TrashIcon class="h-6 w-6"></TrashIcon>
            </button>
            <delete-modal :key="'delete_modal_' + deleteModalKey" :show="open_delete" :route="`/api/buy/` + this.buy.id" v-on:deleted="deleted"></delete-modal>
        </td>
    </tr>
</template>

<script>
import Toggle from "@vueform/toggle";
import TextInput from "../../../components/form/textInput";
import {ArchiveBoxIcon, TrashIcon} from "@heroicons/vue/20/solid";
import DeleteModal from "../../parts/DeleteModal";

export default {
    name: "BuyPayment",
    props: {
        input_buy: {
            type: Object
        },
        show_customer: {
            type: Boolean,
            default: true
        },
        allow_delete: {
            type: Boolean,
            default: false
        }
    },
    components: {
        Toggle, TextInput, ArchiveBoxIcon, TrashIcon, DeleteModal
    },
    data: function () {
        return {
            buy: this.input_buy,
            paid: this.input_buy.paid,
            key: 0,
            show: true,
            open_delete: false,
            deleteModalKey: 0
        }
    },
    methods: {
        async updateBuy() {
            await axios.patch(`/api/buy/` + this.buy.id, {
                paid: this.paid,
                discount: this.buy.discount
            }).then(response => {
                this.buy = response.data.buy;
                this.$notify({type: "success", text: 'Bearbeiten erfolgreich'});
            }).catch(error => {
                this.$notify({type: "error", text: 'Es ist ein Fehler aufgetreten'});
            });

            this.key++;
        },
        deleteBuy() {
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
