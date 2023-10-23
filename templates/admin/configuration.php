<div class="wrap">
    <h2>Flight Management Configuration</h2>
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
                <th scope="row">User</th>
                <td><input type="text" name="<?php echo FLIGHT_MANAGEMENT_PREFIX . 'user' ?>" value="<?php echo esc_attr(get_option(FLIGHT_MANAGEMENT_PREFIX . 'user')); ?>" /></td>
            </tr>
            <tr>
                <th scope="row">Password</th>
                <td><input type="password" name="<?php echo FLIGHT_MANAGEMENT_PREFIX . 'password' ?>" value="<?php echo esc_attr(get_option(FLIGHT_MANAGEMENT_PREFIX . 'password')); ?>" /></td>
            </tr>
            <tr>
                <th scope="row">Mode</th>
                <td>
                    <select name="<?php echo FLIGHT_MANAGEMENT_PREFIX . 'mode' ?>">
                        <option value="testing" <?php selected(get_option(FLIGHT_MANAGEMENT_PREFIX . 'mode'), 'testing'); ?>>Testing</option>
                        <option value="production" <?php selected(get_option(FLIGHT_MANAGEMENT_PREFIX . 'mode'), 'production'); ?>>Production</option>
                    </select>
                </td>
            </tr>
            <tr>
                <th scope="row">AgentSine</th>
                <td><input type="text" name="<?php echo FLIGHT_MANAGEMENT_PREFIX . 'agent_sine' ?>" value="<?php echo esc_attr(get_option(FLIGHT_MANAGEMENT_PREFIX . 'agent_sine')); ?>" /></td>
            </tr>
            <tr>
                <th scope="row">Terminal ID</th>
                <td><input type="text" name="<?php echo FLIGHT_MANAGEMENT_PREFIX . 'terminal_id' ?>" value="<?php echo esc_attr(get_option(FLIGHT_MANAGEMENT_PREFIX . 'terminal_id')); ?>" /></td>
            </tr>
            <tr>
                <th scope="row">Base URL</th>
                <td><input type="text" name="<?php echo FLIGHT_MANAGEMENT_PREFIX . 'base_url' ?>" value="<?php echo esc_attr(get_option(FLIGHT_MANAGEMENT_PREFIX . 'base_url')); ?>" /></td>
            </tr>
        </table>
        <?php submit_button('Save Configuration'); ?>
    </form>
</div>