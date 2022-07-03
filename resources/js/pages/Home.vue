<template>
    <div>

        <!--<h1>Startseite</h1>-->

        <p>Sehr geehrter {{anrede}},</p>

        <p>herzlich willkommen auf den Dispo online Internetseiten der TRENDline GmbH & Co. KG.</p>

        <p>Sollten Sie diese Seiten zum ersten Mal besuchen, so ändern Sie bitte auf der linken Seite ihr Passwort unter dem Menüpunkt &quot;Passwort ändern&quot;. Beim erneuten Anmelden sehen Sie dann für Sie freigegebene Aufträge unter den Menüpunkten &quot;Offene Aufträge&quot;, &quot;Ersatzstichproben&quot; oder &quot;Schichttagsaufträge&quot;.</p>

        <p>Bei Fragen zur Bedienung wenden Sie sich bitte an Ihre Disponenten, die Ihnen gerne weiter helfen werden.</p>

        <p>Für Anregungen und Wünsche stehen wir Ihnen unter der bekannten Adresse zur Verfügung.</p>

        <p>Mit freundlichen Grüßen</p>

        <p>Ihr TRENDline Team</p>

    </div>
</template>

<script>
import store from "../store";

export default {
    name: "Home",

    computed: {

        authenticated() {
            return store.getters.authenticated;
        },

        user() {
            return store.getters.user;
        },

        anrede() {

            if (!store.getters.authenticated)
                return '';

            const user = this.user;
            return user.anrede + ' ' + user.nachname;

        }

    },

}
</script>

<style scoped>

</style>
