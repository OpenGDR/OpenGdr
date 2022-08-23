<template>
    <div class="container">
        <div class="row mb-3">
            <div class="col">
                <div class=" d-flex align-items-center p-3 text-white bg-primary rounded shadow-sm ">
                    <font-awesome-icon icon="fa-solid fa-shield-halved" class="fa-2x" />
                    <div class="lh-1 ms-3">
                        <h1 class="h5 mb-0 text-white lh-1 mb-1">Amministrazione: <strong>Lista Attività</strong></h1>
                    </div>
                </div>
            </div>
        </div>
        <div class="row mb-3" v-if="isAdmin && !generalLoading">
            <div class="col">

                <DataTable
                :columns="columns"
                :options="{
                    processing: true,
                    serverSide: true,
                    order: [[0, 'desc']]
                }"
                ajax="/api/admin/log/get-list"
                class="table table-hover table-striped"
                width="100%"
                >
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Tipologia</th>
                        <th>Livello</th>
                        <th>Modello</th>
                        <th>ID Correlato</th>
                        <th>Note</th>
                        <th>Creato il</th>
                        <th></th>
                    </tr>
                </thead>
                <tfoot>
                    <tr>
                        <th>ID</th>
                        <th>Tipologia</th>
                        <th>Livello</th>
                        <th>Modello</th>
                        <th>ID Correlato</th>
                        <th>Note</th>
                        <th>Creato il</th>
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
                { data: 'type' },
                { data: 'label_level' },
                { data: 'model' },
                { data: 'related_id' },
                { data: 'note' },
                { data: 'created_at' },
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
            if (data.related_link) {
                return '<a href="' + data.related_link + '" class="btn btn-primary">Vedi</a>';
            } else {
                return '';
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
