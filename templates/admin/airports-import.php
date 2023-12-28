<div class="wrap">
    <h1 class="wp-heading-inline">
        Importar data de areopuertos
    </h1>
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

    <form method="post" action="admin.php?page=flight-management-airports" enctype="multipart/form-data">
        <table class="form-table">
            <tr>
                <th scope="row">Archivo</th>
                <td><input type="file" name="file_excel" value="" required="required" accept="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet" /></td>
            </tr>
        </table>
        <input type="hidden" name="action" value="<?php echo $action; ?>" />
        <div>
            <h3>Importante:</h3>
            <div>
                El archivo debe tener el siguiente formato <a href="<?php echo FLIGHT_MANAGEMENT_URL . 'Import Airport Data.xlsx'; ?>" target="_blank">airports.xlsx</a>
            </div>
        </div>
        <?php submit_button('Import'); ?>
    </form>
</div>