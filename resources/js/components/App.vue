<template>
    <nav-bar></nav-bar>
    <router-view />
    <notifications />
</template>

<script>
import NavBar from '../partials/NavBar.vue'
import emitter from '../emitter'
export default {

    components: { NavBar },
    data() {
        return {
            user: null
        };
    },
    created() {
        emitter.$on('auth:resend-new-email', this.resendEmailVerification)
        emitter.$on('user:update', this.getUserData)

        emitter.$on('notify', this.notify)
    },
    mounted() {
        if (window.openGDR.isLoggedin && this.user == null) {
            this.getUserData();
        }
    },
    methods: {
        notify(status, message) {
            this.$notify({
                type: status ? 'success' : 'error',
                text: message,
                duration: 2000
            });
        },
        resendEmailVerification() {
            console.log('resendEmailVerification');
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
        getUserData() {
            if (!window.openGDR.isLoggedin) {
                return;
            }
            this.$axios.get('/sanctum/csrf-cookie').then(response => {
                this.$axios.get('/api/user/data', {})
                    .then(response => {
                        console.log(response.data);
                        if(response.data.success){
                            this.user = response.data.data;
                            if (this.user.email_verified_at == null &&
                                this.$router.currentRoute.value.name != 'utente-non-verificato' &&
                                this.$router.currentRoute.value.name != 'verifica-email') {
                                    this.$router.push('/utente-non-verificato');
                            }
                        }
                    })
                    .catch(function (error) {
                        this.$router.push('/')
                    });
            })
        }
    }
}
</script>
