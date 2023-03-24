
<template>
    <div>
        <label :for="name" v-bind:class="{'text-red-500': hasError}"
               class="block text-sm font-medium text-gray-700">{{ label }}</label>
        <div :class="'mt-1 relative rounded-md shadow-sm ' + inputClass">
            <textarea id="message" rows="4" @input="onChanged"
                      class="block p-2.5 pt-2 w-full text-sm text-gray-900 rounded-lg border border-gray-300 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                      v-model="input">
            </textarea>
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
            type: [String, Number]
        }, error: {
            type: Object,
            default: function () {
                return {}
            }
        },
        readonly: {
            type: Boolean,
            default: false
        },
        type: {
            type: String,
            default: "text"
        },
        suffix: {
            type: String,
            default: ''
        },
        inputClass: {
            type: String,
            default: ''
        },

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
    },
    watch: {
        error: function (newValue, old){
            this.hasError = Object.keys(newValue).length > 0
        },
        value: function (newValue) {
            this.input = newValue;
        }
    }
}
</script>

<style scoped>

</style>
