<template>
    <div>

        <v-data-table
            :headers="headers"
            :items="users"
            :items-per-page="50"
            :sort-by="['nachname']"
            :loading="loading"
            loading-text="Benutzer werden geladen..."
            no-data-text="Keine Benutzer gefunden"
            class="elevation-1"
            :footer-props="{
                'items-per-page-text': 'Benutzer pro Seite'
            }"
        >

            <template v-slot:item.freigabe_ind="{ item }">
                <span>{{ (item.freigabe_ind) ? 'Ja' : 'Nein' }}</span>
            </template>

            <template v-slot:item.actions="{ item }">

                <!-- https://vuetifyjs.com/en/components/data-tables/#crud-actions -->

                <v-btn
                    elevation="0"
                    style="background-color: transparent;"
                    small
                    @click="editUser(item)"
                >
                    <v-icon>mdi-account-edit</v-icon>
                </v-btn>

                <v-btn
                    elevation="0"
                    style="background-color: transparent;"
                    small
                    @click="deleteUser(item)"
                >
                    <v-icon>mdi-account-remove</v-icon>
                </v-btn>

            </template>

        </v-data-table>

        <v-dialog v-model="dialog" max-width="1200px">
            <v-card>

                <v-card-title>
                    <span class="text-h5">{{ formTitle }}</span>
                </v-card-title>

                <v-card-text>

                    <v-container fluid>

                        <v-row>

                            <v-col cols="12" md="6" sm="3">
                                <v-select
                                    :items="anrede"
                                    item-text="text"
                                    item-value="value"
                                    v-model="editedUser.anrede"
                                    label="Anrede"
                                    outlined
                                    hide-details
                                ></v-select>
                            </v-col>

                        </v-row>

                        <v-row>

                            <v-col cols="12" md="6" sm="3">
                                <v-text-field
                                    label="Vorname"
                                    v-model="editedUser.vorname"
                                    outlined
                                    hide-details
                                ></v-text-field>
                            </v-col>

                            <v-col cols="12" md="6" sm="3">
                                <v-text-field
                                    label="Nachname"
                                    v-model="editedUser.nachname"
                                    outlined
                                    hide-details
                                ></v-text-field>
                            </v-col>

                        </v-row>

                        <v-row>

                            <v-col cols="12" md="6" sm="3">
                                <v-text-field
                                    label="Knr"
                                    v-model="editedUser.knr"
                                    outlined
                                    hide-details
                                ></v-text-field>
                            </v-col>

                            <v-col cols="12" md="6" sm="3">
                                <v-text-field
                                    label="E-Mail Adresse"
                                    v-model="editedUser.email"
                                    outlined
                                    hide-details
                                ></v-text-field>
                            </v-col>

                        </v-row>

                        <v-row>

                            <v-col cols="12" md="6" sm="3">
                                <v-text-field
                                    label="IDNR / Benutzername"
                                    v-model="editedUser.idnr"
                                    outlined
                                    hide-details
                                ></v-text-field>
                            </v-col>

                            <v-col cols="12" md="6" sm="3">
                                <v-text-field
                                    label="Passwort"
                                    v-model="editedUser.password"
                                    outlined
                                    hide-details
                                ></v-text-field>
                            </v-col>

                        </v-row>

                        <v-row>

                            <v-col cols="12" md="4" sm="2">

                                <v-switch hide-details
                                    label="Mitarbeiter freigegeben"
                                          v-model="editedUser.freigabe_ind"
                                ></v-switch>

                            </v-col>

                            <v-col cols="12" md="4" sm="2">

                                <v-switch hide-details
                                    label="Passwortänderung verifiziert"
                                          v-model="editedUser.verified"
                                ></v-switch>

                            </v-col>

                            <v-col cols="12" md="4" sm="2">

                                <v-switch hide-details
                                    label="Administrator"
                                          v-model="editedUser.Admin"
                                ></v-switch>

                            </v-col>

                        </v-row>

                    </v-container>

                </v-card-text>

                <v-card-actions>

                    <v-spacer></v-spacer>

                    <v-btn
                        color="primary"
                        text
                        @click="closeEditDialog"
                    >
                        Abbrechen
                    </v-btn>

                    <v-btn
                        color="primary"
                        text
                        @click="saveUserClicked"
                    >
                        Speichern
                    </v-btn>

                </v-card-actions>

            </v-card>
        </v-dialog>

        <v-dialog v-model="deleteDialog" max-width="500px">
            <v-card>
                <v-card-title class="text-h5">Möchten Sie den Benutzer löschen?</v-card-title>
                <v-card-actions>
                    <v-spacer></v-spacer>
                    <v-btn color="primary" text @click="closeDeleteDialog">Abbrechen</v-btn>
                    <v-btn color="primary" text @click="deleteUserClicked">Löschen</v-btn>
                    <v-spacer></v-spacer>
                </v-card-actions>
            </v-card>
        </v-dialog>

    </div>
