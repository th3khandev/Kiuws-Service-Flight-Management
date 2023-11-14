<style>
    table input[type="text"], table input[type="password"], table textarea {
        width: 300px;
    }
</style>

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
        <h3>Kiuws Configuration:</h3>
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
        <hr />
        <h3>Email to:</h3>
        <table class="form-table">
            <tr>
                <th scope="row">Email to</th>
                <td>
                    <textarea type="text" name="<?php echo FLIGHT_MANAGEMENT_PREFIX . 'email_to' ?>" value="<?php echo esc_attr(get_option(FLIGHT_MANAGEMENT_PREFIX . 'email_to')); ?>" rows="5" ><?php echo esc_attr(get_option(FLIGHT_MANAGEMENT_PREFIX . 'email_to')); ?></textarea>
                    <br />
                    <label>Si desea colocar varios receptores, separar con coma (,). Ejemplo: test_1@email.com, test_2@email.com</label>
                </td>
            </tr>
        </table>
        <?php submit_button('Save Configuration'); ?>
    </form>
</div>