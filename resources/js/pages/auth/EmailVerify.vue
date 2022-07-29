<template>
    <div class="d-flex justify-contente-center align-items-center h-100 w-100">
        <main class="card form-signin w-100 m-auto">
            <div class="card-header text-center">
                <h1 class="h3 mb-0">Verifica Email</h1>
            </div>
            <div class="alert" :class="{ 'alert-danger': !success, 'alert-success': success }" role="alert" v-if="statusMsg !== null">
                {{ statusMsg }}
            </div>
            <div class="card-footer text-muted text-center">
                <div class="btn-group" role="group">

                </div>
            </div>
        </main>
    </div>
</template>
<style scoped>
.form-signin {
    max-width: 330px;
}
</style>
<script>
import emitter from '../../emitter';
export default {
    data() {
        return {
            loading: false,
            id: null,
            hash: null,

            success: null,
            statusMsg: null,
        }
    },
    methods: {
    },
    mounted() {
        this.id = this.$route.params.id;
        this.hash = this.$route.params.hash;

        this.loading = true;
        this.$axios.get('/sanctum/csrf-cookie').then(response => {
            this.$axios.post('/api/auth/email-verify', {
                ...this.$route.query, ...{
                    id: this.$route.params.id,
                    hash: this.$route.params.hash,
                }
            })
                .then(response => {
                    this.success = response.data.success;
                    this.statusMsg = response.data.message;
                    if (this.success) {
                        emitter.$emit('notify', {
                            status: true,
                            message: response.data.message
                        })
                        this.$router.push('/dashboard')
                    }
                })
                .catch(function (error) {
                    console.error(error);
                });
        })
    },
    beforeRouteEnter(to, from, next) {
        if (!window.openGDR.isLoggedin) {
            return next('home');
        }
        next();
    }
}
</script>
