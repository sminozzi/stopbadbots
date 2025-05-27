<?php

/**
 * @ Author: Bill Minozzi -
 * @ Copyright: 2023 www.BillMinozzi.com
 * @ Modified time: 2023-07-17 2024-02-27
 */
if (!defined("ABSPATH")) {
    exit();
} // Exit if accessed directly
/**
 * Registers the shortcode to display the daily Fail2Ban block report.
 * Usage: [sbb_fail2ban_daily_report]
 */
//add_shortcode('sbb_fail2ban_daily_report', 'stopbadbots_render_daily_ban_report');
/**
 * Function that retrieves data and renders the daily Fail2Ban block report.
 *
 * @param array $atts Shortcode attributes (not used in this simple version).
 * @return string HTML of the report table or 'no data' message.
 */
/*
 `id` bigint(20) UNSIGNED NOT NULL,
 `ip` varchar(45) NOT NULL,
 `timestamp` datetime NOT NULL,
 `jail` varchar(100) NOT NULL,
 `reason` text DEFAULT NULL,
 `attempts` int(11) NOT NULL,
 `log_line` text DEFAULT NULL,
 `host` varchar(100) DEFAULT NULL,
 `port` int(11) DEFAULT NULL,
 `protocol` varchar(10) DEFAULT NULL,
 `ban_duration` int(11) NOT NULL
 */
function stopbadbots_render_ban_report()
{
    global $wpdb;
    $table_name = $wpdb->prefix . 'stopbadbots_fail2ban_logs';
    // Start output buffer to capture HTML
    //ob_start();
?>
    <div id="stopbadbots-logo">
        <img alt="logo" src="<?php echo esc_attr(
                                    STOPBADBOTSIMAGES
                                ); ?>/logo.png" width="250px" />
    </div>
    <?php
    echo '<h2>SBB Fail2ban Monitor</h2>';
    // echo "Gain valuable insights into your server's security with the SBB Fail2Ban Monitor.";
    // Assuming this output is within a PHP function in your WordPress plugin.
    echo '<div class="sbb_fail2ban_monitor_description">';
    echo '<div class="sbb_fail2ban_monitor_description">';
    echo '<p>' . esc_html__("The use of this page is optional, and it's intended for more advanced users.", 'stopbadbots') . '</p>';
    echo '<p>' . esc_html__("The SBB Fail2Ban Monitor brings your server's powerful Fail2Ban protection into a clear, visual WordPress dashboard – the user-friendly GUI many have been waiting for!", 'stopbadbots') . '</p>';
    echo '<p>' . esc_html__("Currently, you can track key details like the offending IP, when the last attempt occurred, the specific Security Rule (jail) triggered, the number of attempts, and how long the ban lasts", 'stopbadbots') . '</p>';
    echo '<p>' . esc_html__("This is your first step towards richer insights like daily totals, activity graphs, and more detailed ban reasons.", 'stopbadbots') . '</p>';
    echo '<p><small>' .
        esc_html__("Please ensure Fail2Ban is installed and operational on your server, VPS or Cloud VPS for this monitor to function. Our support doesn't include the installation or configuration of Fail2Ban on your server.", 'stopbadbots') .
        esc_html__('All features on this page are available only in our Pro (or premium) version.', 'stopbadbots') .
        '</small></p>';
    echo '<p>';
    echo sprintf(
        esc_html__('Visit our site for more details on %s.', 'stopbadbots'),
        '<a href="https://stopbadbots.com/integrating-antihacker-stopbadbots-with-fail2ban/" target="_blank">' . esc_html__('integrating StopBadBots with Fail2Ban', 'stopbadbots') . '</a>'
    );
    echo '</p>';
    echo '</div>';
    echo '</div>'; // End of .sbb_fail2ban_monitor_description div
    // Table
    // Assuming $wpdb is already available in this context
    // and the variable $table_name has been defined (e.g., $wpdb->prefix . 'stopbadbots_fail2ban_logs';)
    // --- Block Table by Day Section (already present in your code) ---
    // This query is the one you already have to generate the "Blocks by day" table
    $query_30_days = "
    SELECT
        DATE(timestamp) AS ban_date,
        COUNT(*) AS ban_count
    FROM
        `{$table_name}`
    GROUP BY
        ban_date
    ORDER BY
        ban_date ASC
