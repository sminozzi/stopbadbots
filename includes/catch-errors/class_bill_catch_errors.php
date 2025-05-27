<?php

namespace stopbadbots_BillCatchErrors;
// created 06/23/23
// upd: 2023-10-16 -  2025-02-27

if (!defined("ABSPATH")) {
    die("Invalid request.");
}
if (function_exists('is_multisite') and is_multisite()) {
    return;
}

//die(var_dump(__LINE__));

/*
call it
function wpmemory_bill_hooking_catch_errors()
{
	global $wpmemory_is_admin;
	global $wpmemory_plugin_slug;

		require_once dirname(__FILE__) . "/includes/catch-errors/bill_install_catch_errors.php";
		$declared_classes = get_declared_classes();
		foreach ($declared_classes as $class_name) {
			if (strpos($class_name, "bill_catch_errors") !== false) {
				return;
			}
		}
		$wpmemory_plugin_slug = 'wp-memory';
		require_once dirname(__FILE__) . "/includes/catch-errors/class_bill_catch_errors.php";
}
add_action("init", "wpmemory_bill_hooking_catch_errors", 15);
*/






if (file_exists(WPMU_PLUGIN_DIR . '/bill-catch-errors.php')) {
    return;
}




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


// debug4();



add_action("wp_ajax_bill_minozzi_js_error_catched", "stopbadbots_BillCatchErrors\\bill_minozzi_js_error_catched");
add_action("wp_ajax_nopriv_bill_minozzi_js_error_catched", "stopbadbots_BillCatchErrors\\bill_minozzi_js_error_catched");


//die(var_dump(__LINE__));


