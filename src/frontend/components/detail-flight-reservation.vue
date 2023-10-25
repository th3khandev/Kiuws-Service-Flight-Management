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
              <label class="flight-resume-text">Salida: </label>
              {{ flightReservation.depurateDate }}
              <label class="separator"> | </label>
            </div>
            <div class="flight-resume-arrival-date">
              <label class="flight-resume-text">Llegada: </label>
              {{ flightReservation.arrivalDate }}
              <label class="separator"> | </label>
            </div>
            <div class="flight-resume-duration">
              <label class="flight-resume-text">Duración: </label>
              {{ getDurationText(flightReservation.duration) }}
              <label class="separator"> | </label>
            </div>
            <div class="flight-resume-adults">
              <label class="flight-resume-text">Adultos: </label>
              {{ flightReservation.adults }}
              <label class="separator"> | </label>
            </div>
            <div class="flight-resume-children">
              <label class="flight-resume-text">Niños: </label>
              {{ flightReservation.children }}
              <label class="separator"> | </label>
            </div>
            <div class="flight-resume-children">
              <label class="flight-resume-text">Escala(s): </label>
              {{ flightReservation.stops }}
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
                <div class="card">
                  <div class="card-header p-0 mb-0" :id="`passenger-${index}`">
                    <h2 class="mb-0" style="margin: 0px !important">
                      <button
                        class="btn btn-primary btn-block text-left"
                        type="button"
                        data-toggle="collapse"
                        :data-target="`#collapse-passenger-${index}`"
                        aria-expanded="true"
                        :aria-controls="`collapse-passenger-${index}`"
                      >
                        Pasagero #{{ index + 1 }} ({{
                          passenger.type == "adult" ? "Adulto" : "Niño"
                        }})
                      </button>
                    </h2>
                  </div>

                  <div
                    :id="`collapse-passenger-${index}`"
                    :class="`collapse ${index == 0 ? 'show' : ''}`"
                    :aria-labelledby="`passenger-${index}`"
                    data-parent="#accordion-passengers"
                  >
                    <div class="card-body">
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
                            />
                          </div>
                        </div>
                        <!-- User email -->
                        <div class="col-12 col-md-6">
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
                            />
                          </div>
                        </div>
                        <!-- User phone -->
                        <div class="col-12 col-md-6">
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
                              :max="getMinDate(passenger.type)"
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
              <label for="card_name">Nombre tarjeta</label>
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
              <label for="card_document_number">Número de documento</label>
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
          <!-- Card number  and security code CVC -->
          <div class="col-12 col-md-6">
            <div class="row">
              <div class="col-12 col-md-8">
                <div class="form-group">
                  <label for="card_number">Número de tarjeta</label>
                  <input
                    type="text"
                    class="form-control"
                    name="card_number"
                    placeholder="Número de tarjeta"
                    v-model="flightReservation.paymentInfo.cardNumber"
                    required
                  />
                </div>
              </div>
              <div class="col-12 col-md-4">
                <div class="form-group">
                  <label for="card_security_code">Código de seguridad</label>
                  <input
                    type="text"
                    minlength="3"
                    maxlength="3"
                    required
                    class="form-control"
                    name="card_security_code"
                    placeholder="Código de seguridad"
                    v-model="flightReservation.paymentInfo.cardSecurityCode"
                  />
                </div>
              </div>
            </div>
          </div>
          <!-- Card expiration date -->
          <div class="col-12 col-md-6">
            <div class="form-group">
              <label for="card_expiration_date">Fecha de expiración</label>
              <div class="row">
                <div class="col-6 col-md-6">
                  <select
                    name="card_expiration_month"
                    class="form-control"
                    v-model="flightReservation.paymentInfo.cardExpirationMonth"
                    required
                  >
                    <option value="">-- Mes --</option>
                    <option value="01">Enero</option>
                    <option value="02">Febrero</option>
                    <option value="03">Marzo</option>
                    <option value="04">Abril</option>
                    <option value="05">Mayo</option>
                    <option value="06">Junio</option>
                    <option value="07">Julio</option>
                    <option value="08">Agosto</option>
                    <option value="09">Septiembre</option>
                    <option value="10">Octubre</option>
                    <option value="11">Noviembre</option>
                    <option value="12">Diciembre</option>
                  </select>
                </div>
                <div class="col-6 col-md-6">
                  <select
                    class="form-control"
                    name="card_expiration_year"
                    v-model="flightReservation.paymentInfo.cardExpirationYear"
                    required
                  >
                    <option value="">-- Año --</option>
                    <option v-for="year in listYears" :key="year" :value="year">
                      {{ year }}
                    </option>
                  </select>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Button submit -->
        <div class="row">
          <div class="col-12 col-md-12 text-center">
            <button type="submit" class="btn btn-primary">
              Enviar
            </button>
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
  props: ["flightReservation"],
  data: () => ({
    error: false,
    errorMessage: "",
    phoneInputs: [],
    currentYear: 2023,
    listYears: [],
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
        });
        this.phoneInputs.push({ id, iti });
      });
    },
    getMinDate (passengerType) {
      const currentDate = new Date();
      const currentYear = currentDate.getFullYear();
      const currentMonth = currentDate.getMonth() + 1;
      const currentDay = currentDate.getDate();
      const minDate = `${currentYear - (passengerType == 'adult' ? 18 : 2)}-${currentMonth}-${currentDay}`;
      return minDate;
    },
    formIsValid () {
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

      // validate card fields
      const cardNumber = document.querySelector("input[name=card_number]");
      const cardSecurityCode = document.querySelector(
        "input[name=card_security_code]"
      );
      const cardExpirationMonth = document.querySelector(
        "select[name=card_expiration_month]"
      );
      const cardExpirationYear = document.querySelector(
        "select[name=card_expiration_year]"
      );

      if (
        !cardNumber.checkValidity() ||
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
      const cardExpirationMonthValue = parseInt(
        cardExpirationMonth.value
      );

      if (
        cardExpirationYear.value == currentYear &&
        cardExpirationMonthValue < currentMonth
      ) {
        formReservation.classList.add("was-validated");
        // add class is-invalid to fields
        cardExpirationMonth.classList.add("is-invalid");
        cardExpirationYear.classList.add("is-invalid");
        return false;
      }

      // validate card number
      if (cardNumber.value.length < 16) {
        formReservation.classList.add("was-validated");
        // add class is-invalid to fields
        cardNumber.classList.add("is-invalid");
        return false;
      }

      // validate card security code
      if (cardSecurityCode.value.length < 3 || cardSecurityCode.value.length >= 4) {
        formReservation.classList.add("was-validated");
        // add class is-invalid to fields
        cardSecurityCode.classList.add("is-invalid");
        return false;
      }

      formReservation.classList.add("was-validated");
      return true;
    },
    saveReservation() {
      console.log("send form reservation >> ", this.flightReservation);
      // validate form
      if (!this.formIsValid()) {
        console.log('form invalid');
        return;
      }

      // set area country code to users
      this.flightReservation.passengers.forEach((passenger) => {
        passenger.phoneCountryCode = this.phoneInputs[0].iti.getSelectedCountryData().dialCode;
      });

      // send data to backend
      this.$emit("saveReservation", this.flightReservation);
    },
  },
  mounted() {
    console.log(this.flightReservation);
    setTimeout(() => {
      this.setInputsPhoneInthTel();
    }, 1000);
  },
};
</script>
