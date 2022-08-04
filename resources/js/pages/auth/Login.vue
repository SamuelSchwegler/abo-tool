<template>
    <div class="min-h-full flex flex-col justify-center py-12 sm:px-6 lg:px-8">
        <div class="sm:mx-auto sm:w-full sm:max-w-md">
            <img class="mx-auto h-24 w-auto" src="/img/gsh-signet.png" alt="Workflow"/>
            <h2 class="mt-6 text-center text-3xl font-extrabold text-gray-900">Anmelden mit Konto</h2>
            <p class="mt-2 text-center text-sm text-gray-600" v-if="error !== null">
                {{ error }}
            </p>
        </div>

        <div class="mt-8 sm:mx-auto sm:w-full sm:max-w-md">
            <div class="bg-white py-8 px-4 shadow sm:rounded-lg sm:px-10">
                <form class="space-y-6">
                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700"> Email </label>
                        <div class="mt-1">
                            <input id="email" v-model="email" name="email" type="email" autocomplete="email" required=""
                                   class="appearance-none block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"/>
                        </div>
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
            </div>
        </div>
    </div>
</template>

<script>
import {LockClosedIcon} from "@heroicons/vue/solid";

export default {
    name: "login",
    data() {
        return {
            email: "",
            password: "",
            error: null,
            redirect: this.$route.query.redirect ?? ''
        }
    },
    components: {
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
                    })
                        .then(response => {
                            if (response.data.success) {
                                if (this.redirect.length > 0) {
                                    this.$router.go('/' + this.redirect);
                                } else {
                                    this.$router.go('/')
                                }
                            } else {
                                this.error = response.data.message
                            }
                        })
                        .catch(function (error) {
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
