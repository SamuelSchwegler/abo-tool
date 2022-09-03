<template>
    <div class="box bg-white" :key="key">
        <h3 class="title">Details Lieferzone</h3>
        <div class="grid grid-cols-1 gap-4">
            <div>
                <text-input name="name" label="Bezeichnung" v-model="name" :value="name"
                            :error="errors['name']" @change="updateDeliveryService"></text-input>
            </div>
            <div class="grid gap-4">
                <div class="relative flex items-start">
                    <div class="flex items-center h-5">
                        <input id="pickup" name="pickup"
                               type="checkbox" @click="togglePickup()"
                               :checked="pickup"
                               class="focus:ring-indigo-500 h-4 w-4 text-violet border-gray-300"/>
                    </div>
                    <div class="ml-3 text-sm">
                        <label for="pickup" class="font-medium text-gray-700">Abholbar</label>
                    </div>
                </div>
            </div>
            <div v-if="pickup">
                <text-input name="option_description" label="Beschrieb Abholoption" v-model="option_description"
                            :value="option_description"
                            :error="errors['option_description']" @change="updateDeliveryService"></text-input>
            </div>
            <fieldset>
                <legend class="sr-only">Liefertage</legend>
                <div class="space-y-3">
                    <label class="block text-sm font-medium text-gray-700">Liefertage</label>
                    <div class="grid grid-cols-2 gap-4">
                        <div v-for="day in days" :key="day.id" class="relative flex items-start">
                            <div class="flex items-center h-5">
                                <input :id="day.id" name="plan"
                                       type="checkbox" @click="toggleDays(day.id)"
                                       :checked="delivery_days.includes(day.id)"
                                       class="focus:ring-indigo-500 h-4 w-4 text-violet border-gray-300"/>
                            </div>
                            <div class="ml-3 text-sm">
                                <label :for="day.id" class="font-medium text-gray-700">{{ day.name }}</label>
                            </div>
                        </div>
                    </div>
                </div>
            </fieldset>
            <div>
                <text-input name="deadline_distance" label="Vorlauf ab Stichtag in Tagen" v-model="deadline_distance"
                            :value="deadline_distance" :error="errors['deadline_distance']"
                            @change="updateDeliveryService"></text-input>
            </div>
            <div>
                <text-input name="delivery_cost" label="Kosten / Lieferung in CHF" v-model="delivery_cost"
                            :value="delivery_cost"
                            :error="errors['delivery_cost']" @change="updateDeliveryService"></text-input>

            </div>
            <div class="col-span-1" v-if="service === null">
                <button class="btn bg-green w-full mt-6" @click="updateDeliveryService">Erstellen</button>
            </div>
        </div>
    </div>
    <div class="box bg-white">
        <p>Kommende Lieferungen</p>
        <div class="border-t border-gray-200 py-5 sm:p-0">
            <dl class="sm:divide-y sm:divide-gray-200">
                <div class="py-4 sm:py-2 sm:grid sm:grid-cols-4 sm:gap-3">
                    <dt class="text-sm font-medium text-gray-500">Liefertag</dt>
                    <dt class="text-sm font-medium text-gray-500">Stichtag</dt>
                    <dt class="text-sm font-medium text-gray-500">Aktiv</dt>
                    <dt class="text-sm font-medium text-gray-500">Link</dt>
                </div>
                <div class="py-4 sm:py-2 sm:grid sm:grid-cols-4 sm:gap-3"
                     v-for="(delivery, index) in unapproved_deliveries">
                    <dd v-if="delivery.approved" class="mt-1 text-sm text-gray-900 sm:mt-0">{{
                            delivery.date['d.m.Y']
                        }}
                    </dd>
                    <dd v-if="!delivery.approved" class="mt-1 text-sm text-gray-900 sm:mt-0">
                        <text-input @change="updateDelivery(delivery.id, $event)"
                                    :value="delivery.date['d.m.Y']"></text-input>
                    </dd>
                    <dd class="mt-1 text-sm text-gray-900 sm:mt-0">{{ delivery.deadline['d.m.Y'] }}</dd>
                    <dd class="mt-1 text-sm text-gray-900 sm:mt-0">
                        <toggle v-model="delivery.approved" @change="approveDelivery(delivery.id)"></toggle>
                    </dd>
                    <dd class="mt-1 text-sm text-gray-900 sm:mt-0">
                        <router-link :to="'/delivery/' + delivery.id" class="text-indigo-600 hover:text-indigo-900">
                            mehr
                        </router-link>
                    </dd>
                </div>
            </dl>
        </div>
    </div>
