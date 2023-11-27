<template>
  <div class="row">
    <div class="col-12 text-center">
      <h2>Crear reservación</h2>
    </div>
    <div class="col-12 details-selected-section" v-if="flightReservation">
      <!-- Detail params selected -->
      <div class="row details-selected">
        <div class="col-3 col-md-1">
          <img
            src="/wp-content/plugins/Kiuws-Service-Flight-Management/assets/images/airplane_icon.png"
            width="50"
          />
        </div>
        <div class="col-9 col-sm-10 col-md-8 col-lg-8">
          <div class="deatil-selected-cities">
            <label>{{ flightReservation.originAirport }}</label>
            -
            <label>{{ flightReservation.destinationAirport }}</label>
          </div>
          <div class="flight-resume pl-0 pr-0">
            <div class="flight-resume-depurate-date">
              <label class="flight-resume-text me-1">Salida: </label>
              {{ flightReservation.depurateDate }}
              <label class="separator"> | </label>
            </div>
            <div class="flight-resume-arrival-date">
              <label class="flight-resume-text me-1">Llegada: </label>
              {{ flightReservation.arrivalDate }}
              <label class="separator"> | </label>
            </div>
            <div class="flight-resume-duration">
              <label class="flight-resume-text me-1">Duración: </label>
              {{ getDurationText(flightReservation.duration) }}
            </div>
          </div>
          <div class="flight-resume pl-0 pr-0">
            <div class="flight-resume-adults">
              <label class="flight-resume-text me-1">Adultos: </label>
              {{ flightReservation.adults }}
              <label class="separator"> | </label>
            </div>
            <div class="flight-resume-children">
              <label class="flight-resume-text me-1">Niños: </label>
              {{ flightReservation.children }}
              <label class="separator"> | </label>
            </div>
            <div class="flight-resume-children">
              <label class="flight-resume-text me-1">Infantes: </label>
              {{ flightReservation.inf }}
              <label class="separator"> | </label>
            </div>
            <div class="flight-resume-children">
              <label class="flight-resume-text me-1">Escala(s): </label>
              {{ flightReservation.stops }}
              <label class="separator"> | </label>
            </div>
            <div
              class="flight-resume-children"
              v-if="flightReservation.tripType == 2"
            >
              <label class="flight-resume-text me-1">Ida y Vuelta: </label>
              SI
            </div>
          </div>
        </div>
        <div
          class="col-12 col-sm-12 col-md-3 col-lg-3 deatil-selected-button text-center"
        >
          <h3>
            {{ flightReservation.currencyCode }}
            {{ flightReservation.total.toFixed(2) }}
          </h3>
          <button class="btn btn-primary" @click="$emit('goBack')">
            Regresar
          </button>
        </div>
      </div>
      <form
        class="needs-validation"
        id="form-reservation"
        novalidate
        @submit.prevent="saveReservation"
      >
        <!-- Create forms all passengers -->
        <div class="row">
          <div class="col-12 col-md-12">
            <h3 style="margin-bottom: 5px !important">
              Ingrese datos de los pasajeros
            </h3>
            <hr class="m-0" />
          </div>
          <!-- Accordion -->
          <div class="col-12 col-md-12">
            <div class="accordion" id="accordion-passengers">
              <template
                v-for="(passenger, index) in flightReservation.passengers"
              >
                <div class="accordion-item">
                  <div
                    class="accordion-header p-0 mb-0"
                    :id="`passenger-${index}`"
                  >
                    <h2 class="mb-0" style="margin: 0px !important">
                      <button
                        class="accordion-button"
                        type="button"
                        data-bs-toggle="collapse"
                        :data-bs-target="`#collapse-passenger-${index}`"
                        aria-expanded="true"
                        :aria-controls="`collapse-passenger-${index}`"
                      >
                        Pasagero #{{ index + 1 }} ({{
                          passenger.type == "adult"
                            ? "Adulto"
                            : passenger.type == "child"
                            ? "Niño"
                            : "Infante"
                        }})
                      </button>
                    </h2>
                  </div>

                  <div
                    :id="`collapse-passenger-${index}`"
                    :class="`accordion-collapse collapse ${
                      index == 0 ? 'show' : ''
                    }`"
                    :aria-labelledby="`passenger-${index}`"
                    data-bs-parent="#accordion-passengers"
                  >
                    <div class="accordion-body">
                      <div class="row">
                        <!-- User name -->
                        <div class="col-12 col-md-6">
                          <div class="form-group">
                            <label :for="`name_${index}`"
                              >Nombre
                              <span class="text-danger">(*)</span></label
                            >
                            <input
                              type="text"
                              class="form-control"
                              :name="`name_${index}`"
                              placeholder="Nombre"
                              v-model="passenger.name"
                              required
                              minlength="1"
                              maxlength="20"
                              @blur="handleChangeFirstPassenger(index)"
                              :id="`passenger-name-${index}`"
                            />
                            <div class="invalid-feedback">
                              Error validation.
                            </div>
                          </div>
                        </div>
                        <!-- User last name -->
                        <div class="col-12 col-md-6">
                          <div class="form-group">
                            <label :for="`lastName_${index}`"
                              >Apellido
                              <span class="text-danger">(*)</span></label
                            >
                            <input
                              type="text"
                              class="form-control"
                              :name="`lastName_${index}`"
                              placeholder="Apellido"
                              v-model="passenger.lastName"
                              required
                              minlength="1"
                              maxlength="20"
                              @blur="handleChangeFirstPassenger(index)"
                              :id="`passenger-last-name-${index}`"
                            />
                          </div>
                        </div>
                        <!-- User email -->
                        <div class="col-12 col-md-6" v-if="passenger.type == 'adult'">
                          <div class="form-group">
                            <label :for="`email_${index}`"
                              >Email <span class="text-danger">(*)</span></label
                            >
                            <input
                              type="email"
                              class="form-control"
                              :name="`lastName_${index}`"
                              placeholder="Email"
                              v-model="passenger.email"
                              required
                              @blur="handleChangeFirstPassenger(index)"
                              :id="`passenger-email-${index}`"
                            />
                          </div>
                        </div>
                        <!-- User phone -->
                        <div class="col-12 col-md-6" v-if="passenger.type == 'adult'">
                          <div class="form-group">
                            <label class="w-100" :for="`phone_${index}`"
                              >Número de teléfono
                              <span class="text-danger">(*)</span></label
                            >
                            <input
                              type="tel"
                              class="form-control passanger-phone"
                              :name="`phone_${index}`"
                              placeholder="Número de teléfono"
                              :id="`phone_${index}`"
                              v-model="passenger.phoneNumber"
                              required
                              maxlength="13"
                              minlength="6"
                              @blur="handleChangeFirstPassenger(index)"
                            />
                          </div>
                        </div>
                        <!-- User document type  -->
                        <div class="col-12 col-md-6">
                          <div class="form-group">
                            <label :for="`documentType_${index}`">
                              Tipo de documento
                              <span class="text-danger">(*)</span>
                            </label>
                            <!-- Select doc type -->
                            <select
                              class="form-control"
                              :name="`documentType_${index}`"
                              v-model="passenger.documentType"
                              required
                            >
                              <option value="">
                                -- Seleccione el tipo de documento --
                              </option>
                              <option value="NI">Cédula de identidad</option>
                              <option value="PP">PASAPORTE</option>
                            </select>
                          </div>
                        </div>
                        <!-- User document number -->
                        <div class="col-12 col-md-6">
                          <div class="form-group">
                            <label :for="`documentNumber_${index}`">
                              Documento <span class="text-danger">(*)</span>
                            </label>
                            <!-- Input doc number -->
                            <input
                              type="text"
                              class="form-control"
                              :name="`documentNumber_${index}`"
                              placeholder="Número de documento"
                              v-model="passenger.documentNumber"
                              required
                              minlength="5"
                              maxlength="18"
                            />
                          </div>
                        </div>
                        <!-- User gender -->
                        <div class="col-12 col-md-6">
                          <div class="form-group">
                            <label :for="`gender_${index}`"
                              >Sexo <span class="text-danger">(*)</span></label
                            >
                            <select
                              class="form-control"
                              v-model="passenger.gender"
                              required
                            >
                              <option value="">-- Selecione una opción</option>
                              <option value="M">Masculino</option>
                              <option value="F">Femenino</option>
                            </select>
                          </div>
                        </div>
                        <!-- User birthdate -->
                        <div class="col-12 col-md-6">
                          <div class="form-group">
                            <label :for="`birthDate_${index}`"
                              >Fecha de nacimiento
                              <span class="text-danger">(*)</span></label
                            >
                            <input
                              type="date"
                              class="form-control"
                              :name="`birthDate_${index}`"
                              placeholder="Fecha de nacimiento"
                              v-model="passenger.birthDate"
                              :min="getMinDate(passenger.type)"
                              :max="getMaxDate(passenger.type)"
                              required
                            />
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </template>
            </div>
          </div>
        </div>
        <!-- End form passengers -->

        <!-- Form contact form -->
        <div class="row mt-3">
          <div class="col-12 col-md-12 mb-3">
            <h3 style="margin-bottom: 5px !important">
              Ingrese datos de contacto
            </h3>
            <hr class="m-0" />
          </div>
          <!-- Contact name -->
          <div class="col-12 col-md-6">
            <div class="form-group">
              <label for="contact_name">Nombre</label>
              <input
                type="text"
                class="form-control"
                name="contact_name"
                placeholder="Nombre"
                v-model="flightReservation.contactInfo.name"
                required
                minlength="1"
                maxlength="20"
                id="contact_name"
              />
            </div>
          </div>
          <!-- Contact last name -->
          <div class="col-12 col-md-6">
            <div class="form-group">
              <label for="contact_last_name">Apellido</label>
              <input
                type="text"
                class="form-control"
                name="contact_last_name"
                placeholder="Apellido"
                v-model="flightReservation.contactInfo.lastName"
                required
                minlength="1"
                maxlength="20"
                id="contact_last_name"
              />
            </div>
          </div>
          <!-- Contact email -->
          <div class="col-12 col-md-6">
            <div class="form-group">
              <label for="contact_email">Email</label>
              <input
                type="email"
                class="form-control"
                name="contact_email"
                placeholder="Email"
                v-model="flightReservation.contactInfo.email"
                required
                id="contact_email"
              />
            </div>
          </div>
          <!-- Contact phone -->
          <div class="col-12 col-md-6">
            <div class="form-group">
              <label for="contact_phone">Número de teléfono</label>
              <input
                type="tel"
                class="form-control passanger-phone"
                name="contact_phone"
                id="contact_phone"
                placeholder="Número de teléfono"
                v-model="flightReservation.contactInfo.phoneNumber"
                required
                maxlength="13"
                minlength="6"
              />
            </div>
          </div>
        </div>
        <!-- End form contact -->

        <!-- Form payment -->
        <div class="row mt-3">
          <div class="col-12 col-md-12 mb-3">
            <h3 style="margin-bottom: 5px !important">Ingrese datos de pago</h3>
            <hr class="m-0" />
          </div>
          <!-- Card name -->
          <div class="col-12 col-md-6">
            <div class="form-group">
              <label for="card_name"
                >Nombre tarjeta <span class="text-danger">(*)</span>
              </label>
              <input
                type="text"
                class="form-control"
                name="card_name"
                placeholder="Nombre completo del títular de la tarjeta"
                v-model="flightReservation.paymentInfo.cardName"
                required
                minlength="4"
                maxlength="20"
              />
            </div>
          </div>
          <!-- Card document number -->
          <div class="col-12 col-md-6">
            <div class="form-group">
              <label for="card_document_number"
                >Número de documento <span class="text-danger">(*)</span></label
              >
              <input
                type="text"
                class="form-control"
                name="card_document_number"
                placeholder="Documento de identidad del títular de la tarjeta"
                v-model="flightReservation.paymentInfo.cardDocumentNumber"
                required
              />
            </div>
          </div>
        </div>

        <div class="row mt-3 mb-5">
          <!-- Card element -->
          <div class="col-12">
            <label>
              Número de tarjeta <span class="text-danger">(*)</span>
            </label>
          </div>
          <div class="col-12">
            <div id="card-element"></div>
          </div>
        </div>

        <!-- Alert info creating reservation -->
        <div class="row" v-if="creatingReservation">
          <div class="col-12 col-md-12">
            <div class="alert alert-info alert-dismissible">
              <strong>Espere un momento!</strong> estamos creando su
              reservación...
              <div class="spinner-border text-info" role="status">
                <span class="sr-only">Loading...</span>
              </div>
            </div>
          </div>
        </div>

        <!-- Alert success, reservation created -->
        <div class="row" v-if="reservationCreated">
          <div class="col-12 col-md-12">
            <div class="alert alert-success alert-dismissible">
              <strong>Reservación creada!</strong> su reservación ha sido creada
              con éxito.
              <div>
                <strong>Número de reservacion: </strong>{{ bookingCode }}<br />
                <strong>Importante: </strong> por favor guarde este número de
                reservación para futuras consultas.
              </div>
            </div>
          </div>
        </div>

        <!-- Alert error creating reservation -->
        <div class="row" v-if="errorReservation">
          <div class="col-12 col-md-12">
            <div class="alert alert-danger alert-dismissible">
              <strong>Error!</strong> ha ocurrido un error al intentar reservar,
              por favor intente nuevamente.
            </div>
          </div>
        </div>

        <!-- Alert info creating reservation -->
        <div class="row" v-if="processingPayment">
          <div class="col-12 col-md-12">
            <div class="alert alert-info alert-dismissible">
              <strong>Procesando pago!</strong> estamos procesando su pago...
              <div class="spinner-border text-info" role="status">
                <span class="sr-only">Loading...</span>
              </div>
            </div>
          </div>
        </div>

        <!-- Alert success, payment success -->
        <div class="row" v-if="paymentSuccess">
          <div class="col-12 col-md-12">
            <div class="alert alert-success alert-dismissible">
              <strong>Pago completado!</strong> su pago ha sido completado con
              éxito.
            </div>
          </div>
        </div>

        <!-- Alert error processing payment -->
        <div class="row" v-if="errorPayment">
          <div class="col-12 col-md-12">
            <div class="alert alert-danger alert-dismissible">
              <strong>Error!</strong> {{ errorPaymentMessage }}
            </div>
          </div>
        </div>

        <!-- Alert error stripe -->
        <div class="row" v-if="stripeError && messageErrorStripe">
          <div class="col-12 col-md-12">
            <div class="alert alert-danger alert-dismissible">
              <strong>Error!</strong> {{ messageErrorStripe }}
            </div>
          </div>
        </div>

        <!-- Button submit -->
        <div class="row" v-if="!reservationCreated">
          <div class="col-12 col-md-12 text-center alert-dismissible">
            <button
              type="submit"
              class="btn btn-primary"
              :disabled="
                creatingReservation ||
                processingPayment ||
                stripeError ||
                gettingToken
              "
            >
              Guargar reservación
            </button>
          </div>
        </div>

        <div
          class="row"
          v-if="reservationCreated && !paymentSuccess && errorPayment"
        >
          <div class="col-12 col-md-12 text-center">
            <button
              type="button"
              class="btn btn-primary"
              @click="tryAgainPayment"
              :disabled="
                creatingReservation ||
                processingPayment ||
                stripeError ||
                gettingToken
              "
            >
              Procesar pago
            </button>
          </div>
        </div>

        <div
          class="row"
          v-if="reservationCreated && paymentSuccess && bookingCode"
        >
          <div class="col-12 col-md-12 text-center">
            <a href="/">
              <button type="button" class="btn btn-primary">Ir al home</button>
            </a>
          </div>
        </div>
      </form>
    </div>
  </div>
</template>
<script>
// helpers
import { getDurationText } from "../../helpers/flight";
import { isEmailValid } from "../../helpers/functions";

export default {
  name: "DetailFlightReservation",
  props: [
    "flightReservation",
    "creatingReservation",
    "reservationCreated",
    "errorReservation",
    "processingPayment",
    "errorPayment",
    "paymentSuccess",
    "bookingCode",
    "errorPaymentMessage",
  ],
  data: () => ({
    error: false,
    errorMessage: "",
    phoneInputs: [],
    currentYear: 2023,
    listYears: [],
    stripe: null,
    cardElement: null,
    stripeError: true,
    messageErrorStripe: "",
    gettingToken: false,
  }),
  created() {
    const currentDate = new Date();
    this.currentYear = currentDate.getFullYear();
    this.listYears.push(this.currentYear);
    this.createListYearsForCardExpirationDate();
  },
  methods: {
    getDurationText,
    createListYearsForCardExpirationDate() {
      const limitYears = 10;
      for (let i = 1; i <= limitYears; i++) {
        this.listYears.push(this.currentYear + i);
      }
    },
    setInputsPhoneInthTel() {
      const inputs = document.querySelectorAll(".passanger-phone");
      inputs.forEach((input) => {
        // get id input
        const id = input.id;
        const iti = window.intlTelInput(input, {
          utilsScript:
            "https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.8/js/utils.js",
          initialCountry: "auto",
          geoIpLookup: function (success, failure) {
            // make fetch to 'https://ipinfo.io/'
            fetch("https://api.country.is", {
              headers: {
                accept: "application/json ",
              },
            })
              .then((resp) => resp.json())
              .then((resp) => {
                const countryCode = resp.country;
                success(countryCode);
              })
              .catch(() => {
                failure();
              });
          },
        });
        this.phoneInputs.push({ id, iti });
      });
    },
    getMinDate(passengerType) {
      const currentDate = new Date();
      const currentYear = currentDate.getFullYear();
      const currentMonth = currentDate.getMonth() + 1;
      const currentDay = currentDate.getDate();

      if (passengerType == "adult") {
        return "";
      }

      if (passengerType == "inf") {
        return `${currentYear - 2}-${currentMonth}-${currentDay}`;
      }

      const minDate = `${currentYear - 12}-${currentMonth}-${currentDay}`;
      return minDate;
    },
    getMaxDate(passengerType) {
      const currentDate = new Date();
      const currentYear = currentDate.getFullYear();
      const currentMonth = currentDate.getMonth() + 1;
      const currentDay = currentDate.getDate();

      if (passengerType == "adult") {
        return `${currentYear - 18}-${currentMonth}-${currentDay}`;
      }

      if (passengerType == "child") {
        return `${currentYear - 2}-${currentMonth}-${currentDay}`;
      }

      return "";
    },
    formIsValid() {
      const formReservation = document.getElementById("form-reservation");

      // remove class was-validated for form
      formReservation.classList.remove("was-validated");

      if (formReservation.checkValidity() === false) {
        formReservation.classList.add("was-validated");
        return false;
      }

      // validate all email inputs
      const emailInputs = document.querySelectorAll("input[type=email]");
      emailInputs.forEach((input) => {
        if (!input.checkValidity() || !isEmailValid(input.value)) {
          // add class is-invalid to input
          input.classList.add("is-invalid");
          formReservation.classList.add("was-validated");
          return false;
        }
      });

      // validate all phone inputs
      const phoneInputs = document.querySelectorAll("input[type=tel]");
      phoneInputs.forEach((input) => {
        if (!input.checkValidity()) {
          formReservation.classList.add("was-validated");
          return false;
        }
      });

      // validate all inputs with intlTelInput
      this.phoneInputs.forEach((input) => {
        if (!input.iti.isValidNumber()) {
          // get id input
          const id = input.id;
          // get input
          const inputElement = document.getElementById(id);
          // add class is-invalid
          inputElement.classList.add("is-invalid");
          return false;
        } else {
          // get id input
          const id = input.id;
          // get input
          const inputElement = document.getElementById(id);
          // remove class is-invalid
          inputElement.classList.remove("is-invalid");
        }
      });

      /* // validate card fields
      const cardNumber = document.querySelector("input[name=cardnumber]");
      const cardSecurityCode = document.querySelector(
        "input[name=cvc]"
      );
      const cardExpirationMonth = document.querySelector(
        "select[name=card_expiration_month]"
      );
      const cardExpirationYear = document.querySelector(
        "select[name=card_expiration_year]"
      );

      if (
        !cardNumber ||
        !cardSecurityCode.checkValidity() ||
        !cardExpirationMonth.checkValidity() ||
        !cardExpirationYear.checkValidity()
      ) {
        formReservation.classList.add("was-validated");
        return false;
      }

      // validate card expiration date
      const currentMonth = new Date().getMonth() + 1;
      const currentYear = new Date().getFullYear();
      const cardExpirationMonthValue = parseInt(cardExpirationMonth.value);

      if (
        cardExpirationYear.value == currentYear &&
        cardExpirationMonthValue < currentMonth
      ) {
        // add class is-invalid to fields
        cardExpirationMonth.classList.add("is-invalid");
        cardExpirationYear.classList.add("is-invalid");
        return false;
      }

      // validate card number
      if (cardNumber.value.length < 16) {
        // add class is-invalid to fields
        cardNumber.classList.add("is-invalid");
        return false;
      }

      // validate card security code
      if (
        cardSecurityCode.value.length < 3 ||
        cardSecurityCode.value.length >= 4
      ) {
        // validate if value is only numeric
        if (!/^\d+$/.test(cardSecurityCode.value)) {
          // add class is-invalid to fields
          cardSecurityCode.classList.add("is-invalid");
          return false;
        }
        // add class is-invalid to fields
        cardSecurityCode.classList.add("is-invalid");
        return false;
      } */

      formReservation.classList.add("was-validated");
      return true;
    },
    async saveReservation() {
      this.messageErrorStripe = "";

      // validate form
      if (!this.formIsValid()) {
        return;
      }

      // get token card
      const cardToken = await this.getTokenCard();
      if (!cardToken) {
        this.stripeError = true;
        this.messageErrorStripe =
          "Ha ocurrido un error al intentar procesar el pago, por favor verifique los datos e intente nuevamente.";
        return;
      }

      // set area country code to users
      this.flightReservation.passengers.forEach((passenger) => {
        passenger.phoneCountryCode =
          this.phoneInputs[0].iti.getSelectedCountryData().dialCode;
      });

      // send data to backend
      this.$emit("saveReservation", this.flightReservation);
    },
    createStripe() {
      this.stripe = Stripe(FLIGHT_MANAGEMENT.STRIPE_PUBLIC_KEY);
      const elements = this.stripe.elements({
        mode: "payment",
        currency: this.$props.flightReservation.currencyCode.toLowerCase(),
        amount: parseInt((this.$props.flightReservation.total * 100).toFixed(2)),
      });
      this.cardElement = elements.create("card", {
        style: {
          base: {
            fontSize: "16px",
          },
        },
      });
      this.cardElement.mount("#card-element");
      setTimeout(() => {
        this.cardElement.on("change", (event) => {
          const { error, complete } = event;
          if (error) {
            this.stripeError = true;
            const { code } = error;
            if (code == "incomplete_zip") {
              this.messageErrorStripe = "El código postal es requerido";
            } else if (code == "incomplete_cvc") {
              this.messageErrorStripe = "El código de seguridad es requerido";
            } else if (code == "incomplete_expiry") {
              this.messageErrorStripe = "La fecha de expiración es requerida";
            } else if (code == "incomplete_number") {
              this.messageErrorStripe = "El número de tarjeta es requerido";
            } else if (code == "invalid_number") {
              this.messageErrorStripe = "El número de tarjeta es inválido";
            } else if (code == "invalid_expiry_year_past") {
              this.messageErrorStripe = "La fecha de expiración es inválida";
            } else {
              this.messageErrorStripe =
                "Ha ocurrido un error al intentar procesar el pago, por favor verifique los datos e intente nuevamente.";
            }
          } else {
            if (complete) {
              this.stripeError = false;
              this.messageErrorStripe = "";
            } else {
              this.stripeError = true;
              this.messageErrorStripe =
                "Debe completar todos los campos de la tarjeta";
            }
          }
        });
      }, 1000);
    },
    async getTokenCard() {
      this.gettingToken = true;
      const { cardName } = this.$props.flightReservation.paymentInfo;

      try {
        const stripeResponse = await this.stripe.createSource(
          this.cardElement,
          {
            type: "card",
            owner: {
              name: cardName,
            },
            usage: "single_use",
          }
        );

        if (stripeResponse.error) {
          this.stripeError = true;
          this.messageErrorStripe = stripeResponse.error.message;
          return null;
        }

        const { source } = stripeResponse;
        const { id, card } = source;
        this.$props.flightReservation.paymentInfo.cardToken = id;
        // complete with '*' the card number
        this.$props.flightReservation.paymentInfo.cardNumber = `************${card.last4}`;

        // get token data
        const cardToken = id;
        this.gettingToken = false;
        return cardToken;
      } catch (error) {
        this.stripeError = true;
        this.messageErrorStripe =
          "Ha ocurrido un error al intentar procesar el pago, por favor verifique los datos e intente nuevamente.";
        this.gettingToken = false;
        return null;
      }
    },
    async tryAgainPayment() {
      this.$emit("tryAgainPayment");
      // get token card
      const cardToken = await this.getTokenCard();
      if (!cardToken) {
        this.stripeError = true;
        this.messageErrorStripe =
          "Ha ocurrido un error al intentar procesar el pago, por favor verifique los datos e intente nuevamente.";
        return;
      }
      this.$emit("proccessPayment");
    },
    handleChangeFirstPassenger(index) {
      if (index === 0) {
        setTimeout(() => {
          this.$nextTick(() => {
            const firstPassenger = this.flightReservation.passengers[0];
            this.flightReservation.contactInfo.name = firstPassenger.name;
            this.flightReservation.contactInfo.lastName =
              firstPassenger.lastName;
            this.flightReservation.contactInfo.email = firstPassenger.email;
            this.flightReservation.contactInfo.phoneNumber =
              firstPassenger.phoneNumber;

            // get inputs contact
            const inputName = document.getElementById("contact_name");
            const inputLastName = document.getElementById("contact_last_name");
            const inputEmail = document.getElementById("contact_email");
            const inputPhone = document.getElementById("contact_phone");

            // set value
            inputName.value = firstPassenger.name;
            inputLastName.value = firstPassenger.lastName;
            inputEmail.value = firstPassenger.email;
            inputPhone.value = firstPassenger.phoneNumber;
          });
        }, 200);
      }
    },
  },
  mounted() {
    setTimeout(() => {
      this.setInputsPhoneInthTel();
      this.createStripe();
    }, 1000);
  },
};
</script>
