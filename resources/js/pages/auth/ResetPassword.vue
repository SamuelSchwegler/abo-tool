<template>
    <auth-layout title="Passwort zurücksetzen">
        <p class="mt-2 text-center text-sm text-gray-600" v-if="error !== null">
            {{ error }}
        </p>
        <form class="space-y-6">
            <div>
                <text-input v-model="email" :value="email" name="email" label="Email"></text-input>
            </div>
            <div>
                <label for="password" class="block text-sm font-medium text-gray-700"> Neues Passwort </label>
                <div class="mt-1">
                    <input id="password" v-model="password" name="password" type="password"
                           autocomplete="current-password" required=""
                           class="appearance-none block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"/>
                </div>
            </div>
            <div>
                <label for="password" class="block text-sm font-medium text-gray-700"> Passwort
                    wiederholung </label>
                <div class="mt-1">
                    <input id="password_confirmation" v-model="password_confirmation" name="password_confirmation" type="password"
                           autocomplete="current-password" required=""
                           class="appearance-none block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"/>
                </div>
            </div>
            <div>
                <button @click="handleSubmit" type="submit"
                        class="w-full flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-green hover:bg-green-hover focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                    Zurücksetzen
                </button>
            </div>
        </form>
    </auth-layout>
</template>

<script>
import {LockClosedIcon} from "@heroicons/vue/20/solid";
import AuthLayout from "./AuthLayout.vue";
import TextInput from "../../components/form/textInput.vue";

export default {
    name: "PasswordReset",
    data() {
        return {
            email: "",
            password: "",
            password_confirmation: "",
            error: null,
            token: "",
            signet_path: new URL('/img/logo-gsh_header.png', import.meta.url).href
        }
    },
    mounted() {
        this.token = this.$route.params.token;
        this.email = this.$route.query.email;
    },
    components: {
        TextInput,
        AuthLayout,
        LockClosedIcon
    },
    methods: {
        handleSubmit(e) {
            e.preventDefault();
            axios.post('/api/reset-password', {
                email: this.email,
                password: this.password,
                password_confirmation: this.password_confirmation,
                token: this.token
            }).then(response => {
                if(response.data.success) {
                    this.$router.push('/login');
                }
                this.error = response.data.message
            }).catch(error => {
                this.error = error.response.data.message
            });
        }
    },
    beforeRouteEnter(to, from, next) {
        if (window.Laravel.isLoggedIn) {
            return next('dashboard');
        }
        next();
    }
}
</script>
