<template>
    <div class="grid grid-cols-5 gap-8" :key="serviceKey">
        <div class="box">
            <ul>
                <li v-for="(s, index) in services" v-bind:class="{'bg-violet': s.id === service.id, 'bg-green': s.id !== service.id}" class="btn mb-2" >
                    <a class="block w-full" @click="switchService(s.id)">{{ s.name }}</a>
                </li>
                <li class="btn" v-bind:class="{'bg-violet': inCreate, 'bg-green': !inCreate}">
                    <a class="block w-full" @click="createService">Hinzufügen</a>
                </li>
            </ul>
        </div>
        <div class="col-span-2">
            <delivery-service-edit :service="service" v-if="!inCreate"></delivery-service-edit>
            <delivery-service-create v-on:stored="loadService" v-if="inCreate"></delivery-service-create>
        </div>
        <div class="col-span-2">
            <postcode-management :service="service" v-if="!inCreate"></postcode-management>
        </div>
    </div>
</template>

<script>
import PostcodeManagement from "./Parts/PostcodeManagement";
import DeliveryServiceEdit from "./Parts/DeliveryServiceEdit";
import DeliveryServiceCreate from "./Parts/DeliveryServiceCreate";

export default {
    name: "DeliveryServices",
    components: {
        DeliveryServiceEdit, PostcodeManagement, DeliveryServiceCreate
    },
    data() {
        return {
            services: [],
            service: {
                name: ''
            },
            serviceKey: 0,
            inCreate: false,
        }
    },
    methods: {
        switchService(id) {
            this.service = this.services.filter(s => s.id === id)[0];
            this.serviceKey++;
        },
        createService() {
            this.service = {
                name: ''
            };
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
                    this.service = this.services[0];
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
