<!-- This example requires Tailwind CSS v2.0+ -->
<template>
    <Popover class="relative bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6">
            <div
                class="flex justify-between items-center border-b-2 border-gray-100 py-4 md:justify-start md:space-x-10">
                <div class="flex justify-start lg:w-0 lg:flex-1">
                    <a href="/">
                        <span class="sr-only">Logo</span>
                        <img class="h-8 w-auto sm:h-10" src="/img/logo-gsh_header.png" alt=""/>
                    </a>
                </div>
                <div class="-mr-2 -my-2 md:hidden">
                    <PopoverButton
                        class="bg-white rounded-md p-2 inline-flex items-center justify-center text-gray-400 hover:text-gray-500 hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-inset focus:ring-indigo-500">
                        <span class="sr-only">Open menu</span>
                        <MenuIcon class="h-6 w-6" aria-hidden="true"/>
                    </PopoverButton>
                </div>
                <PopoverGroup as="nav" class="hidden md:flex space-x-10" v-if="isLoggedIn">
                    <router-link to="/" class="text-base font-medium text-gray-500 hover:text-gray-900">
                        Startseite
                    </router-link>
                    <router-link to="/my-orders" class="text-base font-medium text-gray-500 hover:text-gray-900">
                       Verwaltung Lieferungen
                    </router-link>
                    <Popover class="relative" v-slot="{ open }" v-if="settings.canAny">
                        <PopoverButton
                            :class="[open ? 'text-gray-900' : 'text-gray-500', 'group bg-white rounded-md inline-flex items-center text-base font-medium hover:text-gray-900 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500']">
                            <span>Einstellungen</span>
                            <ChevronDownIcon
                                :class="[open ? 'text-gray-600' : 'text-gray-400', 'ml-2 h-5 w-5 group-hover:text-gray-500']"
                                aria-hidden="true"/>
                        </PopoverButton>

                        <transition enter-active-class="transition ease-out duration-200"
                                    enter-from-class="opacity-0 translate-y-1"
                                    enter-to-class="opacity-100 translate-y-0"
                                    leave-active-class="transition ease-in duration-150"
                                    leave-from-class="opacity-100 translate-y-0"
                                    leave-to-class="opacity-0 translate-y-1">
                            <PopoverPanel
                                class="absolute z-10 left-1/2 transform -translate-x-1/2 mt-3 px-2 w-screen max-w-md sm:px-0">
                                <div class="rounded-lg shadow-lg ring-1 ring-black ring-opacity-5 overflow-hidden">
                                    <div class="relative grid gap-6 bg-white px-5 py-6 sm:gap-8 sm:p-8">
                                        <a v-for="route in settings.admin_routes" :key="route.name" :href="route.href"
                                           class="-m-3 p-3 flex items-start rounded-lg hover:bg-gray-50">
                                            <component :is="route.icon" class="flex-shrink-0 h-6 w-6 text-indigo-600"
                                                       aria-hidden="true"/>
                                            <div class="ml-4" v-if="route.can">
                                                <p class="text-base font-medium text-gray-900">
                                                    {{ route.name }}
                                                </p>
                                                <p class="mt-1 text-sm text-gray-500">
                                                    {{ route.description }}
                                                </p>
                                            </div>
                                        </a>
                                    </div>
                                </div>
                            </PopoverPanel>
                        </transition>
                    </Popover>
                </PopoverGroup>
                <div class="hidden md:flex items-center justify-end md:flex-1 lg:w-0" v-if="isLoggedIn">
                    <a style="cursor: pointer;" @click="logout"
                       class="ml-8 whitespace-nowrap inline-flex items-center justify-center px-4 py-2 border border-transparent rounded-md shadow-sm text-base font-medium text-white bg-violet hover:bg-indigo-700">
                        Logout </a>
                </div>
                <div class="hidden md:flex items-center justify-end md:flex-1 lg:w-0" v-else>
                    <router-link to="/login"
                                 class="whitespace-nowrap text-base font-medium text-gray-500 hover:text-gray-900">
                        Login
                    </router-link>
                </div>
            </div>
        </div>

        <transition enter-active-class="duration-200 ease-out" enter-from-class="opacity-0 scale-95"
                    enter-to-class="opacity-100 scale-100" leave-active-class="duration-100 ease-in"
                    leave-from-class="opacity-100 scale-100" leave-to-class="opacity-0 scale-95">
            <PopoverPanel focus class="absolute top-0 inset-x-0 p-2 z-40 transition transform origin-top-right md:hidden">
                <div class="rounded-lg shadow-lg ring-1 ring-black ring-opacity-5 bg-white divide-y-2 divide-gray-50">
                    <div class="pt-5 pb-6 px-5">
                        <div class="flex items-center justify-between">
                            <div>
                                <img class="h-8 w-auto"
                                     src="/img/logo-gsh.png"
                                     alt="Workflow"/>
                            </div>
                            <div class="-mr-2">
                                <PopoverButton
                                    class="bg-white rounded-md p-2 inline-flex items-center justify-center text-gray-400 hover:text-gray-500 hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-inset focus:ring-indigo-500">
                                    <span class="sr-only">Close menu</span>
                                    <XIcon class="h-6 w-6" aria-hidden="true"/>
                                </PopoverButton>
                            </div>
                        </div>
                        <div class="mt-6">
                            <nav class="grid gap-y-8">
                                <a v-if="isLoggedIn" v-for="item in settings.routes" :key="item.name" :href="item.href"
                                   class="-m-3 p-3 flex items-center rounded-md hover:bg-gray-50">
                                    <component :is="item.icon" class="flex-shrink-0 h-6 w-6 text-violet"
                                               aria-hidden="true"/>
                                    <span class="ml-3 text-base font-medium text-gray-900">
                    {{ item.name }}
                  </span>
                                </a>
                            </nav>
                        </div>
                    </div>
                    <div class="py-6 px-5 space-y-6">
                        <div>
                            <div class="md:flex items-center justify-end md:flex-1 lg:w-0" v-if="isLoggedIn">
                                <a style="cursor: pointer;" @click="logout"
                                   class="w-full flex items-center justify-center px-4 py-2 border border-transparent rounded-md shadow-sm text-base font-medium text-white bg-violet hover:bg-indigo-700">
                                    Logout </a>
                            </div>
                            <div class="md:flex items-center justify-end md:flex-1 lg:w-0" v-else>
                                <router-link to="/login"
                                             class="w-full flex items-center justify-center px-4 py-2 border border-transparent rounded-md shadow-sm text-base font-medium text-white bg-violet hover:bg-indigo-700">
                                    Login
                                </router-link>
                            </div>
                        </div>
                    </div>
                </div>
            </PopoverPanel>
        </transition>
    </Popover>
