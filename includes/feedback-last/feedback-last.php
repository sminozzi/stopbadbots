<?php namespace  StopBadBots  {
    if (!defined("ABSPATH")) {
        exit(); // Exit if accessed directly
    }
   // $bill_debug = true;
    $bill_debug = false;
    //
    //

	if (function_exists('is_multisite') AND is_multisite()) {
		return;
	}
// >>>>>>>>>>>>>>>>>>>>>>>>>
// call 
/*
function wpmemory_load_feedback()
{
	if (function_exists('is_admin') && function_exists('current_user_can')) {
        if(is_admin() and current_user_can("manage_options")){
			// ob_start();
			require_once dirname(__FILE__) . "/includes/feedback-last/feedback-last.php";
			// ob_end_clean();
		}
	}
}
add_action('wp_loaded', 'wpmemory_load_feedback');
*/
//>>>>>>>>>>>>>>>>>>>>>>>>
// debug2();
    // https://minozzi.eu/wp-admin/plugins.php?action=deactivate&plugin=stopbadbots%2Fstopbadbots.php&plugin_status=all&paged=1&s&_wpnonce=ef9a34aa27
    if (__NAMESPACE__ == "HideSiteTitle") {
        define(__NAMESPACE__ . "\PRODCLASS", "stopbadbots");
        define(__NAMESPACE__ . "\VERSION", HIDE_SITE_TITLE_VERSION);
        // define( __NAMESPACE__ . '\PLUGINHOME', 'https://wptoolsplugin.com' );
        define(__NAMESPACE__ . "\PRODUCTNAME", "Hide Site Title Plugin");
        //define(__NAMESPACE__ . "\LANGUAGE", "wptools");
        $admin_url = admin_url('tools.php?page=stopbadbots&active_tab=3');
        define(__NAMESPACE__ . "\PAGE", $admin_url);
        define(__NAMESPACE__ . "\OPTIN", "wp_tools_optin");
        define(__NAMESPACE__ . "\LAST", "wp_tools_last_feedback");
        define(__NAMESPACE__ . "\URL", HIDE_SITE_TITLE_URL);
        // https://minozzi.eu/wp-admin/plugins.php?action=deactivate&plugin=stopbadbots%2Fstopbadbots.php&plugin_status=all&paged=1&s&_wpnonce=ef9a34aa27
        // https://minozzi.eu/wp-admin/tools.php?page=stopbadbots&active_tab=3
    }
    // https://minozzi.eu/wp-admin/plugins.php
    // ?action=deactivate&plugin=stopbadbots%2Fstopbadbots.php&plugin_status=all&paged=1&s&_wpnonce=e64444e88d
    if (__NAMESPACE__ == "StopBadBots") {
        define(__NAMESPACE__ . "\PRODCLASS", "stopbadbots");
        define(__NAMESPACE__ . "\VERSION", STOPBADBOTSVERSION);
        define(__NAMESPACE__ . "\PRODUCTNAME", "Stop Bad Bots Plugin");
        $admin_url = admin_url('admin.php?page=stop_bad_bots_plugin&tab=more');
        //https://minozzi.eu/wp-admin/admin.php?page=stop_bad_bots_plugin&tab=more
        define(__NAMESPACE__ . "\PAGE", $admin_url);
        define(__NAMESPACE__ . "\URL", STOPBADBOTSURL);
        // page=stopbadbots_new_more_plugins
        define(__NAMESPACE__ . "\LAST", "stopbadbots_last_feedback");
        //
    }
    //
    //
    if (__NAMESPACE__ == "RecaptchaForAll_last_feedback") {
        define(__NAMESPACE__ . "\PRODCLASS", "recaptcha-for-all");
        define(__NAMESPACE__ . "\VERSION", RECAPTCHA_FOR_ALLVERSION);
        define(__NAMESPACE__ . "\PRODUCTNAME", "Recaptcha For All Plugin");
        $admin_url = admin_url('tools.php?page=recaptcha_for_all_admin_page&tab=tools');
        //https://minozzi.eu/wp-admin/tools.php?page=recaptcha_for_all_admin_page&tab=tools&_wpnonce=c075808fd9 
       //
       //
       //debug2($admin_url);

        define(__NAMESPACE__ . "\PAGE", $admin_url);
        define(__NAMESPACE__ . "\URL", RECAPTCHA_FOR_ALLURL);
        // page=cardealers_new_more_plugins
        define(__NAMESPACE__ . "\LAST", "recaptcha_for_all_last_feedback");

    }

    if (__NAMESPACE__ == "CarDealer_last_feedback") {
        define(__NAMESPACE__ . "\PRODCLASS", "cardealer");
        define(__NAMESPACE__ . "\VERSION", CARDEALERVERSION);
        define(__NAMESPACE__ . "\PRODUCTNAME", "Car Dealer Plugin");
        $admin_url = admin_url('admin.php?page=car_dealer_plugin&tab=tools&customize_changeset_uuid=');
        define(__NAMESPACE__ . "\PAGE", $admin_url);
        define(__NAMESPACE__ . "\URL", CARDEALERURL);
        // page=cardealers_new_more_plugins
        define(__NAMESPACE__ . "\LAST", "cardealer_last_feedback");
        //
       // https://minozzi.eu/wp-admin/tools.php?page=cardealers&active_tab=3
    }

    if (__NAMESPACE__ == "wpmemory_last_feedback") {
        define(__NAMESPACE__ . "\PRODCLASS", "wp_memory");
        define(__NAMESPACE__ . "\VERSION", WPMEMORYVERSION);
        define(__NAMESPACE__ . "\PLUGINHOME", "https://wpmemory.com");
        define(__NAMESPACE__ . "\PRODUCTNAME", "WP Memory Plugin");
        define(__NAMESPACE__ . "\LANGUAGE", "wp-memory");
        define(__NAMESPACE__ . "\PAGE", "settings");
        define(__NAMESPACE__ . "\OPTIN", "wp_memor_optin");
        define(__NAMESPACE__ . "\LAST", "wp_memory_last_feedback");
        define(__NAMESPACE__ . "\URL", WPMEMORYURL);
    }
    if (__NAMESPACE__ == "wptools_last_feedback") {
        define(__NAMESPACE__ . "\PRODCLASS", "wptools");
        define(__NAMESPACE__ . "\VERSION", WPTOOLSVERSION);
        // define( __NAMESPACE__ . '\PLUGINHOME', 'https://wptoolsplugin.com' );
        define(__NAMESPACE__ . "\PRODUCTNAME", "WP Tools Plugin");
        define(__NAMESPACE__ . "\LANGUAGE", "wptools");
        define(__NAMESPACE__ . "\PAGE", "settings");
        define(__NAMESPACE__ . "\OPTIN", "wp_tools_optin");
        define(__NAMESPACE__ . "\LAST", "wp_tools_last_feedback");
        define(__NAMESPACE__ . "\URL", WPTOOLSURL);
    }
//
    if($bill_debug)
      update_option(LAST, '1');


    $last_feedback =  sanitize_text_field(get_option(LAST, "1"));
    $last_feedback =  intval(  $last_feedback);

    //debug2($last_feedback );


    if ($last_feedback === '0' || !is_numeric($last_feedback)) {
        // Set it to 2 hours ago if it's '0' or cannot be converted to timestamp
        $last_feedback = time() - (2 * 24 * 3600); // 2 days ago in seconds
    } else {
        //debug2('Valid dismissed time');
    }



    //if ($last_feedback < 2) {
    if ($last_feedback < 2) {
        $delta = 0;
        $last_feedback = time();
    } else {
        $delta = (1 * 24 * 3600);
    }



  
    


    if ($last_feedback + $delta <= time()) {
        // return;
        define(__NAMESPACE__ . "\WPMSHOW", true);
    } else {
        define(__NAMESPACE__ . "\WPMSHOW", false);
        return;
    }

    // debug2(WPMSHOW);



    class Bill_mConfig
    {
        protected static $namespace = __NAMESPACE__;
        protected static $bill_plugin_url = URL;
        protected static $bill_class = PRODCLASS;
        protected static $bill_prod_veersion = VERSION;
        protected static $plugin_slug;
        //protected static $sbb_show_or_not = SBBNOTSHOW;
        function __construct()
        {
            add_action("load-plugins.php", [__CLASS__, "init"]);
            add_action("wp_ajax_bill_feedback", [__CLASS__, "feedback"]);
        }
        public static function get_plugin_slug() {
            // Verificar se já calculamos o slug antes
            if (isset(self::$plugin_slug)) {
                return self::$plugin_slug;
            }
            // Obter o diretório completo do plugin
            $plugin_dir = plugin_dir_path(__FILE__);
            // Verificar se o diretório está dentro de WP_PLUGIN_DIR ou WPMU_PLUGIN_DIR
            if (strpos($plugin_dir, WP_PLUGIN_DIR) === 0) {
                $relative_path = str_replace(WP_PLUGIN_DIR, '', $plugin_dir);
            } elseif (strpos($plugin_dir, WPMU_PLUGIN_DIR) === 0) {
                $relative_path = str_replace(WPMU_PLUGIN_DIR, '', $plugin_dir);
            } else {
                return ''; // Não está em um diretório reconhecido de plugins
            }
            // Remover barras iniciais, se houver
            $relative_path = ltrim($relative_path, '/');
            // Dividir o caminho relativo em partes
            $path_parts = explode('/', $relative_path);
            // O slug do plugin é a primeira parte do caminho relativo
            self::$plugin_slug = $path_parts[0];
            return self::$plugin_slug;
        }
        public static function init()
        {
            add_action("admin_notices", [__CLASS__, "message"]);
            add_action("admin_head", [__CLASS__, "register"]);
            add_action("admin_footer", [__CLASS__, "enqueue"]);
        }
        public static function register()
        {
            wp_enqueue_style(
                PRODCLASS,
                URL . "includes/feedback-last/feedback-last.css"
            );
            if (WPMSHOW) {
                wp_register_script(
                    PRODCLASS,
                    URL . "includes/feedback-last/feedback-last.js",
                    ["jquery"],
                    VERSION,
                    true
                );
            }
        }
        public static function enqueue()
        {
            wp_enqueue_style(PRODCLASS);
            wp_enqueue_script(PRODCLASS);
            // var_dump(__LINE__);
        }
        //


        public static function message()
        {
            if (!update_option(LAST, time())) {
                add_option(LAST, time());
            }
?>
            <div class="<?php echo esc_attr(
                            PRODCLASS
                        ); ?>-wrap-deactivate" style="display:none">
                <div class="bill-vote-gravatar"><a href="https://profiles.wordpress.org/sminozzi" target="_blank"><img src="https://en.gravatar.com/userimage/94727241/31b8438335a13018a1f52661de469b60.jpg?size=100" alt="Bill Minozzi" width="70" height="70"></a></div>
                <div class="bill-vote-message">
                    <?php
                    echo '<h2 style="color:blue;">';
                    // echo esc_attr(PRODUCTNAME);
                    //echo "<br />";
                    echo esc_attr__("What can we do to resolve the problem you're facing?", "stopbadbots");
                    echo "</h2>";
                    ?>
                    <big><strong>
                            <?php esc_attr_e("Depending on your response, we can help keep the plugin running smoothly for you immediately!", "stopbadbots"); ?>
                        </strong></big>
                    <!-- chat -->
                    <div id="chat-box">
                        <div id="chat-header">
                            <h2><?php echo esc_attr__("Artificial Intelligence Support Chat for Issues and Solutions", "stopbadbots"); ?></h2>
                        </div>
                        <div id="gif-container">
                            <div class="spinner999"></div>
                        </div> <!-- Onde o efeito será exibido -->
                        <div id="chat-messages"></div>
                        <div id="error-message" style="display:none;"></div> <!-- Mensagem de erro -->
                        <form id="chat-form">
                            <input type="hidden" id="chat-type" value="last-feedback" />
                            <!-- Novo parâmetro -->
                            <input type="text" id="chat-input" placeholder="<?php echo esc_attr__('Enter your message...', 'stopbadbots'); ?>" />
                            <button type="submit"><?php echo esc_attr__('Send', 'stopbadbots'); ?></button>
                        </form>
                    </div>
                </div>
                <br>
                <div class="bill-minozzi-button-group">
                    <a href="<?php echo esc_url(PAGE); ?>" class="button button-primary <?php echo esc_attr(PRODCLASS); ?>-close-submit_lf discover-plugins-btn">
                        <?php esc_attr_e("Discover New FREE Plugins", "stopbadbots"); ?>
                    </a>
                    <a href="https://BillMinozzi.com/dove/" class="button button-primary <?php echo esc_attr(PRODCLASS); ?>-close-dialog_lf support-page-btn">
                        <?php esc_attr_e("Support Page", "stopbadbots"); ?>
                    </a>
                    <a href="#" class="button <?php echo esc_attr(PRODCLASS); ?>-close-dialog_lf cancel-btn_feedback">
                        <?php esc_attr_e("Cancel", "stopbadbots"); ?>
                    </a>
                    <a href="#" class="button <?php echo esc_attr(PRODCLASS); ?>-deactivate_lf deactivate-btn">
                        <?php esc_attr_e("Just Deactivate", "stopbadbots"); ?>
                    </a>
                </div>
                <br><br>
            </div>
<?php
        } // end function message



    } //end class
    new Bill_mConfig();
    $stringtime = strval(time());
    //debug2(LAST);
    if (!update_option(LAST, $stringtime)) {
        add_option(LAST, $stringtime);
    }
//
    //$last_feedback =  sanitize_text_field(get_option(LAST, "1"));
    //debug2($last_feedback);
} // End Namespace ...
//
?>
