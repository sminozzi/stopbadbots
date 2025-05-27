<?php namespace stopbadbots_BillInstall;
//
/**
 * @ Author: Bill Minozzi
 * @ Copyright: 2020 www.BillMinozzi.com
 * @ Modified time: 2024-04-27  21 06 2024
*/
if (!defined("ABSPATH")) {
    die('We\'re sorry, but you can not directly access this file.');
}
use stopbadbots_BillDiagnose\MemoryChecker;
use stopbadbots_BillDiagnose\ErrorChecker;
// ------------------------- Ago 24 -------------------- 

  if (!class_exists('stopbadbots_BillDiagnose\MemoryChecker')) {
      return;
  }
  if( !function_exists('bill_install_ajaxurl')) {
    return;
  }
  // ------------------------- Ago 24 -------------------- 
$bill_debug = true;
$bill_debug = false;

if($bill_debug)
   delete_option('bill_minozzi_pre_checkup_finished');


/*
// tem outra...
$plugin_path = trailingslashit( dirname( plugin_basename( __FILE__ ) ) ); 
$parts = explode('/', rtrim($plugin_path, '/')); // Divide a string em partes usando '/' como delimitador
$plugin_slug = reset($parts); // Obtém o primeiro elemento da lista
$plugin_url = plugins_url() .'/'. $plugin_slug;
*/
//>>>>>
if (function_exists('is_multisite') AND is_multisite()) {
    return;
}
// >>>>>>>>>>>>>>>>>>>>>>>>>
// call 
/*
function stopbadbots_bill_install()
{
	if (function_exists('is_admin') && function_exists('current_user_can')) {
        if(is_admin() and current_user_can("manage_options")){
			// ob_start();
            $notification_url = "https://wpmemory.com/fix-low-memory-limit/";
            $notification_url2 =
                "https://wptoolsplugin.com/site-language-error-can-crash-your-site/";
            $logo = STOPBADBOTSIMAGES.'/logo.png';
            $plugin_adm_url = admin_url('admin.php?page=stop_bad_bots_plugin');
			//wp-admin/admin.php?page=stop_bad_bots_plugin
            require_once dirname(__FILE__) . "/includes/install-checkup/class_bill_install.php";
			// ob_end_clean();
		}
	}
}
add_action('wp_loaded', 'stopbadbots_bill_install');
*/

//






add_action("wp_head", "bill_install_ajaxurl");
// finished?
// Retrieve the 'bill_minozzi_pre_checkup_finished' option from WordPress options
$finished_time = get_option("bill_minozzi_pre_checkup_finished", "0");

// Check if $finished_time is '0' or cannot be converted to a valid timestamp
if ($finished_time === '0' || !is_numeric($finished_time)) {
    // Set it to 1 year ago if it's '0' or cannot be converted to timestamp
    $finished_time = date('Y-m-d H:i:s', strtotime('-1 year'));
}

// Check if $finished_time is not empty and $bill_debug is false
if (!empty($finished_time) && !$bill_debug) {
    // Calculate the difference in seconds between the current time and $finished_time
    
    
    if (is_numeric($finished_time)) {
        $diff_seconds = time() - $finished_time;
    } else {
        // Attempt to convert $finished_time to a timestamp
        $timestamp = strtotime($finished_time);
        if ($timestamp !== false) {
            $diff_seconds = time() - $timestamp;
        } else {
            $finished_time = date('Y-m-d H:i:s', strtotime('-1 year'));
            $diff_seconds = time() - $finished_time;
        }
    }
    
    //$diff_seconds = time() - $finished_time;
    // Convert 3 months into seconds
    $three_months_seconds = 86400 * 30 * 3; // 30 days * 3 months * 86400 seconds per day
    // If the difference is less than 3 months, cancel the execution of the code
    if ($diff_seconds < $three_months_seconds) {
        return;
    } else {
        // If it's more than 3 months, update $finished_time to an empty string in the option
        update_option("bill_minozzi_pre_checkup_finished", "0");
    }
}
//
//

