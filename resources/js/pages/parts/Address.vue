<template>
    <div class="grid gap-4 grid-cols-4 pb-4" :key="key">
        <div class="col-span-4">
            <text-input :name="prefix + 'street'" v-model="address.street" :value="address.street" label="Strasse"
                        :error="errors['street']" @change="updated"></text-input>
        </div>
        <div class="col-span-1">
            <text-input :name="prefix + 'postcode'" v-model="address.postcode" :value="address.postcode" label="PLZ"
                        @change="postcodeChanged" :error="errors['postcode']"></text-input>
        </div>
        <div class="col-span-3">
            <text-input :name="prefix + 'city'" v-model="address.city" :value="address.city" label="Ort"
                        :error="errors['city']" @change="updated"></text-input>
        </div>
    </div>
</template>

<script>

import textInput from "../../components/form/textInput.vue";

export default {
    name: "Address",
    props: {
        address: {
            type: Object,
            default: function () {
                return {};
            }
        },
        errors: {
            type: Object,
            default: function () {
                return {};
            }
        },
        prefix: {
            type: String,
            default: ''
        }
    },
    data() {
        return {
            key: 0
        }
    },
    components: {
        textInput
    },
    emits: ['updated', 'postcodeChanged'],
    methods: {
        postcodeChanged() {
            delete this.errors['postcode'];
            this.updated();
            this.$emit('postcodeChanged', this.address.postcode);
        },
        updated() {
            this.$emit('updated', this.address);
        }
    },
    watch: {
        errors: function (value, old) {
            this.key++;
        }
    }
}
</script>

<style scoped>

</style>
