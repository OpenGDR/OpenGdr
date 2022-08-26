<template>
    <div class="container">
        <div class="row mb-3">
            <div class="col">
                <div class=" d-flex align-items-center p-3 text-white bg-primary rounded shadow-sm ">
                    <font-awesome-icon icon="fa-solid fa-shield-halved" class="fa-2x" />
                    <div class="lh-1 ms-3">
                        <h1 v-if="create" class="h5 mb-0 text-white lh-1 mb-1">Amministrazione: <strong>Crea razza</strong></h1>
                        <h1 v-else class="h5 mb-0 text-white lh-1 mb-1">Amministrazione: <strong>Modifica razza {{data.name}}</strong></h1>
                    </div>
                </div>
            </div>
        </div>
        <div class="row mb-3" v-if="!generalLoading">
            <div class="col">


            </div>
        </div>

    </div>
</template>
<script>
import emitter from '../../../emitter'
import { mapGetters } from 'vuex'

export default {
    components: {
    },
    computed: {
        ...mapGetters([
            'generalLoading',
            'isAdmin'
        ])
    },
    data() {
        return {
            create: true,
            data: {
                name: null
            }
        }
    },
    mounted() {
        this.create = (this.$route.params.slug) ? false : true;
        this.$axios.get('/sanctum/csrf-cookie').then(response => {
            let permission = (!this.create) ? 'update' : 'create';
            this.$axios.get('/api/auth/permission/check/' + permission + '/race/' + this.$route.params.slug , {})
                .then(response => {
                    if (!response.data.success) {
                        emitter.$emit('notify', response.data.success, response.data.message);
                        this.$router.push('/');
                        return;
                    }

                    if (!this.create) {
                        this.$axios.get('/api/admin/race/data/' + this.$route.params.slug, {})
                            .then(response => {
                                console.log(response.data);
                                if (!response.data.success) {
                                    emitter.$emit('notify', response.data.success, response.data.message);
                                    this.$router.push('/');
                                    return;
                                } else {
                                    this.data = response.data.data;

                                    this.$forceUpdate();
                                    emitter.$emit('loading', false);
                                }

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
    },
    methods: {
        getData() {

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
