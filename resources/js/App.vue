<template>
    <div class="min-h-screen bg-default">
        <navigation></navigation>
        <!-- Page Content -->
        <main class="container mx-auto">
            <div class="py-12">
                <div class="max-w-7xl mx-auto px-6 lg:px-8">
                    <router-view/>
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
        }
    },
    created() {
        if (window.Laravel.isLoggedIn) {
            this.isLoggedIn = true
        }
    }
}
</script>
