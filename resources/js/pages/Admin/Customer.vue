<template v-if="customer.hasOwnProperty('name')" :key="key">
    <div class="sm:flex sm:items-center">
        <div class="sm:flex-auto">
            <h1 class="page-title">{{ customer.name }}</h1>
        </div>
        <div class="mt-4 sm:mt-0 sm:ml-16 sm:flex-none">
            <router-link to="/customers" type="button"
                         class="inline-flex items-center justify-center rounded-md border border-transparent bg-green px-4 py-2 text-sm font-medium text-white shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 sm:w-auto">
                Zu allen Kunden
            </router-link>
        </div>
    </div>
    <customer-data :customer="customer" :errors="[]"></customer-data>
</template>

<script>
import CustomerData from "../../components/parts/CustomerData";

export default {
    name: "Customer",
    components: { CustomerData },
    data() {
      return {
          customer: {},
          key: 0
      }
    },
    methods: {
        async load() {
            await axios.get('/api/customer/' + this.$route.params.id).then(response => {
                this.customer = response.data.customer;
            });
            this.key++;
        }
    },
    created() {
        this.load();
    }
}
</script>

<style scoped>

</style>
