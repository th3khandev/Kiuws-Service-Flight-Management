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
            data-bs-dismiss="modal"
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
            <div class="flight-resume mt-3 mb-3">
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
                {{ children }} <label class="separator">|</label>
              </div>
              <div class="flight-resume-children">
                <label class="flight-resume-text">Infantes: </label>
                {{ inf }}
              </div>
            </div>

            <!-- Detail options return -->
            <div class="row" v-if="tripType == 2">
              <div class="col-12 col-md-12">
                <h6>Seleccione una opción de vuelo de regreso:</h6>
              </div>
              <div class="col-12 col-md-12">
                <div class="accordion" id="accordion-return-flights">
                  <div
                    class="card"
                    v-for="(returnFlight, index) in returnFlights"
                    :key="index"
                  >
                    <div
                      class="card-header"
                      :id="`return-flight-${returnFlight.id}`"
                    >
                      <h2 style="margin-bottom: 0px !important">
                        <button
                          class="btn btn-link btn-block text-left text-primary p-0"
                          type="button"
                          data-bs-toggle="collapse"
                          :data-bs-target="`#collapse-return-flight-${returnFlight.id}`"
                          aria-expanded="true"
                          :aria-controls="`collapse-return-flight-${returnFlight.id}`"
                          @click="handleFlightSelected(returnFlight)"
                        >
                          <input
                            class="form-check-input"
                            type="radio"
                            name="return_flight_selected"
                            :id="`return_flight_${returnFlight.id}`"
                            style="position: relative; margin-left: 0px"
                          />
                          Salida: {{ returnFlight.depurateDate }} | Llegada:
                          {{ returnFlight.arrivalDate }} | Duración:
                          {{ getDurationText(returnFlight.duration) }}
                        </button>
                      </h2>
                    </div>

                    <div
                      :id="`collapse-return-flight-${returnFlight.id}`"
                      :class="`collapse ${index == 0 ? 'show' : ''}`"
                      :aria-labelledby="`return-flight-${returnFlight.id}`"
                      data-bs-parent="#accordion-return-flights"
                    >
                      <div class="card-body">
                        <FligthSegment
                          v-for="segment in returnFlight.flightSegment"
                          :key="segment.flightNumber"
                          :segment="segment"
                          :loading-prices="loadingPrices"
                        />
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
            <!-- End detail options return -->

            <div class="row text-center">
              <div class="col-12 mb-3 mt-2" v-if="error">
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
              <div class="col-12 mt-1 mb-3" v-if="!loadingPrices">
                <div class="row price-total">
                  <div class="col-6 col-md-6 text-right text-end">Base:</div>
                  <div class="col-6 col-md-6 text-left text-start">
                    {{ priceDetail.currencyCode }} {{ parseFloat(priceDetail.baseFare).toFixed(2) }}
                  </div>
                  <div class="col-6 col-md-6 text-right text-end">
                    Impuestos:
                  </div>
                  <div class="col-6 col-md-6 text-left text-start">
                    {{ priceDetail.currencyCode }} {{ parseFloat(priceDetail.totalTaxes).toFixed(2) }}
                  </div>
                  <div class="col-6 col-md-6 text-right text-end">
                    Cargos:
                  </div>
                  <div class="col-6 col-md-6 text-left text-start">
                    {{ priceDetail.currencyCode }} {{ parseFloat(priceDetail.fee).toFixed(2) }}
                  </div>
                  <div class="col-6 col-md-6 text-right text-end">Total:</div>
                  <div class="col-6 col-md-6 text-left text-start">
                    {{ priceDetail.currencyCode }} {{ parseFloat(priceDetail.totalFare + priceDetail.fee).toFixed(2) }}
                  </div>
                </div>
                <div class="row">
                  <div class="col-12 col-md-12 text-center">
                    <button
                      type="button"
                      class="btn btn-primary mt-2"
                      target="modal"
                      data-bs-dismiss="modal"
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
                </div>
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
  props: ["flight", "adults", "children", "inf", "returnFlights", "tripType"],
  data: () => ({
    loadingPrices: false,
    totalPrice: 0,
    error: false,
    reservationError: false,
    priceDetail: {
      baseFare: 0,
      currencyCode: "",
      taxes: [],
      totalFare: 0,
      totalTaxes: 0,
    },
    returnFlightSelected: null,
  }),
  methods: {
    getDurationText,
    getReturnFlightSelectedData() {
      const returnFlightSelected = this.returnFlights.find(
        (flight) => flight.id == this.returnFlightSelected
      );
      if (returnFlightSelected) {
        const { flightSegment } = returnFlightSelected;
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
            inf: this.$props.inf,
          });
        }
        return flightSegmentsBody;
      }
      return [];
    },
    async getFlightPrices() {
      this.loadingPrices = true;
      const { flightSegment } = this.$props.flight;
      if (flightSegment.length > 0) {
        const flightSegmentsBody = [];
        const returnFlightDataBody = {};
        const departureFlightDataBody = {};
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
            inf: this.$props.inf,
          });
        }

        departureFlightDataBody.flightSegments = flightSegmentsBody;

        if (this.$props.tripType == 2) {
          returnFlightDataBody.flightSegments =
            this.getReturnFlightSelectedData();
        }

        getFlightPrice(
          departureFlightDataBody.flightSegments,
          returnFlightDataBody.flightSegments
        )
          .then((res) => res.json())
          .then((data) => {
            if (data.status == "success") {
              this.$props.flight.price = {
                ...data.price,
              };
              this.priceDetail = {
                ...data.price,
              };
              this.totalPrice += data.price.totalFare;
              this.totalPrice += data.price.fee;

              // set res book desig validate by segment
              for (let i = 0; i < data.departure_flight_segments.length; i++) {
                const segment_validate = data.departure_flight_segments[i];
                const _flightSegment = this.$props.flight.flightSegment.find(
                  (segment) =>
                    segment.flightNumber == segment_validate.flightNumber
                );
                if (_flightSegment) {
                  _flightSegment.resBookDesig = segment_validate.resBookDesig;
                }
              }
              // set resbooking desig return flight
              if (this.$props.tripType == 2) {
                for (
                  let i = 0;
                  i < data.return_flight_segments.length;
                  i++
                ) {
                  const return_segment_validate = data.return_flight_segments[i];
                  const _returnFlightSegment = this.$props.returnFlights[0].flightSegment.find(
                    (segment) =>
                      segment.flightNumber == return_segment_validate.flightNumber
                  );
                  if (_returnFlightSegment) {
                    _returnFlightSegment.resBookDesig = return_segment_validate.resBookDesig;
                  }
                }
              }
            } else {
              this.error = true;
              this.reservationError = true;
            }
          })
          .catch((err) => {
            this.error = true;
            console.log('error >> ', err)
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
      const { totalFare, totalTaxes, baseFare, taxes, currencyCode, fee } = price;
      const { totalPrice } = this;

      // get origin and destination
      const origin = flightSegment[0].departureAirport;
      const destination =
        flightSegment[flightSegment.length - 1].arrivalAirport;

      // get return flight selected
      const returnFlightSelected = this.returnFlights.find(
        (flight) => flight.id == this.returnFlightSelected
      );
      let _returnflightSegment = [];
      if (returnFlightSelected) {
        _returnflightSegment = returnFlightSelected.flightSegment;
      }

      this.$emit("createReservation", {
        depurateDate,
        arrivalDate,
        duration,
        id,
        stops,
        adults: this.$props.adults,
        children: this.$props.children,
        inf: this.$props.inf,
        total: totalPrice,
        origin,
        destination,
        currencyCode,
        total: totalFare,
        totalTaxes,
        baseFare,
        taxes,
        fee,
        tripType: this.$props.tripType,
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
        returnSegments: _returnflightSegment.map(segment => {
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
    tryGetPrice() {
      this.totalPrice = 0;
      this.error = false;
      this.reservationError = false;
      this.priceDetail = {
        baseFare: 0,
        currencyCode: "",
        taxes: [],
        totalFare: 0,
        totalTaxes: 0,
      };
      this.getFlightPrices();
    },
    handleFlightSelected(returnFlight) {
      if (this.returnFlightSelected == returnFlight.id) {
        this.returnFlightSelected = null;
        return;
      }

      this.returnFlightSelected = returnFlight.id;
      console.log("handleFlightSelected >> ", returnFlight);
      // check button radio `return_flight_${returnFlight.id}`
      const radio = document.getElementById(`return_flight_${returnFlight.id}`);
      console.log('radio: ', radio);
      if (radio) {
        radio.checked = true;
      }
      setTimeout(() => {
        this.tryGetPrice();
      }, 250);
    },
    setFirstReturnFlight() {
      if (this.returnFlights.length > 0) {
        this.handleFlightSelected(this.returnFlights[0]);
      }
    },
  },
  watch: {
    flight(newValue, oldValue) {
      this.returnFlightSelected = null;
      if (this.$props.tripType == 1) {
        this.tryGetPrice();
      } else {
        setTimeout(() => {
          this.setFirstReturnFlight();
        }, 500);
      }
    },
  },
};
</script>
