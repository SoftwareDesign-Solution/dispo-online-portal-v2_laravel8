<template>
    <v-layout>
        <v-menu
            v-model="fromDateMenu"
            :close-on-content-click="false"
            :nudge-right="40"
            transition="scale-transition"
            offset-y
            max-width="290px"
            min-width="290px"
        >
            <template v-slot:activator="{ on }">
                <v-text-field
                    :label="label"
                    readonly
                    outlined
                    hide-details
                    :value="fromDateDisp"
                    v-on="on"
                ></v-text-field>
            </template>
            <v-date-picker
                locale="de-de"
                :first-day-of-week="1"
                v-model="fromDateVal"
                no-title
                @input="handleInput"
            ></v-date-picker>
        </v-menu>
    </v-layout>
</template>

<script>
import moment from "moment";

export default {
    name: "DatePicker",

    props: ['value', 'label'],

    data() {
      return {

          fromDateMenu: false,
          fromDateVal: this.value,

          minDate: "2019-07-04",
          maxDate: "2019-08-30",

      }
    },

    watch: {
        /*
        $route(to, from) {
            this.loadOrders();
        }
        */
        value: function (newVal, oldVal) {

            if (this.value === '')
                this.fromDateVal = null;

        }
    },

    computed: {
        fromDateDisp() {

            if ((this.fromDateVal == null) || (this.fromDateVal == ''))
                return '';

            return moment(this.fromDateVal).format('DD.MM.YYYY');
            // format date, apply validations, etc. Example below.
            // return this.fromDateVal ? this.formatDate(this.fromDateVal) : "";
        },
    },

    methods: {
        handleInput(e) {
            this.fromDateMenu = false
            this.$emit('input', e);
        }
    },

}
</script>

<style scoped>

</style>
