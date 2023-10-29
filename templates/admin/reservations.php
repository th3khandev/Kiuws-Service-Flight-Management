<?php

use Kiuws_Service_Flight_Management\Includes\ReservationTable;

$reservationTable = new ReservationTable();
$reservationTable->prepare_items();

?>

<style>
    .row-actions {
        left: auto;
    }

    .status {
        padding: 5px;
        border-radius: 8px;
        white-space: nowrap;
        font-weight: 700;
    }

    .status .status-pending {
        background-color: #c9c9c9;
    }

    .status .status-completed {
        background-color: #1eff0b;
    }

    .status .status-cancelled {
        background-color: #f60404;
        color: #ffffff;
    }

    .status .status-booked {
        background-color: #0001fb;
        color: #ffffff;
    }

    .status .status-payed {
        background-color: #53ffb2;
        color: #ffffff;
    }

    /* Estilo para el modal de confirmación */
    .modal {
        display: none;
        /* Inicialmente oculto */
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background-color: rgba(0, 0, 0, 0.7);
        /* Fondo oscuro */
    }

    .modal-content {
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        background-color: #fff;
        padding: 20px;
        border-radius: 5px;
        box-shadow: 0 0 10px rgba(0, 0, 0, 0.5);
        text-align: center;
    }

    /* Botón de cerrar (X) en la esquina superior derecha del modal */
    .close {
        position: absolute;
        top: 10px;
        right: 10px;
        font-size: 20px;
        cursor: pointer;
    }

    /* Estilo para el botón de confirmación dentro del modal */
    .button-primary {
        background-color: #0073e6;
        color: #fff;
        border: none;
        padding: 10px 20px;
        border-radius: 5px;
        cursor: pointer;
    }

    /* Estilo para el botón de cancelación */
    .cancel-button {
        background-color: #f44336 !important;
        color: #ffffff !important;
    }

    /* Estilo para el botón de confirmación */
    .confirm-button {
        background-color: #4CAF50;
    }
</style>

<div class="wrap">
    <h2>Listado de reservaciones</h2>
    
    <!-- Mensajes de éxito o error -->
    <?php settings_errors('flight-management-messages'); ?>


    <?php $reservationTable->display(); ?>
</div>

<form id="cancel-reservation-form" method="post" action="">
    <input type="hidden" name="action" value="cancel_reservation">
    <input type="hidden" name="booking_id" value="">
</form>

<div id="cancel-confirmation-modal" class="modal">
    <div class="modal-content">
        <span class="close">&times;</span>
        <h2>¿Estás seguro de que deseas cancelar esta reservación?</h2>
        <button id="confirm-cancel" class="button-primary">Confirmar Cancelación</button>
        <button id="button-cancel" class="button cancel-button">Cerrar</button>
    </div>
</div>

<script>

    // add event click all a tag with class cancel-booking
    const cancelBookingButtons = document.querySelectorAll('.cancel-booking');
    cancelBookingButtons.forEach(function(button) {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            const bookingId = button.getAttribute('data-id');
            showConfirmationModal(bookingId);
        });
    });

    // Mostrar el modal de confirmación
    function showConfirmationModal(bookingId) {
        const modal = document.getElementById('cancel-confirmation-modal');
        modal.style.display = 'block';

        // Escuchar el clic en el botón de confirmación
        const confirmButton = document.getElementById('confirm-cancel');
        confirmButton.addEventListener('click', function() {
            const form = document.getElementById('cancel-reservation-form');
            const input = form.querySelector('input[name="booking_id"]');
            input.value = bookingId;
            form.submit();
        });

        // Escuchar el clic en el botón "X" (cerrar)
        const closeButton = modal.querySelector('.close');
        closeButton.addEventListener('click', function() {
            hideConfirmationModal();
        });

        // Escuchar el clic en el botón "Cerrar"
        const cancelButton = document.getElementById('button-cancel');
        cancelButton.addEventListener('click', function() {
            hideConfirmationModal();
        });
    }

    // Ocultar el modal de confirmación
    function hideConfirmationModal() {
        const modal = document.getElementById('cancel-confirmation-modal');
        modal.style.display = 'none';
    }

    // add event click to all a tag with class confirm-booking
    const confirmBookingButtons = document.querySelectorAll('.confirm-booking');
    confirmBookingButtons.forEach(function(button) {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            const bookingId = button.getAttribute('data-id');
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
        });
    });
</script>