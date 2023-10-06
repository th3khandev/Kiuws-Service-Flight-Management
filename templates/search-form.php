<div id="flight-search-form">
    <form>
        <div class="row">
            <!-- Add fields -->
            <!-- Origin -->
            <div class="col-12 col-sm-12 col-md-3 col-lg-3">
                <label for="origin" class="form-label">Desde: </label>
                <v-select name="origin" class="form-control" :options="[]"></v-select>
            </div>
            <!-- Destination -->
            <div class="col-12 col-sm-12 col-md-3 col-lg-3">
                <label for="destination" class="form-label">Destino: </label>
                <select name="destination" placeholder="Destino" class="form-control"></select>
            </div>
            <!-- Departure date -->
            <div class="col-12 col-sm-12 col-md-3 col-lg-3">
                <label for="departure_date" class="form-label">Fecha de salida: </label>
                <input type="date" name="departure_date" placeholder="Fecha de salida" class="form-control" />
            </div>
            <!-- Amount Adults and children -->
            <div class="col-12 col-sm-12 col-md-3 col-lg-3">
                <div class="row">
                    <div class="col-12 col-sm-12 col-md-6 col-lg-6">
                        <label for="amount_adults" class="form-label">Adultos: </label>
                        <input type="number" name="amount_adults" placeholder="Adultos" class="form-control" value="1" />
                    </div>
                    <div class="col-12 col-sm-12 col-md-6 col-lg-6">
                        <label for="amount_children" class="form-label">Niños: </label>
                        <input type="number" name="amount_children" placeholder="Niños" class="form-control" value="0" />
                    </div>
                </div>
            </div>
            <div class="col-12 col-md-12 text-center mt-5">
                <button type="submit" class="btn btn-primary">Buscar vuelos</button>
            </div>
        </div>
    </form>
    {{ message }}
</div>