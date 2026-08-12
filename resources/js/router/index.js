import { createRouter, createWebHistory } from 'vue-router'
import ProductsList from '../components/products/ProductsList.vue'
import TrackOrder from '../components/products/TrackOrder.vue'

const routes = [
    { path: '/', name: 'products-list', component: ProductsList },
    { path: '/track-order', name: 'track-order', component: TrackOrder },
]

const router = createRouter({
    history: createWebHistory(),
    routes,
})

export default router