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
    <div class="bg-white">
        <!-- Filters -->
        <section aria-labelledby="filter-heading">
            <h2 id="filter-heading" class="sr-only">Filters</h2>

            <div class="relative bg-white border-b border-gray-200 pb-4">
                <div class="max-w-7xl mx-auto px-4 flex items-center justify-between sm:px-6 lg:px-8">
                    <Menu as="div" class="relative inline-block text-left">
                        <div>
                            <MenuButton
                                class="group inline-flex justify-center text-sm font-medium text-gray-700 hover:text-gray-900">
                                Sortieren
                                <ChevronDownIcon
                                    class="flex-shrink-0 -mr-1 ml-1 h-5 w-5 text-gray-400 group-hover:text-gray-500"
                                    aria-hidden="true"/>
                            </MenuButton>
                        </div>

                        <transition enter-active-class="transition ease-out duration-100"
                                    enter-from-class="transform opacity-0 scale-95"
                                    enter-to-class="transform opacity-100 scale-100"
                                    leave-active-class="transition ease-in duration-75"
                                    leave-from-class="transform opacity-100 scale-100"
                                    leave-to-class="transform opacity-0 scale-95">
                            <MenuItems
                                class="origin-top-left absolute left-0 mt-2 w-40 rounded-md shadow-2xl bg-white ring-1 ring-black ring-opacity-5 focus:outline-none">
                                <div class="py-1">
                                    <MenuItem v-for="option in sortOptions" :key="option.name" v-slot="{ active }">
                                        <a :href="option.href" @click="updateFilter({id: 'order_by'}, option)"
                                           :class="[option.current ? 'font-medium text-gray-900' : 'text-gray-500', active ? 'bg-gray-100' : '', 'block px-4 py-2 text-sm']">
                                            {{ option.name }}
                                        </a>
                                    </MenuItem>
                                </div>
                            </MenuItems>
                        </transition>
                    </Menu>

                    <button type="button"
                            class="inline-block text-sm font-medium text-gray-700 hover:text-gray-900 sm:hidden"
                            @click="open = true">Filters
                    </button>

                    <div class="hidden sm:block">
                        <div class="flow-root">
                            <PopoverGroup class="-mx-4 flex items-center divide-x divide-gray-200">
                                <Popover v-for="(section, sectionIdx) in filters" :key="section.name"
                                         class="px-4 relative inline-block text-left">
                                    <PopoverButton
                                        class="group inline-flex justify-center text-sm font-medium text-gray-700 hover:text-gray-900">
                                        <span>{{ section.name }}</span>
                                        <span v-if="sectionIdx === 0"
                                              class="ml-1.5 rounded py-0.5 px-1.5 bg-gray-200 text-xs font-semibold text-gray-700 tabular-nums">
                                            {{ section.activeCount }}
                                        </span>
                                        <ChevronDownIcon
                                            class="flex-shrink-0 -mr-1 ml-1 h-5 w-5 text-gray-400 group-hover:text-gray-500"
                                            aria-hidden="true"/>
                                    </PopoverButton>

                                    <transition enter-active-class="transition ease-out duration-100"
                                                enter-from-class="transform opacity-0 scale-95"
                                                enter-to-class="transform opacity-100 scale-100"
                                                leave-active-class="transition ease-in duration-75"
                                                leave-from-class="transform opacity-100 scale-100"
                                                leave-to-class="transform opacity-0 scale-95">
                                        <PopoverPanel
                                            class="origin-top-right absolute right-0 mt-2 bg-white rounded-md shadow-2xl p-4 ring-1 ring-black ring-opacity-5 focus:outline-none">
                                            <form class="space-y-4">
                                                <div v-for="(option, optionIdx) in section.options" :key="option.value"
                                                     class="flex items-center">
                                                    <input :id="`filter-${section.id}-${optionIdx}`"
                                                           :name="`${section.id}[]`" :value="option.value"
                                                           type="checkbox" :checked="option.checked"
                                                           @click="updateFilter(section, option)"
                                                           class="h-4 w-4 border-gray-300 rounded text-indigo-600 focus:ring-indigo-500"/>
                                                    <label :for="`filter-${section.id}-${optionIdx}`"
                                                           class="ml-3 pr-6 text-sm font-medium text-gray-900 whitespace-nowrap">
                                                        {{ option.label }}
                                                    </label>
                                                </div>
                                            </form>
                                        </PopoverPanel>
                                    </transition>
                                </Popover>
                            </PopoverGroup>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
</template>

<script>
import {ref} from 'vue'
import {
    Dialog,
    DialogOverlay,
    Disclosure,
    DisclosureButton,
    DisclosurePanel,
    Menu,
    MenuButton,
    MenuItem,
    MenuItems,
    Popover,
    PopoverButton,
    PopoverGroup,
    PopoverPanel,
    TransitionChild,
    TransitionRoot,
} from '@headlessui/vue'
import {XIcon} from '@heroicons/vue/outline'
import {ChevronDownIcon} from '@heroicons/vue/solid'

const sortOptions = [
    {value: 'date', name: 'Datum', href: '#', current: true},
    {value: 'deadline', name: 'Deadline', href: '#', current: false},
]

export default {
    components: {
        Dialog,
        DialogOverlay,
        Disclosure,
        DisclosureButton,
        DisclosurePanel,
        Menu,
        MenuButton,
        MenuItem,
        MenuItems,
        Popover,
        PopoverButton,
        PopoverGroup,
        PopoverPanel,
        TransitionChild,
        TransitionRoot,
        ChevronDownIcon,
        XIcon,
    },
    props: ['delivery_services', 'filter'],
    data() {
        let delivery_options = [];
        let activeCount = 0;

        for (let i = 0; i < this.delivery_services.length; i++) {
            let service = this.delivery_services[i];
            let checked = (this.filter.delivery_service_ids.includes(service.id) || this.filter.delivery_service_ids.length === 0);
            activeCount += checked;

            delivery_options.push({
                value: service.id,
                label: service.name,
                checked: checked,
            });
        }

        let filters = [
            {
                id: 'delivery_service_ids',
                name: 'Lieferservice',
                options: delivery_options,
                activeCount: activeCount
            }
        ]

        return {
            filters
        }
    },
    methods: {
        updateFilter(section, option) {
            this.$emit('filter', {
                section: section.id,
                value: option.value,
                checked: !option.checked
            });
        }
    },
    setup() {
        const open = ref(false)

        return {
            sortOptions,
            open,
        }
    },
}
</script>
