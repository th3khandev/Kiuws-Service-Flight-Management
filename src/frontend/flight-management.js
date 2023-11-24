import Vue from 'vue'
import App from './App.vue'

$(document).ready(function() {
    // add vue app
    setTimeout(() => {
       if (document.getElementById('flight-management-app')) {
        new Vue({
            el: '#flight-management-app',
            render: h => h(App)
        });
    } 
    }, 1000);
});