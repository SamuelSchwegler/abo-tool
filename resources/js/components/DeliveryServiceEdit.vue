<template>
    <div class="box">
        <h3 class="title">Details Lieferzone</h3>
        <div id="add-postcode" class="grid grid-cols-2 gap-4">
            <text-input name="name" label="Bezeichnung" v-model="name" :value="name" @change="updateDeliveryService"></text-input>
        </div>
    </div>
</template>

<script>
import TextInput from "./form/textInput";

export default {
    name: "DeliveryServiceEdit",
    props: ["service"],
    components: {TextInput},
    data() {
        return {
            name: this.service.name
        }
    },
    methods: {
        async updateDeliveryService() {
            await axios.patch(`/api/v1/delivery-service/` + this.service.id + `/`, {
                'name': this.name
            }).then(response => {
                this.name = response.data.service.name;
            }).catch(error => {

            });
        }
    }
}
</script>

<style scoped>

</style>
