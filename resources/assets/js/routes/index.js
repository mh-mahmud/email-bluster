import VueRouter from 'vue-router';
import userRoutes from './user';
import campaignRoutes from './campaign';
const baseRoutes = [];
const routes = baseRoutes.concat(userRoutes,campaignRoutes);

export default new VueRouter({
    routes,
});