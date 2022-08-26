<template>
    <div class="container">
        <div class="row mb-3">
            <div class="col">
                <div class=" d-flex align-items-center p-3 text-white bg-primary rounded shadow-sm ">
                    <font-awesome-icon icon="fa-solid fa-shield-halved" class="fa-2x" />
                    <div class="lh-1 ms-3">
                        <h1 class="h5 mb-0 text-white lh-1 mb-1">Amministrazione: <strong>Lista Utenti</strong></h1>
                    </div>
                </div>
            </div>
        </div>
        <div class="row mb-3" v-if="!generalLoading">
            <div class="col">

                <DataTable
                :columns="columns"
                :options="{
                    processing: true,
                    serverSide: true,
                }"
                ajax="/api/admin/user/get-list"
                class="table table-hover table-striped"
                width="100%"
                >
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Username</th>
                        <th>Tipologia</th>
                        <th>Creato il</th>
                        <th>Ultimo Accesso</th>
                        <th>Stato</th>
                        <th></th>
                    </tr>
                </thead>
                <tfoot>
                    <tr>
                        <th>ID</th>
                        <th>Username</th>
                        <th>Tipologia</th>
                        <th>Creato il</th>
                        <th>Ultimo Accesso</th>
                        <th>Stato</th>
                        <th></th>
                    </tr>
                </tfoot>
                </DataTable>

            </div>
        </div>

    </div>
</template>
<script>
import emitter from '../../../emitter'
import { mapGetters } from 'vuex'
import { h } from 'vue'

import DataTable from 'datatables.net-vue3';
import DataTableBs5 from 'datatables.net-bs5';


DataTable.use(DataTableBs5);

export default {
    components: {
        DataTable
    },
    computed: {
        ...mapGetters([
            'generalLoading',
            'isAdmin'
        ])
    },
    data() {
        return {
            columns: [
                { data: 'id' },
                { data: 'username' },
                { data: 'level_label' },
                { data: 'created_at' },
                { data: 'last_login' },
                { data: 'status_label' },
                {
                    data: null,
                    render: this.formatAction
                }
            ]
        }
    },
    mounted() {

        this.$axios.get('/sanctum/csrf-cookie').then(response => {
            this.$axios.get('/api/auth/permission/check/view/user', {})
                .then(response => {
                    if (!response.data.success) {
                        emitter.$emit('notify', response.data.success, response.data.message);
                        this.$router.push('/');
                        return;
                    }

                })
                .catch(function (error) {
                    console.error(error);
                });
        })
    },
    methods: {
        formatAction(data, type, row) {
            this.$forceUpdate();
            return '<a href="/utente/profilo/' + data.id + '" class="btn btn-primary">Vedi</a>';
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
