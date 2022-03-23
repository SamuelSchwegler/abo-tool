<template>
    <div class="grid gap-4 grid-cols-2 pb-4">
        <div>
            <text-input name="first_name" :value="customer.first_name" label="Vorname"></text-input>
        </div>
        <div>
            <text-input name="last_name" :value="customer.last_name" label="Nachname"></text-input>
        </div>
        <div>
            <text-input name="company_name" :value="customer.company_name" label="Firma"></text-input>
        </div>
        <div>
            <text-input name="phone" :value="customer.phone" label="Telefon"></text-input>
        </div>
    </div>
    <div>

    </div>
</template>

<script>
import TextInput from "../form/textInput";

export default {
    name: "Customer",
    components: {
        TextInput
    },

    props: ["customer"]
}
</script>

<style scoped>

</style>
