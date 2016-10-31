// main.js

import Vue from 'vue'
import VueRouter from 'vue-router'
import LoginView from './components/LoginView.vue'
import MessagingView from './components/MessagingView.vue'

// install router
Vue.use(VueRouter)

// routing
const routes = [
	{ path: '/login', component: LoginView },
	{ path: '/', component: MessagingView }
];

var router = new VueRouter({
	routes
});

const app = new Vue({
	router
}).$mount('#app')