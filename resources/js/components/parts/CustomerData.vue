<template>
    <div class="grid gap-4 grid-cols-2 pb-4">
        <div>
            <text-input name="first_name" :value="c.first_name" v-model="c.first_name" label="Vorname" :error="errors['first_name']"></text-input>
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
import TextInput from "../form/textInput";

export default {
    name: "CustomerData",
    components: {
        TextInput
    },

    props: ["customer", "errors"],
    data() {
        return {
            c: this.customer
        }
    }
}
</script>

<style scoped>

</style>
