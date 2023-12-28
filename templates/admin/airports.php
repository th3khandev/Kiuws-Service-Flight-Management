<?php 
use Kiuws_Service_Flight_Management\Includes\AirportsTable;

$airportsTable = new AirportsTable();
$airportsTable->prepare_items();

?>

<div class="wrap">
    <h1 class="wp-heading-inline">Listado de areopuertos disponibles</h1>
    <a href="admin.php?page=flight-management-airports&action=create" class="page-title-action">Añadir nuevo</a>
    <hr class="wp-header-end" />

    <!-- Show error o success messages -->
    <?php
    if (!empty($error_messages)) {
        // Show error messages
        foreach ($error_messages as $error_message) {
            echo '<div class="error"><p>' . esc_html($error_message) . '</p></div>';
        }
    } else {
        // Show success messages
        settings_errors('flight-management-messages');
    }
    ?>

    <?php $airportsTable->display(); ?>
    
</div>