</template>

<script>
import axios from "../api";

export default {
    name: "Benutzer",

    data() {

        return {
            headers: [
                { text: 'IDNR / Benutzername', value: 'idnr' },
                { text: 'Name', value: 'name' },
                { text: 'E-Mail', value: 'email' },
                { text: 'Freigegeben', value: 'freigabe_ind' },
                { text: 'Aktionen', value: 'actions' }
            ],

            loading: false,

            users: [],

            dialog: false,
            deleteDialog: false,
            editedIndex: -1,

            anrede: [
                { text: 'Frau', value: 'Frau' },
                { text: 'Herr', value: 'Herr' }
            ],

            editedUser: {
                id: 0,
                knr: '',
                idnr: '',
                anrede: '',
                nachname: '',
                vorname: '',
                email: '',
                freigabe_ind: 0,
                verified: 0,
                Admin: 0,
                password: ''
            },

            defaultUser: {
                id: 0,
                knr: '',
                idnr: '',
                anrede: '',
                nachname: '',
                vorname: '',
                email: '',
                freigabe_ind: 0,
                verified: 0,
                Admin: 0,
                password: ''
            }

        }

    },

    mounted() {

        // TODO: Benutzer aus Laravel abrufen
        this.loadUsers();

    },

    computed: {
        formTitle () {
            return this.editedIndex === -1 ? 'Benutzer anlegen' : 'Benutzer bearbeiten';
        },
    },

    methods: {

        async loadUsers() {

            this.loading = true;

            const res = await axios.get('/users');

            this.users = res.data;

            this.loading = false;

        },

        editUser(user) {

            this.editedIndex = this.users.indexOf(user);
            this.editedUser = Object.assign({}, user);
            this.dialog = true;

        },

        deleteUser(user) {
            this.editedIndex = this.users.indexOf(user);
            this.editedUser = Object.assign({}, user);
            this.deleteDialog = true;
        },

        closeEditDialog() {

            this.dialog = false;

            this.$nextTick(() => {
                this.editedUser = Object.assign({}, this.defaultUser)
                this.editedIndex = -1
            })

        },

        closeDeleteDialog() {
            this.deleteDialog = false;
        },

        deleteUserClicked() {

            axios.get('/sanctum/csrf-cookie').then(response => {

                    axios.delete('/users', {
                        data: {
                            Knr: this.editedUser.knr,
                        }
                    })
                    .then(res => {
                        this.deleteDialog = false;
                        this.loadUsers();
                    })

            });

        },

        saveUserClicked() {

            const user = {
                ...this.editedUser
            };

            axios.get('/sanctum/csrf-cookie').then(response => {

                axios.post('/users', {
                    user
                })
                .then(res => {
                    this.loadUsers();
                })

            });

        }

    }

}
</script>

<style scoped>
</style>
