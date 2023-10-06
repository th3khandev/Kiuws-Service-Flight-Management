const { createApp, ref } = Vue;

console.log("Hello from Vue!");

Vue.component('v-select', VueSelect.VueSelect);

const app = new Vue({
  el: "#flight-search-form",
  data: {
    message: "Hello Vue!",
    options: []
  }
});