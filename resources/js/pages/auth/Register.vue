<template>
    <div class="d-flex justify-contente-center align-items-center h-100 w-100">
        <main class="card form-signin w-100 m-auto">
            <div class="card-header text-center">
                <h1 class="h3 mb-0">Registrati</h1>
            </div>
            <div class="alert m-3" :class="{'alert-danger': !success, 'alert-success': success}" role="alert" v-if="statusMsg !== null">
                {{ statusMsg }}
            </div>

            <form class="m-3" v-if="!hideForm">
                <div class="form-floating mb-3">
                    <input type="text" class="form-control" id="username" placeholder="Username" v-model="username" required autofocus autocomplete="off" :disabled="loading">
                    <label for="username">Username *</label>
                </div>
                <div class="form-floating mb-3">
                    <input type="email" class="form-control" id="email" placeholder="E-mail" v-model="email" required autofocus autocomplete="off" :disabled="loading">
                    <label for="email">Email *</label>
                </div>
                <div class="mb-3">
                    <datepicker id="dateOfBirth" class="form-control" v-model="dateOfBirth" :disabled="loading" inputFormat="dd/MM/yyyy" textInput />
                    <label for="dateOfBirth">Giorno di nascita *</label>
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
                        Accedi
                    </template>
                </button>
            </form>
            <div v-else class="m-3">
                <a class="btn btn-outline-primary btn-sm d-block" href="#" @click="resendMail">Rinvia mail</a>
            </div>

            <div class="card-footer text-muted text-center">
                <div class="btn-group" role="group">
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
import Datepicker from 'vue3-datepicker';
import moment from 'moment';
import emitter from '../../emitter';
export default {
    components: {
        Datepicker
    },
    data() {
        return {
            loading: false,
            hideForm: false,
            username: null,
            email: null,
            dateOfBirth: null,
            password: null,
            passwordConfirmation: null,
            success: null,
            statusMsg: null,
            responseData: {}
        }
    },
    methods: {
        resendMail() {
            emitter.$emit('auth:request-new-confirm-email');
        },
        handleSubmit(e) {
            e.preventDefault()

            this.success = this.statusMsg = null;

            if (this.username && this.username.length > 0 &&
                this.dateOfBirth &&
                this.password && this.password.length > 0 &&
                this.passwordConfirmation && this.passwordConfirmation.length > 0 &&
                this.email && this.email.length > 0) {
                this.loading = true;

                this.$axios.get('/sanctum/csrf-cookie').then(response => {
                    this.$axios.post('/api/auth/register', {
                        email: this.email,
                        username: this.username,
                        password: this.password,
                        password_confirmation: this.passwordConfirmation,
                        date_of_birth: moment(this.dateOfBirth).format('YYYY-MM-DD')
                    })
                        .then(response => {

                            this.success = response.data.success;
                            this.statusMsg = response.data.message;
                            this.responseData = response.data.data;
                            if (response.data.success) {
                                this.hideForm = true;
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
    beforeRouteEnter(to, from, next) {
        if (window.openGDR.isLoggedin) {
            return next('dashboard');
        }
        next();
    }
}
</script>
