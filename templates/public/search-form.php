<div id="flight-search-form-app">
    <flight-search-form
        v-show="!loading && step == 1"
        :loading="loading"
        @submit="searhFlights"
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
    {{ message }}
</div>