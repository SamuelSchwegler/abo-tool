<template>
    <div class="sm:flex sm:items-center">
        <div class="sm:flex-auto">
            <h1 class="page-title">Lieferung</h1>
        </div>
        <div class="mt-4 sm:mt-0 sm:ml-16 sm:flex-none">
            <router-link to="/deliveries" type="button"
                         class="inline-flex items-center justify-center rounded-md border border-transparent bg-violet px-4 py-2 text-sm font-medium text-white shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 sm:w-auto">
                Zu allen Lieferungen
            </router-link>
        </div>
    </div>
    <div class="grid gap-4 grid-cols-3 py-4" v-if="delivery.hasOwnProperty('id')">
        <div class="box bg-white">
            <h3 class="title">Lieferdienst</h3>
            <div class="border-t border-gray-200 py-5 sm:p-0">
                <dl class="sm:divide-y sm:divide-gray-200">
                    <div class="py-4 sm:py-5 sm:grid sm:grid-cols-3 sm:gap-3">
                        <dt class="text-sm font-medium text-gray-500">Liederdienst</dt>
                        <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">{{
                                delivery.delivery_service.name
                            }}
                        </dd>
                    </div>
                    <div class="py-4 sm:py-5 sm:grid sm:grid-cols-3 sm:gap-3">
                        <dt class="text-sm font-medium text-gray-500">Datum</dt>
                        <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">{{ delivery.date['d.m.Y'] }}</dd>
                    </div>
                    <div class="py-4 sm:py-5 sm:grid sm:grid-cols-3 sm:gap-3">
                        <dt class="text-sm font-medium text-gray-500">Stichtag</dt>
                        <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">{{
                                delivery.deadline['d.m.Y']
                            }}
                        </dd>
                    </div>
                    <div class="py-4 sm:py-5 sm:grid sm:grid-cols-3 sm:gap-3" v-if="delivery.orders.length > 0">
                        <dt class="text-sm font-medium text-gray-500">Download</dt>
                        <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                            <ul role="list" class="border border-gray-200 rounded-md divide-y divide-gray-200">
                                <li class="pl-3 pr-4 py-3 flex items-center justify-between text-sm">
                                    <div class="w-0 flex-1 flex items-center">
                                        <PaperClipIcon class="flex-shrink-0 h-5 w-5 text-gray-400" aria-hidden="true"/>
                                        <span class="ml-2 flex-1 w-0 truncate"> lieferschein.zip </span>
                                    </div>
                                    <div class="ml-4 flex-shrink-0">
                                        <a :href="'/export/delivery-notes/' + $route.params.id"
                                           class="font-medium text-indigo-600 hover:text-indigo-500"> Lieferscheine </a>
                                    </div>
                                </li>
                                <li class="pl-3 pr-4 py-3 flex items-center justify-between text-sm">
                                    <div class="w-0 flex-1 flex items-center">
                                        <PaperClipIcon class="flex-shrink-0 h-5 w-5 text-gray-400" aria-hidden="true"/>
                                        <span class="ml-2 flex-1 w-0 truncate"> adressen.xlsx </span>
                                    </div>
                                    <div class="ml-4 flex-shrink-0">
                                        <a :href="'/export/delivery-addresses/' + $route.params.id"
                                           class="font-medium text-indigo-600 hover:text-indigo-500"> Adressen </a>
                                    </div>
                                </li>
                            </ul>
                        </dd>
                    </div>
                    <div class="py-4 sm:py-5 sm:grid sm:grid-cols-3 sm:gap-3">
                        <dt class="text-sm font-medium text-gray-500">Produkte</dt>
                        <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                            <div v-for="product in delivery.summary">
                                {{product.name}}: {{product.count}}
                            </div>
                        </dd>
                    </div>
                </dl>
            </div>
        </div>
        <div class="box bg-white col-span-2">
            <h3 class="title">Bestellungen</h3>
            <table class="min-w-full divide-y divide-gray-300" v-if="delivery.orders.length > 0">
                <thead class="bg-gray-50">
                <tr>
                    <th scope="col" class="py-3.5 pl-4 pr-3 text-left text-sm font-semibold text-gray-900 sm:pl-6">
                        Name
                    </th>
                    <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Produkt</th>
                    <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Abstellort</th>
                </tr>
                </thead>
                <tbody class="bg-white">
                <tr v-for="(order, orderIdx) in delivery.orders" :key="order.id"
                    :class="orderIdx % 2 === 0 ? undefined : 'bg-gray-50'">
                    <td class="whitespace-nowrap py-4 pl-4 pr-3 text-sm font-medium text-gray-900 sm:pl-6">
                        <router-link :to="'/customer/' + order.customer.id"
                                     class="text-indigo-600 hover:text-indigo-900">
                            {{ order.customer.name }}
                        </router-link>
                    </td>
                    <td class="whitespace-nowrap px-3 py-3.5 text-sm font-medium text-gray-900">{{
                            order.product.name
                        }}
                    </td>
                    <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">{{ order.depository }}</td>
                </tr>
                </tbody>
            </table>
            <p v-else>Momentan gibt es noch keine Bestellungen für diese Lieferung.</p>
        </div>
    </div>
    <div class="box bg-white">
        <delivery-product-items v-for="(product_items, product) in delivery.items" :delivery-items="product_items" :key="'delivery_items_' + deliveryItemsKey"></delivery-product-items>
    </div>
</template>

<script>
import {PaperClipIcon} from "@heroicons/vue/solid";
import DeliveryProductItems from "./DeliveryProductItems";

export default {
    name: "Delivery",
    components: {
        PaperClipIcon, DeliveryProductItems
    },
    data() {
        return {
            delivery: {},
            deliveryItemsKey: 0
        }
    },
    methods: {
        load() {
            this.$axios.get(`/api/delivery/${this.$route.params.id}`)
                .then(response => {
                    this.delivery = response.data.delivery;
                    this.deliveryItemsKey++;
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
