<?php

use Kiuws_Service_Flight_Management\DB\FlightManagementModel;
use Kiuws_Service_Flight_Management\DB\FlightPassengerModel;

$status_list = FlightManagementModel::getStatusList();

$passenger_types = FlightPassengerModel::get_types();

?>

<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css" integrity="sha384-xOolHFLEh07PJGoPkLv1IbcEPTNtaed2xpHsD9ESMhqIYd0nLMwNLD69Npy4HI+N" crossorigin="anonymous">
<style>
    .line {
        position: relative;
        display: block;
        width: 90%;
        height: 0.125rem;
        margin: 0.25rem auto;
        padding: 0;
        border-radius: 0.5rem;
        background-color: #545860;
        line-height: 0;
        text-align: center;
        margin-top: 15%;
    }

    .line svg {
        position: absolute;
        top: 50%;
        right: -0.25rem;
        display: block;
        width: 1rem;
        height: 1rem;
        margin-top: -0.5rem;
        padding-left: 0.25rem;
        background-color: #fff;
    }
</style>

<div class="wrap">
    <div class="row">
        <div class="col-12">
            <h2>Detalle de la reservación: <?php echo $reservation->booking_id; ?> </h2>
        </div>
    </div>

    <!-- Mensajes de éxito o error -->
    <?php settings_errors('flight-management-messages'); ?>
    <div class="postbox">
        <div class="inside">
            <div class="row">
                <div class="col-12 col-md-4">
                    <h6 class="card-title"><?php echo $reservation->origin; ?></h6>
                </div>
                <div class="col-12 col-md-2">
                    <div class="line"><svg xmlns="http://www.w3.org/2000/svg" xml:space="preserve" viewBox="0 0 12 12" class="LegInfo_planeEnd__ZDkxM">
                            <path fill="#898294" d="M3.922 12h.499a.52.52 0 0 0 .444-.247L7.949 6.8l3.233-.019A.8.8 0 0 0 12 6a.8.8 0 0 0-.818-.781L7.949 5.2 4.866.246A.525.525 0 0 0 4.421 0h-.499a.523.523 0 0 0-.489.71L5.149 5.2H2.296l-.664-1.33a.523.523 0 0 0-.436-.288L0 3.509 1.097 6 0 8.491l1.196-.073a.523.523 0 0 0 .436-.288l.664-1.33h2.853l-1.716 4.49a.523.523 0 0 0 .489.71"></path>
                        </svg></div>
                </div>
                <div class="col-12 col-md-4">
                    <h6 class="card-title"><?php echo $reservation->destination; ?></h6>
                </div>
            </div>
            <div class="row">
                <div class="col-12 col-md-3 mt-2 mb-1">
                    <label for="booking_id">ID de la reservación</label>
                    <input type="text" name="booking_id" id="booking_id" class="w-100" value="<?php echo $reservation->booking_id; ?>" readonly>
                </div>
                <div class="col-12 col-md-3 mt-2 mb-1">
                    <label for="departure_date_time">Fecha de salida</label>
                    <input type="text" name="departure_date_time" id="departure_date_time" class="w-100" value="<?php echo $reservation->departure_date_time; ?>" readonly>
                </div>
                <div class="col-12 col-md-3 mt-2 mb-1">
                    <label for="arrival_date_time">Fecha de llegada</label>
                    <input type="text" name="arrival_date_time" id="arrival_date_time" class="w-100" value="<?php echo $reservation->arrival_date_time; ?>" readonly>
                </div>
                <div class="col-12 col-md-3 mt-2 mb-1">
                    <label for="duration">Duración</label>
                    <input type="text" name="duration" id="duration" class="w-100" value="<?php echo $reservation->duration; ?>" readonly>
                </div>
                <div class="col-12 col-md-3 mt-2 mb-1">
                    <label for="adults">Adultos</label>
                    <input type="text" name="adults" id="adults" class="w-100" value="<?php echo $reservation->adults; ?>" readonly>
                </div>
                <div class="col-12 col-md-3 mt-2 mb-1">
                    <label for="children">Niños</label>
                    <input type="text" name="children" id="children" class="w-100" value="<?php echo $reservation->children; ?>" readonly>
                </div>
                <div class="col-12 col-md-3 mt-2 mb-1">
                    <label for="children">Infante</label>
                    <input type="text" name="children" id="children" class="w-100" value="<?php echo $reservation->inf; ?>" readonly>
                </div>
                <div class="col-12 col-md-3 mt-2 mb-1">
                    <label for="stops">Escalas</label>
                    <input type="text" name="stops" id="stops" class="w-100" value="<?php echo $reservation->stops; ?>" readonly>
                </div>
                <div class="col-12 col-md-3 mt-2 mb-1">
                    <label for="total">Total</label>
                    <input type="text" name="total" id="total" class="w-100" value="<?php echo $reservation->total . ' ' . $reservation->currency_code; ?>" readonly>
                </div>
                <div class="col-12 col-md-3 mt-2 mb-1">
                    <label for="status">Status</label>
                    <input type="text" name="status" id="status" class="w-100" value="<?php echo $status_list[$reservation->status]; ?>" readonly>
                </div>
            </div>
        </div>
    </div>

    <!-- Deatil segments -->
    <div class="row">
        <div class="col-12">
            <h5>Detalle del vuelo</h5>
        </div>
    </div>
    <div class="postbox">
        <div class="inside">
            <?php
            $segments = $reservation->getSegments();
            foreach ($segments as $key => $segment) {
                $number = $key + 1;
            ?>
                <div class="row">
                    <div class="col-12">
                        <h6>
                            <?php echo $number . '. ' . $segment->airline_name . '(' . $segment->airline_code . ') - Vuelo #' . $segment->flight_number; ?>
                        </h6>
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col-12 col-md-3 mt-2 mb-1">
                        <label for="departure_date_time">Hora de salida</label>
                        <input type="text" name="airline" id="departure_date_time" class="w-100" value="<?php echo $segment->departure_date_time; ?>" readonly>
                    </div>
                    <div class="col-12 col-md-3 mt-2 mb-1">
                        <label for="arrival_date_time">Hora de llegada</label>
                        <input type="text" name="arrival_date_time" id="arrival_date_time" class="w-100" value="<?php echo $segment->arrival_date_time; ?>" readonly>
                    </div>
                    <div class="col-12 col-md-3 mt-2 mb-1">
                        <label for="duration">Duración</label>
                        <input type="text" name="duration" id="duration" class="w-100" value="<?php echo $segment->duration; ?>" readonly>
                    </div>
                    <div class="col-12 col-md-3 mt-2 mb-1">
                        <label for="res_book_desig">Clase</label>
                        <input type="text" name="res_book_desig" id="res_book_desig" class="w-100" value="<?php echo $segment->res_book_desig; ?>" readonly>
                    </div>
                </div>
            <?php
            }
            ?>

        </div>
    </div>

    <div class="postbox">
        <div class="inside">
            <?php
            $passengers = $reservation->getPassengers();
            foreach ($passengers as $key => $passenger) {
                $number = $key + 1;
            ?>
                <div class="row">
                    <div class="col-12">
                        <h6>
                            <?php echo 'Pasajero #' . $number . '(' . $passenger_types[$passenger->type] . ')'; ?>
                        </h6>
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col-12 col-md-3 mt-2 mb-1">
                        <label for="name">Nombre</label>
                        <input type="text" name="name" id="name" class="w-100" value="<?php echo $passenger->name; ?>" readonly>
                    </div>
                    <div class="col-12 col-md-3 mt-2 mb-1">
                        <label for="last_name">Apellido</label>
                        <input type="text" name="last_name" id="last_name" class="w-100" value="<?php echo $passenger->last_name; ?>" readonly>
                    </div>
                    <div class="col-12 col-md-3 mt-2 mb-1">
                        <label for="document">Documento de identidad</label>
                        <input type="text" name="document" id="document" class="w-100" value="<?php echo $passenger->document_type . ' ' . $passenger->document_number; ?>" readonly>
                    </div>
                    <div class="col-12 col-md-3 mt-2 mb-1">
                        <label for="email">Email</label>
                        <input type="text" name="email" id="email" class="w-100" value="<?php echo $passenger->email; ?>" readonly>
                    </div>
                    <div class="col-12 col-md-3 mt-2 mb-1">
                        <label for="phone">Teléfono</label>
                        <input type="text" name="phone" id="phone" class="w-100" value="<?php echo $passenger->phone_country_code . ' ' . $passenger->phone_number; ?>" readonly>
                    </div>
                    <div class="col-12 col-md-3 mt-2 mb-1">
                        <label for="gender">Sexo</label>
                        <input type="text" name="gender" id="gender" class="w-100" value="<?php echo $passenger->gender == 'F' ? 'Femenino' : 'Masculino'; ?>" readonly>
                    </div>
                    <div class="col-12 col-md-3 mt-2 mb-1">
                        <label for="birth_date">Fecha de nacimiento</label>
                        <input type="text" name="birth_date" id="birth_date" class="w-100" value="<?php echo $passenger->birth_date; ?>" readonly>
                    </div>
                </div>
            <?php
            }
            ?>
        </div>
    </div>

    <?php
    $payment = $reservation->getPayment();
    if (!is_null($payment)) {
    ?>
        <div class="postbox">
            <div class="inside">
                <div class="row">
                    <div class="col-12">
                        <h6>
                            Información de pago
                        </h6>
                    </div>
                    <div class="col-12 col-md-3">
                        <label for="date">Fecha de pago</label>
                        <input type="text" name="date" id="date" class="w-100" value="<?php echo $payment->date; ?>" readonly>
                    </div>
                    <div class="col-12 col-md-3">
                        <label for="reference">Referencia</label>
                        <input type="text" name="payment_id" id="payment_id" class="w-100" value="<?php echo $payment->reference; ?>" readonly>
                    </div>
                    <div class="col-12 col-md-3">
                        <label for="card_number">Número de tarjeta (útimos 4 digitos)</label>
                        <input type="text" name="card_number" id="card_number" class="w-100" value="<?php echo $payment->card_number; ?>" readonly>
                    </div>
                    <div class="col-12 col-md-3">
                        <label for="card_holder_name">Titular de la tarjeta</label>
                        <input type="text" name="card_holder_name" id="card_holder_name" class="w-100" value="<?php echo $payment->card_holder_name; ?>" readonly>
                    </div>
                    <div class="col-12 col-md-3">
                        <label for="card_holder_document_number">Número de identificacion del titular</label>
                        <input type="text" name="card_holder_document_number" id="card_holder_document_number" class="w-100" value="<?php echo $payment->card_holder_document_number; ?>" readonly>
                    </div>
                    <div class="col-12 col-md-3">
                        <label for="type">Tipo de pago</label>
                        <input type="text" name="type" id="type" class="w-100" value="<?php echo $payment->type; ?>" readonly>
                    </div>
                </div>
            </div>
        </div>
    <?php
    }
    ?>

    <div class="postbox" style="display: none;" id="section-form-payment">
        <form method="post" action="" id="form-add-payment">
            <div class="inside">
                <div class="row">
                    <div class="col-12">
                        <h6>
                            Ingrese la información del pago
                        </h6>
                    </div>
                    <div class="col-12 col-md-3">
                        <label for="reference">Referencia</label>
                        <input type="text" name="reference" class="w-100" value="" require="required" >
                    </div>
                    <div class="col-12 col-md-3">
                        <label for="type">Tipo de pago</label>
                        <input type="text" name="type" class="w-100" value="" require="required" >
                    </div>
                    <div class="col-12 col-md-3">
                        <label for="type">Fecha</label>
                        <input type="date" name="date" class="w-100" value="" require="required"  >
                    </div>
                    <div class="col-12 col-md-3">
                        <input type="hidden" value="add_payment" name="action" />
                        <input type="hidden" name="booking_id" value="<?php echo $reservation->booking_id; ?>" />
                        <button class="btn btn-success mt-3" type="submit">
                            Guardar
                        </button>
                    </div>
                </div>
            </div>
        </form>
    </div>

    <div class="row mt-3">
        <div class="col-12 text-right">
            <a href="admin.php?page=flight-management">
                <button class="btn btn-secondary">
                    Regresar
                </button>
            </a>
            <?php
            if ($reservation->status == FlightManagementModel::STATUS_PAID) {
            ?>
                <button class="btn btn-success" onclick="confirmFlight()">
                    Confirmado
                </button>
            <?php
            }
            ?>
            <?php
            if ($reservation->status == FlightManagementModel::STATUS_BOOKED) {
            ?>
                <button class="btn btn-info" onclick="addPayment()">
                    Agregar Pago
                </button>
            <?php
            }
            ?>
        </div>
    </div>
</div>
</div>


<script>
    function addPayment () {
        const sectionFormPayment = document.getElementById('section-form-payment');
        sectionFormPayment.style.display = 'block';
    }

    function confirmFlight() {
        const bookingId = `<?php echo $reservation->booking_id; ?>`;
        // show confirm 
        const confirm = window.confirm('¿Estás seguro de que deseas marcar esta reservación como completada?');
        if (confirm) {
            // create form 
            const form = document.createElement('form');
            // add display none
            form.style.display = 'none';
            form.setAttribute('method', 'post');
            form.setAttribute('action', '');
            // create input
            const input = document.createElement('input');
            input.setAttribute('type', 'hidden');
            input.setAttribute('name', 'booking_id');
            input.setAttribute('value', bookingId);
            // append input to form
            form.appendChild(input);
            // create input
            const inputAction = document.createElement('input');
            inputAction.setAttribute('type', 'hidden');
            inputAction.setAttribute('name', 'action');
            inputAction.setAttribute('value', 'complete_reservation');
            // append input to form
            form.appendChild(inputAction);
            // append form to body
            document.body.appendChild(form);
            // submit form
            form.submit();
        }
    }
</script>