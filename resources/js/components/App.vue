<template>
    <div class="loader d-flex align-items-center justify-content-center bg-black bg-opacity-25" v-if="generalLoading">
        <div class="spinner-border text-primary" style="width: 3rem; height: 3rem;" role="status">
            <span class="visually-hidden">Loading...</span>
        </div>
    </div>
    <nav-bar></nav-bar>
    <router-view />
    <notifications position="bottom right" />
</template>

<style scoped>
    .loader{
        position: fixed;
        z-index: 999999;
        width: 100vw;
        height: 100vh;
        top: 0;
        left: 0;
    }
</style>

<script>
import NavBar from '../partials/NavBar.vue'
import emitter from '../emitter'
import { mapActions, mapGetters } from 'vuex'

export default {
    components: { NavBar },
    computed: {
        ...mapGetters([
            'userData',
            'generalLoading'
        ])
    },
    data() {
        return {
        };
    },
    created() {
        emitter.$on('auth:resend-new-email', this.resendEmailVerification)
        emitter.$on('user:update', this.getUserDataEvent)

        emitter.$on('notify', this.notify)
        emitter.$on('loading', this.loaderStatus)
    },
    mounted() {
        if (!window.openGDR.isLoggedin) {
            this.loader = false;
        }
    },
    watch: {
        userData(newUserData, oldUserData) {
            this.loader = false;
        }
    },
    methods: {
        ...mapActions([
            'getUserData',
            'updateLoaging',
        ]),
        getUserDataEvent() {
            this.updateLoaging(true);

            if (!window.openGDR.isLoggedin) {
                this.updateLoaging(false);
            }
            this.getUserData(() => {
                /**Verifica se l'email è stata verificata */
                if (this.userData.email_verified_at == null &&
                    this.$router.currentRoute.value.name != 'utente-non-verificato' &&
                    this.$router.currentRoute.value.name != 'verifica-email') {
                    this.$router.push('/utente-non-verificato');
                }
                this.updateLoaging(false);
            })
        },
        notify(status, message) {
            if (message) {
                this.$notify({
                    type: status ? 'success' : 'error',
                    text: message,
                    duration: 2000
                });
            }
        },
        resendEmailVerification() {
            this.$axios.get('/sanctum/csrf-cookie').then(response => {
                this.$axios.post('/api/auth/email-resend', {})
                    .then(response => {
                        this.notify(response.data.success, response.data.message)
                    })
                    .catch(function (error) {
                        console.error(error);
                    });
            })
        },
        loaderStatus(status = false) {
            this.updateLoaging(status);
        }
    }
}
</script>
