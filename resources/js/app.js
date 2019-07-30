
/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */

require('./bootstrap');

window.Vue = require('vue');

import VueRouter from 'vue-router';
Vue.use(VueRouter);

var VueResource = require('vue-resource');
Vue.use(VueResource);

import BootstrapVue from 'bootstrap-vue';
Vue.use(BootstrapVue);

import VeeValidate from 'vee-validate';
Vue.use(VeeValidate, {
    inject: true,
    fieldsBagName: 'veeFields'
});

import _ from 'lodash'
Vue.use(_);

const axios = require('axios');

// Pass token
Vue.http.interceptors.push((request, next) => {
    request.headers.set('X-CSRF-TOKEN', $('meta[name="csrf-token"]').attr('content'));
    next();
});

/**
 * The following block of code may be used to automatically register your
 * Vue components. It will recursively scan this directory for the Vue
 * components and automatically register them with their "basename".
 *
 * Eg. ./components/ExampleComponent.vue -> <example-component></example-component>
 */

Vue.component('lead-page', require('./components/leads/leadpage.vue').default);
Vue.component('funnel-page', require('./components/funnels/funnelpage.vue').default);
Vue.component('emailmanager-page', require('./components/emailmanager/emailmanager.vue').default);

/**
 * Next, we will create a fresh Vue application instance and attach it to
 * the page. Then, you may begin adding components to this application
 * or customize the JavaScript scaffolding to fit your unique needs.
 */

import Layout from "./components/layout/Layout";
window.app = new Vue({
    el: '#app',
    extends: Layout,
    delimiters: ["<%","%>"],
    data: () => ({
        lbh: 0,
    }),
    methods: {
        leftbarHeight() {
            if(document.getElementById("lbh")) {
                let hh = document.getElementById('lbh').clientHeight;
                this.lbh = (window.innerHeight - hh);
            }
        },
    },
    computed: {

    },
    mounted() {
        this.leftbarHeight();
        window.addEventListener("resize", this.leftbarHeight);
    }
    //router
});
