
<?php

//return;


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
    echo '<p>' . esc_html__("The SBB Fail2Ban Monitor brings your server's powerful Fail2Ban protection into a clear, visual WordPress dashboard – the user-friendly GUI many have been waiting for!", 'sbb-text-domain') . '</p>';
    echo '<p>' . esc_html__("Currently, you can track key details like the offending IP, when the last attempt occurred, the specific Security Rule (jail) triggered, the number of attempts, and how long the ban lasts", 'sbb-text-domain') . '</p>';
    echo '<p>' . esc_html__("
This is your first step towards richer insights like daily totals, activity graphs, and more detailed ban reasons.", 'sbb-text-domain') . '</p>';

    echo '<p><small>' .
        esc_html__("Please ensure Fail2Ban is installed and operational on your server, VPS or Cloud VPS for this monitor to function. Our support doesn't include the installation or configuration of Fail2Ban on your server.", 'sbb-text-domain') .
        esc_html__('All features on this page are available only in our Pro (or premium) version.', 'sbb-text-domain') .
        '</small></p>';




    echo '</div>';

    echo '</div>'; // End of .sbb_fail2ban_monitor_description div





    // Table




    // Assuming $wpdb is already available in this context
    // and the variable $table_name has been defined (e.g., $wpdb->prefix . 'stopbadbots_fail2ban_logs';)

    // --- Block Table by Day Section (already present in your code) ---
    // This query is the one you already have to generate the "Blocks by day" table
    $query = "
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

    $results = $wpdb->get_results($query, ARRAY_A); // $results contains data for both table and chart



    // --- End of Table Section ---

    // --- Logic to prepare data for the Chart (reusing $results) ---
    $graph_data = array(); // Format: [[day_idx, count], ...]
    $graph_ticks = array(); // Format: [[day_idx, 'DD/MM'], ...]

    // To ensure all days from the last 30 days are shown, even if without attacks
    $attack_counts_by_date_map = [];
    for ($i = 29; $i >= 0; $i--) { // From 29 days ago to today
        $date = date('Y-m-d', strtotime("-$i days"));
        $attack_counts_by_date_map[$date] = 0;
    }

    // Fill counts with data obtained from $results
    foreach ($results as $row) {
        $attack_counts_by_date_map[$row['ban_date']] = (int)$row['ban_count'];
    }

    // Prepare data for Flot.js from the complete map
    $idx = 0;
    foreach ($attack_counts_by_date_map as $date_str => $count) {
        $graph_data[] = [$idx, $count];
        $graph_ticks[] = [$idx, date('d/m', strtotime($date_str))]; // Format 'DD/MM'
        $idx++;
    }
    // --- End of chart data logic ---

    echo '<h2>Blocks Last 30 Days</h2>';
    echo '<br>';

    // --- JavaScript for the Chart ---
    echo '<script type="text/javascript">';
    echo 'jQuery(function() {';

    // Convert PHP arrays to JavaScript
    echo 'var d2 = [';
    $data_parts = [];
    foreach ($graph_data as $point) {
        $data_parts[] = '[' . esc_attr($point[0]) . ',' . esc_attr($point[1]) . ']';
    }
    echo implode(',', $data_parts);
    echo '];';

    echo 'var ticks = [';
    $tick_parts = [];
    foreach ($graph_ticks as $tick) {
        $tick_parts[] = '[' . esc_attr($tick[0]) . ',"' . esc_attr($tick[1]) . '"]';
    }
    echo implode(',', $tick_parts);
    echo '];';
    ?>
    var options = {
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

    jQuery.plot("#placeholder", [ d2 ], options);
    });
    </script>

<?php
    // Where the chart will be rendered
    echo '<div id="placeholder" style="min-width:250px; max-width:100% !important; height:165px; margin-top: -20px;"></div>';
    // --- End of JavaScript for the Chart ---

    //} // End of 'else' for if (empty($results))







    // Assuming $wpdb is already available in this context
    // and the variable $table_name has been defined (e.g., $wpdb->prefix . 'stopbadbots_fail2ban_logs';)

    // --- Last 24 Hours Block Table Section (totaling by hour and ordering chronologically) ---
    // Query to get blocks in the last 24 hours, grouped by hour and ordered by hour
    $query = "
        SELECT
            DATE_FORMAT(timestamp, '%d/%m %H:00') AS ban_hour,  -- Format as 'DD/MM HH:00'
            COUNT(*) AS ban_count
        FROM
            `{$table_name}`
        WHERE
            timestamp >= NOW() - INTERVAL 24 HOUR
        GROUP BY
            ban_hour
        ORDER BY
            ban_hour DESC  -- **FIX HERE: Order by formatted hour in ascending order**
    ";

    $results = $wpdb->get_results($query, ARRAY_A);

    if (empty($results)) {
        echo '<p>No blocks recorded in the last 24 hours.</p>';
    } else {
        echo '<h2>Blocks in the Last 24 Hours (by hour)</h2>';
        echo '<table class="wp-list-table widefat fixed striped pages">'; // Using WP classes for styling
        echo '<thead>';
        echo '<tr>';
        echo '<th scope="col">Date/Time (DD/MM HH:00)</th>';
        echo '<th scope="col">Number of Blocks</th>';
        echo '</tr>';
        echo '</thead>';
        echo '<tbody id="the-list">';

        foreach ($results as $row) {
            echo '<tr>';
            echo '<td>' . esc_html($row['ban_hour']) . '</td>';
            echo '<td>' . esc_html($row['ban_count']) . '</td>';
            echo '</tr>';
        }

        echo '</tbody>';
        echo '</table>';
    }




    // Assuming $wpdb and $table_name are already defined in your WordPress context
    // global $wpdb;
    // $table_name = $wpdb->prefix . 'your_fail2ban_table'; // Example table name

    // Query to get the last 100 raw data rows
    $query = "
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

    $results = $wpdb->get_results($query, ARRAY_A); // ARRAY_A returns an associative array

    if (empty($results)) {
        echo '<p>No recent block records to display.</p>';
    } else {
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

        foreach ($results as $row) {
            // Format date and time for display
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
    }




    // Return the buffer content and clear it
    return; // ob_get_clean();
}



function stopbadbots_add_menu_fail2ban__________()
{
    $stopbadbots_table_page = add_submenu_page(
        "stop_bad_bots_plugin", // $parent_slug
        "Visits Analytics", // string $page_title
        "Visits Analytics", // string $menu_title
        "manage_options", // string $capability
        "stopbadbots_my-custom-submenu-page-stats",
        "stopbadbots_render_ban_report"
    );
}
//stopbadbots_render_daily_ban_report();


add_action('admin_menu', 'stopbadbots_add_menu_fail2ban');




function stopbadbots_add_menu_fail2ban()
{
    $stopbadbots_table_page = add_submenu_page(
        "stop_bad_bots_plugin", // $parent_slug
        "Fail2ban", // string $page_title
        "Fail2ban", // string $menu_title
        "manage_options", // string $capability
        "stopbadbots_my-custom-submenu-page-fail2ban",
        "stopbadbots_render_ban_report"
    );
}
