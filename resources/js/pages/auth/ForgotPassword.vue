<template>
    <div class="min-h-full flex flex-col justify-center py-12 sm:px-6 lg:px-8">
        <div class="sm:mx-auto sm:w-full sm:max-w-md">
            <img class="mx-auto h-24 w-auto" src="/img/gsh-signet.png" alt="Workflow"/>
            <h2 class="mt-6 text-center text-3xl font-extrabold text-gray-900">Passwort Zurücksetzen</h2>
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
                        <button @click="handleSubmit" type="submit"
                                class="w-full flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-green hover:bg-green-hover focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                            Zurücksetzen
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</template>

<script>
import {LockClosedIcon} from "@heroicons/vue/20/solid";

export default {
    name: "ForgotPassword",
    data() {
        return {
            email: "",
            error: null
        }
    },
    components: {
        LockClosedIcon
    },
    methods: {
        handleSubmit(e) {
            e.preventDefault()
            this.$axios.post('api/forgot-password', {
                email: this.email,
            })
                .then(response => {
                    this.error = response.data.message
                })
                .catch(function (error) {
                    console.error(error);
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
