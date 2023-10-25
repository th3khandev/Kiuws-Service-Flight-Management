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
</style>

<div class="wrap">
    <h2>Listado de reservaciones</h2>
    <?php $reservationTable->display(); ?>
</div>