";
    $results_30_days = $wpdb->get_results($query_30_days, ARRAY_A); // $results_30_days contains data for both table and chart
    // Only render this section if results are not empty
    if (!empty($results_30_days)) {
        // --- Logic to prepare data for the Chart (reusing $results_30_days) ---
        $graph_data_30_days = array(); // Format: [[day_idx, count], ...]
        $graph_ticks_30_days = array(); // Format: [[day_idx, 'DD/MM'], ...]
        // To ensure all days from the last 30 days are shown, even if without attacks
        $attack_counts_by_date_map = [];
        // Assuming server's default timezone for date functions is acceptable, as per original code.
        // For robustness with WordPress timezone settings, one might use current_time('timestamp')
        for ($i = 29; $i >= 0; $i--) { // From 29 days ago to today
            $date_map_key = date('Y-m-d', strtotime("-$i days"));
            $attack_counts_by_date_map[$date_map_key] = 0;
        }
        // Fill counts with data obtained from $results_30_days
        foreach ($results_30_days as $row) {
            // Ensure the date from DB result exists as a key in our map (it should if within last 30 days)
            if (isset($attack_counts_by_date_map[$row['ban_date']])) {
                $attack_counts_by_date_map[$row['ban_date']] = (int)$row['ban_count'];
            }
        }
        // Prepare data for Flot.js from the complete map
        $idx = 0;
        foreach ($attack_counts_by_date_map as $date_str => $count) {
            $graph_data_30_days[] = [$idx, $count];
            $graph_ticks_30_days[] = [$idx, date('d/m', strtotime($date_str))]; // Format 'DD/MM'
            $idx++;
        }
        // --- End of chart data logic ---
        echo '<h2>Blocks Last 30 Days</h2>';
        echo '<br>';
        // --- JavaScript for the Chart ---
        echo '<script type="text/javascript">';
        echo 'jQuery(function() {';
        // Convert PHP arrays to JavaScript
        echo 'var d2 = ['; // Keep original variable name 'd2' for this chart
        $data_parts = [];
        foreach ($graph_data_30_days as $point) {
            $data_parts[] = '[' . esc_js($point[0]) . ',' . esc_js($point[1]) . ']';
        }
        echo implode(',', $data_parts);
        echo '];';
        echo 'var ticks = ['; // Keep original variable name 'ticks' for this chart
        $tick_parts = [];
        foreach ($graph_ticks_30_days as $tick) {
            $tick_parts[] = '[' . esc_js($tick[0]) . ',"' . esc_js($tick[1]) . '"]';
        }
        echo implode(',', $tick_parts);
        echo '];';
    ?>
        var options = { // Keep original variable name 'options'
        series: {
        lines: { show: true },
        points: { show: true },
        color: "#ff0000" // Chart line color (red)
        },
        grid: {
        hoverable: true,
        clickable: true,
        borderColor: "#CCCCCC",
        color: "#333333",
        backgroundColor: { colors: ["#fff", "#eee"]}
        },
        xaxis:{
        font:{
        size:6,
        style:"italic",
        weight:"normal",
        family:"sans-serif",
        color: "#ff0000", // X-axis text color (red)
        variant:"small-caps"
        },
        ticks: ticks,
        // minTickSize: [1, "day"] // Uncomment to force daily ticks, may cause overlap
        },
        yaxis: {
        font:{
        size:8,
        style:"italic",
        weight:"bold",
        family:"sans-serif",
        color: "#616161", // Y-axis text color (gray)
        variant:"small-caps"
        },
        // Use a function to format Y-axis ticks without decimals
        tickFormatter: function stopbadbots_suffixFormatter(val, axis) {
        return (val.toFixed(0));
        }
        }
        };
        jQuery.plot("#placeholder", [ d2 ], options); // Keep original ID 'placeholder'
        });
        </script>
    <?php
        // Where the chart will be rendered
        echo '<div id="placeholder" style="min-width:250px; max-width:100% !important; height:165px; margin-top: -20px;"></div>'; // Keep original ID 'placeholder'
        // --- End of JavaScript for the Chart ---
    } // End of if (!empty($results_30_days)) for Blocks Last 30 Days
    // --- Chart for Blocks in the Last 24 Hours (replaces the table) ---
    // Assuming `timestamp` in DB is stored in server's local timezone, consistent with how 30-day chart seems to operate.
    $twenty_four_hours_ago_server_local = date('Y-m-d H:i:s', time() - (24 * HOUR_IN_SECONDS));
    $query_24h_chart = $wpdb->prepare("
        SELECT
            DATE_FORMAT(timestamp, %s) AS hour_block, -- Format: 'YYYY-MM-DD HH:00:00'
            COUNT(*) AS ban_count
        FROM
            `{$table_name}`
        WHERE
            timestamp >= %s
        GROUP BY
            hour_block
        ORDER BY
            hour_block ASC
    ", '%Y-%m-%d %H:00:00', $twenty_four_hours_ago_server_local);
    $results_24h_raw = $wpdb->get_results($query_24h_chart, ARRAY_A);
    if (!empty($results_24h_raw)) { // Condition similar to the 30-day chart
        echo '<h2>Blocks in the Last 24 Hours</h2>';
        echo '<br>';
        $graph_data_24h = [];
        $graph_ticks_24h = [];
        // Create a map for the last 24 hourly slots, initialized to 0.
        // Keys are server-local hour strings 'YYYY-MM-DD HH:00:00'.
        $hourly_counts_map_local = [];
        $current_server_time = time(); // Current server local time as UNIX timestamp
        for ($h = 0; $h < 24; $h++) {
            // Iterate from 23 hours ago up to the current hour's slot
            $target_hour_ts = $current_server_time - ((23 - $h) * HOUR_IN_SECONDS);
            $hour_key_local = date('Y-m-d H:00:00', $target_hour_ts);
            $hourly_counts_map_local[$hour_key_local] = 0;
        }
        // ksort($hourly_counts_map_local); // Ensure chronological order if loop logic was ambiguous (not needed here)
        // Fill counts from the query results
        foreach ($results_24h_raw as $row) {
            if (isset($hourly_counts_map_local[$row['hour_block']])) {
                $hourly_counts_map_local[$row['hour_block']] = (int)$row['ban_count'];
            }
        }
        // Prepare data for Flot.js
        $idx_24h = 0;
        foreach ($hourly_counts_map_local as $hour_str_local => $count) {
            $graph_data_24h[] = [$idx_24h, $count];
            // Tick label: 'HH' (e.g., 09, 10, 11)
            $tick_label = date('H', strtotime($hour_str_local));
            $graph_ticks_24h[] = [$idx_24h, $tick_label];
            $idx_24h++;
        }
        // --- JavaScript for the 24-Hour Chart ---
        echo '<script type="text/javascript">';
        echo 'jQuery(function() {';
        echo 'var data_hourly_chart = ['; // Unique JS variable name
        $js_data_parts_24h = [];
        foreach ($graph_data_24h as $point) {
            $js_data_parts_24h[] = '[' . esc_js($point[0]) . ',' . esc_js($point[1]) . ']';
        }
        echo implode(',', $js_data_parts_24h);
        echo '];';
        echo 'var ticks_hourly_chart = ['; // Unique JS variable name
        $js_tick_parts_24h = [];
        foreach ($graph_ticks_24h as $tick) {
            $js_tick_parts_24h[] = '[' . esc_js($tick[0]) . ',"' . esc_js($tick[1]) . '"]';
        }
        echo implode(',', $js_tick_parts_24h);
        echo '];';
    ?>
        var options_hourly = { // Unique JS variable name
        series: {
        lines: { show: true },
        points: { show: true },
        color: "#0073aa" // A distinct color (WordPress blue)
        },
        grid: {
        hoverable: true,
        clickable: true,
        borderColor: "#CCCCCC",
        color: "#333333",
        backgroundColor: { colors: ["#fff", "#eee"]}
        },
        xaxis:{
        font:{
        size:9, // Adjusted for hour display
        style:"normal",
        weight:"normal",
        family:"sans-serif",
        color: "#0073aa",
        variant:"normal"
        },
        ticks: ticks_hourly_chart
        },
        yaxis: {
        font:{
        size:8,
        style:"italic",
        weight:"bold",
        family:"sans-serif",
        color: "#616161",
        variant:"small-caps"
        },
        tickFormatter: function stopbadbots_suffixFormatter(val, axis) {
        return (val.toFixed(0)); // Format Y-axis ticks as integers
        }
        }
        };
        jQuery.plot("#placeholder_hourly_blocks", [ data_hourly_chart ], options_hourly); // Unique placeholder ID
        });
        </script>
