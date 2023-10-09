<div id="flight-search-form">
    <form>
        <div class="row">
            <!-- Add fields -->
            <!-- Origin -->
            <div class="col-12 col-sm-12 col-md-6 col-lg-6 mb-2">
                <label for="origin" class="form-label">Desde: </label>
                <v-select name="origin" class="form-control" label="name" :filterable="false" :options="originAirports" @search="onSearchOriginAirports" v-model="originAirport">
                    <template slot="no-options">
                        No se han encontrado aeropuertos
                    </template>
                    <template slot="option" slot-scope="option">
                        <div class="d-center">
                            <img src='/wp-content/plugins/Kiuws-Service-Flight-Management/assets/images/airplane_icon.png' width="30" style="margin-right: 5px;"/> 
                            {{ option.name }} - {{ option.city }}
                        </div>
                    </template>
                    <template slot="selected-option" slot-scope="option">
                        <img src='/wp-content/plugins/Kiuws-Service-Flight-Management/assets/images/airplane_icon.png' width="30" style="margin-right: 5px;"/>
                        <label>{{ option.name }}, <small>{{ option.city }}</small></label>                        
                    </template>
                </v-select>
            </div>
            <!-- Destination -->
            <div class="col-12 col-sm-12 col-md-6 col-lg-6 mb-2">
                <label for="destination" class="form-label">Destino: </label>
                <v-select name="origin" class="form-control" label="name" :filterable="false" :options="destinationAirports" @search="onSearchDestinationAirports" v-model="destinationAirport">
                    <template slot="no-options">
                        No se han encontrado aeropuertos
                    </template>
                    <template slot="option" slot-scope="option">
                        <div class="d-center">
                            <img src='/wp-content/plugins/Kiuws-Service-Flight-Management/assets/images/airplane_icon.png' width="30" style="margin-right: 5px;"/> 
                            {{ option.name }} - {{ option.city }}
                        </div>
                    </template>
                    <template slot="selected-option" slot-scope="option">
                        <img src='/wp-content/plugins/Kiuws-Service-Flight-Management/assets/images/airplane_icon.png' width="30" style="margin-right: 5px;"/>
                        <label>{{ option.name }}, <small>{{ option.city }}</small></label>                        
                    </template>
                </v-select>
            </div>
            <!-- Departure date -->
            <div class="col-12 col-sm-12 col-md-4 col-lg-4 mb-2">
                <label for="departure_date" class="form-label">Fecha de salida: </label>
                <input type="date" name="departure_date" placeholder="Fecha de salida" class="form-control" v-model="departureDate" />
            </div>
            <!-- Return date -->
            <div class="col-12 col-sm-12 col-md-4 col-lg-4 mb-2">
                <label for="return_date" class="form-label">Fecha de regreso: </label>
                <input type="date" name="return_date" placeholder="Fecha de salida" class="form-control" v-model="returnDate" />
            </div>
            <!-- Amount Adults and children -->
            <div class="col-12 col-sm-12 col-md-4 col-lg-4 mb-2">
                <div class="row">
                    <div class="col-12 col-sm-12 col-md-6 col-lg-6">
                        <label for="amount_adults" class="form-label">Adultos: </label>
                        <input type="number" name="amount_adults" placeholder="Adultos" class="form-control" v-model="adults" min="1" max="9" step="1" />
                    </div>
                    <div class="col-12 col-sm-12 col-md-6 col-lg-6">
                        <label for="amount_children" class="form-label">Niños: </label>
                        <input type="number" name="amount_children" placeholder="Niños" class="form-control" v-model="children" min="0" max="9" step="1"/>
                    </div>
                </div>
            </div>
            <div class="col-12" v-if="error">
                <div class="alert alert-danger">
                    <strong>-</strong> {{ errorMessage }}
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
            </div>
            <div class="col-12 col-md-12 text-center mt-5">
                <button type="button" class="btn btn-primary" @click="searhFlights" :disabled="loading">Buscar vuelos</button>
            </div>
        </div>
    </form>
    {{ message }}
</div>