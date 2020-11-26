import Vue from 'vue';
import VueRouter from "vue-router";
import Messagerie from './components/MessagerieComponent';
import Message from './components/MessagesComponent';
import store from "./store/store";

Vue.use(VueRouter)

let $messagerie = document.querySelector('#messagerie')

if ($messagerie) {

    const routes = [
        {path: '/'},
        {path: '/:id', component: Message ,name: 'conversation'}
    ]

    const router = new VueRouter({
        mode: 'history',
        routes,
        base: $messagerie.getAttribute('data-base')
    })


    new Vue ( {
        el: '#messagerie',
        components: {Messagerie},
        store: store,
        router
    })

}
