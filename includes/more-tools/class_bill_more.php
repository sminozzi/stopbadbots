<?php // namespace stopbadbots_BillMore {
if (!defined("ABSPATH")) {
    exit();
} // Exit if accessed directly
/*
$plugin_path = trailingslashit( dirname( plugin_basename( __FILE__ ) ) ); 
$parts = explode('/', rtrim($plugin_path, '/')); // Divide a string em partes usando '/' como delimitador
$plugin_slug = reset($parts); // Obtém o primeiro elemento da lista
$plugin_url = plugins_url() .'/'. $plugin_slug;
*/
// >>>>>>>>>>>>>>>>  conferir e
// ver se tudo scaped e sanitized...
if (function_exists("is_multisite") and is_multisite()) {
    return;
}
/*
// >>>>>>>>>>>>>>>>>>>>>>>>>>>>>>.
// a chamada...
// more...
function stopbadbots_bill_more()
{
    if (function_exists('is_admin') && function_exists('current_user_can')) {
        if(is_admin() and current_user_can("manage_options")){
            $declared_classes = get_declared_classes();
            foreach ($declared_classes as $class_name) {
                if (strpos($class_name, "Bill_show_more_plugins") !== false) {
                    return;
                }
            }
            require_once dirname(__FILE__) . "/includes/more-tools/class_bill_more.php";
        }
    }
}
add_action("init", "stopbadbots_bill_more");
// >>>>>>>>>>>>>>>>>>>>>>>
*/
class stopbadbots_Bill_Minozzi_Add_Ajax_Url
{
    public function __construct()
    {
        // Current class name
        $current_class = __CLASS__;
        // Fill an array with the names of all existing classes
        $classes = get_declared_classes();
        // Filter classes that contain "Bill_Minozzi_add_ajax_url" in the name and are not the current class
        $matching_classes = array_filter($classes, function ($class) use ($current_class) {
            return strpos($class, 'Bill_Minozzi_add_ajax_url') !== false && $class !== $current_class;
        });
        // Check if the function has already been defined in any of the found classes
        $function_defined = false;
        foreach ($matching_classes as $class) {
            if (function_exists($class)) {
                $function_defined = true;
                break;
            }
        }
        // If the function is not defined, define it
        if (!function_exists('Bill_Minozzi_add_ajax_url')) {
            function Bill_Minozzi_add_ajax_url()
            {
                echo '<script type="text/javascript">
                var ajaxurl = "' . esc_attr(admin_url("admin-ajax.php")) . '";
                </script>';
            }
            add_action("wp_head", "Bill_Minozzi_add_ajax_url");
        }
    }
}
// Instantiate the class
$stopbadbots_bill_minozzi_add_ajax_url = new stopbadbots_Bill_Minozzi_Add_Ajax_Url();
class stopbadbots_Bill_show_more_plugins
{
    //
    public function __construct()
    {
        add_action("admin_enqueue_scripts", [$this, "enqueue_scripts"]);
        //    add_action("wp_ajax_bill_install_plugin2", "bill_install_plugin2");
        // add_action('wp_ajax_bill_install_plugin2', array('\stopbadbots_BillMore\Bill_show_more_plugins', 'bill_install_plugin2'));
        //add_action('wp_ajax_bill_install_plugin2', __NAMESPACE__ . '\\bill_install_plugin2');
        //add_action("wp_ajax_bill_feedback", [__CLASS__, "feedback"]);
        // work add_action('wp_ajax_bill_install_plugin2', '\stopbadbots_BillMore\bill_install_plugin2');
    }
    public function bill_plugin_installed($slug)
    {
        if (!function_exists("get_plugins")) {
            // srequire_once ABSPATH . 'wp-admin/includes/plugin.php';
        }
        $all_plugins = \get_plugins();
        foreach ($all_plugins as $key => $value) {
            $plugin_file = $key;
            $slash_position = strpos($plugin_file, "/");
            $folder = substr($plugin_file, 0, $slash_position);
            // match FOLDER against SLUG
            if ($slug == $folder) {
                return true;
            }
        }
        return false;
    }
    public function enqueue_scripts()
    {
        // Register and enqueue the script here
        $plugin_path = trailingslashit(dirname(plugin_basename(__FILE__)));
        $parts = explode("/", rtrim($plugin_path, "/")); // Divide a string em partes usando '/' como delimitador
        $plugin_slug = reset($parts); // Obtém o primeiro elemento da lista
        $plugin_url = plugins_url() . "/" . $plugin_slug;
        $style_handle = substr(
            md5_file(plugin_dir_path(__FILE__) . "/more.css"),
            0,
            8
        );
        wp_enqueue_style($style_handle, plugin_dir_url(__FILE__) . "/more.css");
        wp_register_script(
            $style_handle . "-js",
            plugin_dir_url(__FILE__) . "/more.js",
            ["jquery"]
        );
        wp_enqueue_script($style_handle . "-js");
        wp_register_script(
            "bill-js-toast24",
            $plugin_url . "/assets/js/jquery.toast.js",
            false
        );
        wp_enqueue_script("bill-js-toast24");
    }
    public function bill_show_plugins()
    {

        $plugins_to_install = [];
        $plugins_to_install[0]["Name"] = "Antibots Light";
        $plugins_to_install[0]["Description"] =
            "Beginner-Friendly Plugin: Easy-to-install bot blocker with a simple setup in just 3-4 clicks, ideal for beginners. Block SPAM bots and spiders. Avoid server overload and content steal";
        $plugins_to_install[0]["image"] =
            "https://ps.w.org/antibots/assets/icon-256x256.gif";
        $plugins_to_install[0]["slug"] = "antibots";

        $plugins_to_install[1]["Name"] = "Stop Bad Bots Advanced";
        $plugins_to_install[1]["Description"] =
            "Advanced Plugin: Powerful bot blocker with extensive configuration options, designed for experienced users. Block SPAM bots and spiders. Avoid server overload and content steal";
        $plugins_to_install[1]["image"] =
            "https://ps.w.org/stopbadbots/assets/icon-256x256.gif?rev=2524815";
        $plugins_to_install[1]["slug"] = "stopbadbots";

        $plugins_to_install[2]["Name"] = "Anti Hacker Plugin";
        $plugins_to_install[2]["Description"] =
            "Cyber Attack Protection. Firewall, Malware Scanner, Login Protect, block user enumeration and TOR, disable Json WordPress Rest API, xml-rpc (xmlrpc) & Pingback and more security tools...";
        $plugins_to_install[2]["image"] =
            "https://ps.w.org/antihacker/assets/icon-256x256.gif?rev=2524575";
        $plugins_to_install[2]["slug"] = "antihacker";

        $plugins_to_install[3]["Name"] = "WP Tools";
        $plugins_to_install[3]["Description"] =
            "Enhanced: Unlock Over 47 Essential Tools! Your Ultimate Swiss Army Knife for Elevating Your Website to the Next Level. Also, check for errors, including JavaScript errors. Page Lad Report.";
        $plugins_to_install[3]["image"] =
            "https://ps.w.org/wptools/assets/icon-256x256.gif?rev=2526088";
        $plugins_to_install[3]["slug"] = "wptools";

        $plugins_to_install[4]["Name"] = "reCAPTCHA For All";
        $plugins_to_install[4]["Description"] = "Protect ALL Selected Pages of your site against bots (spam, hackers, fake users and other types of automated abuse)
	  with Cloudflare Turnstile or invisible reCaptcha V3 (Google). You can also block visitors from China.";
        $plugins_to_install[4]["image"] =
            "https://ps.w.org/recaptcha-for-all/assets/icon-256x256.gif?rev=2544899";
        $plugins_to_install[4]["slug"] = "recaptcha-for-all";

        $plugins_to_install[5]["Name"] = "WP Memory";
        $plugins_to_install[5]["Description"] =
            "Check High Memory Usage, Memory Limit, PHP Memory, show result in Site Health Page and help to fix php low memory limit. In-page Memory Usage Report.";
        $plugins_to_install[5]["image"] =
            "https://ps.w.org/wp-memory/assets/icon-256x256.gif?rev=2525936";
        $plugins_to_install[5]["slug"] = "wp-memory";

        $plugins_to_install[6]["Name"] = "Database Backup";
        $plugins_to_install[6]["Description"] =
            "Quick and Easy Database Backup with a Single Click. Verify Tables and Schedule Automatic Backups.";
        $plugins_to_install[6]["image"] =
            "https://ps.w.org/database-backup/assets/icon-256x256.gif?rev=2862571";
        $plugins_to_install[6]["slug"] = "database-backup";

        $plugins_to_install[7]["Name"] = "Database Restore Bigdump";
        $plugins_to_install[7]["Description"] =
            "Database Restore with BigDump script. The ideal solution for restoring very large databases securely.";
        $plugins_to_install[7]["image"] =
            "https://ps.w.org/bigdump-restore/assets/icon-256x256.gif?rev=2872393";
        $plugins_to_install[7]["slug"] = "bigdump-restore";

        $plugins_to_install[8]["Name"] = "Easy Update URLs";
        $plugins_to_install[8]["Description"] =
            "Fix your URLs at database after cloning or moving sites. Easy search and replace content in database";
        $plugins_to_install[8]["image"] =
            "https://ps.w.org/easy-update-urls/assets/icon-256x256.gif?rev=2866408";
        $plugins_to_install[8]["slug"] = "easy-update-urls";

        $plugins_to_install[9]["Name"] = "S3 Cloud Contabo";
        $plugins_to_install[9]["Description"] =
            "Connect you with your Contabo S3-compatible Object Storage.";
        $plugins_to_install[9]["image"] =
            "https://ps.w.org/s3cloud/assets/icon-256x256.gif?rev=2855916";
        $plugins_to_install[9]["slug"] = "s3cloud";

        $plugins_to_install[10]["Name"] = "Tools for S3 AWS Amazon";
        $plugins_to_install[10]["Description"] =
            "Connect you with your Amazon S3-compatible Object Storage.";
        $plugins_to_install[10]["image"] =
            "https://ps.w.org/toolsfors3/assets/icon-256x256.gif?rev=2862487";
        $plugins_to_install[10]["slug"] = "toolsfors3";

        $plugins_to_install[11]["Name"] = "Hide Site Title";
        $plugins_to_install[11]["Description"] =
            "The Hide Site Title Remover plugin allows you to easily remove titles from your WordPress posts and pages, without affecting menus or titles in the admin area.";
        $plugins_to_install[11]["image"] =
            "https://ps.w.org/hide-site-title/assets/icon-256x256.gif?rev=2862487";
        $plugins_to_install[11]["slug"] = "hide-site-title";

        $plugins_to_install[12]["Name"] = "Disable WordPress Sitemap";
        $plugins_to_install[12]["Description"] =
            "The sitemap is automatically created by WordPress from version 5.5. This plugin offers you the option to disable it, allowing you to use another SEO plugin to generate it if desired.";
        $plugins_to_install[12]["image"] =
            "https://ps.w.org/disable-wp-sitemap/assets/icon-256x256.gif?rev=2862487";
        $plugins_to_install[12]["slug"] = "disable-wp-sitemap";

        $plugins_to_install[13]["Name"] = "Site Checkup";
        $plugins_to_install[13]["Description"] =
            "A vital tool for providing easy-to-use checks to ensure your site is functioning properly. It features a wizard that guides you through the process—simply click.";
        $plugins_to_install[13]["image"] =
            "https://ps.w.org/site-checkup/assets/icon-128x128.gif?rev=3132138";
        $plugins_to_install[13]["slug"] = "site-checkup";



?>
        <div style="padding-right:20px;">
            <br>
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
                                '"class="button button-primary bill-install-now-24">Install</a>';
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
            <?php
            wp_nonce_field("bill_install_plugin_class", "nonce_install");
            $plugin_path = trailingslashit(dirname(plugin_basename(__FILE__)));
            $parts = explode("/", rtrim($plugin_path, "/")); // Divide a string em partes usando '/' como delimitador
            $plugin_slug = reset($parts);
            // Obtém o primeiro elemento da lista
            ?>
            <input type="hidden" name="main_slug" id="main_slug" value="<?php echo esc_attr(
                                                                            $plugin_slug
                                                                        ); ?>">
            <center><big>
                    <a href="https://profiles.wordpress.org/sminozzi/#content-plugins" target="_blank">Discover All Plugins</a>
                    &nbsp;&nbsp;
                    <a href="https://profiles.wordpress.org/sminozzi/#content-themes" target="_blank">Discover All Themes</a>
                </big> </center>
        </div>
<?php
    }
} // end class
$show_more_plugins = new stopbadbots_Bill_show_more_plugins();
// } // end namespace
class stopbadbots_PluginInstaller
{
    public function __construct()
    {
        // Adiciona o gancho para ação wp_ajax
        add_action("wp_ajax_bill_install_plugin2", array($this, "bill_install_plugin2"));
    }
    public function bill_install_plugin2()
    {
        if (isset($_POST["nonce"])) {
            $nonce = sanitize_text_field($_POST["nonce"]);
            if (
                !wp_verify_nonce($nonce, "bill_install_plugin_class") &&
                !wp_verify_nonce($nonce, "bill_install")
            ) {
                wp_die("invalid nonce");
            }
        } else
            wp_die("invalid nonce!");
        if (! current_user_can('administrator')) {
            wp_die("invalid user!");
        }
        if (isset($_POST["slug"])) {
            $slug = sanitize_text_field($_POST["slug"]);
        } else {
            echo "Fail error (-5)";
            wp_die();
        }
        $allowed_slugs = [
            "database-backup",
            "bigdump-restore",
            "easy-update-urls",
            "hide-site-title",
            "s3cloud",
            "toolsfors3",
            "antihacker",
            "stopbadbots",
            "wptools",
            "recaptcha-for-all",
            "wp-memory",
            "disable-wp-sitemap",
            "stopbadbots",
            "antibots",
            "site-checkup"
        ];
        if (!in_array($slug, $allowed_slugs)) {
            wp_die("wrong slug");
        }
        // get plugin information
        $api = plugins_api("plugin_information", [
            "slug" => $slug,
            "fields" => ["sections" => false],
        ]);
        if (is_wp_error($api)) {
            echo "Fail error (-1)";
            wp_die();
        }
        if (isset($api->download_link)) {
            $source = $api->download_link;
        } else {
            echo "Fail error (-2)";
            wp_die();
        }
        $nonce = "install-plugin_" . $api->slug;
        $plugin = $slug;
        $skin = new \stopbadbots_bill_install_QuietSkin(["api" => $api]);
        $upgrader = new \Plugin_Upgrader($skin);
        try {
            $upgrader->install($source);
            $all_plugins = get_plugins();
            foreach ($all_plugins as $key => $value) {
                $plugin_file = $key;
                $slash_position = strpos($plugin_file, "/");
                $folder = substr($plugin_file, 0, $slash_position);
            }
        } catch (\Exception $e) {
            echo "Fail error (-4)";
            wp_die();
        }
        wp_die("OK");
    }
}
$plugin_installer = new stopbadbots_PluginInstaller();
//$plugin_installer->install_plugin();
require_once ABSPATH . "wp-admin/includes/plugin-install.php";
require_once ABSPATH . "wp-admin/includes/class-wp-upgrader.php";
class stopbadbots_bill_install_QuietSkin extends \WP_Upgrader_Skin
{
    public function __construct()
    {
        parent::__construct();
        // add_action('admin_enqueue_scripts', array($this, 'enqueue_scripts'));
    }
    public function feedback($string, ...$args)
    {
        // no output
    }
    public function header()
    {
        // no output
    }
    public function footer()
    {
        // no output
    }
}
$quiet_skin = new stopbadbots_bill_install_QuietSkin();
