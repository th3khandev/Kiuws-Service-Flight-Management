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
              :loading-prices="loadingPrices"
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
            <div class="row text-center">
              <div class="col-12" v-if="error">
                <div class="alert alert-danger">
                  <button
                    type="button"
                    class="close"
                    data-dismiss="alert"
                    aria-label="Close"
                    @click="error = false"
                  >
                    <span aria-hidden="true">&times;</span>
                  </button>
                  <strong>-</strong> No se pudo obtener el precio de uno o
                  varios segmentos de vuelo, por favor intente nuevamente.
                </div>
              </div>
              <div class="col-12" v-if="!loadingPrices">
                <h6 class="price-total">
                  {{
                    totalPrice > 0
                      ? `Total: USD ${totalPrice.toFixed(2)}`
                      : "No disponible"
                  }}
                </h6>
                <button
                  type="button"
                  class="btn btn-primary"
                  target="modal"
                  data-dismiss="modal"
                  @click="createReservation"
                  v-if="!reservationError"
                >
                  Crear reservación
                </button>
                <button
                  type="button"
                  class="btn btn-primary"
                  target="modal"
                  data-dismiss="modal"
                  @click="tryGetPrice"
                  v-else
                >
                  Reintentar
                </button>
              </div>
              <div class="col-12 text-center" v-else>
                <Loading />
              </div>
            </div>
          </template>
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
import Loading from "./loading.vue";

export default {
  name: "ModalFlightDetail",
  components: {
    FligthSegment,
    Loading,
  },
  props: ["flight", "adults", "children"],
  data: () => ({
    loadingPrices: false,
    totalPrice: 0,
    error: false,
    reservationError: false,
  }),
  methods: {
    getDurationText,
    async getFlightPrices() {
      this.loadingPrices = true;
      const { flightSegment } = this.$props.flight;
      console.log("getFlightPrices >>> ");
      if (flightSegment.length > 0) {
        const flightSegmentsBody = [];
        for (let i = 0; i < flightSegment.length; i++) {
          const segment = flightSegment[i];
          const {
            departureDateTime,
            arrivalDateTime,
            departureAirport,
            arrivalAirport,
            flightNumber,
            bookingClassAvail,
            marketingAirline,
          } = segment;
          // create string with resBookDesig with , separate
          const resBookDesig = bookingClassAvail
            .map((book) => book.resBookDesigCode)
            .join(",");

          flightSegmentsBody.push({
            depurateDateTime: departureDateTime,
            arrivalDateTime,
            origin: departureAirport,
            destination: arrivalAirport,
            adults: this.$props.adults,
            children: this.$props.children,
            flightNumber,
            resBookDesig,
            airlineCode: marketingAirline,
          });
        }
        console.log("flightSegmentsBody >>> ", flightSegmentsBody);
        getFlightPrice(flightSegmentsBody)
          .then((res) => res.json())
          .then((data) => {
            console.log("data >>> ", data);
            if (data.status == "success") {
              this.$props.flight.price = {
                ...data.price,
              };
              this.totalPrice += data.price.totalFare;
              // set res book desig validate by segment
              for (let i = 0; i < data.flight_segments.length; i++) {
                const segment_validate = data.flight_segments[i];
                const _flightSegment = this.$props.flight.flightSegment.find(
                  (segment) =>
                    segment.flightNumber == segment_validate.flightNumber
                );
                console.log("_flightSegment >> ", _flightSegment);
                _flightSegment.resBookDesig = segment_validate.resBookDesig;
              }
            } else {
              this.error = true;
              this.reservationError = true;
            }
          })
          .catch((err) => {
            this.error = true;
            console.log("err >>> ", err);
          })
          .finally(() => {
            // remove flight numbre from loadingPrices
            this.loadingPrices = false;
          });
      }
    },
    createReservation() {
      const {
        depurateDate,
        arrivalDate,
        duration,
        id,
        stops,
        flightSegment,
        price,
      } = this.$props.flight;
      const { totalFare, totalTaxes, baseFare, taxes, currencyCode } = price;
      const { totalPrice } = this;

      // get origin and destination
      const origin = flightSegment[0].departureAirport;
      const destination =
        flightSegment[flightSegment.length - 1].arrivalAirport;

      this.$emit("createReservation", {
        depurateDate,
        arrivalDate,
        duration,
        id,
        stops,
        adults: this.$props.adults,
        children: this.$props.children,
        total: totalPrice,
        origin,
        destination,
        currencyCode,
        total: totalFare,
        totalTaxes,
        baseFare,
        taxes,
        segment: flightSegment.map((segment) => {
          const {
            arrivalAirport,
            arrivalDateTime,
            departureAirport,
            departureDateTime,
            journeyDuration,
            marketingAirline,
            marketingAirlineName,
            flightNumber,
            resBookDesig,
          } = segment;
          return {
            arrivalAirport,
            arrivalDateTime,
            departureAirport,
            departureDateTime,
            duration: journeyDuration,
            airlineCode: marketingAirline,
            airlineName: marketingAirlineName,
            flightNumber,
            resBookDesig,
          };
        }),
      });
    },
    tryGetPrice () {
      this.totalPrice = 0;
      this.error = false;
      this.reservationError = false;
      this.getFlightPrices();
    }
  },
  watch: {
    flight(newValue, oldValue) {
      console.log("change flight >>> ", newValue, oldValue);
      this.tryGetPrice();
    },
  },
};
</script>
