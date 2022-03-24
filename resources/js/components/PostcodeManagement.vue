<template>
    <div class="box">
        <h3 class="title">Postleitzahlen</h3>
        <div class="postcodes">
            <span class="postcode inline-block rounded-full bg-violet text-sm text-white px-4 py-2 mr-2 mb-2"
                  v-for="(postcode, index) in postcodes">
                <span class="name whitespace-nowrap ">{{ postcode.postcode }}</span>
                <button class="pl-4 delete" @click="removePostcode(postcode.postcode)">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                      <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                    </svg>
                </button>
            </span>
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
            await axios.post(`/api/delivery-service/` + this.service.id + `/add/`, {
                'postcode': this.postcodeToAdd
            }).then(response => {
                this.postcodes = response.data.service.postcodes;
            }).catch(error => {

            });
        },
        async removePostcode(postcode) {
            await axios.post(`/api/delivery-service/` + this.service.id + `/remove/`, {
                'postcode': postcode
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