<?php
        // Where the 24-hour chart will be rendered
        echo '<div id="placeholder_hourly_blocks" style="min-width:250px; max-width:100% !important; height:165px; margin-top: -20px;"></div>'; // Unique placeholder ID
        // --- End of JavaScript for 24-Hour Chart ---
    } // End of if (!empty($results_24h_raw)) for Blocks Last 24 Hours chart
    // --- End of Chart for Blocks in the Last 24 Hours ---
    // Assuming $wpdb and $table_name are already defined in your WordPress context
    // global $wpdb;
    // $table_name = $wpdb->prefix . 'your_fail2ban_table'; // Example table name
    // Query to get the last 100 raw data rows
    $query_last_100 = "
    SELECT
        id,
        ip,
        timestamp,
        jail,
        reason,
        attempts,
        host,
        ban_duration
        -- Columns 'log_line', 'port', 'protocol' were not included in the display
        -- to keep the table concise, but can be added if desired.
    FROM
        `{$table_name}`
    ORDER BY
        timestamp DESC
    LIMIT 100
    "; // Changed from 30 to 100
    $results_last_100 = $wpdb->get_results($query_last_100, ARRAY_A); // ARRAY_A returns an associative array
    if (!empty($results_last_100)) {
        echo '<h2>Last 100 Fail2Ban Block Records</h2>'; // Updated title
        // Div container for the table with scroll
        // max-height defines the maximum height before scroll appears
        // overflow-y: auto enables vertical scroll only when needed
        // overflow-x: auto can be useful if columns are too wide
        // border: 1px solid #ccc; is optional, just to outline the scroll area
        echo '<div style="max-height: 600px; overflow-y: auto; overflow-x: auto; border: 1px solid #ccc;">';
        echo '<table class="wp-list-table widefat fixed striped pages">'; // Using WP classes for styling
        echo '<thead>';
        echo '<tr>';
        // Inline style for sticky headers
        // position: sticky; top: 0; makes the header stick to the top of the scrollable div
        // background-color: #f0f0f0; (or another color) to prevent content below from showing through
        // z-index: 1; to ensure the header stays above the table body when scrolling
        $th_style = 'style="position: sticky; top: 0; background-color: #f9f9f9; z-index: 1; box-shadow: 0 2px 2px -1px rgba(0,0,0,0.1);"';
        echo "<th scope=\"col\" {$th_style}>Date and Time</th>";
        echo "<th scope=\"col\" {$th_style}>IP</th>";
        echo "<th scope=\"col\" {$th_style}>Jail</th>";
        echo "<th scope=\"col\" {$th_style}>Attempts</th>";
        echo "<th scope=\"col\" {$th_style}>Ban Duration (seconds)</th>";
        echo '</tr>';
        echo '</thead>';
        echo '<tbody id="the-list">'; // The id "the-list" is a WP standard, good to keep
        foreach ($results_last_100 as $row) {
            // Format date and time for display
            // Assuming timestamp is server local time. Use wp_date for WP timezone.
            // For simplicity and consistency with potential existing behavior, using date() with strtotime().
            $formatted_datetime = date('d/m/Y H:i:s', strtotime($row['timestamp']));
            echo '<tr>';
            echo '<td>' . esc_html($formatted_datetime) . '</td>';
            echo '<td>' . esc_html($row['ip']) . '</td>';
            echo '<td>' . esc_html($row['jail']) . '</td>';
            echo '<td>' . esc_html($row['attempts']) . '</td>';
            echo '<td>' . esc_html($row['ban_duration']) . '</td>';
            echo '</tr>';
        }
        echo '</tbody>';
        echo '</table>';
        echo '</div>'; // Close the div container
    } // End of if (!empty($results_last_100)) for Last 100 Records
    // Return the buffer content and clear it
    return; // ob_get_clean();
}
function stopbadbots_add_menu_fail2ban()
{
    $stopbadbots_table_page = add_submenu_page(
        "stop_bad_bots_plugin", // $parent_slug
        "Fail2ban Monitor", // string $page_title
        "Fail2ban Monitor", // string $menu_title
        "manage_options", // string $capability
        "stopbadbots_my-custom-submenu-page-fail2ban",
        "stopbadbots_render_ban_report"
    );
}
if (is_admin() and current_user_can("manage_options")) {
    add_action('admin_menu', 'stopbadbots_add_menu_fail2ban');
}
