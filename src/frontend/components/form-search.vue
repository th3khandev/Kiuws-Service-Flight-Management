<template>
  <div class="row" id="flight-search-form">
    <div class="col-12 col-sm-12 col-md-12 col-lg-12">
      <!-- Add checks "Solo ida" o "Ida y vuelta" -->
      <div class="form-check form-check-inline">
        <input
          class="form-check-input"
          type="radio"
          name="trip_type"
          id="trip_type_one_way"
          v-model="tripType"
          value="1"
        />
        <label class="form-check-label" for="trip_type_one_way">Solo ida</label>
      </div>
      <div class="form-check form-check-inline">
        <input
          class="form-check-input"
          type="radio"
          name="trip_type"
          id="trip_type_round_trip"
          v-model="tripType"
          value="2"
        />
        <label class="form-check-label" for="trip_type_round_trip"
          >Ida y Vuelta</label
        >
      </div>
      <div class="form-check form-check-inline">
        <input
          class="form-check-input"
          type="checkbox"
          name="only_direct_flights"
          id="only_direct_flights"
          v-model="onlyDirectFlights"
        />
        <label class="form-check-label" for="only_direct_flights"
          >Solo vuelos directos</label
        >
      </div>
    </div>
    <div class="col-12 col-sm-12 col-md-6 col-lg-6 mb-2">
      <label for="origin" class="form-label">Desde: </label>
      <v-select
        name="origin"
        class="form-control"
        label="name"
        :filterable="true"
        :options="originAirports"
        v-model="originAirport"
        :filter="filterAirports"
      >
        <template slot="no-options">
          No se han encontrado aeropuertos
        </template>
        <template slot="option" slot-scope="option">
          <div class="d-center">
            <img
              src="/wp-content/plugins/Kiuws-Service-Flight-Management/assets/images/airplane_icon.png"
              width="30"
              style="margin-right: 5px; margin-top: 0px; margin-bottom: 0px"
            />
            {{ option.name }} - {{ option.city_name }}
          </div>
        </template>
        <template slot="selected-option" slot-scope="option">
          <img
            src="/wp-content/plugins/Kiuws-Service-Flight-Management/assets/images/airplane_icon.png"
            width="30"
            style="margin-right: 5px; margin-top: 0px; margin-bottom: 0px"
          />
          <label
            >{{ option.name }}, <small>{{ option.city_name }}</small></label
          >
        </template>
      </v-select>
    </div>
    <!-- Destination -->
    <div class="col-12 col-sm-12 col-md-6 col-lg-6 mb-2">
      <label for="destination" class="form-label">Destino: </label>
      <v-select
        name="origin"
        class="form-control"
        label="name"
        :filterable="true"
        :options="destinationAirports"
        v-model="destinationAirport"
        :filter="filterAirports"
      >
        <template slot="no-options">
          No se han encontrado aeropuertos
        </template>
        <template slot="option" slot-scope="option">
          <div class="d-center">
            <img
              src="/wp-content/plugins/Kiuws-Service-Flight-Management/assets/images/airplane_icon.png"
              width="30"
              style="margin-right: 5px; margin-top: 0px; margin-bottom: 0px"
            />
            {{ option.name }} - {{ option.city_name }}
          </div>
        </template>
        <template slot="selected-option" slot-scope="option">
          <img
            src="/wp-content/plugins/Kiuws-Service-Flight-Management/assets/images/airplane_icon.png"
            width="30"
            style="margin-right: 5px; margin-top: 0px; margin-bottom: 0px"
          />
          <label
            >{{ option.name }}, <small>{{ option.city_name }}</small></label
          >
        </template>
      </v-select>
    </div>
    <!-- Departure date -->
    <div class="col-12 col-sm-12 col-md-6 col-lg-6 mb-2">
      <label for="departure_date" class="form-label">Fecha de salida: </label>
      <input
        type="date"
        name="departure_date"
        placeholder="Fecha de salida"
        class="form-control"
        v-model="departureDate"
        :min="currentDate"
        @change="returnDate = null"
      />
    </div>
    <!-- Return date -->
    <div class="col-12 col-sm-12 col-md-6 col-lg-6 mb-2" v-if="tripType == 2">
      <label for="return_date" class="form-label">Fecha de regreso: </label>
      <input
        type="date"
        name="return_date"
        placeholder="Fecha de salida"
        class="form-control"
        v-model="returnDate"
        :min="departureDate"
        :disabled="!departureDate"
      />
    </div>

    <!-- Amount Adults and children -->
    <div
      class="col-12 col-sm-12 col-md-6 col-lg-6 mb-2"
    >
      <div class="row">
        <div class="col-12 col-sm-12 col-md-4 col-lg-4">
          <label for="amount_adults" class="form-label">Adultos: </label>
          <input
            type="number"
            name="amount_adults"
            placeholder="Adultos"
            class="form-control"
            v-model="adults"
            min="1"
            max="9"
            step="1"
          />
        </div>
        <div class="col-12 col-sm-12 col-md-4 col-lg-4">
          <label for="amount_children" class="form-label"
            >Niños (2-12 años):
          </label>
          <input
            type="number"
            name="amount_children"
            placeholder="Niños"
            class="form-control"
            v-model="children"
            min="0"
            max="9"
            step="1"
          />
        </div>
        <div class="col-12 col-sm-12 col-md-4 col-lg-4">
          <label for="amount_children" class="form-label"
            >Infantes (0-2 años):
          </label>
          <input
            type="number"
            name="amount_inf"
            placeholder="Infantes"
            class="form-control"
            v-model="inf"
            min="0"
            max="9"
            step="1"
          />
        </div>
      </div>
    </div>

    <div class="col-12" v-if="error">
      <div class="alert alert-danger alert-dismissible" role="alert">
        <strong>-</strong> {{ errorMessage }}
      </div>
    </div>
    <div class="col-12 col-md-12 text-center mt-5">
      <button
        type="button"
        class="btn btn-primary"
        @click="searhFlights"
        :disabled="loading"
      >
        Buscar vuelos
      </button>
    </div>
  </div>
