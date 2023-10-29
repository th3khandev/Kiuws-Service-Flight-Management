import Vue from 'vue'
import App from './App.vue'

// get element id flight-management-app
const flightManagementApp = document.getElementById('flight-management-app');
console.log('flightManagementApp >> ', flightManagementApp);

const app = new Vue({
    el: '#flight-management-app',
    render: h => h(App)
});

console.log('app >> ', app);