<template>
    <div class="container">
        <div class="row mb-3">
            <div class="col d-flex align-items-center py-3 text-white bg-primary rounded shadow-sm">
                <font-awesome-icon icon="fa-solid fa-user" class="fa-2x" />
                <div class="lh-1 ms-3">
                    <h1 class="h5 mb-0 text-white lh-1">Profilo utente : <strong>{{ userData.username }}</strong></h1>
                    <small>Livello {{ userData.level_label }}</small>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col col-lg-6 px-0">
                <form>
                    <div class="card">
                        <div class="card-header d-flex align-items-center">
                            <h2 class="me-auto h5">
                                Informazioni generali
                            </h2>
                            <div class="btn btn-sm" :class="{ 'btn-outline-primary': !editSection.includes('general'), 'btn-success': editSection.includes('general') }" @click.stop="edit('general')">
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
                        <div class="card-footer d-flex">
                            <button @click.stop="saveGeneral" class="btn btn-primary ms-auto" :disabled="!editSection.includes('general')">Salva</button>
                        </div>
                    </div>
                </form>
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
            'userData'
        ])
    },
    data() {
        return {
            editSection: [],
            general: {
                username: null,
                email: null,
                date_of_birth: null,
                motto: '',
            },
            error: {
                general: {}
            }
        }
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
                            this.loading = false;
                        });
                })
            } else {
                emitter.$emit('notify', false, 'Campi obbligatori non compilati')
            }
        }
    },
    watch: {
        userData(newUserData, oldUserData) {
            this.general.username = newUserData.username;
            this.general.email = newUserData.email;
            this.general.date_of_birth = moment(newUserData.date_of_birth, 'YYYY-MM-DD').toDate();
            this.general.motto = newUserData.motto;
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
