<template>
    <div class="d-flex justify-contente-center align-items-center h-100 w-100">
        <main class="card form-signin w-100 m-auto">
            <div class="card-header text-center">
                <h1 class="h3 mb-0">Recupera Password</h1>
            </div>
            <form class="m-4">
                <div class="alert" :class="{ 'alert-danger': !success, 'alert-success': success }" role="alert" v-if="statusMsg !== null">
                    {{ statusMsg }}
                </div>
                <div class="form-floating mb-3">
                    <input type="email" class="form-control" id="email" placeholder="E-mail" v-model="email" required autofocus autocomplete="off" :disabled="loading">
                    <label for="email">Email *</label>
                </div>
                <div class="form-floating mb-3">
                    <input type="password" class="form-control" id="password" placeholder="Password" v-model="password" required autocomplete="off" :disabled="loading">
                    <label for="password">Password *</label>
                </div>
                <div class="form-floating mb-3">
                    <input type="password" class="form-control" id="password_confirmation" placeholder="Password" v-model="passwordConfirmation" required autocomplete="off" :disabled="loading">
                    <label for="password_confirmation">Ripeti Password *</label>
                </div>
                <button class="w-100 btn btn-lg btn-primary" type="submit" @click="handleSubmit" :disabled="loading">
                    <template v-if="loading">
                        <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                        <span class="visually-hidden">Loading...</span>
                    </template>
                    <template v-else>
                        Recupera Password
                    </template>
                </button>
            </form>

            <div class="card-footer text-muted text-center">
                <div class="btn-group" role="group">
                    <router-link to="/registrati" type="button" class="btn btn-primary btn-sm">Registrati</router-link>
                    <router-link to="/accedi" type="button" class="btn btn-outline-primary btn-sm">Login</router-link>
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
export default {
    data() {
        return {
            loading: false,
            email: null,
            password: null,
            passwordConfirmation: null,
            token: null,
            success: null,
            statusMsg: null,
        }
    },
    methods: {
        handleSubmit(e) {
            e.preventDefault()

            this.success = this.statusMsg = null;

            if (this.email && this.email.length > 0 && this.password && this.password.length > 0 && this.passwordConfirmation && this.passwordConfirmation.length > 0) {
                this.loading = true;
                this.$axios.get('/sanctum/csrf-cookie').then(response => {
                    this.$axios.post('/api/auth/recover-post', {
                        email: this.email,
                        password: this.password,
                        password_confirmation: this.passwordConfirmation,
                        token: this.token
                    })
                        .then(response => {
                            if (response.data.success) {
                                this.success = response.data.success;
                                this.statusMsg = response.data.message;
                            } else {
                                this.success = response.data.success;
                                this.statusMsg = response.data.message;
                            }
                            this.loading = false;
                        })
                        .catch(function (error) {
                            console.error(error);
                            this.loading = false;
                        });
                })
            } else {
                this.success = false;
                this.statusMsg = 'Campi obbligatori non compilati';
            }
        }
    },
    mounted() {
        this.token = this.$route.params.token;
        if ('recoverPassword' in window.openGDR){
            this.email = window.openGDR.recoverPassword.email;
        }
    },
    beforeRouteEnter(to, from, next) {
        if (window.openGDR.isLoggedin) {
            return next('dashboard');
        }
        next();
    }
}
</script>
