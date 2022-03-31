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
                window.location.href = "/";
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
        component: CustomerOrders
    },
    {
        name: 'manage-payments',
        path: '/manage-payments',
        component: ManagePayments,
        beforeEnter: (to, from, next) => {
            console.log(window.Laravel.permissions);
            if (window.Laravel.isLoggedIn && can('manage payments')) {
                next();
            } else {
                window.location.href = "/";
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
                window.location.href = "/";
            }
        }
    },
];

const router = createRouter({
    history: createWebHistory(),
    routes: routes,
    linkActiveClass: 'font-bold'
});

export default router;
