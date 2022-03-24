<template>
    <div class="box">
        <h3 class="title">Details Lieferzone</h3>
        <div id="add-postcode" class="grid grid-cols-3 gap-4">
            <div class="col-span-2">
                <text-input name="name" label="Bezeichnung" v-model="name" :value="name"></text-input>
            </div>
            <div class="col-span-1">
                <button class="btn" @click="store">Erstellen</button>
            </div>
        </div>
    </div>
</template>

<script>
import TextInput from "./form/textInput";

export default {
    name: "DeliveryServiceCreate",
    components: {TextInput},
    emits: ['stored'],
    data() {
        return {
            name: ''
        }
    },
    methods: {
        async store() {
            await axios.post(`/api/delivery-service/`, {
                'name': this.name
            }).then(response => {
                this.$emit('stored', response.data.service.id);
            }).catch(error => {

            });
        }
    }
}
</script>

<style scoped>

</style>
