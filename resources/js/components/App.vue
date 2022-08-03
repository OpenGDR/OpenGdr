<template>
    <nav-bar></nav-bar>
    <router-view />
    <notifications />
</template>

<script>
import NavBar from '../partials/NavBar.vue'
import emitter from '../emitter'
import { mapActions, mapGetters } from 'vuex'

export default {
    components: { NavBar },
    computed: {
        ...mapGetters([
            'userData',
        ])
    },
    data() {
        return {};
    },
    created() {
        emitter.$on('auth:resend-new-email', this.resendEmailVerification)
        emitter.$on('user:update', this.getUserDataEvent)

        emitter.$on('notify', this.notify)
    },
    mounted() {
        if (window.openGDR.isLoggedin && this.user == null) {
            //this.getUserData();
        }
    },
    methods: {
        ...mapActions([
            'getUserData',
        ]),
        getUserDataEvent() {
            this.getUserData(() => {

                /**Verifica se l'email è stata verificata */
                if (this.userData.email_verified_at == null &&
                    this.$router.currentRoute.value.name != 'utente-non-verificato' &&
                    this.$router.currentRoute.value.name != 'verifica-email') {
                    this.$router.push('/utente-non-verificato');
                }
            })
        },
        notify(status, message) {
            this.$notify({
                type: status ? 'success' : 'error',
                text: message,
                duration: 2000
            });
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
        }
    }
}
</script>
