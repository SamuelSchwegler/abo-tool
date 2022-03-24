<template>
    <div class="grid gap-4 grid-cols-4 pb-4">
        <div class="col-span-4">
            <text-input name="street" v-model="address.street" :value="address.street" label="Strasse" :error="errors['street']"></text-input>
        </div>
        <div class="col-span-1">
            <text-input name="postcode" v-model="address.postcode" :value="address.postcode" label="PLZ" :error="errors['postcode']"></text-input>
        </div>
        <div class="col-span-3">
            <text-input name="city" v-model="address.city" :value="address.city" label="Stadt" :error="errors['city']"></text-input>
        </div>
    </div>
</template>

<script>
import textInput from "../form/textInput";

export default {
    name: "Address",
    props: ['address', "errors"],
    components: {
        textInput
    }
}
</script>

<style scoped>

</style>
