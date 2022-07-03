import Vue from 'vue'
import Vuetify from 'vuetify'
import 'vuetify/dist/vuetify.min.css'
Vue.use(Vuetify)

export default new Vuetify({
    theme: {
        themes: {
            light: {
                primary: '#1a3177',//'#3f51b5',
                secondary: '#696969',
                accent: '#8c9eff',
                error: '#F44336', //'#b71c1c',
                success: '#4CAF50',
            },
        },
    },
})
