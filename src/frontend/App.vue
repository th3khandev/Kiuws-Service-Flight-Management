<template>
  <div>
    <FormSearchComponent
      v-show="!loading && step == 1"
      :step="step"
      :loading="loading"
      @submit="handleSearchFlights"
      ref="flightSearchForm"
    />
    <Loading v-if="loading" />
    <div class="row" v-if="!loading && step == 2">
      <div class="col-12 col-md-12">
        <DetailFiltersSelected
          :step="step"
          :adults="adults"
          :children="children"
          :depurate-date="depurateDate"
          :destination-airport="destinationAirport"
          :origin-airport="originAirport"
          @refresh="step = 1"
        />
      </div>
      <div class="col-12 col-md-3">
        <SidebarFilters />
      </div>
      <div class="col-12 col-md-9">
        <Itinerary v-for="(flight, index) in flights" :flight="flight" :key="index" @flight-selected="handleFlightSelected" />
      </div>
    </div>
    <ModalFlightDetail :flight="flightSelected" :children="children" :adults="adults" />
  </div>
</template>
<script>
// services
import { getFlightsAvailable } from "../services/kiuwService";

// components
import FormSearchComponent from "./components/form-search.vue";
import Loading from "./components/loading.vue";
import SidebarFilters from "./components/sidebar-filters.vue";
import Itinerary from "./components/itinerary.vue";
import DetailFiltersSelected from "./components/detail-filters-selected.vue";
import ModalFlightDetail from "./components/modal-flight-detail.vue"

export default {
  name: "App",
  components: {
    FormSearchComponent,
    Loading,
    SidebarFilters,
    Itinerary,
    DetailFiltersSelected,
    ModalFlightDetail
  },
  data() {
    return {
      step: 1,
      loading: false,
      flights: [],
      originAirport: null,
      destinationAirport: null,
      depurateDate: null,
      returnDate: null,
      adults: 1,
      children: 0,
      flightSelected: null,
    };
  },
  methods: {
    handleSearchFlights(
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
      this.flights = [];
      console.log("handleSearchFlights");
      getFlightsAvailable(
        originAirport.code,
        destinationAirport.code,
        depurateDate,
        returnDate,
        adults,
        children
      )
        .then((response) => response.json())
        .then((data) => {
          if (data.status == "error") {
            this.$refs.flightSearchForm.setError(data.message);
          } else if (!data.status) {
            this.$refs.flightSearchForm.setError(
              "Se ha presentado un error, por favor intente más tarde"
            );
          } else if (data.status == "success") {
            const { flights } = data;
            if (flights.length == 0) {
              this.$refs.flightSearchForm.setError(
                "No se encontraron vuelos disponibles, para los datos ingresados"
              );
            } else {
              this.flights = flights;
              this.step = 2;
            }
          }
        })
        .finally(() => {
          this.loading = false;
        });
    },
    handleFlightSelected(flight) {
      this.flightSelected = flight;
    },
  },
};
</script>

<style>
@import url("vue-select/dist/vue-select.css");
@import url("../../assets/css/main.css");
</style>