function bill_minozzi_js_error_catched()
{
    // global $wp_memory_plugin_slug;

    if (!isset($_REQUEST) || !isset($_REQUEST["bill_js_error_catched"])) {
        die("empty error");
    }
    if (!wp_verify_nonce(sanitize_text_field($_POST["_wpnonce"]), "bill-catch-js-errors")) {
        status_header(406, "Invalid nonce");
        die();
    }

    $bill_js_error_catched = sanitize_text_field($_REQUEST["bill_js_error_catched"]);
    $bill_js_error_catched = trim($bill_js_error_catched);
    if (empty($bill_js_error_catched)) {
        die("empty error");
    }

    // Split the error message
    $errors = explode(" | ", $bill_js_error_catched);

    // Configuração do arquivo de log (fora do loop)
    $logFile = ini_get("error_log");
    if (!empty($logFile)) {
        $logFile = trim($logFile);
    }
    if (empty($logFile)) {
        if (defined('WP_DEBUG') && WP_DEBUG) {
            $logFile = trailingslashit(WP_CONTENT_DIR) . 'debug.log';
        } else {
            $logFile = trailingslashit(ABSPATH) . 'error_log';
        }
    }

    $dir = dirname($logFile);
    if (!file_exists($dir)) {
        if (!mkdir($dir, 0755, true)) {
            wp_die("Folder doesn't exist and unable to create: " . $dir);
        }
    }
    if (!is_writable($dir) || !is_readable($dir)) {
        if (!chmod($dir, 0755)) {
            wp_die("Log file directory does not have adequate permissions: " . $dir);
        }
        if (!is_writable($dir) || !is_readable($dir)) {
            wp_die("Log file directory does not have adequate permissions (2): " . $dir);
        }
    }

    // Loop para gravar os erros
    foreach ($errors as $error) {
        $parts = explode(" - ", $error);
        if (count($parts) < 3) {
            continue;
        }
        $errorMessage = $parts[0];
        $errorURL = $parts[1];
        $errorLine = $parts[2];
        $logMessage = "Javascript " . $errorMessage . " - " . $errorURL . " - " . $errorLine;

        /*
        $date_format = get_option('date_format', '');
        if (!empty($date_format)) {
            $formattedMessage = "[" . date_i18n($date_format) . ' ' . date('H:i:s') . "] - " . $logMessage;
        } else {
            $formattedMessage = "[" . date('M-d-Y H:i:s') . "] - " . $logMessage;
        }
        */
        //$default_format = 'Y-m-d H:i:s';
        $formattedMessage = "[" . date('Y-m-d H:i:s') . "] - " . $logMessage;

        $formattedMessage .= PHP_EOL;

        if (error_log($formattedMessage, 3, $logFile)) {
            $ret_error_log = true;
        } else {
            $r = file_put_contents($logFile, $formattedMessage, FILE_APPEND | LOCK_EX);
            if (!$r) {
                $timestamp_string = strval(time());
                update_option('bill_minozzi_error_log_status', $timestamp_string);
            }
        }
    }

    die("OK!");
}
class stopbadbots_bill_catch_errors
{
    public function __construct()
    {
        add_action("wp_head", [$this, "add_bill_javascript_to_header"]);
        add_action("admin_head", [$this, "add_bill_javascript_to_header"]);
    }
    public function add_bill_javascript_to_header()
    {
        $nonce = wp_create_nonce("bill-catch-js-errors");
        $ajax_url = esc_js($this->get_ajax_url()) . "?action=bill_minozzi_js_error_catched&_wpnonce=" . $nonce;
?>
        <script>
            // console.log("Linha 192");
            // alert();

            var errorQueue = [];
            let bill_timeout;
            var errorMessage = '';

            function isBot() {
                const bots = ['crawler', 'spider', 'baidu', 'duckduckgo', 'bot', 'googlebot', 'bingbot', 'facebook', 'slurp', 'twitter', 'yahoo'];
                const userAgent = navigator.userAgent.toLowerCase();
                return bots.some(bot => userAgent.includes(bot));
            }
            /*
            window.onerror = function(msg, url, line) {
                var errorMessage = [
                    'Message: ' + msg,
                    'URL: ' + url,
                    'Line: ' + line
                ].join(' - ');
                // Filter bots errors...
                if (isBot()) {
                    return;
                }
                //console.log(errorMessage);
                errorQueue.push(errorMessage);
                if (errorQueue.length >= 5) {
                    sendErrorsToServer();
                } else {
                    clearTimeout(bill_timeout);
                    bill_timeout = setTimeout(sendErrorsToServer, 5000);
                }
            }
                */


            // Captura erros síncronos e alguns assíncronos




            window.addEventListener('error', function(event) {

                // errorMessage = '';

                var msg = event.message;
                if (msg === "Script error.") {
                    console.error("Script error detected - maybe problem cross-origin");
                    return;
                }
                errorMessage = [
                    'Message: ' + msg,
                    'URL: ' + event.filename,
                    'Line: ' + event.lineno
                ].join(' - ');


                //  console.log(errorMessage);



                if (isBot()) {
                    return;
                }
                errorQueue.push(errorMessage);
                handleErrorQueue();

                //console.log(errorMessage);
                //console.log(msg);


            });

            // Captura rejeições de promessas
            window.addEventListener('unhandledrejection', function(event) {
                errorMessage = 'Promise Rejection: ' + (event.reason || 'Unknown reason');
                if (isBot()) {
                    return;
                }
                errorQueue.push(errorMessage);
                handleErrorQueue();
            });

            /// console.log(msg);


            // Função auxiliar para gerenciar a fila de erros
            function handleErrorQueue() {

                // console.log(errorQueue);

                if (errorQueue.length >= 5) {
                    sendErrorsToServer();
                } else {
                    clearTimeout(bill_timeout);
                    bill_timeout = setTimeout(sendErrorsToServer, 5000);
                }
            }


            function sendErrorsToServer() {
                if (errorQueue.length > 0) {
                    var message = errorQueue.join(' | ');
                    // console.log(message);
                    var xhr = new XMLHttpRequest();
                    var nonce = '<?php echo esc_js($nonce); ?>';
                    var ajaxurl = '<?php echo $ajax_url; ?>'; // Não é necessário esc_js aqui
                    xhr.open('POST', encodeURI(ajaxurl));
                    xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
                    xhr.onload = function() {
                        if (xhr.status === 200) {
                            // console.log('Success:', xhr.responseText);
                        } else {
                            console.log('Error:', xhr.status);
                        }
                    };
                    xhr.onerror = function() {
                        console.error('Request failed');
                    };
                    xhr.send('action=bill_minozzi_js_error_catched&_wpnonce=' + nonce + '&bill_js_error_catched=' + encodeURIComponent(message));
                    errorQueue = []; // Limpa a fila de erros após o envio
                }
            }
            window.addEventListener('beforeunload', sendErrorsToServer);
        </script>
<?php
    }
    private function get_ajax_url()
    {
        return esc_attr(admin_url("admin-ajax.php"));
    }
}
new stopbadbots_bill_catch_errors();
//
