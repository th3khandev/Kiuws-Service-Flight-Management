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
          <h6>Detalle:</h6>
          <template v-if="flight">
            <FligthSegment
              v-for="segment in flight.flightSegment"
              :key="segment.flightNumber"
              :segment="segment"
              :loading-prices="isLoadingPrices(segment.flightNumber)"
              @tryGetPrice="getSegmentPrice(segment)"
            />
            <div class="flight-resume">
              <div class="flight-resume-depurate-date">
                <label class="flight-resume-text">Salida: </label>
                {{ flight.depurateDate }}
                <label class="separator">|</label>
              </div>
              <div class="flight-resume-arrival-date">
                <label class="flight-resume-text">Llegada: </label>
                {{ flight.arrivalDate }}
                <label class="separator">|</label>
              </div>
              <div class="flight-resume-duration">
                <label class="flight-resume-text">Duración: </label>
                {{ getDurationText(flight.duration) }}
                <label class="separator">|</label>
              </div>
              <div class="flight-resume-adults">
                <label class="flight-resume-text">Adultos: </label>
                {{ adults }} <label class="separator">|</label>
              </div>
              <div class="flight-resume-children">
                <label class="flight-resume-text">Niños: </label>
                {{ children }}
              </div>
            </div>
          </template>
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
// service
import { getFlightPrice } from "../../services/kiuwService";

// helpers
import { getDurationText } from "../../helpers/flight";

// components
import FligthSegment from "./fligth-segment.vue";

export default {
  name: "ModalFlightDetail",
  components: {
    FligthSegment,
  },
  props: ["flight", "adults", "children"],
  data: () => ({
    loadingPrices: [],
  }),
  methods: {
    getDurationText,
    async getSegmentPrice(segment) {
      const {
        departureDateTime,
        arrivalDateTime,
        departureAirport,
        arrivalAirport,
        flightNumber,
        bookingClassAvail,
        marketingAirline,
      } = segment;
      this.loadingPrices.push(flightNumber)
      segment.price = null;
      // create string with resBookDesig with , separate
      const resBookDesig = bookingClassAvail.map(book => book.resBookDesigCode).join(',');
      await getFlightPrice(
        departureDateTime,
        arrivalDateTime,
        departureAirport,
        arrivalAirport,
        this.$props.adults,
        this.$props.children,
        flightNumber,
        resBookDesig,
        marketingAirline
      )
        .then((res) => res.json())
        .then((data) => {
          console.log("data >>> ", data);
          if (data.status == "success") {
            segment.price = {
              ...data.price,
              error: false,
            };
          } else {
            segment.price = {
              totalFare: "No disponible",
              currency: "USD",
              error: true,
            };
          }
        })
        .catch(() => {
          segment.price = {
            totalFare: "No disponible",
            currency: "USD",
            error: true,
          };
        }).finally(() => {
          // remove flight numbre from loadingPrices
          this.loadingPrices = this.loadingPrices.filter(flight => flight != flightNumber)
        });
    },
    async getFlightPrices() {
      const { flightSegment } = this.$props.flight;
      console.log("getFlightPrices >>> ");
      if (flightSegment.length > 0) {
        for (let i = 0; i < flightSegment.length; i++) {
          const segment = flightSegment[i];
          await this.getSegmentPrice(segment);
        }
      }
    },
    isLoadingPrices (flightNumber) {
      return this.loadingPrices.includes(flightNumber)
    }
  },
  watch: {
    flight(newValue, oldValue) {
      console.log("change flight >>> ", newValue, oldValue);
      this.getFlightPrices();
    },
  },
};
</script>
<style scope>
.flight-resume {
  display: flex;
  padding: 1rem 1.5rem;
  color: #545860;
  font-size: 12px;
}

.flight-resume-text {
  font-weight: 600;
}

.separator {
  margin: 0 0.5rem;
  font-weight: 700;
}

@media (max-width: 768px) {
  .flight-resume {
    flex-direction: column;
  }
  .separator {
    display: none;
  }
}
</style>
