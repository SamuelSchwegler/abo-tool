<template>
    <div class="min-h-screen bg-default">
        <navigation :key="'nav_key_' + navKey"></navigation>
        <!-- Page Content -->
        <main class="container mx-auto">
            <div class="py-12">
                <div class="max-w-7xl mx-auto px-6 lg:px-8">
                    <router-view @authentication="authentication($event)"/>
                </div>
            </div>
            <notifications position="bottom right"></notifications>
        </main>
    </div>
</template>

<script>
import Navigation from "./pages/parts/Navigation";

export default {
    name: "App",
    components: {Navigation},
    data() {
        return {
            isLoggedIn: false,
            navKey: 0
        }
    },
    methods: {
        authentication(user) {
            if(user.hasOwnProperty('email')) {
                this.isLoggedIn = true;
                window.Laravel.isLoggedIn = true;
                this.navKey++;
            }
        }
    },
    created() {
        if (window.Laravel.isLoggedIn) {
            this.isLoggedIn = true
        }
    }
}
</script>