////////////////////////////////////// dismisss
//
// Retrieve the current page from the query parameters
$current_page = isset($_GET["page"]) ? sanitize_text_field($_GET["page"]) : "";
// Retrieve the 'bill_minozzi_pre_checkup_dismissed' option from WordPress options
$dismissed_time = get_option("bill_minozzi_pre_checkup_dismissed", '0');
// Check if $dismissed_time is '0' or cannot be converted to a valid timestamp
if ($dismissed_time === '0' || !is_numeric($dismissed_time)) {
    // Set it to 2 hours ago if it's '0' or cannot be converted to timestamp
    $dismissed_time = time() - 2 * 3600; // 2 hours ago in seconds
} else {
    //debug2('Valid dismissed time');
}
// Check if we are on the pre-checkup page or if the finished or dismissed POST variables are set
if (
    $current_page !== "bill_pre-checkup" &&
    !isset($_POST["finished"]) &&
    !isset($_POST["dismiss"])
) {
    // Set $bill_wait_time based on $bill_debug
    if ($bill_debug) {
        $bill_wait_time = 60; // Debug mode: 1 minute
    } else {
        $bill_wait_time = 3600; // Production mode: 1 hour
    }
    // Calculate the difference in seconds between the current time and $dismissed_time
    $diff_seconds = time() - $dismissed_time;
    // If the difference is less than the wait time, cancel the execution of the code
    if ($diff_seconds < $bill_wait_time) {
        if (!$bill_debug) {
            return; // Don't show alert if dismissed within the last hour in production mode
        } else {
            // debug2('Continuing execution in debug mode');
        }
    } else {
        // If it's more than the wait time, update the dismissed time to an empty string
        update_option("bill_minozzi_pre_checkup_dismissed", "0");
    }
    // Continue execution here for other conditions or actions
}
// end dismisss
//
// add_action("wp_ajax_bill_install_plugin", "bill_install_plugin");
/////////////// Class Begin //////////////////
class stopbadbots_Bill_Class_Plugins_Install
{
    public function __construct(
        $plugin_slug,
        $notification_url,
        $notification_url2,
        $plugin_text_domain,
        $logo,
        $plugin_adm_url
    ) {
        //
        $this->plugin_slug = $plugin_slug;
        $this->notification_url2 = $notification_url2;
        $this->notification_url = $notification_url;
        $this->plugin_text_domain = $plugin_text_domain;
        $this->logo = $logo;
        $this->plugin_adm_url = $plugin_adm_url;
        // Register the hook to be executed when the plugin is activated
        register_activation_hook(__FILE__, [$this, "plugin_activation"]);
        // Add bill_pre-checkup page
        add_action("admin_menu", [$this, "add_pre_checkup_page"]);
        // Check if the user exited the bill_pre-checkup page without clicking Finished or Dismiss
        add_action("admin_init", [$this, "check_pre_checkup_status"]);
        add_action("admin_enqueue_scripts", [$this, "enqueue_custom_style_and_scripts"]);
    }
    public function enqueue_custom_style_and_scripts()
    {
        // Enfileirar o arquivo style.css
        wp_enqueue_style(
            "custom-plugin-style",
            plugin_dir_url(__FILE__) . "/class_install_styles.css"
        );
        wp_enqueue_script(
            "bill-install-script",
            plugin_dir_url(__FILE__) . "/bill-install-script.js", 
            [],
            false,
            "footer" // Or "admin_footer" for loading in admin area footer
        );
    }
    // Function to be executed when the plugin is activated
    public function plugin_activation()
    {
        wp_safe_redirect(admin_url("?page=bill_pre-checkup"));
        exit();
    }
    // Add bill_pre-checkup page
    public function add_pre_checkup_page()
    {
        add_menu_page(
            "bill_pre-checkup",
            "Installing New Plugin",
            "manage_options",
            "bill_pre-checkup",
            [$this, "pre_checkup_page_content"]
        );
    }
    function show_pre_checkup_alert($slug)
    {
        //
        ?>
        <div class="notice notice-warning is-dismissible bill-installation-msg">
            <?php $msg = "<p></p><big>".
                "The installation of the " . $slug . " plugin is incomplete.";
            echo "<p>" . wp_kses_post($msg) . "</p>" ?>
            <a href="<?php echo esc_url("?page=bill_pre-checkup");?>">Resume Installation.</a>
            or 
                <a href="#" class="bill-dismiss-one-hour">Remember me Later.</a>
            </p>
            <?php wp_nonce_field( 'bill_install_2', 'nonce' ); ?>
            <input type="hidden" id="data-admin-url-msg" value="<?php echo esc_url($this->plugin_adm_url); ?>">
            <p></p></big>
        </div>
        <?php
    }
    public function pre_checkup_page_content()
    {
        $step = isset($_GET["step"]) ? intval(sanitize_text_field($_GET["step"])) : 0; 
        // Check if the image file exists
        if (! empty($this->logo)) {
            echo '<div id="bill-install-logo">';
            echo '<img src="' . esc_attr($this->logo) . '" width="250">';
            echo '</div>';
            //
            //
        }
        // Exibe o conteúdo com base no valor de 'step'
        //
        wp_nonce_field( 'bill_install', 'nonce' );
        switch ($step) { case 1: ?> 
                <div class="bill_install_wrap">
                    <h2><?php echo esc_attr($this->plugin_slug); ?> &nbsp;Step 1 of 3</h2>
                    <p><strong>Server Memory Overview</strong></p>
                    <?php
                    //
                    // Criar uma instância de ex Bill_Class_Diagnose
                    $diagnose_instance = new stopbadbots_Bill_Class_Plugins_Install(
                        $this->notification_url,
                        $this->notification_url2,
                        $this->plugin_text_domain,
                        $this->plugin_slug,
                        $this->logo,
                        $this->plugin_adm_url
                    );
                        $memoryChecker = new MemoryChecker();//
                        $data = $memoryChecker->check_memory();
                    // Check if $data is an array
                    if (is_array($data)) {
                        // Check if each key exists before accessing it
                        if (array_key_exists("msg_type", $data)) {
                            if($data["msg_type"] == "notok")
                               echo "Unable to retrieve memory data from your server. This could be due to a hosting issue.";
                        }
                        if (
                            array_key_exists("free", $data) &&
                            array_key_exists("percent", $data)
                        ) {
                            // Check if free memory is less than 30MB or if the percentage of memory used is above 80%
                            if ($data["free"] < 30 || $data["percent"] > 0.8) {
                                // Change the color of the message to red
                                $data["color"] = "color:red;";
                                // Set the warning message
                                $data["msg_type"] = "warning";
                            }
                            // Display the results
                            echo "Percentage of used memory: " .
                                number_format($data["percent"] * 100, 0) .
                                "%<br>";
                            echo "Free memory: " . esc_attr($data["free"]) . "MB<br>";
                        }
                        // Check if 'usage' key exists before accessing it
                        if (array_key_exists("usage", $data)) {
                            echo "Memory Usage: " . esc_attr($data["usage"]) . "MB<br>";
                        }
                        if (array_key_exists("limit", $data)) {
                            echo "PHP Memory Limit: " . esc_attr($data["limit"]) . "MB<br>";
                        }
                        // Check if 'wp_limit' key exists before accessing it
                        if (array_key_exists("wp_limit", $data)) {
                            echo "WordPress Memory Limit: " .
                                esc_attr($data["wp_limit"]) .
                                "MB<br>";
                        }
                        // Display the status message
                        echo "<br /><strong>" . "Status: " . "</strong>";
                        if ($data["msg_type"] !== "warning") {
                            echo "All good.";
                            echo "<br>";
                        } else {
                            echo '<p style="color: red;">';
                            echo esc_attr__(
                                "Your WordPress Memory Limit is too low, which can lead to critical issues on your site due to insufficient resources. Promptly address this issue before continuing.",
                                'restore-classic-widgets'
                            );
                            echo "</p>";
                            echo "</b>";
                            ?>
                                </b>
                                <a href= "https://wpmemory.com/fix-low-memory-limit/" target="_blank">
                                <?php echo esc_attr__(
                                    "Learn More",
                                    'restore-classic-widgets'
                                ); ?>
                                </a>
                                </p>
                                <br>
                               <?php
                               $all_plugins = get_plugins();
                               $is_wp_memory_installed = false;
                               foreach ($all_plugins as $plugin_info) {
                                   if ($plugin_info["Name"] === "WP Memory") {
                                       $is_wp_memory_installed = true;
                                       break; // Exit the loop once found
                                   }
                               }
                               if (!$is_wp_memory_installed) { ?>
                                    If you'd like help with memory management, this free plugin can help.
                                    <br>
                                    <a href="#" id="bill-install-wpmemory" class="button button-primary bill-install-plugin-now">Install WPmemory Free</a>
                                    <button id="loading-spinner" class="button button-primary" style="display: none;" aria-label="Loading...">
                                <span class="loading-text">Installing...</span>
                                </button>
                          <?php }
                        }
                    } else {
                        echo "Unable to retrieve memory data from your server. This could be due to a hosting issue (2).";
                    }
                    //
                    ?>
                    <div class="bill_install_button-container">
                        <a class="button button-primary" href="<?php echo esc_url(
                            add_query_arg("step", $step - 1)
                        ); ?>">< Prev</a>
                        <a class="button button-primary" href="<?php echo esc_url(
                            add_query_arg("step", $step + 1)
                        ); ?>">Next ></a>
                        <button class="button button-secondary bill-dismiss-one-hour" data-admin-url="<?php echo esc_url(
                            $this->plugin_adm_url 
                                                        ); ?>">Dismiss One Hour</button>
                    </div>
                </div>
                <?php break;case 2: ?>
                <div class="bill_install_wrap">
                    <h2><?php echo esc_attr($this->plugin_slug); ?> &nbsp;Step 2 of 3 </h2>
                    <p><strong>Server Errors and Warnings</strong></p>
                    <?php
                    // Criar uma instância de ex Bill_Class_Diagnose
                    $diagnose_instance = new stopbadbots_Bill_Class_Plugins_Install(
                        $this->notification_url,
                        $this->notification_url2,
                        $this->plugin_text_domain,
                        $this->plugin_slug,
                        $this->logo,
                        $this->plugin_adm_url
                    );
                    //
                    // Chamar o método check_memory() da instância criada
                    // da class ErrorChecker
                    //
                    $errorChecker = new ErrorChecker();//
                    $errors_result  = $errorChecker->bill_check_errors_today(2);
                    if ($errors_result) {
                        echo '<p style="color: red;">';
                        echo "Errors or warnings have been found in your server's error log for the last 48 hours. We recommend examining these errors and addressing them immediately to avoid potential issues, ensuring greater stability for your site.";
                        echo "<br />";
                        echo "</p>";
                        ?>
                        <a href="https://wptoolsplugin.com/site-language-error-can-crash-your-site/" target="_blank">
                            <?php echo esc_attr__(
                                "Learn More",
                                'restore-classic-widgets'
                            ); ?>
                        </a>
                        </p>
                        <br>
                        <?php
                        $all_plugins = get_plugins();
                        $is_wp_tools_installed = false;
                        foreach ($all_plugins as $plugin_info) {
                            if ($plugin_info["Name"] === "wptools") {
                                $is_wp_tools_installed = true;
                                break; // Exit the loop once found
                            }
                        }
                        if (!$is_wp_tools_installed) { ?>
                            If you'd like help with errors management, this free plugin can help.
                            <br>
                            <a href="#" id="bill-install-wptools" class="button button-primary bill-install-wpt-plugin-now">Install WPtools Free</a>
                            <button id="loading-spinner" class="button button-primary" style="display: none;" aria-label="Loading...">
                                <span class="loading-text">Loading...</span>
                            </button>
                        <?php }
                    } else {
                        echo "No errors or warnings have been found in the last 48 hours. However, it's advisable to examine the error log for a longer time frame.";
                    }
                    ?>
                    <div class="bill_install_button-container">
                        <a class="button button-primary" href="<?php echo esc_url(
                            add_query_arg("step", $step - 1)
                        ); ?>">< Prev</a>
                        <a class="button button-primary" href="<?php echo esc_url(
                            add_query_arg("step", $step + 1)
                        ); ?>">Next ></a>
                        <button class="button button-secondary bill-dismiss-one-hour" data-admin-url="<?php echo esc_url(
                             $this->plugin_adm_url ); ?>">Dismiss One Hour</button>
                    </div>
                </div>
                <?php break;case 3: ?> 
                <div class="bill_install_wrap">
                <input type="hidden" id="main_slug" name="main_slug" value="<?php echo esc_attr( $this->plugin_slug ); ?>">
                <input type="hidden" id="data-admin-url-finished" value="<?php echo esc_url($this->plugin_adm_url); ?>">
                <div id="bill-wrap-install-modal" class="bill-wrap-install-modal" style="display:none">
                    <h3>Please wait</h3>
                    <big>
                        <h4>
                            Installing plugin <div id="billpluginslugModal">...</div>
                        </h4>
                    </big>
                    <img src="/wp-admin/images/wpspin_light-2x.gif" id="billimagewaitfbl" style="display:none;margin-left:0px;margin-top:0px;" />
                    <br />
                </div>
                    <h2><?php echo esc_attr($this->plugin_slug); ?> &nbsp;Step 3/3 (Final)</h2>
                    <p><strong>Server Security and Performance</strong></p>
                    <p>
                    Our first plugin was launched over 10 years ago, 
                    and we've witnessed the <strong>increasing complexity</strong> of user needs 
                    and the entire computing landscape. That's why we've continuously 
                    updated all our plugins.
                     Therefore, we've developed <strong>new solutions</strong> 
                     such as <strong>protection against bot attacks that cause server overloads, 
                     real analytics filtering bot visits, reports on page loading times, 
                     easy database backups, spam form blockers, hacker protection, 
                     and much more.</strong>
                     Below is the list of these free plugins, all installable with just one click, 
                     <strong>seamlessly integrating to enhance your website's performance.</strong>
                    </p>
                    <div class="bill_install_bill_install_button-container">
                        <a class="button button-primary" href="<?php echo esc_url(
                            add_query_arg("step", $step - 1)
                        ); ?>">< Prev</a>
                        <button class="button button-primary bill-install-finished">Finished</button>
                    </div>
                    <hr>
                    <?php
                    //
                    //$plugin = new \Bill_show_more_plugins();
                    $plugin = new \stopbadbots_Bill_show_more_plugins();
                    $plugin->bill_show_plugins();
        ?>
                </div>
                <?php break;default:// Conteúdo padrão para outros passos ou quando 'step' não está definido // Adicione casos para outros passos conforme necessário
         ?>
                <div class="bill_install_wrap">
                    <h2><?php echo esc_attr($this->plugin_slug); ?> &nbsp;Welcome</h2>
                    <p>
                    This installer will guide you to ensure that our plugin is perfectly installed and that your <strong>server environment allows its proper functioning </strong>.
                    By proceeding, you agree that you have read and understood the 
                    <a href="https://siterightaway.net/terms-of-use-of-our-plugins-and-themes/" target="_blank">terms of use</a>
                    of our plugins.
                    <br /><br />
                    <strong>To ensure a smooth and successful process, please complete each step (3 steps) carefully and then click Finished.</strong>
                    </p>
                    <div class="bill_install_button-container">
                        <a class="button button-primary" href="<?php echo esc_url(
                            add_query_arg("step", $step + 1)
                        ); ?>">Next ></a>
                        <!-- admin_url() . -->       
                        <button class="button button-secondary bill-dismiss-one-hour" data-admin-url="<?php echo esc_url(
                            $this->plugin_adm_url
                        ); ?>">Dismiss One Hour</button>
                    </div>
                </div>
                <?php break;
        }
        ?>
        <input type="hidden" id="main_slug" name="main_slug" value="<?php echo esc_attr( $this->plugin_slug ); ?>">
        <input type="hidden" id="data-admin-url" value="<?php echo esc_url($this->plugin_adm_url); ?>">
        <?php
    }
    // Check if the user exited the bill_pre-checkup page without clicking Finished or Dismiss
    public function check_pre_checkup_status()
    {
        $current_page = isset($_GET["page"]) ? $_GET["page"] : "";
        if (
            $current_page !== "bill_pre-checkup" &&
            !isset($_POST["finished"]) &&
            !isset($_POST["dismiss"])
        ) {
            // Display the alert
            // add_action("admin_notices", [$this, "show_pre_checkup_alert"]);
            $self = $this;
            $plugin_slug = $this->plugin_slug;
            // Agora use $self e $plugin_slug dentro da função anônima
            add_action("admin_notices", function () use ($self, $plugin_slug) {
                $self->show_pre_checkup_alert($plugin_slug);
            });
        }
    }
    // Display the bill_pre-checkup alert
} // end class
// $plugin_install = new Bill_Class_Plugins_Install('your-plugin-slug');
$plugin_file = plugin_basename(__FILE__);
$plugin_install = new stopbadbots_Bill_Class_Plugins_Install(
    $plugin_slug,
    $notification_url,
    $notification_url2,
    $plugin_text_domain,
    $logo,
    $plugin_adm_url
);
if(!function_exists('bill_dismiss_pre_checkup_handler')){
    function bill_dismiss_pre_checkup_handler()
    {
        //From alert
        if (!isset($_POST['nonce']))
        wp_die('Invalid nonce (1).');
        if (!wp_verify_nonce(sanitize_text_field($_POST['nonce']), 'bill_install') && !wp_verify_nonce($_POST['nonce'], 'bill_install_2')) {
            // Se o nonce não for válido, encerre a execução e retorne uma mensagem de erro
            //('Invalid nonce.');
            wp_die('Invalid nonce (2).');
        }
        // Update the option here
        update_option("bill_minozzi_pre_checkup_dismissed", time());
        // echo wp_json_encode(["success" => true]);
        wp_die('OK!!!!'); // Exit after sending JSON response
    }
}
//
//
if(!function_exists('bill_finished_pre_checkup_handler')){
    function bill_finished_pre_checkup_handler()
    {
        if (!isset($_POST['nonce']) || !wp_verify_nonce(sanitize_text_field($_POST['nonce']), 'bill_install')) {
            wp_die('Invalid nonce.');
        }
        // Update the option here
        update_option("bill_minozzi_pre_checkup_finished", time());

        $finished_time = get_option("bill_minozzi_pre_checkup_finished", "0");
// debug2($finished_time);


       // debug2();

        wp_die("OK");
    }
}
if (!class_exists('class_bill_show_plugins')) {
class class_bill_show_plugins
{
    public function bill_plugin_installed($slug)
    {
        $all_plugins = get_plugins();
        foreach ($all_plugins as $key => $value) {
            $plugin_file = $key;
            $slash_position = strpos($plugin_file, '/');
            $folder = substr($plugin_file, 0, $slash_position);
            // match FOLDER against SLUG
            if ($slug == $folder) {
                return true;
            }
        }
        return false;
    }
    public function bill_show_plugins()
    {
        $plugins_to_install = [];
        $plugins_to_install[0]["Name"] = "Anti Hacker Plugin";
        $plugins_to_install[0]["Description"] =
            "Cyber Attack Protection. Firewall, Malware Scanner, Login Protect, block user enumeration and TOR, disable Json WordPress Rest API, xml-rpc (xmlrpc) & Pingback and more security tools...";
        $plugins_to_install[0]["image"] =
            "https://ps.w.org/antihacker/assets/icon-256x256.gif?rev=2524575";
        $plugins_to_install[0]["slug"] = "antihacker";
        $plugins_to_install[1]["Name"] = "Stop Bad Bots";
        $plugins_to_install[1]["Description"] =
            "Stop Bad Bots, Block SPAM bots, Crawlers and spiders also from botnets. Save bandwidth, avoid server overload and content steal. Blocks also by IP. Visitor Analytics with Separated Bots";
        $plugins_to_install[1]["image"] =
            "https://ps.w.org/stopbadbots/assets/icon-256x256.gif?rev=2524815";
        $plugins_to_install[1]["slug"] = "stopbadbots";
        $plugins_to_install[2]["Name"] = "WP Tools";
        $plugins_to_install[2]["Description"] =
            "Enhanced: Unlock Over 47 Essential Tools! Your Ultimate Swiss Army Knife for Elevating Your Website to the Next Level. Also, check for errors, including JavaScript errors. Page Lad Report.";
        $plugins_to_install[2]["image"] =
            "https://ps.w.org/wptools/assets/icon-256x256.gif?rev=2526088";
        $plugins_to_install[2]["slug"] = "wptools";
        $plugins_to_install[3]["Name"] = "reCAPTCHA For All";
        $plugins_to_install[3][
            "Description"
        ] = "Protect ALL Selected Pages of your site against bots (spam, hackers, fake users and other types of automated abuse)
	  with Cloudflare Turnstile or invisible reCaptcha V3 (Google). You can also block visitors from China.";
        $plugins_to_install[3]["image"] =
            "https://ps.w.org/recaptcha-for-all/assets/icon-256x256.gif?rev=2544899";
        $plugins_to_install[3]["slug"] = "recaptcha-for-all";
        $plugins_to_install[4]["Name"] = "WP Memory";
        $plugins_to_install[4]["Description"] =
            "Check High Memory Usage, Memory Limit, PHP Memory, show result in Site Health Page and help to fix php low memory limit. In-page Memory Usage Report.";
        $plugins_to_install[4]["image"] =
            "https://ps.w.org/wp-memory/assets/icon-256x256.gif?rev=2525936";
        $plugins_to_install[4]["slug"] = "wp-memory";
        $plugins_to_install[5]["Name"] = "Database Backup";
        $plugins_to_install[5]["Description"] =
            "Quick and Easy Database Backup with a Single Click. Verify Tables and Schedule Automatic Backups.";
        $plugins_to_install[5]["image"] =
            "https://ps.w.org/database-backup/assets/icon-256x256.gif?rev=2862571";
        $plugins_to_install[5]["slug"] = "database-backup";
        $plugins_to_install[6]["Name"] = "Database Restore Bigdump";
        $plugins_to_install[6]["Description"] =
            "Database Restore with BigDump script. The ideal solution for restoring very large databases securely.";
        $plugins_to_install[6]["image"] =
            "https://ps.w.org/bigdump-restore/assets/icon-256x256.gif?rev=2872393";
        $plugins_to_install[6]["slug"] = "bigdump-restore";
        $plugins_to_install[7]["Name"] = "Easy Update URLs";
        $plugins_to_install[7]["Description"] =
            "Fix your URLs at database after cloning or moving sites.";
        $plugins_to_install[7]["image"] =
            "https://ps.w.org/easy-update-urls/assets/icon-256x256.gif?rev=2866408";
        $plugins_to_install[7]["slug"] = "easy-update-urls";
        $plugins_to_install[8]["Name"] = "S3 Cloud Contabo";
        $plugins_to_install[8]["Description"] =
            "Connect you with your Contabo S3-compatible Object Storage.";
        $plugins_to_install[8]["image"] =
            "https://ps.w.org/s3cloud/assets/icon-256x256.gif?rev=2855916";
        $plugins_to_install[8]["slug"] = "s3cloud";
        $plugins_to_install[9]["Name"] = "Tools for S3 AWS Amazon";
        $plugins_to_install[9]["Description"] =
            "Connect you with your Amazon S3-compatible Object Storage.";
        $plugins_to_install[9]["image"] =
            "https://ps.w.org/toolsfors3/assets/icon-256x256.gif?rev=2862487";
        $plugins_to_install[9]["slug"] = "toolsfors3";
        $plugins_to_install[10]["Name"] = "Hide Site Title";
        $plugins_to_install[10]["Description"] =
            "The Hide Site Title Remover plugin allows you to easily remove titles from your WordPress posts and pages, without affecting menus or titles in the admin area.";
        $plugins_to_install[10]["image"] =
            "https://ps.w.org/restore_classic_widgets/assets/icon-256x256.gif?rev=2862487";
        $plugins_to_install[10]["slug"] = "restore_classic_widgets";
        $plugins_to_install[11]["Name"] = "Disable WordPress Sitemap";
        $plugins_to_install[11]["Description"] =
            "The sitemap is automatically created by WordPress from version 5.5. This plugin offers you the option to disable it, allowing you to use another SEO plugin to generate it if desired.";
        $plugins_to_install[11]["image"] =
            "https://ps.w.org/disable-wp-sitemap/assets/icon-256x256.gif?rev=2862487";
        $plugins_to_install[11]["slug"] = "disable-wp-sitemap";
        ?>
        <div style="padding-right:20px;">
		<br>
		<h2>Enhance: Free, Convenient Plugin Suite by the Same Author. Instant Installation: A Single Click on the Install Button.</h2>
		<table style="margin-right:20px; border-spacing: 0 25px; " class="widefat" cellspacing="0" id="bill_class_install-more-plugins-table">
			<tbody class="bill_class_install-more-plugins-body">
				<?php
        $counter = 0;
        $total = count($plugins_to_install);
     for ($i = 0; $i < $total; $i++) {
        if ($counter % 2 == 0) {
            echo '<tr style="background:#f6f6f1;">';
        }
        ++$counter;
        if ($counter % 2 == 1) {
            echo '<td style="max-width:140px; max-height:140px; padding-left: 40px;" >';
        } else {
            echo '<td style="max-width:140px; max-height:140px;" >';
        }
        echo '<img style="width:100px;" src="' .
            esc_url($plugins_to_install[$i]["image"]) .
            '">';
        echo "</td>";
        echo '<td style="width:40%;">';
        echo "<h3>" . esc_attr($plugins_to_install[$i]["Name"]) . "</h3>";
        echo esc_attr($plugins_to_install[$i]["Description"]);
        echo "<br>";
        echo "</td>";
        echo '<td style="max-width:140px; max-height:140px;" >';
        if ($this->bill_plugin_installed($plugins_to_install[$i]["slug"])) {
            echo '<a href="#" class="button activate-now">Installed</a>';
        } else {
            echo '<a href="#" id="_' .
                esc_attr($plugins_to_install[$i]["slug"]) .
                '"class="button button-primary bill-install-now">Install</a>';
        }
        echo "</td>";
        if ($counter % 2 == 1) {
            echo '<td style="width; 100px; border-left: 1px solid gray;">';
            echo "</td>";
        }
        if ($counter % 2 == 0) {
            echo "</tr>";
        }
    }
    ?>
			</tbody>
		</table>
        <center><big>
        <a href="https://profiles.wordpress.org/sminozzi/#content-plugins" target="_blank">Discover All Plugins</a>
        &nbsp;&nbsp;
        <a href="https://profiles.wordpress.org/sminozzi/#content-themes" target="_blank">Discover All Themes</a>
    </big> </center>
        </div>
    <?php
    }
} // end class
} //end if class exists...
$plugin_displayer = new class_bill_show_plugins();
//bill_install_ajaxurl();
function bill_dismiss_pre_checkup_handler2()
{   wp_die('OK!!');
}
add_action('wp_ajax_bill_dismiss_pre_checkup_handler', __NAMESPACE__ . '\\bill_dismiss_pre_checkup_handler');
add_action('wp_ajax_bill_finished_pre_checkup_handler', __NAMESPACE__ . '\\bill_finished_pre_checkup_handler');
