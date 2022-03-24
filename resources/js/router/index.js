import {createWebHistory, createRouter} from "vue-router";

import Home from '../pages/Home';
import Register from "../pages/auth/Register";
import Login from "../pages/auth/Login";
import Dashboard from "../pages/Dashboard";
import BundleContact from "../pages/Buy/BundleContact";
import Bill from "../pages/Buy/Bill";
import CustomerOrders from "../pages/CustomerOrders";
import ManagePayments from "../pages/Admin/ManagePayments";
import DeliveryServices from "../pages/Admin/DeliveryServices";

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
        component: Bill
    },
    {
        name: 'register',
        path: '/register',
        component: Register
    },
    {
        name: 'login',
        path: '/login',
        component: Login
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
        component: ManagePayments
    },
    {
        name: 'delivery-services',
        path: '/delivery-services',
        component: DeliveryServices
    },
];

const router = createRouter({
    history: createWebHistory(),
    routes: routes,
    linkActiveClass: 'font-bold'
});

export default router;
