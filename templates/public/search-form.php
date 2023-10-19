<div id="flight-search-form-app">
    <flight-search-form
        v-show="!loading && step == 1"
        :loading="loading"
        @submit="searhFlights"
        ref="flightSearchForm"
        :step="step"
    ></flight-search-form>
    <div class="row" v-if="loading">
        <!-- Show spinner -->
        <div class="text-center col-12 p-3">
            <div class="spinner-border" role="status">
                <div class="lds-spinner">
                    <div></div>
                    <div></div>
                    <div></div>
                    <div></div>
                    <div></div>
                    <div></div>
                    <div></div>
                    <div></div>
                    <div></div>
                    <div></div>
                    <div></div>
                    <div></div>
                </div>
            </div>
        </div>
    </div>
    <div v-if="!loading && step == 2">
        <flight-details-selected v-if="step == 2" @refresh="refreshSearh" :step="step" :adults="adults" :children="children" :origin-airport="originAirport" :destination-airport="destinationAirport" :depurate-date="depurateDate" ></flight-details-selected>
        <div class="row">
            <div class="col-12 col-sm-12 col-md-4 col-lg-4">
                <flight-sidebar></flight-sidebar>
            </div>
            <div class="col-12 col-sm-12 col-md-8 col-lg-8">
                <flight-itinerary v-for="flight in flights" :flight="flight"></flight-itinerary>
            </div>
        </div>
    </div>
</div>