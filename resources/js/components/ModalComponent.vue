<template>
    <v-dialog
        v-model="modal"
        width="500"
    >
        <v-card>
            <v-card-title class="text-h5" color="primary">
                {{ title }}
            </v-card-title>

            <v-card-text>

                <slot></slot>

            </v-card-text>

            <v-divider></v-divider>

            <v-card-actions>
                <v-spacer></v-spacer>
                <v-btn
                    color="primary"
                    text
                    @click="closeClicked"
                >
                    Schließen
                </v-btn>
            </v-card-actions>
        </v-card>
    </v-dialog>
</template>

<script>
export default {
    name: "ModalComponent",

    props: ['value', 'title'],

    data() {
        return {
            modal: this.value
        }
    },

    updated() {
        this.modal = this.value;
    },

    methods: {

        closeClicked() {
            this.modal = false;
            this.$emit('input', false);
        }

    }
}
</script>

<style scoped>

</style>
