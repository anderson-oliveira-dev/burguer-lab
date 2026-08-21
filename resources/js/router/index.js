import { createRouter, createWebHistory } from 'vue-router'
import { useAuthStore } from '../components/stores/auth.js'

import Master from '../components/Master.vue'
import Home from '../components/home/Home.vue'
import Orders from '../components/orders/Orders.vue'
import Cart from '../components/cart/Cart.vue'
import Profile from '../components/profile/Profile.vue'
import Register from '../components/login/Register.vue'
import Checkout from '../components/orders/Checkout.vue'
import OrderDetails from '../components/orders/OrderDetails.vue'
import ChangePassword from '../components/profile/ChangePassword.vue'

const routes = [
    {
        path: '/',
        component: Master,
        children: [
            { path: '', redirect: '/home' },
            { path: 'home', name: 'home', component: Home },
            { path: 'orders', name: 'orders', component: Orders },
            { path: 'cart', name: 'cart', component: Cart },
            { path: 'profile', name: 'profile', component: Profile },
            {
                path: '/profile/change-password/',
                name: 'change-password',
                component: ChangePassword,
                meta: { requiresAuth: true },
            },

            { path: 'checkout', name: 'checkout', component: Checkout },
            {
                path: '/orders/:id',
                name: 'order-detail',
                component: OrderDetails,
                meta: { requiresAuth: true },
            }
        ]
    },
    { path: '/register', name: 'register', component: Register }
];

const router = createRouter({
    history: createWebHistory(),
    routes,
})

export default router