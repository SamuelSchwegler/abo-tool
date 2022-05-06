<!-- This example requires Tailwind CSS v2.0+ -->
<template>
    <footer class="bg-footer">
        <div class="container max-w-7xl mx-auto py-12 px-4 sm:px-6 md:flex md:items-center md:justify-between lg:px-8">
            <div class="flex justify-center space-x-6 md:order-2">
                <a v-for="item in navigation" :key="item.name" :href="item.href" class="text-gray-600 hover:text-gray-400">
                    <span class="sr-only">{{ item.name }}</span>
                    <component :is="item.icon" class="h-6 w-6" aria-hidden="true" />
                </a>
            </div>
            <div class="mt-8 md:mt-0 md:order-1">
                <p class="text-left text-base text-gray-600">Informationen zum Gemüse-Abo auf
                    <a class="text-gray-800 hover:text-gray-500" target="_blank" href="https://gartenbauschule-
huenibach.ch/Angebote/Gemueseabo">unserer Website</a>
                </p>
                <p class="text-left text-base text-gray-600">&copy; 2022 Gartenbauschule Hünibach
                    <a class="text-gray-800 hover:text-gray-500" href="mailto:gemueseabo@gsh.ch">gemueseabo@gsh.ch</a>
                </p>
            </div>
        </div>
    </footer>
</template>

<script>
import {LinkIcon} from "@heroicons/vue/solid";

const navigation = [
    {
        name: 'Gartenbauschule',
        href: 'https://gsh.ch',
        icon: LinkIcon
    },
]

export default {
    name: "Footer",
    setup() {
        return {
            navigation,
        }
    },
}
</script>
