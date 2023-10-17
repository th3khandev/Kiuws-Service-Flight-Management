import flightSearchFormComponent from "./components/flightSearchFormComponent.js";
import flightItineraryComponent from "./components/flightItineraryComponent.js";
import flightSidebarComponent from "./components/flightSidebarComponent.js";

Vue.component("v-select", VueSelect.VueSelect);

const app = new Vue({
  el: "#flight-search-form-app",
  data: {
    message: "Hello Vue!",
    loading: false,
    step: 1,
    flights: [],
  },
  components: {
    "flight-search-form": flightSearchFormComponent,
    "flight-itinerary": flightItineraryComponent,
    "flight-sidebar": flightSidebarComponent,
  },
  methods: {
    searhFlights(
      originAirport,
      destinationAirport,
      departureDate,
      returnDate,
      adults,
      children
    ) {
      console.log("Send form >>> ");
      this.loading = true;
      fetch(
        `/api/get-flight-available?origin=${originAirport}&destination=${destinationAirport}&depurate_date=${departureDate}&return_date=${returnDate}&adults=${adults}&children=${children}`,
        {
          method: "GET",
          headers: {
            "Content-Type": "application/json",
            "Access-Control-Allow-Origin": "*",
          },
        }
      )
        .then((response) => response.json())
        .then((data) => {
          console.log(data);
          if (data.status == 'error') {
            this.$refs.flightSearchForm.setError(data.message);
          } else if (!data.status) {
            this.$refs.flightSearchForm.setError("Se ha presentado un error, por favor intente más tarde");
          } else if (data.status == 'success') {
            const { flights } = data;
            if (flights.length == 0) {
              this.$refs.flightSearchForm.setError("No se encontraron vuelos disponibles, para los datos ingresados");
            } else {
              this.flights = flights;
              this.step = 2;
            }
          }
        })
        .catch((error) => {
          console.error(error);
        })
        .finally(() => {
          this.loading = false;
        });
    },
  },
});
