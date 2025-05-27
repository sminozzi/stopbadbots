<?php
// 18 fev 25
// Bill


if (!defined("ABSPATH")) {
    die("Invalid request.");
}
if (function_exists('is_multisite') and is_multisite()) {
    return;
}

include_once(ABSPATH . 'wp-admin/includes/plugin.php');

if (!function_exists('is_plugin_active')) {
    // debug4();
    return;
}


if (is_plugin_active('antibots/antibots.php')) {
    return;
}

if (is_plugin_active('stopbadbots/stopbadbots.php')) {
    return;
}

if (is_plugin_active('antihacker/antihacker.php')) {
    return;
}

// debug4();

/*
function wpmemory_bill_hooking_catch_bots()
{
	$declared_classes = get_declared_classes();
	foreach ($declared_classes as $class_name) {
		if (strpos($class_name, "Bill_Catch_Bots") !== false) {
			return;
		}
	}
	require_once dirname(__FILE__) . "/includes/catch-bots/class_bill_catch_bots.php";
}
add_action("init", "wpmemory_bill_hooking_catch_bots", 15);
*/
class Bill_Catch_Bots
{
    private $wpdb;
    private $table_name;
    private $bill_catch_bots_ip;
    private $bill_bad_host = array();
    public function __construct()
    {
        global $wpdb;
        $this->wpdb = $wpdb;
        $this->table_name = $wpdb->prefix . 'bill_catch_bots';
        $bill_bad_host = [
            '1and1.com',
            'ALICOULD',
            'ALISOFT',
            'ALIBABA',
            'a2hosting.com',
            'ahrefs.com',
            'akamai.com',
            'akamai.net',
            'Amazon',
            'apple',
            'ARUBA-NET',
            'azure.com',
            'bluehost',
            'bluehost.com',
            'CHINANET',
            'clients.your-server.de',
            'cloudflare',
            'colocrossing',
            'contabo.com',
            'CONTABO',
            'digitalocean.com',
            'DIGITALOCEAN',
            'dreamhost',
            'dreamhost.com',
            'ExonHost',
            'fastly.com',
            'fastly.net',
            'Gandi',
            'GoDaddy',
            'Go-Daddy',
            'googleusercontent.com',
            'greengeeks.com',
            'heroku.com',
            'Hetzner',
            'hipl',
            'hosting',
            'hostgator.com',
            'HostHatch',
            'hosteurope.com',
            'hostinger.com',
            'hostpapa.com',
            'hostwinds.com',
            'hwclouds',
            'huaway',
            'HWCLOUDS',
            'ibm.com',
            'inmotionhosting.com',
            'Internap',
            'IONOS',
            'ipage.com',
            'ipfire.org',
            'justhost.com',
            'kimsufi.com',
            'LeaseWeb',
            'lightningbase.com',
            'Limestone',
            'LINODE',
            'linode.com',
            'Linode',
            'liquidweb.com',
            'MICROSOFT',
            'MSFT',
            'moonfruit.com',
            'namecheap.com',
            'Netsons',
            'oraclecloud.com',
            'OVH',
            'reliablesite.net',
            'researchscan',
            'rackspace.com',
            'rev.synaix.de',
            'scaleway.com',
            'secureserver.net',
            'semrush',
            'server',
            'siteground.com',
            'startdedicated.com',
            'softlayer',
            'tencent.com',
            'TMDHosting',
            'upcloud.com',
            'verizon.net',
            'vps',
            'vps.ovh',
            'vultr.com',
            'webhostingpad.com',
            'wix.com'
        ];
        $this->bill_bad_host = $bill_bad_host;
        $this->bill_catch_bots_ip = $this->bill_catch_bots_findip();

        // debug4($this->bill_catch_bots_findip());

        $this->bill_catch_bots_create_table();

        $user_agent = $this->bill_catch_bots_get_ua();

        // debug4();

        if (!$this->bill_isourserver($this->bill_catch_bots_ip) && !$this->bill_is_search_engine($user_agent)) {
            // debug4();
            add_action('shutdown', [$this, 'bill_catch_bots_capture_and_insert']);
        } else
            // debug4();

            //$transient_name = 'bill_daily_cleanup_done';
            if (false === get_transient('bill_daily_cleanup_done')) {
                $this->bill_daily_cleanup();
            }
    }

