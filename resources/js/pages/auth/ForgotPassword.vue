<template>
    <auth-layout title="Passwort vergessen">
        <form class="space-y-6">
            <div>
                <text-input v-model="email" label="Email" :error="error.email"></text-input>
            </div>
            <div>
                <vue-button @click="handleSubmit" color="green" class="w-full">
                    Zurücksetzen
                </vue-button>
            </div>
        </form>
    </auth-layout>
</template>

<script>
import {LockClosedIcon} from "@heroicons/vue/20/solid";
import AuthLayout from "./AuthLayout.vue";
import VueButton from "../../components/form/button.vue";
import TextInput from "../../components/form/textInput.vue";

export default {
    name: "ForgotPassword",
    data() {
        return {
            email: "",
            error: [],
        }
    },
    components: {
        TextInput,
        VueButton,
        AuthLayout,
        LockClosedIcon
    },
    methods: {
        handleSubmit(e) {
            e.preventDefault()
            this.$axios.post('api/forgot-password', {
                email: this.email,
            }).then(response => {
            }).catch(error => {
                this.error = error.response.data.errors;
            });
        }
    }
}
</script>
