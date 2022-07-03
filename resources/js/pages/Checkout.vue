<template>
    <div>

        <!--<h2>Checkout</h2>-->

        <v-data-table
            :headers="headers"
            :items="orders"
            :items-per-page="5"
            :sort-by="['Ab_DatumUhrzeit']"
            class="elevation-1"
            :loading="loading"
            loading-text="Aufträge werden geladen..."
            no-data-text="Keine Aufträge gefunden"
        >

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
                <span v-if="item.Vorschlagsdatum !== null">{{ new Date(item.Vorschlagsdatum).toLocaleDateString() }}</span>
            </template>

            <template v-slot:item.Honorar="{ item }">
                <span>{{ parseFloat(item.Honorar).toFixed(2) }}</span>
            </template>

            <template v-slot:item.Auslagen="{ item }">
                <span>{{ parseFloat(item.Auslagen).toFixed(2) }}</span>
            </template>

            <template v-slot:item.actions="{ item }">

                <v-btn
                    elevation="0"
                    style="background-color: transparent;"
                    small
                    @click="deleteOrder(item)"
                >
                    <v-icon color="error">mdi-trash-can-outline</v-icon>
                </v-btn>

            </template>

        </v-data-table>

        <v-btn class="mt-5" color="primary" @click="bookOrders">Verbindlich buchen</v-btn>

    </div>
</template>

<script>
import axios from "../api";

export default {
    name: "Checkout",

    data() {
        return {

            headers: [
                { text: 'Datum', value: 'Auftragsdatum' },
                { text: 'PaketNr', value: 'PaketNr' },
                { text: 'Fahrtnummer', value: 'FahrtNr' },
                { text: 'Abfahrtszeit', value: 'Ab_Zeit' },
                { text: 'Abfahrtsort', value: 'Ab_Ort' },
                { text: 'Ankunftszeit', value: 'An_Zeit' },
                { text: 'Ankunftsort', value: 'An_Ort' },
                { text: 'BS', value: 'Bs' },
                { text: 'Vorschlagsdatum', value: 'Vorschlagsdatum' },
                { text: 'Honorar', value: 'Honorar', align: 'right' },
                { text: 'Auslagen', value: 'Auslagen', align: 'right' },
                { text: 'Aktionen', value: 'actions' }
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

        this.loadOrderCart();

    },

    methods: {

        loadOrderCart() {

            this.loading = true;

            axios.get('/sanctum/csrf-cookie').then(response => {

                axios.get('/checkout', this.request).then(({data}) => {

                    this.orders = data;

                    this.loading = false;

                })
                .catch(error => {
                    this.loading = false;
                    console.log(error);
                });

            });

        },

        deleteOrder(item) {

            if (item == undefined)
                return;

            axios.get('/sanctum/csrf-cookie').then(response => {

               axios.delete('/checkout/deleteorder',
                   { data: {
                       PaketNr: item.PaketNr
                   }}
               ).then(({data}) => {

                   this.loadOrderCart();

               });

            });

        },

        bookOrders() {

            const bookOrders = this.orders.map((order) => {
                return {
                    id: order.id,
                    Knr: order.Knr,
                    PaketNr: order.PaketNr,
                    Vorschlagsdatum: order.Vorschlagsdatum,
                    Auslagen: order.Auslagen,
                    Honorar: order.Honorar,
                    Type: order.Type
                };
            })

            axios.get('/sanctum/csrf-cookie').then(response => {

                axios.post('/bookings/bookorders', {
                    orders: bookOrders
                }).then((res) => {

                    alert(res.data);

                    this.loadOrderCart();

                });

            });

        }

    }

}
</script>

<style scoped>

</style>
