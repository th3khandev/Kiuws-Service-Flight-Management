const { createApp, ref } = Vue;

console.log("Hello from Vue!");

Vue.component('v-select', VueSelect.VueSelect);

const app = new Vue({
  el: "#flight-search-form",
  data: {
    message: "Hello Vue!",
    originAirports: [],
    destinationAirports: [],
    originAirport: null,
    destinationAirport: null,
    departureDate: null,
    returnDate: null,
    adults: 1,
    children: 0,
    loading: false,
    error: false,
    errorMessage: null,
  },
  created() {
  },
  methods: {
    onSearchOriginAirports (search, loading) {
      if (search.length > 3) {
        loading(true);
        this.search(search, loading, this, 'origin');
      }
    },
    onSearchDestinationAirports (search, loading) {
      if (search.length > 3) {
        loading(true);
        this.search(search, loading, this, 'destination');
      }
    },
    search: _.debounce((search, loading, vm, type) => {
      fetch(
        `/api/get-airport-codes?search=${search}`,
        {
          method: "GET",
          headers: {
            "Content-Type": "application/json",
            "Access-Control-Allow-Origin": "*",
          }
        }
      )
        .then(response => response.json())
        .then(data => {
          console.log(data);
          if (type == 'origin') {
            vm.originAirports = data.airports;
          } else {
            vm.destinationAirports = data.airports;
          }
          loading(false);
        })
        .catch(error => {
          console.error(error);
        })
    }),
    validForm () {
      if (!this.originAirport) {
        this.error = true;
        this.errorMessage = 'Debe seleccionar una ciudad destino';
        return false;
      }
      if (!this.destinationAirport) {
        this.error = true;
        this.errorMessage = 'Debe seleccionar una ciudad destino';
        return false;
      }
      if (!this.departureDate) {
        this.error = true;
        this.errorMessage = 'Debe seleccionar una fecha de salida';
        return false;
      }
      return true;
    },
    searhFlights () {
      console.log("Search flights >>> ");
      console.log("origin airport >>> ", this.originAirport);
      console.log("destination airport >>> ", this.destinationAirport);
      console.log("departure date >>> ", this.departureDate);
      console.log("return date >>> ", this.returnDate);
      console.log("adults >>> ", this.adults);
      console.log("children >>> ", this.children);
      if (this.validForm()) {
        console.log("Send form >>> ");
      }
    }
  },
});