    private function bill_daily_cleanup()
    {

        // Query para deletar todos os registros, exceto os 500 mais recentes.
        // OBS: Como MySQL não permite deletar de uma tabela usando a mesma tabela em subconsulta,
        // usamos uma subconsulta "embrulhada" (alias) para contornar essa limitação.
        // Certifique-se de que o nome da tabela está correto e, se necessário, use backticks para evitar conflitos.
        $table_name = esc_sql($this->table_name);

        // Defina o limite
        $limit = 1000;

        // Use $wpdb->prepare para escapar o valor do limite
        $sql = $this->wpdb->prepare(
            "DELETE FROM `$table_name`
                WHERE id NOT IN (
                    SELECT id FROM (
                        SELECT id FROM `$table_name` ORDER BY id DESC LIMIT %d
                    ) AS temp
                )",
            $limit
        );

        // Executa a query
        $this->wpdb->query($sql);
        //
        //
        //
        //
        //



        // Cria o transient com duração de 86400 segundos (1 dia).
        //$transient_name = 'bill_daily_cleanup_done';
        set_transient('bill_daily_cleanup_done', true, 86400);
    }
    private function bill_is_search_engine($ua)
    {
        // Convert the user agent to lowercase for case-insensitive comparison
        $ua_lower = strtolower($ua);
        $search_bots = array(
            'facebookexternalhit',
            'twitterbot',
            'googlebot',
            'google-inspectiontool',
            'msn.com',
            'msnbot',
            'bingbot',
            'slurp',
            'baiduspider',
            'yandexbot',
            'adsbot-google', // Google Ads bot
            'mediapartners-google', // Google AdSense bot
        );
        foreach ($search_bots as $bot) {
            if (stripos($ua_lower, $bot) !== false) {
                return true; // It's a search engine bot
            }
        }
        return false; // Not a known search engine bot
    }
    private function bill_isourserver($bill_catch_bots_ip)
    {
        try {
            if (isset($_SERVER['SERVER_ADDR'])) {
                $server_ip = sanitize_text_field($_SERVER['SERVER_ADDR']);
            } elseif (function_exists("gethostname") and function_exists("gethostbyname")) {
                $server_ip = sanitize_text_field(gethostbyname(gethostname()));
            } else {
                return false;
            }
        } catch (Exception $e) {
            // echo 'Caught exception: ',  $e->getMessage(), "\n";
            return false;
        }
        if (!filter_var($server_ip, FILTER_VALIDATE_IP))
            return false;
        if ($server_ip == $bill_catch_bots_ip) {
            return true;
        }
        return false;
    }
    private function bill_catch_bots_create_table()
    {
        global $wpdb;
        $table_name = $wpdb->prefix . 'bill_catch_some_bots';
        $table_exists = $wpdb->get_var("SHOW TABLES LIKE '$table_name'");
        if ($table_exists == $table_name) {
            return;
        }
        $charset_collate = $this->wpdb->get_charset_collate();
        $sql = "CREATE TABLE $this->table_name (
            id mediumint(9) NOT NULL AUTO_INCREMENT,
            data timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
            ip varchar(45) DEFAULT NULL,
            pag text DEFAULT NULL,
            ua text DEFAULT NULL,
            bot tinyint(1) DEFAULT 0,
            http_code smallint(3) DEFAULT NULL,
            PRIMARY KEY (id)
        ) $charset_collate;";
        require_once ABSPATH . 'wp-admin/includes/upgrade.php';
        dbDelta($sql);
    }
    public function bill_catch_bots_capture_and_insert()
    {
        // debug4();
        $pagina_atual = $this->bill_catch_bots_get_current_url();
        $user_agent = $this->bill_catch_bots_get_ua();
        $http_code = http_response_code();
        $is_bot = $this->bill_catch_bots_is_bad_hosting($this->bill_catch_bots_ip) ? 1 : 0;
        // debug4($is_bot);

        if ($is_bot === 1) {
            $this->bill_catch_bots_insert_data($this->bill_catch_bots_ip, $pagina_atual, $user_agent, $is_bot, $http_code);
        }
    }
    private function bill_catch_bots_insert_data($ip, $pag, $ua, $bot, $http_code)
    {
        global $wpdb;
        $table_name = $wpdb->prefix . 'bill_catch_some_bots';


        $table_exists = $wpdb->get_var("SHOW TABLES LIKE '$table_name'");
        if (!$table_exists == $table_name) {
            $charset_collate = $this->wpdb->get_charset_collate();
            $sql = "CREATE TABLE $this->table_name (
            id mediumint(9) NOT NULL AUTO_INCREMENT,
            data timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
            ip varchar(45) DEFAULT NULL,
            pag text DEFAULT NULL,
            ua text DEFAULT NULL,
            bot tinyint(1) DEFAULT 0,
            http_code smallint(3) DEFAULT NULL,
            PRIMARY KEY (id)
        ) $charset_collate;";
            require_once ABSPATH . 'wp-admin/includes/upgrade.php';
            dbDelta($sql);
        } else {
            // Sanitização dos dados
            $ip  = filter_var($ip, FILTER_VALIDATE_IP) ? $ip : '0.0.0.0';
            $pag = sanitize_text_field(wp_unslash($pag));
            $ua  = sanitize_text_field(wp_unslash($ua));
            $bot = sanitize_text_field(wp_unslash($bot));
            // Inserção segura com prepare()
            $result = $wpdb->query(
                $wpdb->prepare(
                    "INSERT INTO $table_name (ip, pag, ua, bot, http_code) VALUES (%s, %s, %s, %s, %s)",
                    $ip,
                    $pag,
                    $ua,
                    $bot,
                    $http_code
                )
            );

            // debug4($result);


            if ($result === false) {
                error_log("DB ERROR: " . $wpdb->last_error);
                error_log("Query: " . $wpdb->last_query);
                //// // debug4(["error" => $wpdb->last_error, "query" => $wpdb->last_query]);
            } else {
                // error_log("Insert OK, ID: " . $wpdb->insert_id);
            }
        }
    }
    private function bill_catch_check_host_ripe($ip)
    {
        // Validate the IP address format
        $ip = filter_var($ip, FILTER_VALIDATE_IP);
        if (!$ip) {
            return false; // Invalid IP
        }
        // Check if the data is already cached in a transient
        $cache_key = 'bill_host_' . md5($ip);
        $cached_data = get_transient($cache_key);
        // If cached data exists return it
        if ($cached_data !== false) {
            return $cached_data;
        }
        // // debug4($ip);
        // Construct the RDAP API URL
        $urlcurl = 'https://rdap.db.ripe.net/ip/' . $ip;
        try {
            // Set up request options with timeout
            $request_options = array(
                'timeout'   => 5, // Set a timeout of 5 seconds
                'sslverify' => true, // Verify SSL for security
            );
            // Perform the HTTP request
            $response = wp_remote_get($urlcurl, $request_options);
            // Check if the request was successful
            $http_code = wp_remote_retrieve_response_code($response);
            if ($http_code !== 200) {
                return false; // API did not return a successful response
            }
            // // debug4($http_code);
            // Ensure the response is an array and contains a body
            if (is_array($response) && isset($response['body'])) {
                $decoded_response = json_decode($response['body'], true);
                //($decoded_response);
                // Ensure the JSON decoding succeeded
                if (json_last_error() === JSON_ERROR_NONE) {
                    // Cache the decoded response in a transient for 1 hour
                    // set_transient($cache_key, $decoded_response, HOUR_IN_SECONDS);
                    set_transient($cache_key, $decoded_response, 3 * MINUTE_IN_SECONDS);
                    //// // debug4($decoded_response);
                    return $decoded_response; // Process and return the result
                }
            }
            return false; // Fallback if the response or decoding is invalid
        } catch (Exception $e) {
            // Log the exception message for debugging
            error_log('Exception in bill_catch_check_host_ripe: ' . $e->getMessage());
            return false; // Return false in case of an exception
        }
    }
    private function bill_catch_bots_is_bad_hosting($ip)
    {
        // $ret = bill_catch_check_host_ripe($ip);
        $ret = $this->bill_catch_check_host_ripe($ip);
        if (!isset($ret) || !is_array($ret)) {
            // A chave 'body' não existe no array $ret
            // // debug4();
            return false;
        } else {
            $bodyArray = $ret;
        }
        //// // debug4($bill_bad_host);
        foreach ($this->bill_bad_host as $host) {
            if ($this->bill_procurarPalavra($bodyArray, $host)) {
                // echo "A palavra '$palavra' foi encontrada no array.<br>";
                // // debug4();
                return true;
            }
        }
        // // debug4();
        return false; // Retorna false se nenhum host for encontrado (importante!)
    }
    private function bill_procurarPalavra($array, $palavra)
    {
        foreach ($array as $chave => $valor) {
            // Verifique se $chave e $valor são strings ANTES de usar strpos()
            if (is_string($chave) && strpos($chave, $palavra) !== false) {
                return true;
            }
            if (is_string($valor) && strpos($valor, $palavra) !== false) {
                return true;
            }
            if (is_array($valor)) {
                if ($this->bill_procurarPalavra($valor, $palavra)) { // Use $this->
                    return true;
                }
            }
        }
        return false;
    }
    private function bill_catch_bots_findip()
    {
        // $ip = "";
        $headers = [
            "HTTP_CF_CONNECTING_IP", // CloudFlare
            "HTTP_CLIENT_IP", // Bill
            "HTTP_X_REAL_IP", // Bill
            "HTTP_X_FORWARDED", // Bill
            "HTTP_FORWARDED_FOR", // Bill
            "HTTP_FORWARDED", // Bill
            "HTTP_X_CLUSTER_CLIENT_IP", //Bill
            "HTTP_X_FORWARDED_FOR", // Squid and most other forward and reverse proxies
            "REMOTE_ADDR", // Default source of remote IP
        ];
        for ($x = 0; $x < 8; $x++) {
            foreach ($headers as $header) {
                if (!isset($_SERVER[$header])) {
                    continue;
                }
                $myheader = trim(sanitize_text_field($_SERVER[$header]));
                if (empty($myheader)) {
                    continue;
                }
                $ip = trim(sanitize_text_field($_SERVER[$header]));
                if (empty($ip)) {
                    continue;
                }
                if (
                    false !==
                    ($comma_index = strpos(
                        sanitize_text_field($_SERVER[$header]),
                        ","
                    ))
                ) {
                    $ip = substr($ip, 0, $comma_index);
                }
                // First run through. Only accept an IP not in the reserved or private range.
                if ($ip == "127.0.0.1") {
                    $ip = "";
                    continue;
                }
                if (0 === $x) {
                    $ip = filter_var(
                        $ip,
                        FILTER_VALIDATE_IP,
                        FILTER_FLAG_NO_RES_RANGE | FILTER_FLAG_NO_PRIV_RANGE
                    );
                } else {
                    $ip = filter_var($ip, FILTER_VALIDATE_IP);
                }
                if (!empty($ip)) {
                    break;
                }
            }
            if (!empty($ip)) {
                break;
            }
        }
        if (!empty($ip)) {
            return $ip;
        } else {
            return "unknow";
        }
    }
    private function bill_catch_bots_get_ua()
    {
        return isset($_SERVER['HTTP_USER_AGENT']) ? sanitize_text_field($_SERVER['HTTP_USER_AGENT']) : 'Unknown';
    }
    private function bill_catch_bots_get_current_url()
    {
        return isset($_SERVER['REQUEST_URI']) ? esc_url_raw($_SERVER['REQUEST_URI']) : '/';
    }
}
new Bill_Catch_Bots();
