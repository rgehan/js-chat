// main.js

import Vue from 'vue';
import VueRouter from 'vue-router';

import App from './components/App.vue';
import LoginView from './components/LoginView.vue';
import MessagingView from './components/MessagingView.vue';

// install router
Vue.use(VueRouter);

// routing
const routes = [
    { path: '/login', name: 'login', component: LoginView },
    { path: '/', name: 'home', component: MessagingView }
];

var router = new VueRouter({
    routes
});

const app = new Vue({
    el: '#app',
    router,
    render: h => h(App),
});