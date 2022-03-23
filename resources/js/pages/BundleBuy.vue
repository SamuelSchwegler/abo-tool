<template>
    <div class="grid grid-cols-2 gap-4">
        <div class="box">
            <h3 class="title">Ihre Bestellung</h3>
            <bundle :bundle="bundle" v-if="bundle.hasOwnProperty('id')"></bundle>
        </div>
        <div class="box">

            <span v-if="isLoggedIn">
                <customer :customer="user.customer"></customer>
            </span>
            <span v-else>Nicht eingeloggt</span>
        </div>
    </div>
</template>

<script>
import Bundle from "../components/Bundle";
import Customer from "../components/parts/Customer";

export default {
    name: "BundleBuy",
    components: {
        Bundle, Customer
    },

    data: function () {
        return {
            bundle: {},
            isLoggedIn: window.Laravel.isLoggedIn,
            user: window.Laravel.user
        }
    },
    created() {
        this.$axios.get(`/api/bundle/${this.$route.params.id}`)
            .then(response => {
                this.bundle = response.data.data;
            })
            .catch(function (error) {
                console.error(error);
            });

    },
}
</script>

<style scoped>

</style>
