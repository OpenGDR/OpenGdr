<template>
    <div class="container">
        <div class="row mb-3">
            <div class="col">
                <div class=" d-flex align-items-center p-3 text-white bg-primary rounded shadow-sm ">
                    <font-awesome-icon icon="fa-solid fa-user" class="fa-2x" />
                    <div class="lh-1 ms-3">
                        <h1 class="h5 mb-0 text-white lh-1 mb-1">Profilo utente : <strong>{{ data.username }}</strong></h1>
                        <div class="badge bg-success">
                            {{ data.level_label }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row mb-3" v-if="isAdmin && !generalLoading">
            <div class="col">
                <div class="card">
                    <div class="card-header">
                        <h2 class="me-auto h5">
                            Amministrazione
                        </h2>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-4 col-lg-3 mb-3">
                                <font-awesome-icon icon="fa-solid fa-calendar-days" class="me-1" /> Creato il<br /> {{ data.created_at }}
                            </div>
                            <div class="col-4 col-lg-3 mb-3">
                                <div v-if="data.banned == 1">
                                    <font-awesome-icon class="text-danger me-1" icon="fa-solid fa-circle-exclamation" /> Bannato il<br /> {{ data.banned_at }}
                                </div>
                                <div v-else>
                                    Utente attivo
                                </div>
                            </div>
                            <div class="col-4 col-lg-3 mb-3">
                                <div v-if="data.email_verified_at != null">
                                    Email verificata il<br /> {{ data.email_verified_at }}
                                </div>
                                <div v-else>
                                    <font-awesome-icon class="text-danger me-1" icon="fa-solid fa-circle-exclamation" /> Email non verificata
                                </div>
                            </div>
                            <div class="col-4 col-lg-3 mb-3">
                                <div v-if="data.last_login != null">
                                    <font-awesome-icon icon="fa-solid fa-calendar-days" class="me-1" /> Ultimo accesso: <br />{{ data.last_login }}
                                </div>
                                <div v-else>
                                    <font-awesome-icon class="text-danger me-1" icon="fa-solid fa-circle-exclamation" /> Nessun accesso eseguito
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer justify-content-end d-flex" v-if="data.id != userData.id && data.level != 1">
                        <button v-if="data.banned == 0" type="button" @click="bannedUser" class="btn btn-danger">Banna</button>
                        <button v-else type="button" @click="bannedUser" class="btn btn-outline-danger">Rimuovi ban</button>
                    </div>
                </div>

            </div>
        </div>
        <div class="row mb-3">
            <div class="col col-lg-6">
                <div class="card">
                    <div class="card-header d-flex align-items-center">
                        <h2 class="me-auto h5">
                            Informazioni generali
                        </h2>
                        <div v-if="permission.edit" class="btn btn-sm" :class="{ 'btn-outline-primary': !editSection.includes('general'), 'btn-success': editSection.includes('general') }" @click.stop="edit('general')">
                            <font-awesome-icon icon="fa-solid fa-pen" class="me-1" /> Modifica
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label for="username" class="form-label">Username *</label>
                            <input type="text" class="form-control" :class="{ 'is-invalid' : ('username' in error.general )}" id="username" placeholder="username" v-model="general.username" :disabled="!editSection.includes('general')">
                            <div v-if="'username' in error.general && error.general.username.length" class="invalid-feedback">
                                <div v-for="elm in error.general.username" v-html="elm"></div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="email" class="form-label">Email *</label>
                            <input type="email" class="form-control" :class="{ 'is-invalid': ('email' in error.general )}" id="email" placeholder="name@example.com" v-model="general.email" :disabled="!editSection.includes('general')">
                            <div v-if="'email' in error.general && error.general.email.length" class="invalid-feedback">
                                <div v-for="elm in error.general.email" v-html="elm"></div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="dateOfBirth">Giorno di nascita *</label>
                            <datepicker id="dateOfBirth" class="form-control" :class="{ 'is-invalid': ('date_of_birth' in error.general )}" v-model="general.date_of_birth" :disabled="!editSection.includes('general')" inputFormat="dd/MM/yyyy" textInput />
                            <div v-if="'date_of_birth' in error.general && error.general.date_of_birth.length" class="invalid-feedback">
                                <div v-for="elm in error.general.date_of_birth" v-html="elm"></div>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="motto" class="form-label">Motto</label>
                            <input type="text" class="form-control" id="motto" :class="{ 'is-invalid': ('motto' in error.general )}" placeholder="Questo è il mio motto!" v-model="general.motto" :disabled="!editSection.includes('general')">
                            <div v-if="'motto' in error.general && error.general.motto.length" class="invalid-feedback">
                                <div v-for="elm in error.general.motto" v-html="elm"></div>
                            </div>
                        </div>

                    </div>
                    <div class="card-footer d-flex" v-if="permission.edit">
                        <button @click.stop="saveGeneral" class="btn btn-primary ms-auto" :disabled="!editSection.includes('general')">Salva</button>
                    </div>
                </div>
            </div>
            <div class="col col-lg-6" v-if="permission.edit">
                <div class="card">
                    <div class="card-header d-flex align-items-center">
                        <h2 class="me-auto h5">
                            Modifica Password
                        </h2>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label for="password" class="form-label">Password *</label>
                            <input type="password" class="form-control" :class="{ 'is-invalid': ('password' in error.password )}" id="password" placeholder="password" v-model="password.password">
                            <div v-if="'password' in error.password && error.password.password.length" class="invalid-feedback">
                                <div v-for="elm in error.password.password" v-html="elm"></div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="password_confirmation" class="form-label">Conferma Password *</label>
                            <input type="password" class="form-control" :class="{ 'is-invalid': ('password_confirmation' in error.password )}" id="password_confirmation" placeholder="Conferma Password" v-model="password.password_confirmation">
                            <div v-if="'password_confirmation' in error.password && error.password.password_confirmation.length" class="invalid-feedback">
                                <div v-for="elm in error.password.password_confirmation" v-html="elm"></div>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer d-flex">
                        <button @click.stop="saveNewPassword" class="btn btn-primary ms-auto">Salva</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
<script>
import Datepicker from 'vue3-datepicker';
import moment from 'moment';
import emitter from '../../emitter'
import { mapGetters } from 'vuex'
export default {
    components: {
        Datepicker
    },
    computed: {
        ...mapGetters([
            'generalLoading',
            'userData',
            'isAdmin'
        ])
    },
    data() {
        return {
            data: {},
            editSection: [],
            permission: {
                edit: false
            },
            general: {
                id: null,
                username: null,
                email: null,
                date_of_birth: null,
                motto: '',
            },
            password: {
                id: null,
                password: null,
                password_confirmation: null
            },
            error: {
                general: {},
                password: {}
            }
        }
    },
    mounted() {

        this.$axios.get('/sanctum/csrf-cookie').then(response => {
            this.$axios.get('/api/auth/permission/check/view/user/' + this.$route.params.id, {})
                .then(response => {

                    if (!response.data.success) {
                        emitter.$emit('notify', response.data.success, response.data.message);
                        this.$router.push('/');
                        return;
                    }
                    this.$axios.get('/api/user/data/' + this.$route.params.id, {})
                        .then(response => {
                            if (!response.data.success) {
                                emitter.$emit('notify', response.data.success, response.data.message);
                                this.$router.push('/');
                                return;
                            } else {
                                this.data = response.data.data;
                                this.general.id = this.data.id;
                                this.password.id = this.data.id;


                                this.general.username = this.data.username;
                                this.general.email = this.data.email;
                                this.general.date_of_birth = moment(this.data.date_of_birth, 'YYYY-MM-DD').toDate();
                                this.general.motto = this.data.motto;

                                this.$axios.get('/api/auth/permission/check/update/user/' + this.$route.params.id, {})
                                    .then(response => {
                                        this.permission.edit = response.data.success;
                                    })
                                    .catch(function (error) {
                                        console.error(error);
                                    });
                            }

                        })
                        .catch(function (error) {
                            console.error(error);
                        });
                })
                .catch(function (error) {
                    console.error(error);
                });
        })
    },
    methods: {
        edit(section) {
            if (this.editSection.includes(section)) {
                let index = this.editSection.indexOf(section);
                if (index !== -1) {
                    this.editSection.splice(index, 1);
                }
            } else {
                this.editSection.push(section);
            }
        },
        saveGeneral(ev) {
            ev.preventDefault()
            if (!this.permission.edit) return;
            if (this.general.username && this.general.username.length > 0 &&
                this.general.email && this.general.email.length > 0 &&
                this.general.date_of_birth
            ) {
                let dataToSend = { ...this.general };
                dataToSend.date_of_birth = moment(this.general.date_of_birth).format('YYYY-MM-DD');
                this.edit('general');
                this.error.general = {};
                this.$axios.get('/sanctum/csrf-cookie').then(response => {
                    this.$axios.post('/api/user/general', dataToSend)
                        .then(response => {
                            if (!response.data.success) {
                                this.edit('general');
                                this.error.general = response.data.data;
                            }
                            emitter.$emit('loading', false);
                            emitter.$emit('notify', response.data.success, response.data.message);
                        })
                        .catch(function (error) {
                            console.error(error);
                            emitter.$emit('loading', false);
                        });
                })
            } else {
                emitter.$emit('notify', false, 'Campi obbligatori non compilati')
            }
        },
        saveNewPassword(ev) {
            ev.preventDefault()
            if (!this.permission.edit) return;
            if (this.password.password && this.password.password.length > 0 &&
                this.password.password_confirmation && this.password.password_confirmation.length > 0
            ) {
                this.error.password = {};
                this.$axios.get('/sanctum/csrf-cookie').then(response => {
                    this.$axios.post('/api/user/new-password', this.password)
                        .then(response => {
                            if (!response.data.success) {
                                this.error.password = response.data.data;
                            } else {
                                this.password.password = this.password.password_confirmation = null;
                            }
                            emitter.$emit('loading', false);
                            emitter.$emit('notify', response.data.success, response.data.message);
                        })
                        .catch(function (error) {
                            console.error(error);
                        });
                })
            } else {
                emitter.$emit('notify', false, 'Campi obbligatori non compilati')
            }
        }
    },
    beforeRouteEnter(to, from, next) {
        if (!window.openGDR.isLoggedin) {
            return next('home');
        }
        next();
    }
}
</script>
