<template>
    <nav class="navbar fixed-top navbar-dark bg-dark" :class="{ 'navbar-expand-lg': !isLoggedIn }">
        <div class="container-fluid">
            <router-link class="navbar-brand" :to="isLoggedIn?'/dashboard':'/'">OpenGDR [Alpha]</router-link>
            <template v-if="!isLoggedIn">
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarNav">
                    <ul class="navbar-nav ms-auto">
                        <li class="nav-item">
                            <router-link to="/accedi" class="nav-link">Login</router-link>
                        </li>
                        <li class="nav-item">
                            <router-link to="/registrati" class="nav-link">Registrati</router-link>
                        </li>
                        <li class="nav-item">
                            <router-link to="/chi-siamo" class="nav-link">Chi Siamo</router-link>
                        </li>
                    </ul>
                </div>
            </template>
            <template v-else>
                <button class="navbar-toggler" type="button" data-bs-toggle="offcanvas" data-bs-target="#offcanvasRight" aria-controls="offcanvasRight">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="offcanvas offcanvas-end text-bg-dark" tabindex="-1" id="offcanvasRight" aria-labelledby="offcanvasRightLabel">
                    <div class="offcanvas-header bg-primary">
                        <h5 class="offcanvas-title" id="offcanvasRightLabel">{{ userData.username }}</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="offcanvas" aria-label="Close"></button>
                    </div>
                    <div class="offcanvas-body">
                        <ul class="navbar-nav">
                            <li class="nav-item dropdown">
                                <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                    Impostazioni
                                </a>
                                <ul class="dropdown-menu">
                                    <li>
                                        <router-link :to="'/utente/profilo/' + userData.id" class="dropdown-item" href="#">Profilo</router-link>
                                    </li>
                                    <li><a class="dropdown-item" href="#">Another action</a></li>
                                    <li><a class="dropdown-item" href="#">Something else here</a></li>
                                </ul>
                            </li>

                            <li class="nav-item dropdown" v-if="isAdmin && userData.permissions.admin.show">
                                <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                    Amministratione
                                </a>
                                <ul class="dropdown-menu">
                                    <li v-if="userData.permissions.admin.user.list">
                                         <router-link to="/admin/users/list" class="dropdown-item">Lista Utenti</router-link>
                                    </li>
                                    <li v-if="userData.permissions.admin.log.list">
                                         <router-link to="/admin/log/list" class="dropdown-item">Log Attività</router-link>
                                    </li>
                                </ul>
                            </li>

                            <li class="nav-item">
                                <a href="#" @click="logout" class="nav-link">Esci</a>
                            </li>
                        </ul>
                    </div>
                </div>
            </template>
        </div>
    </nav>
</template>

<script>
import emitter from '../emitter'
import { mapGetters } from 'vuex'

export default {
    name: "App",
    computed: {
        ...mapGetters([
            'userData',
            'isAdmin',
            'isLoggedIn'
        ])
    },
    data() {
        return {
        }
    },
    created() {
    },
    methods: {
        logout(e) {
            e.preventDefault();
            this.$axios.get("/sanctum/csrf-cookie").then(response => {
                emitter.$emit('loading', true);
                this.$axios.post("/api/auth/logout")
                    .then(response => {
                        if (response.data.success) {
                            setTimeout(() => {
                                window.openGDR.isLoggedin = false;
                                emitter.$emit('user:update');
                                window.location.href = "/";
                            }, 1500);
                        }
                        else {
                            console.log(response);
                        }
                    })
                    .catch(function (error) {
                        console.error(error);
                    });
            });
        }
    },
}
</script>
