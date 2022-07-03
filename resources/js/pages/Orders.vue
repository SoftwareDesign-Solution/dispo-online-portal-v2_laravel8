<template>
    <div>

        <!--<h2>Offene Aufträge</h2>-->

        <auftragsfilter
            class="mb-5"
            v-bind:from="from"
            v-bind:filter="filter"
            v-bind:to="to"
            v-bind:projects="projects"
            v-bind:shiftdays="shiftdays"
            v-bind:hideAuftragsdatum="type === 'PastOrders'"
            v-on:filterChanged="filterChanged"></auftragsfilter>

        <!--  -->
        <v-data-table
            :headers="headers"
            :items="orders"
            :items-per-page="100"
            :group-by="['PaketNr']"
            :custom-sort="customSort"
            :sort-by="['Ab_DatumUhrzeit']"
            class="elevation-1"
            :loading="loading"
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
                <v-select v-if="((type == 'PastOrders') || (type == 'ShiftDayOrders') || (item.Datumsvorschlag == 1))"
                          :items="item.Schichttage"
                          item-text="Datum"
                          item-value="Datum"
                          label="Bitte auswählen..."
                          no-data-text="Keine Schichttage verfügbar"
                          :single-line="true"
                ></v-select>
            </template>

            <template v-slot:item.Auslagen="{ item }">
                <v-text-field v-if="item.ObergrenzeAuslagen > 0" type="number" min="0" single-line clearable v-on:change="changedAuslagen($event, item)" v-model="item.Auslagen"></v-text-field>
            </template>

        </v-data-table>

    </div>
</template>

<script>
import OrderFilter from "../components/OrderFilter";
import axios from "../api";

export default {
    name: "Orders",

    components: {
        'auftragsfilter': OrderFilter
    },

    props: {
        type: String
    },

    data() {
        return {
            from: [],
            to: [],
            projects: [],
            shiftdays: [],
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
            headers: [
                { text: 'Tour', value: 'PaketNr', sortable: true },
                { text: 'Projekt', value: 'Projekt' },
                { text: 'Datum', value: 'Auftragsdatum' },
                { text: 'Schichttag', value: 'Schichttag' },
                { text: 'Ab', value: 'Ab_Zeit' },
                { text: 'Start', value: 'Ab_Ort' },
                { text: 'An', value: 'An_Zeit' },
                { text: 'Ziel', value: 'An_Ort' },
                { text: 'BS', value: 'Bs' },
                { text: 'Dauer', value: 'Dauer' },
                { text: 'Personen', value: 'AnzahlPers' },
                { text: 'Vorschlagsdatum', value: 'Vorschlagsdatum' },
                { text: 'Auslagen', value: 'Auslagen' },
                //{ text: 'Aktionen', value: 'actions' }
            ],
            loading: false,
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

    watch: {
        /*
        $route(to, from) {
            this.loadOrders();
        }
        */
        type: function(newVal, oldVal) {

            this.filter = {
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
            };

            this.orders = [];

            this.loadOrders();
        }
    },

    methods: {

        filterChanged(filter) {

            this.filter = filter;

            this.loadOrders();

            /*
            const filter2 = {
                ...filter,
                type: this.type
            };

            console.log(filter2);

            this.loading = true;

            axios.get('/sanctum/csrf-cookie').then(response => {

                axios.post('/orders', {
                    filter: filter2
                })
                    .then(res => {

                        console.log(res.data);

                        this.projects = res.data.Projects;
                        this.from = res.data.Start;
                        this.to = res.data.Ziel;
                        this.orders = res.data.data;

                        this.loading = false;

                    })
                    .catch(error => {
                        this.loading = false;
                        console.log(error);
                    });

                /*
                const res = await axios.post('/orders', {
                    filter: filter
                });

                this.orders = res.data;
                *

            });
            */

        },

        loadOrders() {

            const filter2 = {
                ...this.filter,
                type: this.type
            };

            this.loading = true;

            axios.get('/sanctum/csrf-cookie').then(response => {

                axios.post('/orders', {
                    filter: filter2
                })
                    .then(res => {

                        //console.log(res.data);

                        this.projects = res.data.Projects;
                        this.from = res.data.Start;
                        this.to = res.data.Ziel;
                        this.shiftdays = res.data.Shiftdays;
                        this.orders = res.data.data;

                        this.loading = false;

                    })
                    .catch(error => {
                        this.loading = false;
                        console.log(error);
                    });

                /*
                const res = await axios.post('/orders', {
                    filter: filter
                });

                this.orders = res.data;
                */

            });

        },

        customSort(items, sortBy, sortDesc) {

            //console.log(sortBy);

            /*
             * Mögliche Sortierungen
             * Datum = Auftragsdatum
             * Ab = Ab_Zeit
             * An = An_Zeit
             */

            if (sortBy.includes('Auftragsdatum'))
            {
                //console.log('Auftragsdatum');
            }
            else if (sortBy.includes('Ab_Zeit'))
            {
                //console.log('Ab_Zeit');
            }
            else if (sortBy.includes('An_Zeit'))
            {
                //console.log('An_Zeit');
            }

            // https://stackoverflow.com/a/59353996

            items.sort((a, b) => {

                if (sortBy.includes('Auftragsdatum'))
                {
                    if (sortDesc[1]) {
                        return (a.OrderFilter.Auftragsdatum < b.OrderFilter.Auftragsdatum) ? -1 : 1;
                    }
                    else {
                        return (a.OrderFilter.Auftragsdatum > b.OrderFilter.Auftragsdatum) ? -1 : 1;
                    }
                }
                else if (sortBy.includes('Ab_Zeit'))
                {
                    if (sortDesc[1]) {
                        return (a.OrderFilter.Ab_DatumUhrzeit < b.OrderFilter.Ab_DatumUhrzeit) ? -1 : 1;
                    }
                    else {
                        return (a.OrderFilter.Ab_DatumUhrzeit > b.OrderFilter.Ab_DatumUhrzeit) ? -1 : 1;
                    }
                }
                else if (sortBy.includes('An_Zeit'))
                {
                    if (sortDesc[1]) {
                        return (a.OrderFilter.An_DatumUhrzeit < b.OrderFilter.An_DatumUhrzeit) ? -1 : 1;
                    }
                    else {
                        return (a.OrderFilter.An_DatumUhrzeit > b.OrderFilter.An_DatumUhrzeit) ? -1 : 1;
                    }
                }
                else
                    return -1;

            });

            return items;
        },

        addOrders(items) {


            //console.log(items);

            const orders = items.map((order) => {

                return {
                    //...order,
                    id: order.id,
                    Knr: order.Knr,
                    Projekt_Knr: order.Projekt_Knr,
                    Auftragsdatum: order.Auftragsdatum,
                    PaketNr: order.PaketNr,
                    Vorschlagsdatum: order.Vorschlagsdatum,
                    Auslagen: order.Auslagen,
                    Type: this.type
                };

            });

            console.log(orders);

            // Aufträge in der Auftragsübersicht ausblenden


            axios.get('/sanctum/csrf-cookie').then(response => {

                axios.post('/checkout/addorder', {
                    orders: orders
                })
                    .then(res => {
                        alert(res.data);
                    })
                    .catch(error => {
                        console.log(error);
                    });

            });

        },

        changedAuslagen(event, order) {

            if (parseFloat(order.ObergrenzeAuslagen) < parseFloat(event))
            {
                alert('Auslage überschreitet das vorgebene Limit, Auftrag wird gebucht und die Auslagen werden geprüft.');
            }

        }

    }

}
</script>

<style scoped>

</style>
