<!-- This example requires Tailwind CSS v2.0+ -->
<template>
    <div class="rounded-md bg-violet p-4">
        <div class="flex">
            <div class="flex-shrink-0">
                <InformationCircleIcon class="h-5 w-5 text-white" aria-hidden="true" />
            </div>
            <div class="ml-3 flex-1 md:flex md:justify-between">
                <p class="text-sm text-white">{{ text }}</p>
                <p class="mt-3 text-sm md:mt-0 md:ml-6" v-if="detailsExists">
                    <a href="#" class="whitespace-nowrap font-medium text-blue-700 hover:text-blue-600">Details <span aria-hidden="true">&rarr;</span></a>
                </p>
            </div>
        </div>
    </div>
</template>

<script>
import { InformationCircleIcon } from '@heroicons/vue/20/solid'

export default {
    components: {
        InformationCircleIcon,
    },
    props: ["text"],
    name: "Alert",
    setup() {
        return {
            detailsExists: false
        }
    }
}
</script>
