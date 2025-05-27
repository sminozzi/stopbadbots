<?php

/**
 * Plugin Name: Bill Catch Errors
 * Description: Captures JavaScript errors and logs them on the server.
 * Version: 7.0
 * Author: Bill Minozzi
 * Author URI: https://BillMinozzi.com
 * Text Domain: bill-catch-errors
 * License:     GPL2
 */
if (!defined("ABSPATH")) {
    die("Invalid request.");
}
// 2 2025 ==========================
if (function_exists('is_multisite') && is_multisite()) {
    return;
}
add_action('wp_enqueue_scripts', function () {
    wp_enqueue_script('jquery-migrate');
}, 10);
$bill_format_error_log_data = get_transient('bill_error_log_date_format');
if (!$bill_format_error_log_data) {
    $bill_format_error_log_info = bill_get_error_log_date_string();
    $bill_format_error_log_data = bill_detect_error_log_date_format($bill_format_error_log_info);
}
function bill_get_error_log_date_string()
{
    $error_log_path = ini_get("error_log");
    if (!empty($error_log_path)) {
        $error_log_path = trim($error_log_path);
    } else {
        if (defined('WP_DEBUG') && WP_DEBUG) {
            $error_log_path = trailingslashit(WP_CONTENT_DIR) . 'debug.log';
        } else {
            $error_log_path = trailingslashit(ABSPATH) . 'error_log';
        }
    }
    $logFile = $error_log_path;
    // Check if log file exists and is readable
    if (!file_exists($logFile) || !is_readable($logFile)) {
        // debug5("Log file not found or not readable: $logFile");
        return bill_fallback_date_format();
    }
    $logFileHandle = fopen($logFile, 'r'); // Open file
    if (!$logFileHandle) {
        // debug5("Failed to open the log file: $logFile");
        return bill_fallback_date_format();
    }
    $firstLine = fgets($logFileHandle); // Read first line
    fclose($logFileHandle); // Close the file handle
    // debug5("First line of log file: " . var_export($firstLine, true));
    // Extract date from log line
    if (preg_match('/^\[(.*?)\]/', trim($firstLine), $matches)) {
        $dateString = $matches[1];
        // debug5("Extracted date string: $dateString");
        return $dateString;
    } else {
        // debug5("No date found in the first line. Falling back.");
        return bill_fallback_date_format();
    }
}
function bill_fallback_date_format()
{
    $default_format = 'Y-m-d H:i:s';
    return $default_format;
}
function bill_splitDate($date)
{
    // Lista de padrões para diferentes formatos
    $patterns = [
        '/^(\d{1,2})[-\/\s]([A-Za-z]+)[-\/\s](\d{4})$/',  // 22-Jan-2025, 22/Jan/2025, 22 Jan 2025
        '/^(\d{1,2})[-\/\s](\d{1,2})[-\/\s](\d{4})$/',     // 22-01-2025, 22/01/2025, 22 01 2025
        '/^(\d{4})[-\/\s](\d{1,2})[-\/\s](\d{1,2})$/'      // 2025-01-22, 2025/01/22, 2025 01 22
    ];
    foreach ($patterns as $pattern) {
        if (preg_match($pattern, $date, $matches)) {
            return array_slice($matches, 1, 3); // Retorna apenas as 3 partes da data
        }
    }
    return false;
}
function bill_split_line($logLine)
{
    $fuso = false;
    $numeroDeEspacos = substr_count($logLine, ' ');
    // debug5("Número de espaços em branco: $numeroDeEspacos");
    if ($numeroDeEspacos < 1) {
        return false;  // Se não houver data e hora adequadas
    }
    // Verificar o número de espaços e tratar de forma distinta
    if ($numeroDeEspacos > 2) {
        $partes = preg_split('/\s+/', $logLine);  // Divide por espaços em branco
        // debug5($partes);
        $data = array_slice($partes, 0, 3);
        $horaArray = preg_grep('/:/', $partes);
        $hora = reset($horaArray); // Pega o primeiro valor encontrado
        // debug5($hora);
        $lastElement = end($partes); // Obtém o último elemento
        if (strpos($lastElement, ':') === false) {
            $fuso = $lastElement;
        }
        // debug5($fuso);
        $listaFusos = timezone_identifiers_list();
        if ($fuso && !in_array($fuso, $listaFusos)) {
            // debug5("Fuso inválido detectado: $fuso");
            $fuso = null;  // Se não for um fuso válido, descartamos
        } else {
            // debug5("Fuso válido: $fuso");
        }
        return [
            'data' => $data,
            'fuso' => $fuso
        ];
    } elseif ($numeroDeEspacos <= 2) {
        // Se tiver até 2 espaços, apenas data e hora e talvez Fuso
        $partes = preg_split('/\s+/', $logLine);  // Divide por espaços em branco
        // debug5("Partes separadas: " . print_r($partes, true));
        if (strlen($partes[0]) > 4) {
            //      $partes = ['02-Feb-2025', '12:34:56', 'UTC'];
            $r = bill_splitDate($partes[0]);
            // Substituir o primeiro elemento por 3 novos elementos
            // array_splice($partes, 0, 1, ['NovaData', 'NovaHora', 'NovaUTC']);
            array_splice($partes, 0, 1, $r);
        }
        // debug5($partes);
        if (count($partes) < 4) {
            //$retornar =  bill_splitDate($partes[0]);
            // debug5($partes);
            // return $retornar;
            return [
                'data' => $partes,
                'fuso' => ''
            ];
            return $return;
        }
        // Identifica a hora (parte sempre estará em segundo lugar)
        $horaArray = preg_grep('/:/', $partes);
        //$hora = reset($horaArray); // Pega o primeiro valor encontrado
        // debug5($hora);
        $lastElement = end($partes); // Obtém o último elemento
        if (strpos($lastElement, ':') === false) {
            $fuso = $lastElement;
        }
        $retornar_data = array_slice($partes, 0, 3);
        return [
            'data' => $retornar_data,
            'fuso' => $fuso
        ];
    } else {
        // Se tiver apenas 1 espaço (ou nenhum), não há informações suficientes
        return null;
    }
}
/* Detect date format 
A função analisa uma linha de log ($logLine) para detectar o formato de data em inglês 
(ex.: "29 January 2025 12:44:30" ou "02-Feb-2025 12:34:56 UTC") 
e retorna uma string no formato reconhecido com hora e, opcionalmente, fuso horário. 
Ela armazena o formato detectado em um transient para uso futuro.
*/
function bill_detect_error_log_date_format($logLine)
{
    $bill_used_separators = ['-', '/', ' '];
    $bill_separador_used_in_error_log_date = null;
    foreach ($bill_used_separators as $separator) {
        if (strpbrk($logLine, $separator) !== false) {
            $bill_separador_used_in_error_log_date = $separator;
            break;
        }
    }
    $r = bill_split_line($logLine);
    $data_partes = $r['data'];
    $fuso = $r['fuso'];
    $dia = null;
    $mes = null;
    $ano = null;
    $numeros_menores_12 = 0; // Contador para detectar ambiguidade
    foreach ($data_partes as $data_parte) {
        if (isset($dia) && isset($mes) && isset($ano)) {
            break;
        }
        try {
            if (!isset($mes) && preg_match('/[a-zA-Z]+/', $data_parte)) {
                $mes = $data_parte; // Mês com letras, sem ambiguidade
            } elseif (!isset($ano) && strlen($data_parte) == 4 && is_numeric($data_parte)) {
                $ano = $data_parte; // Ano com 4 dígitos, sem ambiguidade
            } elseif (!isset($dia) && is_numeric($data_parte) && strlen($data_parte) <= 2) {
                $valor = intval($data_parte);
                if ($valor > 12 && $valor <= 31) {
                    $dia = $data_parte; // Dia claro, sem ambiguidade
                } elseif ($valor <= 12) {
                    $numeros_menores_12++; // Incrementa contador de ambiguidade
                    if (!isset($dia)) {
                        $dia = $data_parte; // Pode ser dia ou mês
                    } elseif (!isset($mes)) {
                        $mes = $data_parte; // Pode ser dia ou mês
                    }
                }
            }
        } catch (Exception $e) {
            return bill_fallback_date_format();
        }
    }
    // Se houver ambiguidade (dois números <= 12), retorna fallback
    if ($numeros_menores_12 >= 2 && !isset($mes) || (isset($dia) && isset($mes) && intval($dia) <= 12 && intval($mes) <= 12)) {
        //debug4("Ambiguidade detectada na data: $logLine");
        return bill_fallback_date_format();
    }
    $original = $logLine;
    $format = '';
    if ($dia && $mes && $ano) {
        $separador = $bill_separador_used_in_error_log_date ?? ' ';
        if (strpos($original, $dia) !== false && strpos($original, $mes) !== false && strpos($original, $ano) !== false) {
            if (preg_match('/[a-zA-Z]/', $mes)) {
                $format = "d{$separador}M{$separador}Y";
            } else {
                $format = "d{$separador}m{$separador}Y";
            }
        } elseif (strpos($original, $mes) !== false && strpos($original, $dia) !== false && strpos($original, $ano) !== false) {
            if (preg_match('/[a-zA-Z]/', $mes)) {
                $format = "M{$separador}d{$separador}Y";
            } else {
                $format = "m{$separador}d{$separador}Y";
            }
        } elseif (strpos($original, $ano) !== false && strpos($original, $mes) !== false && strpos($original, $dia) !== false) {
            if (strlen($ano) == 4) {
                $format = preg_match('/[a-zA-Z]/', $mes) ? "Y{$separador}M{$separador}d" : "Y{$separador}m{$separador}d";
            } else {
                $format = preg_match('/[a-zA-Z]/', $mes) ? "y{$separador}M{$separador}d" : "y{$separador}m{$separador}d";
            }
        }
    }
    if (empty($format)) {
        $format = bill_fallback_date_format();
    }
    if (strpos($format, 'H:i:s') === false) {
        $format .= " H:i:s";
    }
    set_transient('bill_error_log_date_format', $format, 30 * DAY_IN_SECONDS);
    $listaFusos = timezone_identifiers_list();
    $retorno = !empty($fuso) && in_array($fuso, $listaFusos) ? date($format) . ' ' . $fuso : date($format);
    return $retorno;
}
add_action("wp_ajax_bill_minozzi_js_error_catched", "bill_minozzi_js_error_catched");
add_action("wp_ajax_nopriv_bill_minozzi_js_error_catched", "bill_minozzi_js_error_catched");
function bill_minozzi_js_error_catched()
{
    $bill_format_error_log_data = get_transient('bill_error_log_date_format');
    if (!$bill_format_error_log_data) {
        $bill_format_error_log_data = bill_fallback_date_format();
    }
    $error_log_updated = "NOT OK!";
    if (!isset($_REQUEST) || !isset($_REQUEST["bill_js_error_catched"])) {
        wp_die("empty error");
    }
    if (!wp_verify_nonce(sanitize_text_field($_POST["_wpnonce"]), "bill-catch-js-errors")) {
        status_header(406, "Invalid nonce");
        wp_die("Bad Nonce!");
    }
    $bill_js_error_catched = sanitize_text_field($_REQUEST["bill_js_error_catched"]);
    $bill_js_error_catched = trim($bill_js_error_catched);
    if (empty($bill_js_error_catched)) {
        wp_die("empty error");
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
    try {
        $dir = dirname($logFile);
        if (!file_exists($dir)) {
            if (!is_writable(dirname($dir)) || !mkdir($dir, 0755, true)) {
                wp_die("Unable to create log directory: $dir");
            }
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
    } catch (Exception $e) {
        wp_die("Log setup error: " . $e->getMessage());
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
        $formattedMessage = "[" . date($bill_format_error_log_data) . "] - " . $logMessage . PHP_EOL;
        //$ret_error_log = false;
        if (error_log($formattedMessage, 3, $logFile)) {
            //$ret_error_log = true;
            $error_log_updated = "OK!";
        } else {
            try {
                $r = file_put_contents($logFile, $formattedMessage, FILE_APPEND | LOCK_EX);
                if ($r) {
                    $error_log_updated = "OK!";
                } else {
                    $timestamp_string = strval(time());
                    update_option('bill_minozzi_error_log_status', $timestamp_string);
                }
            } catch (Exception $e) {
                wp_die("Fail to write at error_log " . $e->getMessage());
            }
        }
    }
    die($error_log_updated);
}
class bill_minozzi_bill_catch_errors
{
    public function __construct()
    {
        add_action("wp_head", [$this, "add_bill_javascript_to_header"]);
        add_action("admin_head", [$this, "add_bill_javascript_to_header"]);
        // $this->gravar2(__LINE__);
    }
    public function add_bill_javascript_to_header()
    {
        $nonce = wp_create_nonce("bill-catch-js-errors");
        $ajax_url = esc_js($this->get_ajax_url()) . "?action=bill_minozzi_js_error_catched&_wpnonce=" . $nonce;
?>
        <script type="text/javascript">
            if (typeof jQuery !== 'undefined' && typeof jQuery.migrateWarnings !== 'undefined') {
                jQuery.migrateTrace = true; // Habilitar stack traces
                jQuery.migrateMute = false; // Garantir avisos no console
            }
            let bill_timeout;

            function isBot() {
                const bots = ['crawler', 'spider', 'baidu', 'duckduckgo', 'bot', 'googlebot', 'bingbot', 'facebook', 'slurp', 'twitter', 'yahoo'];
                const userAgent = navigator.userAgent.toLowerCase();
                return bots.some(bot => userAgent.includes(bot));
            }
            const originalConsoleWarn = console.warn; // Armazenar o console.warn original
            const sentWarnings = [];
            const bill_errorQueue = [];
            const slugs = [
                "antibots", "antihacker", "bigdump-restore", "boatdealer", "cardealer",
                "database-backup", "disable-wp-sitemap", "easy-update-urls", "hide-site-title",
                "lazy-load-disable", "multidealer", "real-estate-right-now", "recaptcha-for-all",
                "reportattacks", "restore-classic-widgets", "s3cloud", "site-checkup",
                "stopbadbots", "toolsfors", "toolstruthsocial", "wp-memory", "wptools"
            ];

            function hasSlug(warningMessage) {
                return slugs.some(slug => warningMessage.includes(slug));
            }
            // Sobrescrita de console.warn para capturar avisos JQMigrate
            console.warn = function(message, ...args) {
                // Processar avisos JQMIGRATE
                if (typeof message === 'string' && message.includes('JQMIGRATE')) {
                    if (!sentWarnings.includes(message)) {
                        sentWarnings.push(message);
                        let file = 'unknown';
                        let line = '0';
                        try {
                            const stackTrace = new Error().stack.split('\n');
                            for (let i = 1; i < stackTrace.length && i < 10; i++) {
                                const match = stackTrace[i].match(/at\s+.*?\((.*):(\d+):(\d+)\)/) ||
                                    stackTrace[i].match(/at\s+(.*):(\d+):(\d+)/);
                                if (match && match[1].includes('.js') &&
                                    !match[1].includes('jquery-migrate.js') &&
                                    !match[1].includes('jquery.js')) {
                                    file = match[1];
                                    line = match[2];
                                    break;
                                }
                            }
                        } catch (e) {
                            // Ignorar erros
                        }
                        const warningMessage = message.replace('JQMIGRATE:', 'Error:').trim() + ' - URL: ' + file + ' - Line: ' + line;
                        if (!hasSlug(warningMessage)) {
                            bill_errorQueue.push(warningMessage);
                            handleErrorQueue();
                        }
                    }
                }
                // Repassar todas as mensagens para o console.warn original
                originalConsoleWarn.apply(console, [message, ...args]);
            };
            //originalConsoleWarn.apply(console, arguments);
            // Restaura o console.warn original após 6 segundos
            setTimeout(() => {
                console.warn = originalConsoleWarn;
            }, 6000);

            function handleErrorQueue() {
                // Filtrar mensagens de bots antes de processar
                if (isBot()) {
                    bill_errorQueue = []; // Limpar a fila se for bot
                    return;
                }
                if (bill_errorQueue.length >= 5) {
                    sendErrorsToServer();
                } else {
                    clearTimeout(bill_timeout);
                    bill_timeout = setTimeout(sendErrorsToServer, 7000);
                }
            }

            function sendErrorsToServer() {
                if (bill_errorQueue.length > 0) {
                    const message = bill_errorQueue.join(' | ');
                    //console.log('[Bill Catch] Enviando ao Servidor:', message); // Log temporário para depuração
                    const xhr = new XMLHttpRequest();
                    const nonce = '<?php echo esc_js($nonce); ?>';
                    const ajax_url = '<?php echo $ajax_url; ?>';
                    xhr.open('POST', encodeURI(ajax_url));
                    xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
                    xhr.send('action=bill_minozzi_js_error_catched&_wpnonce=' + nonce + '&bill_js_error_catched=' + encodeURIComponent(message));
                    // bill_errorQueue = [];
                    bill_errorQueue.length = 0; // Limpa o array sem reatribuir
                }
            }
        </script>
<?php
    }
    private function get_ajax_url()
    {
        return esc_attr(admin_url("admin-ajax.php"));
    }
}
new bill_minozzi_bill_catch_errors();
?>