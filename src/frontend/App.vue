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
          :inf="inf"
          :depurate-date="depurateDate"
          :destination-airport="destinationAirport"
          :origin-airport="originAirport"
          @refresh="step = 1"
        />
      </div>
      <div class="col-12 col-md-1">
        <!-- <SidebarFilters /> -->
      </div>
      <div class="col-12 col-md-9">
        <Itinerary
          v-for="(flight, index) in flights"
          :flight="flight"
          :key="index"
          @flight-selected="handleFlightSelected"
        />
      </div>
      <div class="col-12 col-md-1">
        <!-- <SidebarFilters /> -->
      </div>
    </div>
    <div class="row" v-if="!loading && step == 3">
      <div class="col-12 col-md-12">
        <DetailFlightReservation
          :flightReservation="flightReservation"
          :creatingReservation="creatingReservation"
          :reservationCreated="reservationCreated"
          :errorReservation="errorReservation"
          :processingPayment="processingPayment"
          :errorPayment="errorPayment"
          :paymentSuccess="paymentSuccess"
          :bookingCode="bookingCode"
          :errorPaymentMessage="errorPaymentMessage"
          ref="detailFlightReservationForm"
          @goBack="step = 2"
          @saveReservation="saveReservation"
          @proccessPayment="processPayment"
          @tryAgainPayment="tryAgainPayment"
        />
      </div>
    </div>
    <ModalFlightDetail
      :flight="flightSelected"
      :children="children"
      :adults="adults"
      :inf="inf"
      :return-flights="returnFlights"
      :trip-type="tripType"
      @createReservation="createReservation"
    />
  </div>
</template>
<script>
// services
import {
  getFlightsAvailable,
  createReservation,
  processPayment,
} from "../services/kiuwService";

// components
import FormSearchComponent from "./components/form-search.vue";
import Loading from "./components/loading.vue";
import SidebarFilters from "./components/sidebar-filters.vue";
import Itinerary from "./components/itinerary.vue";
import DetailFiltersSelected from "./components/detail-filters-selected.vue";
import ModalFlightDetail from "./components/modal-flight-detail.vue";
import DetailFlightReservation from "./components/detail-flight-reservation.vue";

