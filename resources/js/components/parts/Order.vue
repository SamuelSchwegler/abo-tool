<template>
    <tr  v-bind:class="{'bg-gray-200': order.deadline_passed}" :key="'order_key_' + order.id + '_' + key">
        <td class="border-b border-slate-100 dark:border-slate-700 px-4 py-1 text-slate-500 dark:text-slate-400">
            <Toggle class="h-5 w-5" v-model="running" @change="toggleCancel" :disabled="order.deadline_passed"
                    :classes="{ toggleOnDisabled: 'bg-green-300'}"></Toggle>
        </td>
        <td class="border-b border-slate-100 dark:border-slate-700 px-4 py-1 text-slate-500 dark:text-slate-400">
            {{ order.delivery.date['d.m.Y'] }}
        </td>
        <td class="border-b border-slate-100 dark:border-slate-700 px-4 py-1 text-slate-500 dark:text-slate-400">
            <text-input v-model="this.order.depository"  :value="this.order.depository" :disabled="order.deadline_passed" @change="updateOrder"></text-input>
        </td>
        <td class="border-b border-slate-100 dark:border-slate-700 px-4 py-1 text-slate-500 dark:text-slate-400">
            {{ order.delivery.deadline['d.m.Y'] }}
        </td>
    </tr>
</template>

<script>
import Toggle from '@vueform/toggle'
import textInput from "../form/textInput";

export default {
    name: "Order",
    props: ["input_order"],
    emits: ['toggleOrder'],
    components: {
        Toggle, textInput
    },
    data: function () {
        return {
            order: this.input_order,
            running: !this.input_order.canceled,
            key: 0
        }
    },
    methods: {
        async toggleCancel() {
            await axios.patch(`/api/order/` + this.order.id + `/toggle-cancel/`).then(response => {
                this.order = response.data.order;
                let text = !this.running ? 'Sie haben sich von der Lieferung abgemeldet' : 'Sie haben Sich für die Lieferung angemeldet';
                this.$notify({ type: "success", text: text });
                this.$emit('toggleOrder', {product_id: this.order.product.id, running: this.running});
                this.key++;
            }).catch(error => {
                console.log(error);
                this.$notify({ type: "danger", text: 'Es ist ein Fehler aufgetreten' });
            });
        },
        async updateOrder() {
            await axios.patch(`/api/order/` + this.order.id, {
                depository: this.order.depository
            }).then(response => {
                this.order = response.data.order;
                this.key++;
                this.$notify({ type: "success", text: 'Bearbeiten erfolgreich' });
            }).catch(error => {
                this.$notify({ type: "danger", text: 'Es ist ein Fehler aufgetreten' });
            });
        }
    }
}
</script>
<style scoped>

</style>
