<template>
    <div>
        <label :for="name" v-bind:class="{'text-red-500': hasError}"
               class="block text-sm font-medium text-gray-700">{{ label }}</label>
        <div class="mt-1 relative rounded-md shadow-sm">
            <input :type="type" :name="name" v-model="input" @input="onChanged"
                   v-bind:class="{'border-red-500': hasError}" v-bind:readonly="readonly"
                   class="focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300
                   rounded-md read-only:bg-gray-100"/>
            <div v-if="suffix.length > 0" class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                <span class="text-gray-500 sm:text-sm" id="price-currency"> {{ suffix }} </span>
            </div>
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
