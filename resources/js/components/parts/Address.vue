<template>
    <div class="grid gap-4 grid-cols-4 pb-4">
        <div class="col-span-4">
            <text-input name="street" :value="address.street" label="Strasse"></text-input>
        </div>
        <div class="col-span-1">
            <text-input name="postcode" :value="address.postcode" label="PLZ"></text-input>
        </div>
        <div class="col-span-3">
            <text-input name="city" :value="address.city" label="Stadt"></text-input>
        </div>
    </div>
</template>

<script>
import textInput from "../form/textInput";

export default {
    name: "Address",
    props: ['address'],
    components: {
        textInput
    }
}
</script>

<style scoped>

</style>
