import {createWebHistory, createRouter} from "vue-router";

import Home from '../pages/Home.vue';
import Login from "../pages/auth/Login.vue";
import Dashboard from "../pages/Dashboard.vue";
import BundleContact from "../pages/Buy/BundleContact.vue";
import Bill from "../pages/Buy/Bill.vue";
import CustomerOrders from "../pages/CustomerOrders.vue";
import ManagePayments from "../pages/Admin/ManagePayments.vue";
import DeliveryServices from "../pages/Admin/DeliveryServices.vue";
import ForgotPassword from "../pages/auth/ForgotPassword.vue";
import ResetPassword from "../pages/auth/ResetPassword.vue";
import Deliveries from "../pages/Admin/Deliveries.vue";
import Customers from "../pages/Admin/Customers.vue";
import Delivery from "../pages/Admin/Delivery.vue";
import Customer from "../pages/Admin/Customer.vue";
import CustomerCreate from "../pages/Admin/CustomerCreate.vue";
import CustomerBuys from "../pages/Admin/CustomerBuys.vue";
import DeliveriesDate from "../pages/Admin/DeliveriesDate.vue";
import Users from "../pages/Admin/Users.vue";
import UserCreate from "../pages/Admin/UserCreate.vue";
import UserEdit from "../pages/Admin/UserEdit.vue";
import Bundles from "../pages/Admin/Bundles.vue";

const can = (can) => {
    if (window.Laravel.permissions.length > 0) {
        return window.Laravel.permissions.some(r => can.includes(r))
    }

    return false;
};

export const routes = [
    {
        name: 'home',
        path: '/',
        component: Home
    },
    {
        name: 'bundle.buy',
        path: '/bundle/buy/:id',
        component: BundleContact
    },
    {
        name: 'buy.bill',
        path: '/buy/:id',
        component: Bill,
        beforeRouteEnter(to, from, next) {
            if (!window.Laravel.isLoggedIn) {
                window.location.href = "/login";
            }
            next();
        }
    },
    {
        name: 'login',
        path: '/login',
        component: Login
    },
    {
        name: 'forgot-password',
        path: '/forgot-password',
        component: ForgotPassword
    },
    {
        name: 'reset-password',
        path: '/reset-password/:token',
        component: ResetPassword
    },
    {
        name: 'dashboard',
        path: '/dashboard',
        component: Dashboard
    },
    {
        name: 'orders',
        path: '/my-orders',
        component: CustomerOrders,
        beforeEnter: (to, from, next) => {
            if (window.Laravel.isLoggedIn) {
                next();
            } else {
                window.location.href = "/login?redirect=my-orders";
            }
        }
    },
    {
        name: 'manage-payments',
        path: '/manage-payments',
        component: ManagePayments,
        beforeEnter: (to, from, next) => {
            if (window.Laravel.isLoggedIn && can('manage payments')) {
                next();
            } else {
                window.location.href = "/login";
            }
        }
    },
    {
        name: 'delivery-services',
        path: '/delivery-services',
        component: DeliveryServices,
        beforeEnter: (to, from, next) => {
            if (window.Laravel.isLoggedIn && can('manage delivery services')) {
                next();
            } else {
                window.location.href = "/login";
            }
        }
    },
    {
        name: 'delivery-service',
        path: '/delivery-service/:id',
        component: DeliveryServices,
        beforeEnter: (to, from, next) => {
            if (window.Laravel.isLoggedIn && can('manage delivery services')) {
                next();
            } else {
                window.location.href = "/login";
            }
        }
    },
    {
        name: 'deliveries',
        path: '/deliveries',
        component: Deliveries,
        beforeEnter: (to, from, next) => {
            if (window.Laravel.isLoggedIn && can('manage deliveries')) {
                next();
            } else {
                window.location.href = "/";
            }
        }
    },
    {
        name: 'deliveries-date',
        path: '/deliveries/:date',
        component: DeliveriesDate,
        beforeEnter: (to, from, next) => {
            if (window.Laravel.isLoggedIn && can('manage deliveries')) {
                next();
            } else {
                window.location.href = "/";
            }
        }
    },
    {
        name: 'delivery',
        path: '/delivery/:id',
        component: Delivery,
        beforeEnter: (to, from, next) => {
            if (window.Laravel.isLoggedIn && can('manage deliveries')) {
                next();
            } else {
                window.location.href = "/";
            }
        }
    },
    {
        name: 'customers',
        path: '/customers',
        component: Customers,
        beforeEnter: (to, from, next) => {
            if (window.Laravel.isLoggedIn && can('manage customers')) {
                next();
            } else {
                window.location.href = "/";
            }
        }
    },
    {
        name: 'customer',
        path: '/customer/:id',
        component: Customer,
        beforeEnter: (to, from, next) => {
            if (window.Laravel.isLoggedIn && can('manage customers')) {
                next();
            } else {
                window.location.href = "/";
            }
        }
    },
    {
        name: 'customer create',
        path: '/customer',
        component: CustomerCreate,
        beforeEnter: (to, from, next) => {
            if (window.Laravel.isLoggedIn && can('manage customers')) {
                next();
            } else {
                window.location.href = "/";
            }
        }
    },
    {
        name: 'customer-orders',
        path: '/customer/:id/orders',
        component: CustomerOrders
    },
    {
        name:'customer-buys',
        path: '/customer/:id/buys',
        component: CustomerBuys,
        beforeEnter: (to, from, next) => {
            if (window.Laravel.isLoggedIn && can('manage customers')) {
                next();
            } else {
                window.location.href = "/";
            }
        }
    },
    {
        name: 'users',
        path: '/users',
        component: Users,
        beforeEnter: (to, from, next) => {
            if (window.Laravel.isLoggedIn && can('manage users')) {
                next();
            } else {
                window.location.href = "/";
            }
        }
    },
    {
        name: 'users',
        path: '/users',
        component: Users,
        beforeEnter: (to, from, next) => {
            if (window.Laravel.isLoggedIn && can('manage users')) {
                next();
            } else {
                window.location.href = "/";
            }
        }
    },
    {
        name: 'user edit',
        path: '/user/:id',
        component: UserEdit,
        beforeEnter: (to, from, next) => {
            if (window.Laravel.isLoggedIn && can('manage users')) {
                next();
            } else {
                window.location.href = "/";
            }
        }
    },
    {
        name: 'user create',
        path: '/user/',
        component: UserCreate,
        beforeEnter: (to, from, next) => {
            if (window.Laravel.isLoggedIn && can('manage users')) {
                next();
            } else {
                window.location.href = "/";
            }
        }
    },
    {
        name: 'bundles',
        path: '/bundles/',
        component: Bundles,
        beforeEnter: (to, from, next) => {
            if (window.Laravel.isLoggedIn && can('manage bundles')) {
                next();
            } else {
                window.location.href = "/";
            }
        }
    }
];

const router = createRouter({
    history: createWebHistory(),
    routes: routes,
    linkActiveClass: 'font-bold'
});

export default router;