</template>

<script>

import TextInput from "../../../components/form/textInput";
import helpers from "../../../helpers";
import Toggle from "@vueform/toggle";

export default {
    name: "DeliveryServiceEdit",
    emits: ['stored'],
    props: {
        service: {
            type: Object,
            default: null
        }
    },
    components: {TextInput, Toggle},
    data() {
        return {
            s: this.service,
            pickup: (this.service !== null) ? this.service.pickup : false,
            name: (this.service !== null) ? this.service.name : '',
            days: [
                {id: 'mon', name: 'Montag'},
                {id: 'tue', name: 'Dienstag'},
                {id: 'wed', name: 'Mittwoch'},
                {id: 'thu', name: 'Donnerstag'},
                {id: 'fri', name: 'Freitag'},
                {id: 'sat', name: 'Samstag'},
                {id: 'sun', name: 'Sonntag'}
            ],
            delivery_days: (this.service !== null) ? this.service.days : [],
            deadline_distance: (this.service !== null) ? this.service.deadline_distance : 2,
            errors: [],
            unapproved_deliveries: (this.service !== null) ? this.service.unapproved_deliveries : [],
            delivery_cost: (this.service !== null) ? this.service.delivery_cost : 0,
            option_description: (this.service !== null) ? this.service.option_description : '',
            key: 0,
        }
    },
    methods: {
        async updateDeliveryService() {
            let data = {
                'name': this.name,
                'days': this.delivery_days,
                'deadline_distance': this.deadline_distance,
                'delivery_cost': this.delivery_cost,
                'pickup': this.pickup,
                'option_description': this.option_description
            }
            if (this.service === null) {
                await axios.post(`/api/delivery-service/`, data).then(response => {
                    this.name = response.data.service.name;
                    this.$emit('stored', response.data.service.id);
                    this.service = response.data.service;
                    this.errors = [];
                    this.$notify({type: "success", text: 'Erstellen erfolgreich'});
                }).catch(error => {
                    this.errors = error.response.data.errors;
                });
            } else {
                await axios.patch(`/api/delivery-service/` + this.service.id + `/`, data).then(response => {
                    this.name = response.data.service.name;
                    this.errors = [];
                    this.$notify({type: "success", text: 'Bearbeiten erfolgreich'});
                }).catch(error => {
                    this.errors = error.response.data.errors;
                });
            }
            this.key++;
        },
        async approveDelivery(id) {
            this.$axios.patch(`/api/delivery/${id}/toggle-approved`, {}).then(response => {
                this.delivery = response.data.delivery;
                this.$notify({type: "success", text: 'Bearbeiten erfolgreich'});
            }).catch(function (error) {
                console.log(error);
            });
        },
        async updateDelivery(id, event) {
            this.$axios.patch(`/api/delivery/${id}`, {
                date: event.target.value
            }).then(response => {
                this.s = response.data.delivery_service;
                this.$notify({type: "success", text: 'Bearbeiten erfolgreich'});
            }).catch(errors => {
                this.$notify({type: "error", text: 'Es ist ein Fehler aufgetreten'});
                console.log(errors);
            })
        },
        toggleDays(id) {
            this.delivery_days = helpers.toggleValueInArray(this.delivery_days, id);
            this.updateDeliveryService();
        },
        togglePickup() {
            this.pickup = !this.pickup;
            this.updateDeliveryService();
        }
    },
    watch: {
        s: function (newValue, old) {
            this.unapproved_deliveries = (newValue !== null) ? newValue.unapproved_deliveries : [];
        }
    }
}
</script>

<style scoped>

</style>
