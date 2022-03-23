<template>
    <tr :key="key">
        <td class="border-b border-slate-100 dark:border-slate-700 p-4 text-slate-500 dark:text-slate-400">
            {{ buy.id }}
        </td>
        <td class="border-b border-slate-100 dark:border-slate-700 p-4 text-slate-500 dark:text-slate-400">
            {{ buy.customer.name }}
        </td>
        <td class="border-b border-slate-100 dark:border-slate-700 p-4 text-slate-500 dark:text-slate-400 text-right">
            {{ buy.price }} CHF
        </td>
        <td class="border-b border-slate-100 dark:border-slate-700 p-4 text-slate-500 dark:text-slate-400">
            <Toggle v-model="paid" @change="updateBuy"></Toggle>
        </td>
        <td class="border-b border-slate-100 dark:border-slate-700 p-4 text-slate-500 dark:text-slate-400">
            {{ buy.created['d.m.Y']}}
        </td>
        <td class="border-b border-slate-100 dark:border-slate-700 p-4 text-slate-500 dark:text-slate-400 text-right">
            {{ buy.age }}
        </td>
        <td class="border-b border-slate-100 dark:border-slate-700 p-4 text-slate-500 dark:text-slate-400">
            <a target="_blank" :href="'/buy/' + buy.id + '/bill'" title="PDF Download"
               class="btn inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest active:bg-gray-900 focus:outline-none focus:border-gray-900 focus:ring ring-gray-300 disabled:opacity-25 transition ease-in-out duration-150">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24"
                     stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round"
                          d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                </svg>
            </a>
        </td>
    </tr>
</template>

<script>
import Toggle from "@vueform/toggle";

export default {
    name: "BuyPayment",
    props: ['input_buy'],
    components: {
        Toggle
    },
    data: function () {
        return {
            buy: this.input_buy,
            paid: this.input_buy.paid,
            key: 0
        }
    },
    methods: {
        async updateBuy() {
            await axios.patch(`/api/v1/buy/` + this.buy.id, {
                paid: this.paid
            }).then(response => {
                this.buy = response.data.buy;
                this.$notify({type: "success", text: 'Bearbeiten erfolgreich'});
            }).catch(error => {
                this.$notify({type: "danger", text: 'Es ist ein Fehler aufgetreten'});
            });

            this.key++;
        }
    }
}
</script>

<style src="@vueform/toggle/themes/default.css"></style>
<style scoped>

</style>
