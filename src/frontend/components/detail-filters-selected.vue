<template>
  <div class="row">
    <div class="col-12 text-center">
      <h3>Resultados de la busqueda</h3>
    </div>
    <div class="col-12 details-selected-section">
      <!-- Detail params selected -->
      <div class="row details-selected">
        <div class="col-3 col-md-1">
          <img
            src="/wp-content/plugins/Kiuws-Service-Flight-Management/assets/images/airplane_icon.png"
            width="50"
          />
        </div>
        <div class="col-9 col-sm-10 col-md-8 col-lg-8">
          <div
            class="deatil-selected-cities"
            v-if="originAirport && destinationAirport"
          >
            <label
              >{{ originAirport.city_name }} <span>({{ originAirport.code }})</span>,
              {{ originAirport.name }}</label
            >
            -
            <label
              >{{ destinationAirport.city_name }}
              <span>({{ destinationAirport.code }})</span>,
              {{ destinationAirport.name }}</label
            >
          </div>
          <div class="deatil-selected-date">
            <label>{{ depurateDate }}</label>
          </div>
          <div class="deatil-selected-passengers">
            <label
              >{{ adults }} adultos | {{ children }} niños | {{ inf }} infantes
            </label>
          </div>
        </div>
        <div
          class="col-12 col-sm-12 col-md-3 col-lg-3 deatil-selected-button text-center"
        >
          <button
            class="btn btn-primary btn-gray"
            type="button"
            @click="$emit('refresh')"
          >
            Actualizar busqueda
          </button>
        </div>
      </div>
    </div>
  </div>
</template>
<script>
export default {
  name: "DetailFiltersSelected",
  props: [
    "step",
    "originAirport",
    "destinationAirport",
    "depurateDate",
    "adults",
    "children",
    "inf",
  ],
};
</script>
