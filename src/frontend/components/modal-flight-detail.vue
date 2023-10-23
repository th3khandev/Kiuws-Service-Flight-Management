<template>
  <div
    class="modal fade"
    id="flight-modal"
    tabindex="-1"
    aria-labelledby="flight-modal-label"
    aria-hidden="true"
  >
    <div
      class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-lg"
    >
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="flight-modal-label">
            Detalle de su vuelo seleccionado
          </h5>
          <button
            type="button"
            class="close"
            data-dismiss="modal"
            aria-label="Close"
          >
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <!-- Itinerary -->
          <div class="itinerary-container" v-if="flight">
            <div class="itinerary-info">
              <div class="itinerary-info-depurate-date">
                <span class="info-depurate-date">
                  {{ flight.depurateDate.split(" ")[1] }}
                </span>
                <span class="info-city-code">
                  {{ flight.flightSegment[0].departureAirport }}
                </span>
              </div>
              <div class="itinerary-info-depurate-duration">
                <span>{{ flight.duration }}</span>
                <div class="line">
                  <svg
                    xmlns="http://www.w3.org/2000/svg"
                    xml:space="preserve"
                    viewBox="0 0 12 12"
                    class="LegInfo_planeEnd__ZDkxM"
                  >
                    <path
                      fill="#898294"
                      d="M3.922 12h.499a.52.52 0 0 0 .444-.247L7.949 6.8l3.233-.019A.8.8 0 0 0 12 6a.8.8 0 0 0-.818-.781L7.949 5.2 4.866.246A.525.525 0 0 0 4.421 0h-.499a.523.523 0 0 0-.489.71L5.149 5.2H2.296l-.664-1.33a.523.523 0 0 0-.436-.288L0 3.509 1.097 6 0 8.491l1.196-.073a.523.523 0 0 0 .436-.288l.664-1.33h2.853l-1.716 4.49a.523.523 0 0 0 .489.71"
                    ></path>
                  </svg>
                </div>
              </div>
              <div class="itinerary-info-arrival-date">
                <span class="info-depurate-date">
                  {{ flight.arrivalDate.split(" ")[1] }}
                </span>
                <span class="info-city-code">
                  {{
                    flight.flightSegment[flight.flightSegment.length - 1]
                      .arrivalAirport
                  }}
                </span>
              </div>
            </div>
          </div>
          <!-- End itinerary -->

          <!-- Flight details -->
          <h6>Detalle: </h6>
          <div class="flight-detail-modal">
            <div class="flight-detail-modal-airline">
              <div class=""></div>
            </div>
            <div></div>
            <div></div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">
            Cerrar
          </button>
          <button type="button" class="btn btn-primary">Reservar</button>
        </div>
      </div>
    </div>
  </div>
</template>
<script>
export default {
  name: "ModalFlightDetail",
  props: ["flight"],
}
</script>
