import { createWebHistory, createRouter } from "vue-router";
import emitter from '../emitter';

/**
 * PUBBLICI
 */
import Home from '../pages/public/Home.vue';
import About from '../pages/public/About.vue';

/**
 * AUTH
 */
import Register from '../pages/auth/Register.vue';
import Login from '../pages/auth/Login.vue';
import PasswordRecover from '../pages/auth/PasswordRecover.vue';
import PasswordRecoverInput from '../pages/auth/PasswordRecoverInput.vue';
import EmailVerify from '../pages/auth/EmailVerify.vue';
import RequestEmailVerification from '../pages/auth/RequestEmailVerification.vue';

/**
 * Utente
 */
import UtenteProfilo from '../pages/user/Profilo.vue';

import Dashboard from '../pages/Dashboard.vue';


/**
 * Amministrazione
 */
import AdminUserList from '../pages/admin/user/List.vue';
import AdminRaceList from '../pages/admin/race/List.vue';
import AdminRaceEdit from '../pages/admin/race/Edit.vue';
import AdminLogList from '../pages/admin/log/List.vue';

export const routes = [
    {
        name: 'home',
        path: '/',
        component: Home
    },
    {
        name: 'chi-siamo',
        path: '/chi-siamo',
        component: About
    },
    {
        name: 'registrati',
        path: '/registrati',
        component: Register
    },
    {
        name: 'accedi',
        path: '/accedi',
        component: Login
    },
    {
        name: 'recupero-password',
        path: '/recupero-password',
        component: PasswordRecover
    },
    {
        name: 'recupero-password-input',
        path: '/recupero-password/:token',
        component: PasswordRecoverInput
    },
    {
        name: 'verifica-email',
        path: '/email/verify/:id/:hash',
        component: EmailVerify
    },
    {
        name: 'utente-non-verificato',
        path: '/utente-non-verificato',
        component: RequestEmailVerification
    },
    {
        name: 'utente-profilo',
        path: '/utente/profilo/:id',
        component: UtenteProfilo
    },
    {
        name: 'dashboard',
        path: '/dashboard',
        component: Dashboard
    },
    {
        name: 'admin-lista-utenti',
        path: '/admin/users/list',
        component: AdminUserList
    },
    {
        name: 'admin-lista-razze',
        path: '/admin/race/list',
        component: AdminRaceList
    },
    {
        name: 'utente-profilo',
        path: '/admin/race/update/:slug?',
        component: AdminRaceEdit
    },
    {
        name: 'admin-lista-log',
        path: '/admin/log/list',
        component: AdminLogList
    }
];

const router = createRouter({
    history: createWebHistory(),
    routes: routes,
});

router.beforeEach((to, from) => {
    emitter.$emit('user:update');
})

export default router;