</template>

<script>
import {Popover, PopoverButton, PopoverGroup, PopoverPanel} from '@headlessui/vue'
import {
    MenuIcon,
    XIcon,
} from '@heroicons/vue/outline'
import {CashIcon, ChevronDownIcon, MailIcon, MapIcon, UserGroupIcon, HomeIcon, BookOpenIcon} from '@heroicons/vue/solid'

export default {
    components: {
        Popover,
        PopoverButton,
        PopoverGroup,
        PopoverPanel,
        ChevronDownIcon,
        MenuIcon,
        MailIcon,
        HomeIcon,
        XIcon,
        BookOpenIcon
    },
    name: 'Navigation',
    data: function () {
        let customer_routes = [
            {
                name: 'Bestellen',
                description: 'Neue Abos bestellen.',
                href: '/',
                icon: HomeIcon,
                can: true
            },
            {
                name: 'Verwaltung Lieferungen',
                description: '',
                href: '/my-orders',
                icon: BookOpenIcon,
                can: true
            }
        ];
        let admin_routes = [];

        if(this.can('manage payments')) {
            admin_routes.push({
                name: 'Rechnungen',
                description: 'Zahlungen verwalten.',
                href: '/manage-payments',
                icon: CashIcon,
                can: this.can('manage payments')
            });
        }

        if(this.can('manage deliveries')) {
            admin_routes.push({
                name: 'Lieferungen',
                description: 'Lieferungen bearbeiten',
                href: '/deliveries',
                icon: MailIcon,
                can: this.can('manage deliveries')
            });
        }

        if(this.can('manage delivery services')) {
            admin_routes.push({
                name: 'Lieferzonen',
                description: 'Postleitzahlen in Lieferzonen einteilen',
                href: '/delivery-services',
                icon: MapIcon,
                can: this.can('manage delivery services')
            });
        }

        if(this.can('manage customers')) {
            admin_routes.push({
                name: 'Kunden',
                description: 'Kundenverwaltung',
                href: '/customers',
                icon: UserGroupIcon,
                can: this.can('manage customers')
            });
        }

        return {
            isLoggedIn: false,
            settings: {
                canAny: this.can('manage payments') || this.can('manage deliveries'),
                routes: [
                    ...customer_routes,
                    ...admin_routes
                ],
                admin_routes: admin_routes
            },
        }
    },
    created() {
        if (window.Laravel.isLoggedIn) {
            this.isLoggedIn = true
        }
    },
    methods: {
        logout(e) {
            e.preventDefault()
            this.$axios.get('/sanctum/csrf-cookie').then(response => {
                this.$axios.post('/api/logout')
                    .then(response => {
                        if (response.data.success) {
                            this.isLoggedIn = false;
                            window.location.href = "/"
                        } else {
                            console.log(response)
                        }
                    })
                    .catch(function (error) {
                        console.error(error);
                    });
            })
        },
    }
}
</script>
