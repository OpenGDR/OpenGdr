import axios from 'axios'
import { createStore } from "vuex";

const storage = createStore({
    state: {
        loading: false,
        user: {
            permissions: {
                admin: {
                    show: false
                }
            }
        },
    },
    mutations: {
    },
    getters: {
        generalLoading(state) {
            return state.loading;
        },
        userData(state) {
            return state.user;
        },
        isLoggedIn(state) {
            return (Object.keys(state.user).length && window.openGDR.isLoggedin)
        },
        isAdmin(state) {
            return (Object.keys(state.user).length && state.user.level == 1);
        }
    },
    actions: {
        updateLoaging({ state }, status) {
            state.loading = status;
        },
        getUserData({ state }, callback) {
            if (!window.openGDR.isLoggedin) {
                return;
            }
            axios.get('/sanctum/csrf-cookie').then((response) => {
                axios.get('/api/user/data', {})
                    .then(response => {
                        if (response.data.success) {
                            state.user = response.data.data;
                        }
                        callback();
                    })
                    .catch(function (error) {
                        console.error(error);
                    });
            })
        }
    },
});


export default storage;
