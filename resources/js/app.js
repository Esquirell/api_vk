require('./bootstrap');

import Vue from 'vue'
import vuetify from './plugins/vuetify'
import App from './components/App.vue'

import VueRouter from 'vue-router'
Vue.use(VueRouter)
import routes from './routes'

window.Vue = require('vue').default;

Vue.component('example-component', require('./components/ExampleComponent.vue').default);

import { Form } from 'vform';
window.Form = Form;


import Swal from 'sweetalert2';
const Toast = Swal.mixin({
    toast: true,
    position: 'top-end',
    showConfirmButton: false,
    timer: 3200,
    timerProgressBar: true,
    onOpen: (toast) => {
        toast.addEventListener('mouseenter', Swal.stopTimer)
        toast.addEventListener('mouseleave', Swal.resumeTimer)
    }
})
window.Swal = Swal;
window.Toast = Toast;


const app = new Vue({
    el: '#app',
    components: { App },
    router: new VueRouter(routes),
    vuetify,
});
