import { createRouter, createWebHistory } from 'vue-router'
import { useAuthStore } from '../components/stores/auth.js'

import Master from '../components/Master.vue'
import Home from '../components/home/Home.vue'
import Orders from '../components/orders/Orders.vue'
import Cart from '../components/cart/Cart.vue'
import Profile from '../components/profile/Profile.vue'
import Register from '../components/login/Register.vue'
import Checkout from '../components/orders/Checkout.vue'

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

            { path: 'checkout', name: 'checkout', component: Checkout }
        ]
    },
    { path: '/register', name: 'register', component: Register }
];

// Quando tiver páginas que precisam de autenticação
//{ path: '/products', component: () => import('...'), meta: { requiresAuth: true } },


const router = createRouter({
    history: createWebHistory(),
    routes,
})

export default router