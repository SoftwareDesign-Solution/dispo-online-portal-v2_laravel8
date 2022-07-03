<template>
    <v-app>
        <v-container fluid fill-height>
            <v-layout align-center justify-center>
                <v-flex xs12 sm8 md4>
                    <v-card class="elevation-12">
                        <v-toolbar color="primary">
                            <v-toolbar-title class="white--text">ANMELDUNG</v-toolbar-title>
                            <v-spacer></v-spacer>
                            <v-toolbar-title class="float-right white--text">Passwort vergessen</v-toolbar-title>
                        </v-toolbar>
                        <v-card-text>

                            <h1>TRENDline Dispo Online</h1>

                            <v-form>
                                <v-text-field
                                    name="login"
                                    label="Kreditoren-Nr."
                                    type="text"
                                    v-model="request.idnr"
                                ></v-text-field>
                                <v-text-field
                                    id="password"
                                    name="password"
                                    label="Password"
                                    type="password"
                                    v-model="request.password"
                                ></v-text-field>
                            </v-form>
                        </v-card-text>
                        <v-card-actions>

                            <v-btn color="primary" @click="loginClicked">Anmelden</v-btn>

                        </v-card-actions>

                        <v-card-actions>

                            <v-divider></v-divider>

                            <v-card-text>
                                <ul class="float-right d-inline-flex" style="list-style: none;">
                                    <li class="ml-2">
                                        <strong @click="datenschutzClicked">Datenschutzerklärung</strong>
                                    </li>
                                    <li class="ml-2">
                                        <strong @click="impressumClicked">Impressum</strong>
                                    </li>
                                </ul>
                            </v-card-text>

                        </v-card-actions>
                    </v-card>
                </v-flex>
            </v-layout>

            <datenschutz-dialog v-model="impressum" title="Datenschutzerklärung">
                <Datenschutzerklaerung></Datenschutzerklaerung>
            </datenschutz-dialog>

            <impressum-dialog v-model="datenschutz" title="Impressum">
                <Impressum></Impressum>
            </impressum-dialog>

        </v-container>
    </v-app>
</template>

<script>
import ModalComponent from "../components/ModalComponent";
import Datenschutzerklaerung from "./Datenschutzerklaerung";
import Impressum from "./Impressum";
import store from "../store";
import axios from "../api";

export default {
    name: "Login",
    components: {
        'datenschutz-dialog': ModalComponent,
        'impressum-dialog': ModalComponent,
        Datenschutzerklaerung,
        Impressum
    },


    data() {
        return {
            request: {
                idnr: '',
                password: ''
            },
            impressum: false,
            datenschutz: false,
        }
    },

    methods: {

        loginClicked() {

            axios.get('/sanctum/csrf-cookie').then(response => {

                axios.post('/auth/login', this.request).then(res => {

                    console.log(res.data);

                    if ((res.data.authenticated) && (res.data.access_token !== ''))
                    {
                        localStorage.setItem('jwt', res.data.access_token)
                        store.commit('SET_TOKEN', res.data.access_token);
                        store.dispatch('login');
                    }

                }).catch(error => console.log(error));

            });

        },

        /*
        getUser() {

            axios.get('/sanctum/csrf-cookie').then(response => {

                axios.get('/user').then(response2 => {
                    console.log(response2);
                });

            });

        },
        */

        datenschutzClicked() {
            this.datenschutz = true;
        },

        impressumClicked() {
            this.impressum = true;
        }

    }

}
</script>

<style>
</style>
