<template>
    <div class="container">
        <div class="row mb-3">
            <div class="col">
                <div class=" d-flex align-items-center p-3 text-white bg-primary rounded shadow-sm ">
                    <font-awesome-icon icon="fa-solid fa-shield-halved" class="fa-2x" />
                    <div class="lh-1 ms-3">
                        <h1 class="h5 mb-0 text-white lh-1 mb-1">Amministrazione: <strong>Lista Razze</strong></h1>
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
                ajax="/api/admin/race/get-list"
                class="table table-hover table-striped"
                width="100%"
                >
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nome</th>
                        <th>Conteggio</th>
                        <th>Visibile</th>
                        <th>Aperta</th>
                        <th>Registrazione</th>
                        <th></th>
                    </tr>
                </thead>
                <tfoot>
                    <tr>
                        <th>ID</th>
                        <th>Nome</th>
                        <th>Conteggio</th>
                        <th>Visibile</th>
                        <th>Aperta</th>
                        <th>Registrazione</th>
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
                { data: 'name' },
                { data: 'counter' },
                {
                    data: 'visible',
                    render: this.formatCheck
                },
                {
                    data: 'open',
                    render: this.formatCheck
                },
                {
                    data: 'enable_for_registration',
                    render: this.formatCheck },
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
            return '<a href="' + data.link_edit + '" class="btn btn-primary">Modifica</a>';
        },
        formatCheck(data, type, row) {
            if (data == '1') {
                return '<span class="badge text-bg-success">SI</span>';
            } else {
                return '<span class="badge text-bg-danger">NO</span>';
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
