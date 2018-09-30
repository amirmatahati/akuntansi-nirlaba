//import './bootstrap';
require('./bootstrap');
import Vue from 'vue'; // Importing Vue Library
import VueRouter from 'vue-router'; // importing Vue router library
import router from './routes';
window.Vue = Vue;
import VueResource from 'vue-resource';
import axios from 'axios';
import BootstrapVue from 'bootstrap-vue';
import 'bootstrap/dist/css/bootstrap.css';
import 'bootstrap-vue/dist/bootstrap-vue.css';
import Popover  from 'vue-js-popover';
import Loading from 'vue-loading-overlay';
import 'vue-loading-overlay/dist/vue-loading.min.css';
import bModal from 'bootstrap-vue/es/components/modal/modal';
import bModalDirective from 'bootstrap-vue/es/directives/modal/modal';
import 'vue-awesome/icons/flag';
import 'vue-awesome/icons';
import Icon from 'vue-awesome/components/Icon';
import VueSweetalert2 from 'vue-sweetalert2';
import datePicker from 'vue-bootstrap-datetimepicker';
import 'pc-bootstrap4-datetimepicker/build/css/bootstrap-datetimepicker.css';
import 'flatpickr/dist/flatpickr.css';
import flatPickr from 'vue-flatpickr-component';
import HighchartsVue from 'highcharts-vue';
import VueHighcharts from 'vue2-highcharts';
import VueBootstrapTypeahead from 'vue-bootstrap-typeahead';


Vue.component('pagination', require('./components/Pagination.vue'));
Vue.component('vue-bootstrap-typeahead', VueBootstrapTypeahead)
Vue.component('icon', Icon);


Vue.use(VueRouter);
Vue.use(VueResource);
Vue.use(BootstrapVue);
Vue.use(Popover);
Vue.use(bModal);
Vue.use(bModalDirective);
Vue.use(Loading);
Vue.use(VueSweetalert2);
Vue.use(datePicker);
Vue.use(flatPickr);
Vue.use(HighchartsVue);

const app = new Vue({
    el: '#app',
    router,
	components: {
            Loading,
			Icon
        },

});
$.extend(true, $.fn.datetimepicker.defaults, {
  icons: {
    time: 'far fa-clock',
    date: 'far fa-calendar',
    up: 'fas fa-arrow-up',
    down: 'fas fa-arrow-down',
    previous: 'fas fa-chevron-left',
    next: 'fas fa-chevron-right',
    today: 'fas fa-calendar-check',
    clear: 'far fa-trash-alt',
    close: 'far fa-times-circle'
  }
});
