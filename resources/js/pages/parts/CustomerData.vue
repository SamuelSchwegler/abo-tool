<template>
    <div class="grid gap-4 grid-cols-2 pb-4" :key="key">
        <div>
            <text-input name="first_name" :value="c.first_name" v-model="c.first_name" label="Vorname" :error="errors['first_name']" @change="update"></text-input>
        </div>
        <div>
            <text-input name="last_name" :value="c.last_name" v-model="c.last_name" label="Nachname" :error="errors['last_name']"></text-input>
        </div>
        <div>
            <text-input name="company_name" :value="c.company_name" v-model="c.company_name" label="Firma" :error="errors['company_name']"></text-input>
        </div>
        <div>
            <text-input name="phone" :value="c.phone" v-model="c.phone" label="Telefon" :error="errors['phone']"></text-input>
        </div>
    </div>

</template>

<script>

import TextInput from "../../components/form/textInput";

export default {
    name: "CustomerData",
    components: {
        TextInput
    },
    props: {
      customer: {
          type: Object
      },
      errors: {
          type: Array,
          default: function () {
              return [];
          }
      },
      editable: {
          type: Boolean,
          default: false
      }
    },
    emits: ['updated'],
    data() {
        return {
            c: this.customer,
            key: 0
        }
    },
    methods: {
        async update() {
            if(this.editable) {
                await axios.patch('/api/customer/' + this.$route.params.id, {
                    first_name: this.c.first_name,
                    last_name: this.c.last_name,
                    company_name: this.c.company_name,
                    phone: this.c.phone
                }).then(response => {
                    this.c = response.data.customer;

                    this.$emit('updated', this.c);
                }).catch(errors => {
                    console.log(errors);
                });
            }
        },
    }
}
</script>

<style scoped>

</style>
