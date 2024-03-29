<!--
  This example requires Tailwind CSS v2.0+

  This example requires some changes to your config:

  ```
  // tailwind.config.js
  module.exports = {
    // ...
    plugins: [
      // ...
      require('@tailwindcss/forms'),
    ],
  }
  ```
-->
<template>
    <Combobox as="div" v-model="selectedItem">
        <label v-if="label.length > 0"
               class="block text-sm font-medium text-gray-700">{{ label }}</label>
        <div class="relative mt-1">
            <ComboboxInput
                class="w-full rounded-md border border-gray-300 bg-white py-2 pl-3 pr-10 shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500 sm:text-sm"
                @change="changed($event)" :display-value="(query) => query !== undefined ? query.name : ''"/>
            <ComboboxButton class="absolute inset-y-0 right-0 flex items-center rounded-r-md px-2 focus:outline-none">
                <ArrowsUpDown class="h-5 w-5 text-gray-400" aria-hidden="true"/>
            </ComboboxButton>

            <ComboboxOptions v-if="filteredItems.length > 0"
                             class="absolute z-10 mt-1 max-h-60 w-full overflow-auto rounded-md bg-white py-1 text-base shadow-lg ring-1 ring-black ring-opacity-5 focus:outline-none sm:text-sm">
                <ComboboxOption v-for="item in filteredItems" :key="item.id" :value="item" as="template"
                                v-slot="{ active, selected }">
                    <li :class="['relative cursor-default select-none py-2 pl-3 pr-9', active ? 'bg-indigo-600 text-white' : 'text-gray-900']">
            <span :class="['block truncate', selected && 'font-semibold']" v-if="item.hasOwnProperty('name')">
              {{ item.name }}
            </span>

                        <span v-if="selected"
                              :class="['absolute inset-y-0 right-0 flex items-center pr-4', active ? 'text-white' : 'text-indigo-600']">
              <CheckIcon class="h-5 w-5" aria-hidden="true"/>
            </span>
                    </li>
                </ComboboxOption>
            </ComboboxOptions>
        </div>
    </Combobox>
</template>

<script>
import {computed, ref} from 'vue'
import {ArrowsUpDownIcon, CheckIcon} from '@heroicons/vue/20/solid'
import {
    Combobox,
    ComboboxButton,
    ComboboxInput,
    ComboboxLabel,
    ComboboxOption,
    ComboboxOptions,
} from '@headlessui/vue'

export default {
    components: {
        CheckIcon,
        Combobox,
        ComboboxButton,
        ComboboxInput,
        ComboboxLabel,
        ComboboxOption,
        ComboboxOptions,
        ArrowsUpDownIcon,
    },
    setup(props) {
        const items = props.items;
        const query = ref('')
        const selectedItem = ref()
        const filteredItems = computed(() =>
            query.value === ''
                ? items
                : items.filter((item) => {
                    return item.name.toLowerCase().includes(query.value.toLowerCase())
                })
        )

        return {
            query,
            selectedItem,
            filteredItems,
        }
    },
    name: "SearchSelect",
    props: {
        items: {
            type: Array,
            default: function () {
                return [];
            }
        },
        item: {
            type: [String, Object],
            default: ""
        },
        label: {
            type: String,
            default: ""
        }
    },
    emits: ['selected'],
    methods: {
        changed(event) {
            this.query = event.target.value;
        },
    },
    watch: {
        selectedItem: function (newValue, old){
            this.$emit('selected',newValue.name);
        },
        query: function (newValue, old){
            this.$emit('selected',newValue);
        }
    }
}
</script>
