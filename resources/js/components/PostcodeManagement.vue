<template>
    <div class="box">
        <h3 class="title">Postleitzahlen</h3>
        <div class="postcodes">
            <span class="inline-block whitespace-nowrap rounded-full bg-violet text-sm text-white px-4 py-2 mr-2 mb-2" v-for="(postcode, index) in postcodes">{{ postcode.postcode }}</span>
        </div>
        <hr class="my-4">
        <div id="add-postcode" class="grid grid-cols-5 gap-4">
            <div class="col-span-3">
                <text-input name="postcode" label="PLZ hinzufügen" v-model="postcodeToAdd"></text-input>
            </div>
            <div class="col-span-2 pt-6">
                <button class="btn w-full" @click="addPostcode">Hinzufügen</button>
            </div>
        </div>
    </div>
</template>

<script>
import TextInput from "./form/textInput";
export default {
    components: {TextInput},
    props: ['service'],
    name: "PostcodeManagement",
    data() {
      return {
          postcodeToAdd: '',
          postcodes: this.service.postcodes
      }
    },
    methods: {
        async addPostcode() {
            await axios.post(`/api/v1/delivery-service/` + this.service.id + `/add/`, {
                'postcode': this.postcodeToAdd
            }).then(response => {
                this.postcodes = response.data.service.postcodes;
            }).catch(error => {

            });
        }
    }
}
</script>

<style scoped>
    .postcodes {
        min-height: 100px;
    }
</style>
