<div class="wrap">
    <h2>Stripe Configuration</h2>
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
    <form method="post" action="">
        <table class="form-table">
            <tr>
                <th scope="row">Mode</th>
                <td>
                    <select name="<?php echo FLIGHT_MANAGEMENT_PREFIX . 'stripe_mode' ?>">
                        <option value="testing" <?php selected(get_option(FLIGHT_MANAGEMENT_PREFIX . 'stripe_mode'), 'testing'); ?>>Testing</option>
                        <option value="production" <?php selected(get_option(FLIGHT_MANAGEMENT_PREFIX . 'stripe_mode'), 'production'); ?>>Production</option>
                    </select>
                </td>
            </tr>
        </table>
        <hr />
        <h2>Testing keys</h2>
        <table class="form-table">
            <tr>
                <th scope="row">Public</th>
                <td><input type="text" name="<?php echo FLIGHT_MANAGEMENT_PREFIX . 'stripe_testing_public_key' ?>" value="<?php echo esc_attr(get_option(FLIGHT_MANAGEMENT_PREFIX . 'stripe_testing_public_key')); ?>" /></td>
            </tr>
            <tr>
                <th scope="row">Private</th>
                <td><input type="text" name="<?php echo FLIGHT_MANAGEMENT_PREFIX . 'stripe_testing_private_key' ?>" value="<?php echo esc_attr(get_option(FLIGHT_MANAGEMENT_PREFIX . 'stripe_testing_private_key')); ?>" /></td>
            </tr>
        </table>

        <hr/>
        <h2>Production keys</h2>
        <table class="form-table">
            <tr>
                <th scope="row">Public</th>
                <td><input type="text" name="<?php echo FLIGHT_MANAGEMENT_PREFIX . 'stripe_production_public_key' ?>" value="<?php echo esc_attr(get_option(FLIGHT_MANAGEMENT_PREFIX . 'stripe_production_public_key')); ?>" /></td>
            </tr>
            <tr>
                <th scope="row">Private</th>
                <td><input type="text" name="<?php echo FLIGHT_MANAGEMENT_PREFIX . 'stripe_production_private_key' ?>" value="<?php echo esc_attr(get_option(FLIGHT_MANAGEMENT_PREFIX . 'stripe_production_private_key')); ?>" /></td>
            </tr>
        </table>
        <?php submit_button('Save Configuration'); ?>
    </form>
</div>