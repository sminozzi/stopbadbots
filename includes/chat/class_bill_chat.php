<?php

namespace stopbadbots_BillChat;
// 2024-12=18 // 2025-01-04
if (!defined('ABSPATH')) {
    die('Invalid request.');
}
if (function_exists('is_multisite') && is_multisite()) {
    return;
}

class ChatPlugin
{
    public function __construct()
    {
        // Hooks para AJAX
        add_action('wp_ajax_bill_chat_send_message', [$this, 'bill_chat_send_message']);
        //add_action('wp_ajax_nopriv_bill_chat_send_message', [$this, 'bill_chat_send_message']);
        add_action('wp_ajax_bill_chat_reset_messages', [$this, 'bill_chat_reset_messages']);
        //add_action('wp_ajax_nopriv_bill_chat_reset_messages', [$this, 'bill_chat_reset_messages']);
        add_action('wp_ajax_bill_chat_load_messages', [$this, 'bill_chat_load_messages']);
        // Registrar os scripts
        add_action('admin_init', [$this, 'chat_plugin_scripts']);
        add_action('admin_init', [$this, 'enqueue_chat_scripts']);
    }
    public function chat_plugin_scripts()
    {
        wp_enqueue_style(
            'chat-style',
            plugin_dir_url(__FILE__) . 'chat.css'
        );
    }
    public function enqueue_chat_scripts()
    {
        wp_enqueue_script(
            'chat-script',
            plugin_dir_url(__FILE__) . 'chat.js',
            array('jquery'),
            '',
            true
        );
        wp_localize_script('chat-script', 'bill_data', array(
            'ajax_url'                 => admin_url('admin-ajax.php'),
            'reset_success'            => esc_attr__('Chat messages reset successfully.', 'stopbadbots'),
            'reset_error'              => esc_attr__('Error resetting chat messages.', 'stopbadbots'),
            'invalid_message'          => esc_attr__('Invalid message received:', 'stopbadbots'),
            'invalid_response_format'  => esc_attr__('Invalid response format:', 'stopbadbots'),
            'response_processing_error' => esc_attr__('Error processing server response:', 'stopbadbots'),
            'not_json'                 => esc_attr__('Response is not valid JSON.', 'stopbadbots'),
            'ajax_error'               => esc_attr__('AJAX request failed:', 'stopbadbots'),
            'send_error'               => esc_attr__('Error sending the message. Please try again later.', 'stopbadbots'),
            'empty_message_error'      => esc_attr__('Please enter a message!', 'stopbadbots'),
        ));
    }
    /**
     * Função para carregar as mensagens do chat.
     */
    public function bill_chat_load_messages()
    {
        if (ob_get_length()) {
            ob_clean();
        }
        $messages = get_option('chat_messages', []);
        $last_count = isset($_POST['last_count']) ? intval($_POST['last_count']) : 0;
        // Verifica se há novas mensagens
        $new_messages = [];
        if (count($messages) > $last_count) {
            $new_messages = array_slice($messages, $last_count);
        }
        // Retorna as mensagens no formato JSON
        wp_send_json([
            'message_count' => count($messages),
            'messages' => array_map(function ($message) {
                return [
                    'text' => esc_html($message['text']),
                    'sender' => esc_html($message['sender'])
                ];
            }, $new_messages)
        ]);
        wp_die();
    }
    public function bill_chat_load_messages_NEW()
    {
        // Verifica se é uma solicitação AJAX
        if (!wp_doing_ajax()) {
            wp_die('Acesso negado', 403);
        }
        $messages = get_option('chat_messages', []);
        $last_count = isset($_POST['last_count']) ? intval($_POST['last_count']) : 0;
        // Verifica se há novas mensagens
        $new_messages = [];
        if (count($messages) > $last_count) {
            $new_messages = array_slice($messages, $last_count);
        }
        // Retorna as mensagens no formato JSON
        wp_send_json([
            'message_count' => count($messages),
            'messages' => array_map(function ($message) {
                return [
                    'text' => esc_html($message['text']),
                    'sender' => esc_html($message['sender'])
                ];
            }, $new_messages)
        ]);
    }
    public function bill_read_file($file, $lines)
    {
        // Check if the file exists and is readable
        clearstatcache(true, $file); // Clear cache to ensure current file state
        if (!file_exists($file) || !is_readable($file)) {
            return []; // Return empty array in case of error
        }
        $text = [];
        // Check if SplFileObject is available
        /*
        if (class_exists('SplFileObject')) {
            try {
                // Open the file with SplFileObject (using global namespace)
                $fileObj = new \SplFileObject($file, 'r');
                // Move to the end to count total lines
                $fileObj->seek(PHP_INT_MAX);
                $totalLines = $fileObj->key(); // Total number of lines (zero-based index)
                // Calculate the starting line for the last $lines
                $startLine = max(0, $totalLines - $lines);
                // Move the pointer to the starting line
                $fileObj->seek($startLine);
                // Read lines until the end
                while (!$fileObj->eof() && count($text) < $lines) {
                    $line = $fileObj->fgets();
                    if ($line === false && file_exists($file)) {
                        usleep(500000); // Wait 0.5 seconds if reading fails
                        $line = $fileObj->fgets(); // Retry reading the line
                    }
                    if ($line !== false) {
                        $text[] = rtrim($line); // Remove trailing newlines
                    }
                }
            } catch (\Exception $e) {
                // In case of error, return empty array and log the issue
                error_log("Error reading $file with SplFileObject: " . $e->getMessage());
                return [];
            }
        } else {
        */
        // Fallback to original method with fopen
        $handle = fopen($file, "r");
        if (!$handle) {
            return [];
        }
        $bufferSize = 8192; // 8KB
        $currentChunk = '';
        $linecounter = 0;
        fseek($handle, 0, SEEK_END);
        $filesize = ftell($handle);
        if ($filesize < $bufferSize) {
            $bufferSize = $filesize;
        }
        if ($bufferSize < 1) {
            fclose($handle);
            return [];
        }
        $pos = $filesize - $bufferSize;
        while ($pos >= 0 && $linecounter < $lines) {
            if ($pos < 0) {
                $pos = 0;
            }
            fseek($handle, $pos);
            $chunk = fread($handle, $bufferSize);
            if ($chunk === false && file_exists($file)) {
                usleep(500000); // Wait 0.5 seconds if reading fails
                $chunk = fread($handle, $bufferSize); // Retry reading the chunk
            }
            $currentChunk = $chunk . $currentChunk;
            $linesInChunk = explode("\n", $currentChunk);
            $currentChunk = array_shift($linesInChunk);
            foreach (array_reverse($linesInChunk) as $line) {
                $text[] = $line;
                $linecounter++;
                if ($linecounter >= $lines) {
                    break 2;
                }
            }
            $pos -= $bufferSize;
        }
        if (!empty($currentChunk)) {
            $text[] = $currentChunk;
        }
        fclose($handle);
        // }
        return $text;
    }
    /**
     * Função para chamar a API do ChatGPT.
     */
    public function bill_chat_call_chatgpt_api($data, $chatType, $chatVersion)
    {
        //ini_set('display_errors', 1);
        //ini_set('display_startup_errors', 1);
        //error_reporting(E_ALL);
        // $transient_name = 'bill_chat';
        // delete_transient($transient_name);
        // if (false === get_transient($transient_name)) {
        // Transiente não existe, cria um novo com a data atual
        //$current_date = date('Y-m-d H:i:s'); // Formato da data: Ano-Mês-Dia Hora:Minuto:Segundo
        //set_transient($transient_name, $current_date, DAY_IN_SECONDS); // Transiente com duração de 1 dia
        $bill_chat_erros = '';
        try {
            function filter_log_content($content)
            {
                if (is_array($content)) {
                    // Filtra o array, removendo valores vazios (strings vazias, null, false, etc.)
                    $filteredArray = array_filter($content);
                    return empty($filteredArray) ? '' : $content;
                } elseif (is_object($content)) {
                    // Se for um objeto, retorna string vazia
                    return '';
                } else {
                    // Mantém o conteúdo original se não for array ou objeto
                    return $content;
                }
            }
            $bill_folders = ChatPlugin::get_path_logs();
            $log_type = "PHP Error Log";
            $bill_chat_erros = "Log ($log_type) not found or not readable.";
            foreach ($bill_folders as $bill_folder) {
                if (!file_exists($bill_folder) && !is_readable($bill_folder)) {
                    continue;
                }
                $returned_bill_chat_erros = $this->bill_read_file($bill_folder, 40);
                $returned_bill_chat_erros = filter_log_content($returned_bill_chat_erros);
                $returned_bill_chat_erros = filter_log_content($returned_bill_chat_erros);
                if (! empty($returned_bill_chat_erros)) {
                    $bill_chat_erros = $returned_bill_chat_erros;
                    break;
                }
            }
        } catch (Exception $e) {
            $bill_chat_erros = "An error occurred to read error logs: " . $e->getMessage();
        }
        // Filtra $bill_chat_erros novamente (caso tenha sido modificado)
        //$bill_chat_erros = filter_log_content($bill_chat_erros);
        $plugin_path = plugin_basename(__FILE__); // Retorna algo como "plugin-folder/plugin-file.php"
        $language = get_locale();
        $plugin_slug = explode('/', $plugin_path)[0]; // Pega apenas o primeiro diretório (a raiz)
        $domain = parse_url(home_url(), PHP_URL_HOST);
        if (empty($bill_chat_erros)) {
            $bill_chat_erros = 'No errors found!';
        }
        //2025
        $stopbadbots_checkup = \stopbadbots_sysinfo_get();
        $data2 = [
            'param1' => $data,
            'param2' => $stopbadbots_checkup,
            'param3' => $bill_chat_erros,
            'param4' => $language,
            'param5' => $plugin_slug,
            'param6' => $domain,
            'param7' => $chatType,
            'param8' => $chatVersion,
        ];
        $response = wp_remote_post('https://BillMinozzi.com/chat/api/api.php', [
            'timeout' => 60,
            'headers' => [
                'Content-Type' => 'application/json',
            ],
            'body' => json_encode($data2),
        ]);
        if (is_wp_error($response)) {
            $error_message = sanitize_text_field($response->get_error_message());
        } else {
            $body = sanitize_text_field(wp_remote_retrieve_body($response));
            $data = json_decode($body, true);
        }
        if (isset($data['success']) && $data['success'] === true) {
            $message = $data['message'];
        } else {
            $message = esc_attr__("Error contacting the Artificial Intelligence (API). Please try again later.", 'stopbadbots');
        }
        return $message;
    }
    /**
     * Função para enviar a mensagem do usuário e obter a resposta do ChatGPT.
     */
    public static function get_path_logs()
    {
        $bill_folders = [];

        /*
        $caminho_padrao = realpath(ABSPATH . "error_log");
        $bill_folders[] = $caminho_padrao;
        $bill_folders[] = realpath(ABSPATH . "php_errorlog");
        */
        /*
         // PHP error log (defined in php.ini)
         $error_log_path = trim(ini_get("error_log"));
         if (!is_null($error_log_path) && $error_log_path != trim(ABSPATH . "error_log")) {
             $bill_folders[] = $error_log_path;
         }
         */
        // Opção 2 (mais robusta): Adiciona se estiver definido e for diferente do padrão

        //$error_log_path = trim(ini_get("error_log"));


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

        $bill_folders[] = $error_log_path;

        /*
        $caminho_padrao = realpath(ABSPATH . "error_log");
        $caminho_atual = realpath($error_log_path);

        if (!empty($error_log_path) && $caminho_atual != $caminho_padrao && !in_array($error_log_path, $bill_folders)) {
            $bill_folders[] = $error_log_path;
        }
        */
        // Logs in WordPress root directory
        //
        $bill_folders[] = WP_CONTENT_DIR . "/debug.log";
        // Logs in current plugin directory
        $bill_folders[] = plugin_dir_path(__FILE__) . "error_log";
        $bill_folders[] = plugin_dir_path(__FILE__) . "php_errorlog";
        // Logs in current theme directory
        $bill_folders[] = get_theme_root() . "/error_log";
        $bill_folders[] = get_theme_root() . "/php_errorlog";
        // Logs in administration area (if it exists)
        $bill_admin_path = str_replace(get_bloginfo("url") . "/", ABSPATH, get_admin_url());
        $bill_folders[] = $bill_admin_path . "/error_log";
        $bill_folders[] = $bill_admin_path . "/php_errorlog";
        // Logs in plugin subdirectories
        try {
            $bill_plugins = array_slice(scandir(plugin_dir_path(__FILE__)), 2);
            foreach ($bill_plugins as $bill_plugin) {
                $plugin_path = plugin_dir_path(__FILE__) . $bill_plugin;
                if (is_dir($plugin_path)) {
                    $bill_folders[] = $plugin_path . "/error_log";
                    $bill_folders[] = $plugin_path . "/php_errorlog";
                }
            }
        } catch (Exception $e) {
            // Handle the exception
            error_log("Error scanning plugins directory: " . $e->getMessage());
        }
        // Logs in theme subdirectories
        /*
         $bill_themes = array_slice(scandir(get_theme_root()), 2);
         foreach ($bill_themes as $bill_theme) {
             $theme_path = get_theme_root() . "/" . $bill_theme;
             if (is_dir($theme_path)) {
                 $bill_folders[] = $theme_path . "/error_log";
                 $bill_folders[] = $theme_path . "/php_errorlog";
             }
         }
         */
        try {
            $bill_themes = array_slice(scandir(get_theme_root()), 2);
            foreach ($bill_themes as $bill_theme) {
                if (is_dir(get_theme_root() . "/" . $bill_theme)) {
                    $bill_folders[] = get_theme_root() . "/" . $bill_theme . "/error_log";
                    $bill_folders[] = get_theme_root() . "/" . $bill_theme . "/php_errorlog";
                }
            }
        } catch (Exception $e) {
            // Handle the exception
            error_log("Error scanning theme directory: " . $e->getMessage());
        }

        //error_log(var_export($bill_folders));
        //debug4($bill_folders);
        //die();


        return $bill_folders;
    }
    public function bill_chat_send_message()
    {
        // Captura e sanitiza a mensagem
        $message = sanitize_text_field($_POST['message']);
        // Verifica e sanitiza o chat_type, atribuindo 'default' caso não exista
        $chatType = isset($_POST['chat_type']) ? sanitize_text_field($_POST['chat_type']) : 'default';
        if (empty($message)) {
            if ($chatType == 'auto-checkup') {
                $message = esc_attr("Auto Checkup for Erros button clicked...", 'stopbadbots');
            } elseif ($chatType == 'auto-checkup2') {
                $message = esc_attr("Auto Checkup Server button clicked...", 'stopbadbots');
            }
        }
        //  if (empty($message)) {
        //    $message = esc_attr("Auto Checkup button clicked...", 'stopbadbots');
        // }
        // error_log(var_export($chatType));
        $chatVersion = isset($_POST['chat_version']) ? sanitize_text_field($_POST['chat_version']) : '1.00';
        // Chama a API e obtém a resposta
        $response_data = $this->bill_chat_call_chatgpt_api($message, $chatType, $chatVersion);
        // Verifique se a resposta foi obtida corretamente
        if (!empty($response_data)) {
            $output = $response_data;
            $resposta_formatada = $output;
        } else {
            $output = "Error to get response from AI source!";
            $output = esc_attr__("Error to get response from AI source!", 'stopbadbots');
        }
        // Prepara as mensagens
        $messages = get_option('chat_messages', []);
        $messages[] = [
            'text' => $message,
            'sender' => 'user'
        ];
        $messages[] = [
            'text' => $resposta_formatada,
            'sender' => 'chatgpt'
        ];
        update_option('chat_messages', $messages);
        wp_die();
    }
    /**
     * Função para resetar as mensagens.
     */
    public function bill_chat_reset_messages()
    {
        update_option('chat_messages', []);
        wp_die();
    }
}
new ChatPlugin();