export default {
  name: "App",
  components: {
    FormSearchComponent,
    Loading,
    SidebarFilters,
    Itinerary,
    DetailFiltersSelected,
    ModalFlightDetail,
    DetailFlightReservation,
  },
  data() {
    return {
      step: 1,
      loading: false,
      flights: [],
      returnFlights: [],
      originAirport: null,
      destinationAirport: null,
      depurateDate: null,
      returnDate: null,
      adults: 1,
      children: 0,
      inf: 0,
      tripType: 1,
      flightSelected: null,
      flightReservation: null,
      creatingReservation: false,
      reservationCreated: false,
      processingPayment: false,
      errorReservation: false,
      errorPayment: false,
      paymentSuccess: false,
      bookingCode: null,
      errorPaymentMessage: null,
      onlyDirectFlights: false,
    };
  },
  methods: {
    handleSearchFlights(
      originAirport,
      destinationAirport,
      depurateDate,
      returnDate,
      adults,
      children,
      inf,
      tripType,
      onlyDirectFlights
    ) {
      this.originAirport = originAirport;
      this.destinationAirport = destinationAirport;
      this.adults = adults;
      this.children = children;
      this.inf = inf;
      this.depurateDate = depurateDate;
      this.loading = true;
      this.flights = [];
      this.flightReservation = null;
      this.errorReservation = false;
      this.errorPayment = false;
      this.paymentSuccess = false;
      this.bookingCode = null;
      this.errorPaymentMessage = null;
      this.tripType = tripType;
      this.onlyDirectFlights = onlyDirectFlights;
      this.returnFlights = [];
      getFlightsAvailable(
        originAirport.code,
        destinationAirport.code,
        depurateDate,
        returnDate,
        adults,
        children,
        inf,
        tripType,
        onlyDirectFlights
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
            const { flights, returnFlights } = data;
            if (flights.length == 0) {
              this.$refs.flightSearchForm.setError(
                "No se encontraron vuelos disponibles, para los datos ingresados"
              );
            } else {
              this.flights = flights;
              this.returnFlights = returnFlights;
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
    createPassengerData(type = "adult") {
      return {
        name: "",
        lastName: "",
        type,
        gender: "",
        birthDate: "",
        nationality: "",
        documentType: "PP",
        documentNumber: "",
        passportIssueDate: "",
        passportExpirationDate: "",
        email: "",
        phoneCountryCode: "",
        phoneNumber: "",
      };
    },
    createReservation(reservationFlightData) {
      this.reservationCreated = false;
      this.errorPaymentMessage = null;
      this.flightReservation = {
        ...reservationFlightData,
        destinationAirport: `${this.destinationAirport.country_name} ${this.destinationAirport.city_name} (${this.destinationAirport.code}), ${this.destinationAirport.name}`,
        originAirport: `${this.originAirport.country_name} ${this.originAirport.city_name} (${this.originAirport.code}), ${this.originAirport.name}`,
        passengers: [],
      };

      // create passengers by adults amount
      for (let i = 0; i < this.flightReservation.adults; i++) {
        this.flightReservation.passengers.push(this.createPassengerData());
      }

      // create passengers by children amount
      for (let i = 0; i < this.flightReservation.children; i++) {
        this.flightReservation.passengers.push(
          this.createPassengerData("child")
        );
      }

      // create passengers by inf amount
      for (let i = 0; i < this.flightReservation.inf; i++) {
        this.flightReservation.passengers.push(this.createPassengerData("inf"));
      }

      // create contact info
      this.flightReservation.contactInfo = {
        name: "",
        lastName: "",
        email: "",
        phoneCountryCode: "",
        phoneNumber: "",
      };

      // create payment info
      this.flightReservation.paymentInfo = {
        cardNumber: "",
        cardDocumentNumber: "",
        cardName: "",
        cardToken: "",
      };

      this.step = 3;
    },
    tryAgainPayment() {
      this.errorPayment = false;
      this.paymentSuccess = false;
    },
    processPayment() {
      this.processingPayment = true;
      this.errorPaymentMessage = null;
      this.errorPayment = false;
      const { paymentInfo, contactInfo, total, currencyCode } =
        this.flightReservation;
      const { email } = contactInfo;
      const { cardName, cardNumber, cardDocumentNumber, cardToken } =
        paymentInfo;
      processPayment(
        cardName,
        cardDocumentNumber,
        cardNumber,
        email,
        total,
        currencyCode,
        this.bookingCode,
        cardToken
      )
        .then((response) => response.json())
        .then((response) => {
          if (response.success) {
            this.paymentSuccess = true;
          } else {
            this.errorPayment = true;
            this.errorPaymentMessage = response.message;
          }
        })
        .finally(() => {
          this.processingPayment = false;
        });
    },
    saveReservation(reservationData) {
      this.creatingReservation = true;
      this.reservationCreated = false;
      this.errorReservation = false;
      this.errorPayment = false;
      this.paymentSuccess = false;
      this.bookingCode = null;
      createReservation(reservationData)
        .then((response) => response.json())
        .then((data) => {
          if (data.status == "success") {
            this.reservationCreated = true;
            this.bookingCode = data.bookingId;
            this.processPayment();
          } else {
            this.errorReservation = true;
          }
        })
        .catch(() => {
          this.errorReservation = true;
        })
        .finally(() => {
          this.creatingReservation = false;
        });
    },
  },
};
</script>

<style>
@import url("bootstrap/dist/css/bootstrap.min.css");
@import url("vue-select/dist/vue-select.css");
@import url("../../assets/css/main.css");
</style>
