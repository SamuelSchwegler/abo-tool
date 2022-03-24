<template>
    <div>
        <label :for="name" v-bind:class="{'text-red-500': hasError}"
               class="block text-sm font-medium text-gray-700">{{ label }}</label>
        <div class="mt-1 relative rounded-md shadow-sm">
            <input type="text" :name="name" v-model="input" @input="onChanged"
                   v-bind:class="{'border-red-500': hasError}"
                   class="focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md"/>
        </div>
        <span class="flex items-center font-medium tracking-wide text-red-500 text-xs mt-1 ml-1" v-if="hasError">
			<span v-for="e in error">{{ e }}</span>
		</span>
    </div>
</template>

<script>
export default {
    name: "textInput",
    props: {
        label: {
            type: String
        }, name: {
            type: String
        }, value: {
            type: String
        }, error: {
            type: Object,
            default: function () {
                return {}
            }
        }
    },
    data() {
        return {
            input: this.value,
            hasError: Object.keys(this.error).length > 0
        }
    },
    emits: ['update:modelValue'],
    setup(props, {emit}) {
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
