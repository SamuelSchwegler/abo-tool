import {createWebHistory, createRouter} from "vue-router";

import Home from '../pages/Home';
import Login from "../pages/auth/Login";
import Dashboard from "../pages/Dashboard";
import BundleContact from "../pages/Buy/BundleContact";
import Bill from "../pages/Buy/Bill";
import CustomerOrders from "../pages/CustomerOrders";
import ManagePayments from "../pages/Admin/ManagePayments";
import DeliveryServices from "../pages/Admin/DeliveryServices";
import ForgotPassword from "../pages/auth/ForgotPassword";
import ResetPassword from "../pages/auth/ResetPassword";
import Deliveries from "../pages/Admin/Deliveries";
import Customers from "../pages/Admin/Customers";
import Delivery from "../pages/Admin/Delivery";
import Customer from "../pages/Admin/Customer";
import CustomerCreate from "../pages/Admin/CustomerCreate";
import CustomerBuys from "../pages/Admin/CustomerBuys";
import DeliveriesDate from "../pages/Admin/DeliveriesDate";

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
    }
];

const router = createRouter({
    history: createWebHistory(),
    routes: routes,
    linkActiveClass: 'font-bold'
});

export default router;
