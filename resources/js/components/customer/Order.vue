<template>
    <tr  v-bind:class="{'bg-gray-200': order.deadline_passed}">
        <td class="border-b border-slate-100 dark:border-slate-700 p-4 pl-8 text-slate-500 dark:text-slate-400">
            <Toggle class="h-5 w-5" v-model="running" @change="toggleCancel" :disabled="order.deadline_passed"></Toggle>
        </td>
        <td class="border-b border-slate-100 dark:border-slate-700 p-4 pl-8 text-slate-500 dark:text-slate-400">
            {{ order.delivery.date['d.m.Y'] }}
        </td>
        <td>
            <text-input v-model="this.order.despository" :disabled="order.deadline_passed"></text-input>
        </td>
        <td class="border-b border-slate-100 dark:border-slate-700 p-4 pl-8 text-slate-500 dark:text-slate-400">
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
    components: {
        Toggle, textInput
    },
    data: function () {
        return {
            order: this.input_order,
            running: !this.input_order.canceled,
        }
    },
    methods: {
        async toggleCancel() {
            await axios.patch(`/api/v1/order/` + this.order.id + `/toggle-cancel/`).then(response => {
                this.order = response.data.order;
            }).catch(error => {

            });
        }
    }
}
</script>
<style src="@vueform/toggle/themes/default.css"></style>
<style scoped>

</style>
