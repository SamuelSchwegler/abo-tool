<template>
    <div>
        <text-input name="email" v-model="credentials.email" label="Email"
                    :error="errors['email']"></text-input>
    </div>
    <div>
        <text-input name="password" v-model="credentials.password" label="Passwort"
                    :error="errors['password']"></text-input>
    </div>
</template>

<script>
import TextInput from "../form/textInput";

export default {
    name: "LoginCredentials",
    props: ['credentials', 'errors'],
    components: {
        TextInput
    }
}
</script>

<style scoped>

</style>
