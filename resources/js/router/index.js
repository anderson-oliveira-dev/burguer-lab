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
import Forbidden from '../components/common/Forbidden.vue'
import NotFound from '../components/common/NotFound.vue'

const routes = [
    {
        path: '/',
        component: Master,
        children: [
            { path: '', redirect: '/home' },
            { path: 'home', name: 'home', component: Home },
            { path: 'orders', name: 'orders', component: Orders },
            { path: 'cart', name: 'cart', component: Cart, meta: { roles: ['client'], allowGuest: true }},
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
                //meta: { requiresAuth: true, roles: ['client', 'admin'] } como usar
            },
            { path: '/403', name: 'forbidden', component: Forbidden },
            { path: '/:pathMatch(.*)*', name: 'not-found', component: NotFound }
        ]
    },
    { path: '/register', name: 'register', component: Register }
];

const router = createRouter({
    history: createWebHistory(),
    routes,
});

router.beforeEach(async (to, from, next) => {
    const authStore = useAuthStore();

    if (!authStore.user && authStore.token) {
        await authStore.fetchUser();
    }

    const isAuthenticated = authStore.isAuthenticated;
    const userRole = authStore.userRole;

    if (to.meta.requiresAuth && !isAuthenticated) {
        return next({ name: 'home' });
    }

    if (to.meta.roles) {
        if (to.meta.allowGuest && !isAuthenticated) {
            return next();
        }
        if (!isAuthenticated) {
            return next({ name: 'home' });
        }
        if (!to.meta.roles.includes(userRole)) {
            return next({ name: 'forbidden' });
        }
    }

    next();
});

export default router