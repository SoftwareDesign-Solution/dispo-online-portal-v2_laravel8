import Vue from 'vue';
import VueRouter from "vue-router";
import store from "./store";

import Login from "./pages/Login";
import AuthenticatedApp from "./views/AuthenticatedApp";
import Home from "./pages/Home";
import Orders from "./pages/Orders";
import OpenOrders from "./pages/OpenOrders";
import Benutzer from "./pages/Benutzer";
import PasswortAendern from "./pages/PasswortAendern";
import Datenschutzerklaerung from "./pages/Datenschutzerklaerung";
import Impressum from "./pages/Impressum";
import Checkout from "./pages/Checkout";

Vue.use(VueRouter);

const router = new VueRouter({
    base: 'dispo-online-portal-v2/public',
    mode: 'history',
    routes: [
        {
            path: '/login',
            name: 'Login',
            component: Login,
            meta: {
                middleware: 'guest'
            }
        },
        {
            path: '',
            component: AuthenticatedApp,
            meta: {
                auth: true,
                middleware: 'user'
            },
            children: [
                {
                    path: '/',
                    name: 'Startseite',
                    component: Home,
                    meta: {
                        auth: true,
                        middleware: 'user'
                    }
                },
                {
                    path: '/offeneauftraege',
                    name: 'Offene Aufträge',
                    component: Orders,
                    props: {
                        type: 'OpenOrders'
                    },
                    meta: {
                        auth: true,
                        middleware: 'user'
                    }
                },
                {
                    path: '/ersatzstichproben',
                    name: 'Ersatzstichproben',
                    component: Orders,
                    props: {
                        type: 'PastOrders'
                    },
                    meta: {
                        auth: true,
                        middleware: 'user'
                    }
                },
                {
                    path: '/schichttagsauftrage',
                    name: 'Schichttagsaufträge',
                    component: Orders,
                    props: {
                        type: 'ShiftDayOrders'
                    },
                    meta: {
                        auth: true,
                        middleware: 'user',
                    }
                },
                {
                    path: '/benutzer',
                    name: 'Benutzer',
                    component: Benutzer,
                    meta: {
                        auth: true,
                        middleware: 'admin'
                    }
                },
                {
                    path: '/passwortaendern',
                    name: 'Passwort ändern',
                    component: PasswortAendern,
                    meta: {
                        auth: true,
                        middleware: 'user'
                    }
                },
                {
                    path: '/datenschutzerklaerung',
                    name: 'Datenschutzerklärung',
                    component: Datenschutzerklaerung,
                    meta: {
                        auth: true,
                        middleware: 'user'
                    }
                },
                {
                    path: '/impressum',
                    name: 'Impressum',
                    component: Impressum,
                    meta: {
                        auth: true,
                        middleware: 'user'
                    }
                },
                {
                    path: '/checkout',
                    name: 'Auftragskorb',
                    component: Checkout,
                    meta: {
                        auth: true,
                        middleware: 'user'
                    }
                }
            ]
        },
        /*
        {
            path: '/',
            name: 'Startseite',
            component: Home,
            meta: {
                auth: true
            }
        },
        {
            path: '/offeneauftraege',
            name: 'Offene Aufträge',
            component: Orders,
            props: {
                type: 'OpenOrders'
            },
            meta: {
                auth: true
            }
        },
        {
            path: '/ersatzstichproben',
            name: 'Ersatzstichproben',
            component: Orders,
            props: {
                type: 'PastOrders'
            },
            meta: {
                auth: true
            }
        },
        {
            path: '/schichttagsauftrage',
            name: 'Schichttagsaufträge',
            component: Orders,
            props: {
                type: 'ShiftDayOrders'
            },
            meta: {
                auth: true
            }
        },
        {
            path: '/benutzer',
            name: 'Benutzer',
            component: Benutzer,
            meta: {
                auth: true
            }
        },
        {
            path: '/passwortaendern',
            name: 'Passwort ändern',
            component: PasswortAendern,
            meta: {
                auth: true
            }
        },
        {
            path: '/datenschutzerklaerung',
            name: 'Datenschutzerklärung',
            component: Datenschutzerklaerung,
            meta: {
                auth: true
            }
        },
        {
            path: '/impressum',
            name: 'Impressum',
            component: Impressum,
            meta: {
                auth: true
            }
        },
        {
            path: '/checkout',
            name: 'Checkout',
            component: Checkout,
            meta: {
                auth: true
            }
        }
        */
        /*
        {
            path: '',
            name: '',
            component: null
        }
        */

    ]
});

router.beforeEach((to, from, next) => {

    document.title = `${to.name} - TRENDline Dispo Online`

    //console.log(store.state.authenticated);
    //console.log(to.name);

    // Middleware Guest
    if (to.meta.middleware === "guest") {

        // Authenifiziert?
        if (store.state.authenticated) {
            next({ name: 'Startseite' });
        }

        next();

    } else {

        // Authenifiziert?
        if (store.state.authenticated) {

            // Middleware Admin
            if (to.meta.middleware === 'admin') {

                if (store.state.user.Admin === 1) {
                    next();
                }
                else
                {
                    next({ name: 'Startseite' });
                }

            }

            next();

        } else {
            next({ name: 'Login' });
        }

    }

});

export default router;
