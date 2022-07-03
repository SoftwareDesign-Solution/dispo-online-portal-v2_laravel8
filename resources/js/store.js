import Vue from 'vue';
import Vuex from 'vuex';
import createPersistedState from 'vuex-persistedstate'
import axios from "./api";
import router from "./routes";

Vue.use(Vuex);

const store = new Vuex.Store({

    plugins:[
        createPersistedState()
    ],

    state: {
        authenticated:false,
        token: '',
        user:{}
    },

    getters: {
        authenticated(state){
            return state.authenticated
        },
        token(state) {
            return state.token
        },
        user(state){
            return state.user
        }
    },

    mutations: {
        SET_AUTHENTICATED (state, value) {
            state.authenticated = value
        },
        SET_TOKEN (state, value) {
            state.token = value
        },
        SET_USER (state, value) {
            state.user = value
        }
    },

    actions: {
        login({commit}){
            return axios.get('/user').then(({data})=>{
                console.log(data);
                commit('SET_USER', data);
                commit('SET_AUTHENTICATED',true)
                router.push({name:'Startseite'})
            }).catch(({response:{data}})=>{
                commit('SET_USER', {});
                commit('SET_AUTHENTICATED',false)
            })
        },
        logout({commit}){
            commit('SET_USER',{});
            commit('SET_TOKEN','');
            commit('SET_AUTHENTICATED',false);
            localStorage.setItem('jwt','');
        }
    }

});

export default store;
