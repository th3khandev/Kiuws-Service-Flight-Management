<div class="wrap">
    <h1 class="wp-heading-inline">
        <?php
        if ($action == 'create') {
            echo 'Crear nuevo aeropuerto';
        } else {
            echo 'Editar aeropuerto';
        }
        ?>
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

    <form method="post" action="admin.php?page=flight-management-airports">
        <table class="form-table">
            <tr>
                <th scope="row">Código</th>
                <td><input type="text" name="code" value="<?php echo $code; ?>" required="required" /></td>
            </tr>
            <tr>
                <th scope="row">Nombre</th>
                <td><input type="text" name="name" value="<?php echo $name; ?>" required="required" /></td>
            </tr>
            <tr>
                <th scope="row">Ciudad</th>
                <td><input type="text" name="city_name" value="<?php echo $city; ?>" required="required" /></td>
            </tr>
            <tr>
                <th scope="row">Estado/Región</th>
                <td><input type="text" name="state_name" value="<?php echo $state; ?>" required="required" /></td>
            </tr>
            <tr>
                <th scope="row">País</th>
                <td><input type="text" name="country_name" value="<?php echo $country; ?>" required="required" /></td>
            </tr>
        </table>

        <input type="hidden" name="action" value="<?php echo $action; ?>" />
        <input type="hidden" name="airport_id" value="<?php echo $airport_id; ?>" />
        <?php submit_button('Save'); ?>
    </form>


</div>