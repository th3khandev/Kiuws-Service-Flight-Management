Vue.config.devtools = true;
Vue.config.productionTip = false;

Vue.component("v-select", VueSelect.VueSelect);

const app = new Vue({
  el: "#flight-search-form-app",
  data () {
    return {
      message: "Hello Vue!",
      loading: false,
      step: 1,
      flights: [],
      originAirport: null,
      destinationAirport: null,
      adults: 1,
      children: 0,
      depurateDate: ""
    }
  },
  components: {
    "flight-search-form": flightSearchFormComponent,
    "flight-itinerary": flightItineraryComponent,
    "flight-sidebar": flightSidebarComponent,
    "flight-details-selected": flightDetailsSelectedComponent,
  },
  methods: {
    searhFlights(
      originAirport,
      destinationAirport,
      depurateDate,
      returnDate,
      adults,
      children
    ) {
      this.originAirport = originAirport;
      this.destinationAirport = destinationAirport;
      this.adults = adults;
      this.children = children;
      this.depurateDate = depurateDate;
      this.loading = true;
      fetch(
        `/api/get-flight-available?origin=${originAirport.code}&destination=${destinationAirport.code}&depurate_date=${depurateDate}&return_date=${returnDate}&adults=${adults}&children=${children}`,
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
    refreshSearh () {
      this.step = 1;
    }
  },
});
