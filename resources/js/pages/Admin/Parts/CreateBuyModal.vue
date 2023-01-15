<!-- This example requires Tailwind CSS v2.0+ -->
<template>
    <TransitionRoot as="template" :show="open">
        <Dialog as="div" class="fixed z-10 inset-0 overflow-y-auto" @close="open = false">
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <TransitionChild as="template" enter="ease-out duration-300" enter-from="opacity-0"
                                 enter-to="opacity-100" leave="ease-in duration-200" leave-from="opacity-100"
                                 leave-to="opacity-0">
                    <DialogOverlay class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity"/>
                </TransitionChild>

                <!-- This element is to trick the browser into centering the modal contents. -->
                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
                <TransitionChild as="template" enter="ease-out duration-300"
                                 enter-from="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                                 enter-to="opacity-100 translate-y-0 sm:scale-100" leave="ease-in duration-200"
                                 leave-from="opacity-100 translate-y-0 sm:scale-100"
                                 leave-to="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95">
                    <div
                        class="relative inline-block align-bottom bg-white rounded-lg px-4 pt-5 pb-4 text-left shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full sm:p-6">
                        <div>
                            <div class="mt-3 sm:mt-5">
                                <DialogTitle as="h3" class="text-lg leading-6 font-medium text-grey-800">
                                    Rechnung erstellen
                                </DialogTitle>
                                <div class="mt-5 sm:mt-6 sm:grid sm:grid-cols-2 sm:gap-3 sm:grid-flow-row-dense">
                                    <div class="relative rounded-md shadow-sm sm:min-w-0 sm:flex-1">
                                        <dropdown-select label="Kunde" :items="customers" v-model="customer"></dropdown-select>
                                    </div>
                                    <div class="relative rounded-md shadow-sm sm:min-w-0 sm:flex-1">
                                        <dropdown-select label="Bundle" :items="bundles" v-model="bundle"></dropdown-select>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="mt-5 sm:mt-6 sm:grid sm:grid-cols-2 sm:gap-3 sm:grid-flow-row-dense">
                            <button type="button"
                                    class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-violet text-base font-medium text-white focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:col-start-2 sm:text-sm"
                                    @click="create">Erstellen
                            </button>
                            <button type="button"
                                    class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:col-start-1 sm:text-sm"
                                    @click="open = false" ref="cancelButtonRef">Abbrechen
                            </button>
                        </div>
                    </div>
                </TransitionChild>
            </div>
        </Dialog>
    </TransitionRoot>
</template>

<script>
import {Dialog, DialogOverlay, DialogTitle, TransitionChild, TransitionRoot} from '@headlessui/vue'
import {CheckIcon} from '@heroicons/vue/20/solid'
import DropdownSelect from "../../parts/DropdownSelect.vue";

export default {
    props: {
        show: Boolean,
    },
    components: {
        DropdownSelect,
        Dialog,
        DialogOverlay,
        DialogTitle,
        TransitionChild,
        TransitionRoot,
        CheckIcon,
    },
    data: function () {
        return {
            open: this.show,
            customers: [],
            bundles: [],
            customerSearchKey: 0,
            bundle: {},
            customer: {}
        }
    },
    methods: {
        create() {
            this.$axios.post('/api/buy', {
                bundle_id: this.bundle.id,
                product_id: this.bundle.product.id,
                customer_id: this.customer.id
            }).then(response => {
                this.open = false;
                this.$notify({type: "success", text: 'Erstellen erfolgreich'});
            }).catch(error => {
                this.$notify({type: "error", text: 'Es ist ein Fehler aufgetreten'});
            });
        },
        load() {
            this.$axios.get(`/api/customers/`)
                .then(response => {
                    this.customers = response.data.customers;
                })
                .catch(function (error) {
                    console.log(error);
                });

            this.$axios.get(`/api/bundles/`)
                .then(response => {
                    this.bundles = response.data.data;
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
