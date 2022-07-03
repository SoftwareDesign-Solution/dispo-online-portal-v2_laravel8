<template>
    <div>

        <!--<h2>Offene Aufträge</h2>-->

        <auftragsfilter class="mb-5" v-on:filterChanged="filterChanged"></auftragsfilter>

        <v-data-table
            :headers="headers"
            :items="orders"
            :items-per-page="100"
            group-by="PaketNr"
            class="elevation-1"
            loading-text="Aufträge werden geladen..."
            no-data-text="Keine Aufträge gefunden"
            :footer-props="{
                'items-per-page-text': 'Aufträge pro Seite'
            }"
        >

            <template v-slot:group.header="{group, isOpen, toggle, items}">
                <td :colspan="headers.length">
                    <v-icon @click="toggle">
                        {{ isOpen ? 'mdi-minus' : 'mdi-plus' }}
                    </v-icon>
                    <span>{{ group }}</span>
                    <v-btn class="float-right" color="primary" small @click="addOrders(items)">Hinzufügen</v-btn> <!-- @click="addClick(items)" -->
                </td>
            </template>

            <template v-slot:item.Auftragsdatum="{ item }">
                <span>{{ new Date(item.Ab_DatumUhrzeit).toLocaleDateString() }}</span>
            </template>

            <template v-slot:item.Ab_Zeit="{ item }">
                <span>{{ new Date(item.Ab_DatumUhrzeit).toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' }) }}</span>
            </template>

            <template v-slot:item.An_Zeit="{ item }">
                <span>{{ new Date(item.An_DatumUhrzeit).toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' }) }}</span>
            </template>

            <template v-slot:item.Vorschlagsdatum="{ item }">
                <v-select
                    :items="item.Schichttage"
                    item-text="Datum"
                    item-value="Datum"
                    label="Bitte auswählen..."
                    no-data-text="Keine Schichttage verfügbar"
                    :single-line="true"
                ></v-select>
            </template>

            <template v-slot:item.Auslagen="{ item }">
                <v-text-field v-if="item.ObergrenzeAuslagen > 0" type="number" min="0" :max="item.ObergrenzeAuslagen" single-line></v-text-field>
            </template>

        </v-data-table>

        {{ type }}

    </div>
</template>

<script>
import OrderFilter from "../components/OrderFilter";
import axios from "../api";

export default {
    name: "OpenOrders",

    components: {
        'auftragsfilter': OrderFilter
    },

    props: {
      type: String
    },

    data() {
        return {
            filter: {
                auftragsdatum: '',
                timeFrom: '',
                dateTo: '',
                timeTo: '',
                project: '',
                start: '',
                ziel: ''
            },
            headers: [
                { text: 'Tour', value: 'PaketNr' },
                { text: 'Projekt', value: 'Projekt' },
                { text: 'Datum', value: 'Auftragsdatum' },
                { text: 'Ab', value: 'Ab_Zeit' },
                { text: 'Start', value: 'Ab_Ort' },
                { text: 'An', value: 'An_Zeit' },
                { text: 'Ziel', value: 'An_Ort' },
                { text: 'BS', value: 'Bs' },
                { text: 'Dauer', value: 'Dauer' },
                { text: 'Vorschlagsdatum', value: 'Vorschlagsdatum' },
                { text: 'Auslagen', value: 'Auslagen' },
                //{ text: 'Aktionen', value: 'actions' }
            ],
            orders: [],
                /*
                {
                    Knr: 1394826,
                    PaketNr: '1281727',
                    Projekt: 'RES5',
                    Auftragsdatum: '2022-02-19',
                    Ab_DatumUhrzeit: '2022-02-19T00:37:00',
                    Ab_Zeit: '00:37:00',
                    Ab_Ort: 'Aachen Hbf',
                    An_DatumUhrzeit: '2022-02-19T02:24:00',
                    An_Zeit: '02:24:00',
                    An_Ort: 'Duisburg Hbf',
                    Bs: '1',
                    Dauer: 107,
                    ObergrenzeAuslagen: 0,
                    dateselect: '',
                    datevalues: ['11.02.2022', '12.02.2022', '13.02.2022', '14.02.2022'],
                },
                {
                    Knr: 1394850,
                    PaketNr: '1281590',
                    Projekt: 'RES5',
                    Auftragsdatum: '2022-02-26',
                    Ab_DatumUhrzeit: '2022-02-26T02:24:00',
                    Ab_Zeit: '02:24:00',
                    Ab_Ort: 'Mönchengladbach Hbf',
                    An_DatumUhrzeit: '2022-02-26T05:43:00',
                    An_Zeit: '05:43:00',
                    An_Ort: 'Essen Hbf',
                    Bs: '1',
                    Dauer: 65,
                    ObergrenzeAuslagen: 10,
                    dateselect: '',
                    datevalues: ['11.02.2022', '12.02.2022', '13.02.2022', '14.02.2022'],
                },
                {
                    Knr: 1394851,
                    PaketNr: '1281590',
                    Projekt: 'RES5',
                    Auftragsdatum: '2022-02-26',
                    Ab_DatumUhrzeit: '2022-02-26T03:35:00',
                    Ab_Zeit: '03:35:00',
                    Ab_Ort: 'Duisburg Hbf',
                    An_DatumUhrzeit: '2022-02-26T04:20:00',
                    An_Zeit: '04:20:00',
                    An_Ort: 'Mönchengladbach Hbf',
                    Bs: '1',
                    Dauer: 45,
                    ObergrenzeAuslagen: 10,
                    dateselect: '',
                    datevalues: ['11.02.2022', '12.02.2022', '13.02.2022', '14.02.2022'],
                },
                {
                    Knr: 1394896,
                    PaketNr: '1281590',
                    Projekt: 'RES5',
                    Auftragsdatum: '2022-02-26',
                    Ab_DatumUhrzeit: '2022-02-26T06:16:00',
                    Ab_Zeit: '06:16:00',
                    Ab_Ort: 'Essen Hbf',
                    An_DatumUhrzeit: '2022-02-26T08:27:00',
                    An_Zeit: '08:27:00',
                    An_Ort: 'Aachen Hbf',
                    Bs: '1',
                    Dauer: 131,
                    ObergrenzeAuslagen: 10,
                    dateselect: '',
                    datevalues: ['11.02.2022', '12.02.2022', '13.02.2022', '14.02.2022'],
                }
            ],
            */
        }
    },

    mounted() {

        this.loadOrders();

    },

    methods: {

        filterChanged(filter) {
            console.log(filter);
        },

        async loadOrders() {

            const res = await axios.get('/orders');

            this.orders = res.data;

        },

        addOrders(items) {
            console.table(items);
        }

    }

}
</script>

<style scoped>

</style>
