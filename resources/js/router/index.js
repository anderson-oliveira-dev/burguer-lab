import { createRouter, createWebHistory } from 'vue-router'

import Home from '../components/home/Home.vue'
import Orders from '../components/orders/Orders.vue'
import Cart from '../components/cart/Cart.vue'
import Profile from '../components/profile/Profile.vue'

const routes = [
    { path: '/', redirect: '/home' },
    { path: '/home', name: 'home', component: Home },
    { path: '/orders', name: 'orders', component: Orders },
    { path: '/cart', name: 'cart', component: Cart },
    { path: '/profile', name: 'profile', component: Profile}
]

const router = createRouter({
    history: createWebHistory(),
    routes,
})

export default router