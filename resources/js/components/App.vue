<template>
    <nav-bar></nav-bar>

    <router-view />

</template>

<script>
import NavBar from '../partials/NavBar.vue'
export default {
    name: "App",
    data() {
        return {
            isLoggedIn: false,
        };
    },
    created() {
        if (window.openGDR.isLoggedin) {
            this.isLoggedIn = true;
        }
    },
    methods: {

    },
    components: { NavBar }
}
</script>
