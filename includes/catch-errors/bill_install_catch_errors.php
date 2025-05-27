<?php
// namespace wp_memory_BillCatchErrors;
// created 25/02/25


if (!defined("ABSPATH")) {
    die("Invalid request.");
}
if (function_exists('is_multisite') and is_multisite()) {
    return;
}

/*
    //call it
    if (!function_exists('bill_install_mu_plugin')) {
                    require_once dirname(__FILE__) . "/includes/catch-errors/class_bill_install_catch_errors.php";
    }
    */

$plugin_file_path1 = ABSPATH . 'wp-admin/includes/plugin.php';
if (file_exists($plugin_file_path1)) {
    include_once($plugin_file_path1);
}
/*
    if (function_exists('is_plugin_active')){
        $bill_plugins_to_check = array(
            'wp_memory/wp_memory.php',  
        );
        foreach ($bill_plugins_to_check as $plugin_path) {
            if (is_plugin_active($plugin_path)) 
            return;
        }
    }
    */



$transient_name = 'bill_tried_to_install_mu_plugin';
$transient_check = get_transient($transient_name);



if ($transient_check !== false) {
    return false;
}
$transient_value = true; // Ou qualquer valor que você queira armazenar no transiente
$expiration = 60 * 60 * 24 * 1; // 1 dia em segundos
set_transient($transient_name, $transient_value, $expiration);

$bill_install_plugin_name_1 = 'bill-catch-errors.php';
$bill_install_plugin_name_1 = trim($bill_install_plugin_name_1); // Remove espaços extras


bill_check_install_mu_plugin();




function bill_check_install_mu_plugin()
{


    //global $bill_install_plugin_name_1;
    $bill_install_plugin_name_1 = 'bill-catch-errors.php';
    $bill_install_plugin_name_1 = trim($bill_install_plugin_name_1); // Remove espaços extras






    // Retrieve all must-use plugins
    $wp_mu_plugins = get_mu_plugins();
    // Check if the plugin exists in the list of mu-plugins


    if (isset($wp_mu_plugins[$bill_install_plugin_name_1])) {


        // Get the plugin's data
        $plugin_data = $wp_mu_plugins[$bill_install_plugin_name_1];
        $plugin_version = $plugin_data['Version'];


        // Check the version
        if (version_compare($plugin_version, '4.1', '>=')) {
            // A versão do plugin é 4.1 ou superior
            // nada a fazer, deixa rolar...
            return;
        }
    }
    if (bill_install_mu_plugin()) {
        return;
    } else {
        // error_log(var_export(__LINE__));
    }
}

function bill_install_mu_plugin()
{
    // global $bill_install_plugin_name_1;

    $bill_install_plugin_name_1 = 'bill-catch-errors.php';
    $bill_install_plugin_name_1 = trim($bill_install_plugin_name_1); // Remove espaços extras




    $install_mu_plugin_dir = WP_PLUGIN_DIR . '/stopbadbots/includes/mu-plugins'; // Current path inside wp_memory
    $mu_plugins_dir = WPMU_PLUGIN_DIR; // MU-Plugins directory
    $transient_name = 'bill_unable_to_create_mu_folder';
    $transient_check = get_transient($transient_name);
    if ($transient_check !== false) {
        return false;
    }
    try {
        // Check if the MU-Plugins directory exists
        if (!is_dir($mu_plugins_dir)) {
            // Try to create the directory with the appropriate permissions
            if (!mkdir($mu_plugins_dir, 0755, true)) {
                $transient_name = 'bill_unable_to_create_mu_folder';
                $transient_value = true; // Ou qualquer valor que você queira armazenar no transiente
                $expiration = 60 * 60 * 24 * 30; // 1 mês em segundos
                set_transient($transient_name, $transient_value, $expiration);
                error_log("Unable to create the MU-Plugins directory: " . $mu_plugins_dir);
                return false;
            }
        }
        // Check if the MU-Plugins directory is readable and writable




        if (!is_readable($mu_plugins_dir) || !is_writable($mu_plugins_dir)) {
            // Tenta corrigir as permissões para 0755


            if (!@chmod($mu_plugins_dir, 0755)) {
                //  error_log("Failed to set permissions on MU-Plugins directory: " . $mu_plugins_dir);


                $transient_name = 'bill_unable_to_create_mu_folder';
                $transient_value = true; // Ou qualquer valor que você queira armazenar no transiente
                $expiration = 60 * 60 * 24 * 30; // 1 mês em segundos
                set_transient($transient_name, $transient_value, $expiration);


                return false;
            } else {
                // Verifica novamente após tentar corrigir
                if (!is_readable($mu_plugins_dir) || !is_writable($mu_plugins_dir)) {
                    $transient_name = 'bill_unable_to_create_mu_folder';
                    $transient_value = true; // Ou qualquer valor que você queira armazenar no transiente
                    $expiration = 60 * 60 * 24 * 30; // 1 mês em segundos
                    set_transient($transient_name, $transient_value, $expiration);
                    // error_log("The MU-Plugins directory does not have the appropriate permissions after chmod attempt: " . $mu_plugins_dir);
                }
            }
        }


        // Define the plugin file path in the wp_memory directory
        $source = $install_mu_plugin_dir . '/' . $bill_install_plugin_name_1;
        $destination = $mu_plugins_dir . '/' . $bill_install_plugin_name_1;
        //debug4($source);
        //debug4($destination);
        // Check if the plugin file exists in the source directory
        if (!file_exists($source)) {
            error_log("The plugin file was not found in the source directory: " . $source);
            $transient_name = 'bill_unable_to_create_mu_folder';
            $transient_value = true; // Ou qualquer valor que você queira armazenar no transiente
            $expiration = 60 * 60 * 24 * 30; // 1 mês em segundos
            set_transient($transient_name, $transient_value, $expiration);

            return false;
        }

        if (is_dir($source)) {
            $transient_name = 'bill_unable_to_create_mu_folder';
            $transient_value = true; // Ou qualquer valor que você queira armazenar no transiente
            $expiration = 60 * 60 * 24 * 30; // 1 mês em segundos
            set_transient($transient_name, $transient_value, $expiration);
            return false;
        }
        if (!file_exists($source)) {
            $transient_name = 'bill_unable_to_create_mu_folder';
            $transient_value = true; // Ou qualquer valor que você queira armazenar no transiente
            $expiration = 60 * 60 * 24 * 30; // 1 mês em segundos
            set_transient($transient_name, $transient_value, $expiration);
            return false;
        }

        // Copy the plugin file to the MU-Plugins directory

        if (!@copy($source, $destination)) {
            // error_log("Unable to copy the plugin file to the MU-Plugins directory: " . $destination);

            $transient_name = 'bill_unable_to_create_mu_folder';
            $transient_value = true; // Ou qualquer valor que você queira armazenar no transiente
            $expiration = 60 * 60 * 24 * 30; // 1 mês em segundos
            set_transient($transient_name, $transient_value, $expiration);

            return false;
        }
        // Success
        return true;
    } catch (Exception $e) {
        // Log the error
        error_log("Error copying the plugin file to the MU-Plugins directory: " . $e->getMessage());
        $transient_name = 'bill_unable_to_create_mu_folder';
        $transient_value = true; // Ou qualquer valor que você queira armazenar no transiente
        $expiration = 60 * 60 * 24 * 30; // 1 mês em segundos
        set_transient($transient_name, $transient_value, $expiration);
        return false;
    }
}
