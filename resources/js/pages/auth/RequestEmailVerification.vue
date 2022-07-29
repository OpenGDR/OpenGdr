<template>
    <div class="d-flex justify-contente-center align-items-center h-100 w-100">
        <main class="card form-signin w-100 m-auto">
            <div class="card-header text-center">
                <h1 class="h3 mb-0">Devi verificare la tua mail</h1>
            </div>
            <div class="card-body">
                <p>Ti è stata inviata una mail durante la registrazione, clicca sul link per attivare la tua utenza</p>

                <a href="#" @click="resendEmailVerification" class="btn btn-sm btn-primary d-block">Reinvia la mail</a>
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
        }
    },
    methods: {
        resendEmailVerification() {
            emitter.$emit('auth:resend-new-email');
        }
    },
    mounted() {
    },
    beforeRouteEnter(to, from, next) {
        if (!window.openGDR.isLoggedin) {
            return next('home');
        }
        next();
    }
}
</script>
