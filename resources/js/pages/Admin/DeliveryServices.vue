<template>
    <div class="sm:flex sm:items-center mb-4">
        <div class="sm:flex-auto">
            <h1 class="page-title">Lieferzonen</h1>
        </div>
    </div>
    <div class="grid grid-cols-5 gap-8" :key="serviceKey">
        <div class="box bg-white">
            <ul>
                <li v-for="(s, index) in services" v-bind:class="{'bg-violet': (service !== null && s.id === service.id), 'bg-green': (service === null || s.id !== service.id)}" class="btn mb-2 cursor-pointer" >
                    <a class="block w-full" @click="switchService(s.id)">{{ s.name }}</a>
                </li>
                <li class="btn cursor-pointer" v-bind:class="{'bg-violet': inCreate, 'bg-green': !inCreate}">
                    <a class="block w-full" @click="createService">Hinzufügen</a>
                </li>
            </ul>
        </div>
        <div class="col-span-2">
            <delivery-service-edit :service="service" v-on:stored="loadService"></delivery-service-edit>
        </div>
        <div class="col-span-2" v-if="service !== null && !service.pickup">
            <postcode-management :service="service"></postcode-management>
        </div>
    </div>
</template>

<script>
import PostcodeManagement from "./Parts/PostcodeManagement";
import DeliveryServiceEdit from "./Parts/DeliveryServiceEdit";

export default {
    name: "DeliveryServices",
    components: {
        DeliveryServiceEdit, PostcodeManagement
    },
    data() {
        return {
            services: [],
            service: null,
            serviceKey: 0,
            inCreate: false,
        }
    },
    methods: {
        switchService(id) {
            this.service = this.services.filter(s => s.id === id)[0] ?? null;
            this.inCreate = false;
            this.serviceKey++;
        },
        createService() {
            this.service = null;
            this.inCreate = true;
            this.serviceKey++;
        },
        async loadService(id) {
            console.log(id);
            this.inCreate = false;
            await this.load();
            this.switchService(id);
        },
        async load() {
            await this.$axios.get(`/api/delivery-services/`)
                .then(response => {
                    this.services = response.data.services;
                    if(this.$route.params.hasOwnProperty('id')) {
                        this.service = this.services.filter(service => service.id === parseInt(this.$route.params.id))[0];
                    } else {
                        this.service = this.services[0] ?? null;
                    }
                    this.serviceKey++;
                })
                .catch(function (error) {
                    console.log(error);
                });
        }
    },
    created() {
        this.load();
    }
}
</script>

<style scoped>

</style>
