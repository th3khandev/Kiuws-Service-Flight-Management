const flightItineraryComponent = {
  template: `
  <div class="itinerary-container">
    <div class="itinerary-airline">
        <img :src="flight.flightSegment[0].marketingAirlineLogo" :alt="flight.flightSegment[0].marketingAirlineName" />
        <h3 v-if="flight.flightSegment[0].marketingAirlineLogo.includes('default.png')">
            {{ flight.flightSegment[0].marketingAirlineName }}
        </h3>
    </div>
    <div class="col-12 col-md-4">
        {{ flight.depurateDate.split(' ')[1] }} - {{ flight.arrivalDate.split(' ')[1] }}<br/>
        {{ flight.duration }} H <br/>
        {{ flight.stops }} Escalas
    </div>
    <div class="col-12 col-md-4">
        <button type="button" class="btn btn-primary">
            Reservar
        </button>
    </div>
  </div>`,
  data: () => ({
    massage: "este es el sidebar",
  }),
  props: ['flight'],
  methods: {},
};

export default flightItineraryComponent;
