<template>
  <div>

      <!--<h1>Passwort ändern</h1>-->

      <p>
          Es ist ein sicheres Kennwort erforderlich. Geben Sie 8 bis 16<br />Zeichen ein. Verwenden Sie keine gängigen Wörter oder <br />Namen. Kombinieren Sie Groß- und Kleinbuchstaben,<br />Ziffern und Symbole.
      </p>

      <form>

          <p>Benutzer</p>
          <p><strong>161074</strong></p>

          <v-text-field label="Bisheriges Passwort"></v-text-field>

          <v-text-field label="Passwort"></v-text-field>

          <v-text-field label="Passwort (Wdh.)"></v-text-field>

          <v-btn color="primary">Übernehmen</v-btn>

      </form>

  </div>
</template>

<script>
export default {
    name: "PasswortAendern"
}
</script>

<style scoped>

</style>
