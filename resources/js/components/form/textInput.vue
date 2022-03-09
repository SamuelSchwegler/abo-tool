<template>
    <div>
        <label :for="name" class="block text-sm font-medium text-gray-700">{{ label }}</label>
        <div class="mt-1 relative rounded-md shadow-sm">
            <input type="text" :name="name" v-model="input" @input="onChanged"
                   class="focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md" />
        </div>
    </div>
</template>

<script>
export default {
    name: "textInput",
    props: ["label", "name", "value"],
    data() {
        return {
            input: this.value
        }
    },
    emits: ['update:modelValue'],
    setup(props, { emit }) {
        function onChanged(e) {
            emit('update:modelValue', e.currentTarget.value);
        }

        return {
            onChanged
        }
    }
}
</script>

<style scoped>

</style>
