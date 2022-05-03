<template>
    <div class="grid gap-4 md:grid-cols-2 grid-cols-1 pb-4" :key="key">
        <div>
            <text-input name="email" :value="u.email" v-model="u.email" label="Email" :error="errors['email']" :readonly="!editable" @change="update"></text-input>
        </div>
    </div>
</template>

<script>
import TextInput from "../../components/form/textInput";

export default {
    name: "UserData",
    components: {
        TextInput
    },
    props: {
        user: {
            type: Object
        },
        errors: {
            type: Array,
            default: function () {
                return [];
            }
        },
        editable: {
            type: Boolean,
            default: false
        }
    },
    emits: ['updated'],
    data() {
        return {
            u: this.user,
            key: 0
        }
    },
    methods: {
        update() {
            this.$emit('updated', this.u);
        },
    }
}
</script>

<style scoped>

</style>