</template>

<script>
// components
import vSelect from "vue-select";

export default {
  name: "FormSearchComponent",
  components: {
    vSelect,
  },
  data: () => ({
    originAirports: FLIGHT_MANAGEMENT.AIRPORTS,
    destinationAirports: FLIGHT_MANAGEMENT.AIRPORTS,
    originAirport: null,
    destinationAirport: null,
    departureDate: null,
    returnDate: null,
    adults: 1,
    children: 0,
    inf: 0,
    error: false,
    errorMessage: null,
    tripType: 1,
    currentDate: new Date().toISOString().slice(0, 10),
    gettingAirports: false,
    onlyDirectFlights: false,
  }),
  props: ["loading", "step"],
  methods: {
    filterAirports(airports, queryText, itemText) {
      return airports.filter((airport) => {
        return (
          airport.name.toLowerCase().includes(queryText.toLowerCase()) ||
          airport.city_name.toLowerCase().includes(queryText.toLowerCase())
        );
      });
    },
    validForm() {
      if (!this.originAirport) {
        this.setError("Debe seleccionar una ciudad destino");
        return false;
      }
      if (!this.destinationAirport) {
        this.setError("Debe seleccionar una ciudad destino");
        return false;
      }
      if (!this.departureDate) {
        this.setError("Debe seleccionar una fecha de salida");
        return false;
      }
      if (this.adults < 1 || this.adults > 9) {
        this.setError("El campo 'Adultos' debe ser entre 1 y 9");
        return false;
      }
      if (this.children > 9) {
        this.setError("El campo 'Niños' no deber ser mayor a 9");
        return false;
      }

      if (this.tripType == 2) {
        if (!this.returnDate) {
          this.setError("Debe seleccionar una fecha de regreso");
          return false;
        }
      }

      return true;
    },
    setError(message) {
      this.error = true;
      this.errorMessage = message;
    },
    searhFlights() {
      if (this.validForm()) {
        this.error = false;
        this.errorMessage = "";
        this.$emit(
          "submit",
          this.originAirport,
          this.destinationAirport,
          this.departureDate,
          this.returnDate,
          this.adults,
          this.children,
          this.inf,
          this.tripType,
          this.onlyDirectFlights
        );
      }
    },
  },
};
</script>
