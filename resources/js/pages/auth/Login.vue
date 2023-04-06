<template>
    <auth-layout title="Anmelden mit Konto">
        <p class="mt-2 text-center text-sm text-gray-600" v-if="error !== null">
            {{ error }}
        </p>
        <form class="space-y-6">
            <div>
                <text-input v-model="email" label="Email"></text-input>
            </div>
            <div>
                <label for="password" class="block text-sm font-medium text-gray-700"> Passwort </label>
                <div class="mt-1">
                    <input id="password" v-model="password" name="password" type="password"
                           autocomplete="current-password" required=""
                           class="appearance-none block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"/>
                </div>
            </div>

            <div class="flex items-center justify-between">
                <div class="text-sm">
                    <router-link to="/forgot-password"
                                 class="font-medium text-indigo-600 hover:text-indigo-500"> Passwort Vergessen?
                    </router-link>
                </div>
            </div>

            <div>
                <button @click="handleSubmit" type="submit"
                        class="w-full flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-green hover:bg-green-hover focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                    Anmelden
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
    name: "login",
    data() {
        return {
            email: "",
            password: "",
            error: null,
            redirect: this.$route.query.redirect ?? '',
            signet_path: new URL('/img/logo-gsh_header.png', import.meta.url).href
        }
    },
    components: {
        TextInput,
        AuthLayout,
        LockClosedIcon
    },
    methods: {
        handleSubmit(e) {
            e.preventDefault()
            if (this.password.length > 0) {
                this.$axios.get('/sanctum/csrf-cookie').then(response => {
                    this.$axios.post('api/login', {
                        email: this.email,
                        password: this.password,
                    }).then(response => {
                        if (response.data.success) {
                            if (this.redirect.length > 0) {
                                this.$router.go('/' + this.redirect);
                            } else {
                                this.$router.go('/')
                            }
                        } else {
                            this.error = response.data.message
                        }
                    }).catch(function (error) {
                        console.error(error);
                    });
                })
            }
        }
    },
    beforeRouteEnter(to, from, next) {
        if (window.Laravel.isLoggedIn) {
            if (to.query.hasOwnProperty('redirect')) {
                return next('/' + to.query.redirect);
            }
            return next('/');
        }
        next();
    }
}
</script>
