<template>
    <v-app>
        <v-navigation-drawer app color="primary" v-model="drawer" :clipped="clipped">

            <v-list-item>
                <v-list-item-content class="ml-4 white--text">
                    <v-list-item-title class="text-h6" color="white">
                        TRENDline Dispo<br />Online
                    </v-list-item-title>
                </v-list-item-content>
            </v-list-item>

            <v-divider></v-divider>

            <v-list>

                <v-list-item :to="{ name: 'Startseite' }" exact>
                    <v-list-item-content class="ml-4 white--text">
                        <v-list-item-title color="white">Startseite</v-list-item-title>
                    </v-list-item-content>
                </v-list-item>

                <v-divider></v-divider>

                <v-list-item :to="{ name: 'Offene Aufträge' }" exact>
                    <v-list-item-content class="ml-4 white--text">
                        <v-list-item-title color="white">Offene Aufträge</v-list-item-title>
                    </v-list-item-content>
                </v-list-item>

                <v-list-item :to="{ name: 'Ersatzstichproben' }" exact>
                    <v-list-item-content class="ml-4 white--text">
                        <v-list-item-title color="white">Ersatzstichproben</v-list-item-title>
                    </v-list-item-content>
                </v-list-item>

                <v-list-item :to="{ name: 'Schichttagsaufträge' }" exact>
                    <v-list-item-content class="ml-4 white--text">
                        <v-list-item-title color="white">Schichttagsaufträge</v-list-item-title>
                    </v-list-item-content>
                </v-list-item>

                <v-divider></v-divider>

                <v-list-item :to="{ name: 'Benutzer' }" exact v-if="user.Admin === 1">
                    <v-list-item-content class="ml-4 white--text">
                        <v-list-item-title color="white">Benutzerverwaltung</v-list-item-title>
                    </v-list-item-content>
                </v-list-item>

                <v-list-item :to="{ name: 'Passwort ändern' }" exact>
                    <v-list-item-content class="ml-4 white--text">
                        <v-list-item-title color="white">Passwort ändern</v-list-item-title>
                    </v-list-item-content>
                </v-list-item>

                <v-divider></v-divider>

                <v-list-item :to="{ name: 'Datenschutzerklärung' }" exact>
                    <v-list-item-content class="ml-4 white--text">
                        <v-list-item-title color="white">Datenschutzerklärung</v-list-item-title>
                    </v-list-item-content>
                </v-list-item>

                <v-list-item :to="{ name: 'Impressum' }" exact>
                    <v-list-item-content class="ml-4 white--text">
                        <v-list-item-title color="white">Impressum</v-list-item-title>
                    </v-list-item-content>
                </v-list-item>

                <v-divider></v-divider>

                <v-list-item @click="logout">
                    <v-list-item-content class="ml-4 white--text">
                        <v-list-item-title color="white">Abmelden</v-list-item-title>
                    </v-list-item-content>
                </v-list-item>

            </v-list>

        </v-navigation-drawer>

        <v-app-bar app class="white" height="60px" :clipped-left="clipped">

            <v-app-bar-nav-icon @click="drawer = !drawer"></v-app-bar-nav-icon>

            <v-toolbar-title>{{ this.$route.name }}</v-toolbar-title>

            <v-spacer />

            <!--
            <v-btn class="mr-5">
                Manuel Kübler
            </v-btn>
            -->

            <v-menu offset-y>
                <template v-slot:activator="{ on, attrs }">
                    <v-btn
                        color="primary"
                        class="mr-5"
                        dark
                        v-bind="attrs"
                        v-on="on"
                    >
                        {{ username }}
                    </v-btn>
                </template>
                <v-list>
                    <v-list-item>
                        <v-list-item-title @click="logout">Abmelden</v-list-item-title>
                    </v-list-item>
                </v-list>
            </v-menu>

            <v-btn color="primary" to="/checkout">
                Auftragskorb
            </v-btn>

        </v-app-bar>

        <v-main>

            <!-- Provides the application the proper gutter -->
            <div class="pa-2">

                <v-container>

                    <router-view></router-view>

                </v-container>

            </div>
        </v-main>

        <v-navigation-drawer
            v-model="showOrderCart"
            absolute
            right
        >
            <template v-slot:prepend>
                <v-list-item>
                    <v-list-item-content>
                        <v-list-item-title>Auftragskorb (0)</v-list-item-title>
                    </v-list-item-content>
                </v-list-item>
            </template>

            <v-divider></v-divider>

            <v-list dense>
                <v-list-item>
                    <v-list-item-content>
                        <v-list-item-title>Test</v-list-item-title>
                    </v-list-item-content>
                </v-list-item>
            </v-list>
        </v-navigation-drawer>

    </v-app>
</template>

<script>
import store from "../store";
import axios from "../api";

export default {
    name: "AuthenticatedApp",
    components: {},


    data() {
        return {
            drawer: true,
            clipped: false,
            showOrderCart: false
        }
    },

    computed: {

        authenticated() {
            return store.getters.authenticated;
        },

        user() {
            return store.getters.user;
        },

        username() {
            const user = this.user;
            return user.vorname + ' ' + user.nachname;
        },

        anrede() {
            const user = this.user;
            return user.anrede + ' ' + user.nachname;
        }

    },

    mounted() {

    },

    methods: {

        logout() {

            axios.get('/sanctum/csrf-cookie').then(response => {

                store.dispatch('logout');
                this.$router.push({ name: 'login' });

                /*
                axios.post('/logout').then(data => {

                    store.dispatch('logout');
                    this.$router.push({ name: 'login' });

                });
                */

            });

        }

    }

}
</script>

<style scoped>

</style>
