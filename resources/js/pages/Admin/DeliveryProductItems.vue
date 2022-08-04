<template>
    <div class="max-w-md mx-auto sm:max-w-3xl">
        <div>
            <div class="text-center">
                <h2 class="mt-2 text-lg font-medium text-gray-900">Lieferinhalte für {{ deliveryItems.name }}</h2>
                <p class="mt-1 text-sm text-gray-500" v-if="items.length === 0">Momentan wurden noch keine Produkte der
                    Lieferung hinzugefügt</p>
                <p class="mt-1 text-sm text-gray-500" v-if="!overall && synced">
                    Wenn hier Inhalte geändert werden, sind sie nicht mehr synchronisiert mit dem
                    <router-link :to="'/deliveries/' + dateFormat['Y-m-d']"
                                 class="text-indigo-600 hover:text-indigo-900">
                        {{ dateFormat['d.m.Y'] }}
                    </router-link>
                    .
                </p>
            </div>
            <div class="mt-6 sm:flex sm:items-center">
                <label for="items" class="sr-only">Produkt</label>
                <div class="relative rounded-md shadow-sm sm:min-w-0 sm:flex-1">
                    <search-select @change="changeItem($event)" @selected="selectedItem" :items="allItems" :value="item"
                                   :key="'item-search_' + itemSearchKey"></search-select>
                </div>
                <div class="mt-3 sm:mt-0 sm:ml-4 sm:flex-shrink-0">
                    <button @click="addItem()"
                            class="block w-full text-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-green focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                        Hinzufügen
                    </button>
                </div>
            </div>
        </div>
        <div class="my-10">
            <ul role="list" class="mt-4 grid grid-cols-1 gap-4 sm:grid-cols-2">
                <li v-for="(item, itemIdx) in items" :key="itemIdx">
                    <button type="button"
                            v-bind:class="{'bg-indigo-200': synced}"
                            class="group p-2 w-full flex items-center justify-between rounded-full border border-gray-300 shadow-sm space-x-3 text-left hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
            <span class="min-w-0 flex-1 flex items-center space-x-3">
              <span class="block min-w-0 flex-1 pl-4">
                <span class="block text-sm font-medium text-gray-900 truncate">{{ item.name }}</span>
              </span>
            </span>
                        <span class="flex-shrink-0 h-10 w-10 inline-flex items-center justify-center">
              <MinusIcon class="h-5 w-5 text-gray-400 group-hover:text-gray-500" aria-hidden="true"
                         @click="removeItem(item.id)"/>
            </span>
                    </button>
                </li>
            </ul>
        </div>
    </div>
    <template v-if="synced && !overall">
        <hr class="my-4">
        <router-link :to="'/deliveries/' + dateFormat['Y-m-d']" class="text-indigo-600 hover:text-indigo-900">
            Lieferinhalte sind synchronisiert
        </router-link>
    </template>
</template>

<script>
import {MinusIcon, ShoppingBagIcon} from '@heroicons/vue/solid'
import SearchSelect from "../parts/SearchSelect";

export default {
    props: {
        deliveryItems: {
            type: Object,
            default: function () {
                return {
                    items: [],
                    synced: false,
                    dateFormat: Object
                };
            }
        },
        overall: {
            type: [Boolean],
            default: false
        }
    },
    components: {
        MinusIcon, ShoppingBagIcon, SearchSelect
    },
    data: function () {
        return {
            items: this.deliveryItems.items,
            synced: this.deliveryItems.synced,
            dateFormat: this.deliveryItems.dateFormat,
            item: "",
            allItems: [],
            itemSearchKey: 0,
        }
    },
    methods: {
        changeItem(event) {
            this.item = event.originalTarget.value;
        },
        selectedItem(item) {
            this.item = item;
        },
        async addItem() {
            if (this.item.length > 0) {
                let route = "";
                if (!this.overall) {
                    route = '/api/delivery/' + this.$route.params.id + '/' + this.deliveryItems.product_id + '/item/';
                    this.synced = false;
                } else {
                    route = '/api/deliveries/' + this.dateFormat['Y-m-d'] + '/' + this.deliveryItems.product_id + '/item/'
                }

                await axios.post(route, {
                    item: this.item
                }).then(response => {
                    if (!this.overall) {
                        let items = response.data.delivery.items.filter(item => item.product_id === this.deliveryItems.product_id)[0];
                        this.items = items.items;
                        this.synced = items.synced;
                    } else {
                        this.items = response.data.items;
                    }

                    this.item = "";
                    this.itemSearchKey++;

                }).catch(errors => {
                    console.log(errors);
                })
            }

        },
        async removeItem(item_id) {
            let route = "";
            if (!this.overall) {
                route = '/api/delivery/' + this.$route.params.id + '/' + this.deliveryItems.product_id + '/item/' + item_id
            } else {
                route = '/api/deliveries/' + this.dateFormat['Y-m-d'] + '/' + this.deliveryItems.product_id + '/item/' + item_id
            }

            await axios.delete(route).then(response => {
                if (!this.overall) {
                    let items = response.data.delivery.items.filter(item => item.product_id === this.deliveryItems.product_id)[0];
                    this.items = items.items;
                    this.synced = items.synced;
                } else {
                    this.items = response.data.items;
                    this.synced = response.data.synced;
                }
            }).catch(errors => {
                console.log(errors);
            })
        },
        async load() {
            await axios.get('/api/items').then(response => {
                this.allItems = response.data.data;
                this.itemSearchKey++;
            }).catch(errors => {
                console.log(errors);
            })
        }
    },
    created() {
        this.load();
    },
    name: "DeliveryItems"
}
</script>
