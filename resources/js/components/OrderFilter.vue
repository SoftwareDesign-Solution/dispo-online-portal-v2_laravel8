<template>
    <div>

        <v-row>

            <v-col cols="12" sm="8" md="4" v-if="!hideAuftragsdatum">
                <date-picker
                    label="Auftragsdatum von"
                    v-model="filter.auftragsdatumFrom"
                ></date-picker>
            </v-col>

            <v-col cols="12" sm="8" md="4" v-if="!hideAuftragsdatum">
                <date-picker
                    label="Auftragsdatum bis"
                    v-model="filter.auftragsdatumTo"
                ></date-picker>
            </v-col>

            <v-col cols="12" sm="8" md="4">
                <v-text-field
                    label="Uhrzeit von"
                    v-mask="'##:##'"
                    v-model="filter.timeFrom"
                    outlined
                    hide-details
                ></v-text-field>
            </v-col>

            <v-col cols="12" sm="8" md="4">
                <v-text-field
                    label="Uhrzeit bis"
                    v-mask="'##:##'"
                    v-model="filter.timeTo"
                    outlined
                    hide-details
                ></v-text-field>
            </v-col>

            <v-col cols="12" sm="8" md="4">
                <v-select
                    :items="from"
                    item-text="text"
                    item-value="value"
                    label="Start"
                    v-model="filter.start"
                    outlined
                    hide-details
                ></v-select>
            </v-col>

            <v-col cols="12" sm="8" md="4">
                <v-select
                    :items="to"
                    item-text="text"
                    item-value="value"
                    label="Ziel"
                    v-model="filter.ziel"
                    outlined
                    hide-details
                ></v-select>
            </v-col>

            <v-col cols="12" sm="8" md="4">
                <v-select
                    :items="projects"
                    item-text="text"
                    item-value="value"
                    label="Projekt"
                    v-model="filter.project"
                    outlined
                    hide-details
                ></v-select>
            </v-col>

            <v-col cols="12" sm="8" md="4">
                <v-select
                    :items="shiftdays"
                    item-text="text"
                    item-value="value"
                    label="Schichttage"
                    v-model="filter.schichttag"
                    outlined
                    hide-details
                ></v-select>
            </v-col>

        </v-row>

        <v-row>

            <v-col cols="12">

                <v-btn color="success" class="mr-5" @click="applyClicked">
                    <v-icon>mdi-filter-check</v-icon> <!-- mdi-filter-check -->
                    Anwenden
                </v-btn>

                <v-btn color="error" @click="resetClicked">
                    <v-icon>mdi-filter-remove</v-icon> <!-- mdi-filter-remove -->
                    Zurücksetzen
                </v-btn>

            </v-col>

        </v-row>

    </div>
</template>

<script>
import DatePicker from './DatePicker';


export default {
    name: "OrderFilter",

    components: {
        DatePicker
    },

    props: {
        filter: {
            auftragsdatumFrom: '',
            auftragsdatumTo: '',
            timeFrom: '',
            dateTo: '',
            timeTo: '',
            project: '',
            start: '',
            ziel: '',
            schichttag: '',
            type: '',
        },
        from: Array,
        to: Array,
        projects: Array,
        shiftdays: Array,
        hideAuftragsdatum: false,
    },

    data() {

        return {
            /*
            filter: {
                auftragsdatumFrom: '',
                auftragsdatumTo: '',
                timeFrom: '',
                dateTo: '',
                timeTo: '',
                project: '',
                start: '',
                ziel: ''
            },
            */
        }
    },

    methods: {

        applyClicked() {
            this.$emit('filterChanged', this.filter);
        },

        resetClicked() {
            this.filter.auftragsdatumFrom = '';
            this.filter.auftragsdatumTo = '';
            this.filter.ziel = '';
            this.filter.start = '';
            this.filter.project = '';
            this.filter.timeFrom = '';
            this.filter.timeTo = '';
            this.filter.schichttag = '';
            this.$emit('filterChanged', this.filter);
        }
    }
}
</script>

<style scoped>

</style>
