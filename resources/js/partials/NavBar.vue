<template>
    <nav class="navbar  fixed-top navbar-expand-lg navbar-dark bg-dark">
        <div class="container-fluid">
            <router-link class="navbar-brand" :to="isLoggedIn?'/dashboard':'/'">OpenGDR [Alpha]</router-link>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item" v-if="!isLoggedIn">
                        <router-link to="/accedi" class="nav-link">Login</router-link>
                    </li>
                    <li class="nav-item" v-if="!isLoggedIn">
                        <router-link to="/registrati" class="nav-link">Registrati</router-link>
                    </li>
                    <li class="nav-item" v-if="!isLoggedIn">
                        <router-link to="/chi-siamo" class="nav-link">Chi Siamo</router-link>
                    </li>
                    <li class="nav-item" v-if="isLoggedIn">
                        <a href="#" @click="logout" class="nav-link">Esci</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>
</template>

<script>
export default {
    name: "App",
    data() {
        return {
            isLoggedIn: false,
        }
    },
    created() {
        if (window.openGDR.isLoggedin) {
            this.isLoggedIn = true
        }
    },
    methods: {
        logout(e) {
            e.preventDefault();
            this.$axios.get("/sanctum/csrf-cookie").then(response => {
                this.$axios.post("/api/auth/logout")
                    .then(response => {
                        if (response.data.success) {
                            window.location.href = "/";
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
