<template>
    <tr  v-bind:class="{'bg-gray-200': order.deadline_passed, 'bg-red-200': !order.affordable}" :key="'order_key_' + order.id + '_' + key"
        v-bind:title="!order.affordable ? 'diese Bestellung ist Momentan nicht möglich, weil zu wenig Guthaben besteht.' : ''">
        <td class="border-b border-slate-100 dark:border-slate-700 px-4 py-1 text-slate-500 dark:text-slate-400">
            <Toggle class="h-5 w-5" v-model="running" @change="toggleCancel" :disabled="toggleDisabled"
                    :classes="{ toggleOnDisabled: 'bg-green-300'}"></Toggle>
        </td>
        <td class="border-b border-slate-100 dark:border-slate-700 px-4 py-1 text-slate-500 dark:text-slate-400">
            <template v-if="can('manage customers')">
                <router-link :to="'/delivery/' + order.delivery.id" class="text-indigo-600 hover:text-indigo-900">
                    {{ order.delivery.date['d.m.Y'] }}
                </router-link>
            </template>
            <template v-else>
                {{ order.delivery.date['d.m.Y'] }}
            </template>
        </td>
        <td v-if="multiple_products" class="border-b border-slate-100 dark:border-slate-700 px-4 py-1 text-slate-500 dark:text-slate-400">
            {{ order.product.name }}
        </td>
        <td class="border-b border-slate-100 dark:border-slate-700 px-4 py-1 text-slate-500 dark:text-slate-400">
            <text-input v-model="this.order.depository"  :value="this.order.depository" :disabled="toggleDisabled" @change="updateOrder"></text-input>
        </td>
        <td class="border-b border-slate-100 dark:border-slate-700 px-4 py-1 text-slate-500 dark:text-slate-400">
            {{ order.delivery.deadline['d.m.Y'] }}
        </td>
        <td v-if="can('manage customers')" class="border-b border-slate-100 dark:border-slate-700 px-4 py-1 text-slate-500 dark:text-slate-400">
            <text-input v-model="this.order.internal_comment"  :value="this.order.internal_comment" @change="updateOrder"></text-input>
        </td>
    </tr>
</template>

<script>
import Toggle from '@vueform/toggle'
import textInput from "../form/textInput";

export default {
    name: "Order",
    props: {
        input_order: Object,
        multiple_products: {
            type: Boolean,
            default: false
        }
    },
    emits: ['toggleOrder'],
    components: {
        Toggle, textInput
    },
    data: function () {
        return {
            order: this.input_order,
            running: !this.input_order.canceled,
            key: 0,
            toggleDisabled: this.input_order.deadline_passed && !this.can('manage customers')
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
                this.$notify({ type: "error", text: 'Es ist ein Fehler aufgetreten' });
            });
        },
        async updateOrder() {
            await axios.patch(`/api/order/` + this.order.id, {
                depository: this.order.depository,
                internal_comment: this.order.internal_comment
            }).then(response => {
                this.order = response.data.order;
                this.key++;
                this.$notify({ type: "success", text: 'Bearbeiten erfolgreich' });
            }).catch(error => {
                this.$notify({ type: "error", text: 'Es ist ein Fehler aufgetreten' });
            });
        }
    }
}
</script>
<style scoped>

</style>
