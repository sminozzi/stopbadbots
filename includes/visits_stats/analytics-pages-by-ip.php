<?php
/*
Description: Creates a chart of average pages viewed per IP over the last 30 days.
Version: 1.0
Author: Bill Minozzi
2/24
*/
if (!defined('ABSPATH')) {
	die('Invalid request.');
}

    // Query for pages viewed by IP per month
    /*
    $pages_per_ip_per_month_results = $wpdb->get_results("
        SELECT DATE_FORMAT(date, '%Y-%m') AS visit_month, ip, COUNT(*) AS num_pages_visited
        FROM {$wpdb->prefix}sbb_visitorslog 
        WHERE bot = 0
        AND date >= CURDATE() - INTERVAL 12 MONTH
        GROUP BY DATE_FORMAT(date, '%Y-%m'), ip;
    ");
    */

    $table_name = $wpdb->prefix. 'sbb_visitorslog';
    /*
    $results = $wpdb->get_results($wpdb->prepare("
    SELECT 
        CASE 
            WHEN referer = '' THEN 'Direct Visits'
            WHEN SUBSTRING_INDEX(SUBSTRING_INDEX(referer, '://', -1), '/', 1) = %s THEN 'Site Navigation'
            ELSE SUBSTRING_INDEX(SUBSTRING_INDEX(referer, '://', -1), '/', 1)
        END AS referrer,
        COUNT(DISTINCT ip) AS total_visits
    FROM 
        %i
    WHERE 
        DATE(date) >= CURDATE() - INTERVAL %d DAY 
        AND bot = '0'
        AND referer != 'Direct Visits'  
    GROUP BY 
        referrer
    ORDER BY 
        total_visits DESC
    LIMIT %d, %d
", $current_domain, $table_name, $period, $offset, $per_page));
*/

$pages_per_ip_per_month_results = $wpdb->get_results(
    $wpdb->prepare("
        SELECT DATE_FORMAT(date, '%%Y-%%m') AS visit_month, ip, COUNT(*) AS num_pages_visited
        FROM %i
        WHERE bot = 0
        AND date >= CURDATE() - INTERVAL 12 MONTH
        GROUP BY DATE_FORMAT(date, '%%Y-%%m'), ip;
    ", "{$wpdb->prefix}sbb_visitorslog")
);



    // Prepare data for the chart
    $data = [];
    foreach ($pages_per_ip_per_month_results as $row) {
        $visit_month = $row->visit_month;
        $ip = $row->ip;
        $num_pages_visited = $row->num_pages_visited;
        // Calculate the average number of pages visited per IP per month
        if (!isset($data[$visit_month])) {
            $data[$visit_month] = [];
        }
        $data[$visit_month][] = $num_pages_visited;
    }

    
    // Calculate the average number of pages visited per IP per month
    $averages = [];
    foreach ($data as $visit_month => $pages_visited_array) {
        $averages[$visit_month] = array_sum($pages_visited_array) / count($pages_visited_array);
    }
    // Prepare labels and data for the chart
    $labels = array_keys($averages);
    $data = array_values($averages);
    // Calculate the maximum value for y-axis
    $max_y = !empty($data) ? ceil(max($data)) : 0;
    ?>
    <canvas id="pages-chart" style="width:600px;max-height:300px;"></canvas>
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        var ctx = document.getElementById('pages-chart').getContext('2d');
        var myChart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: <?php echo wp_json_encode($labels); ?>,
                datasets: [{
                    label: 'Average Pages Viewed per IP',
                    data: <?php echo wp_json_encode($data); ?>,
                    borderColor: 'rgb(255, 99, 132)',
                    tension: 0.1,
                    fill: {
                            target: 'origin',
                            above: 'rgba(255, 99, 132, 0.2)', // Cor de preenchimento mais clara acima da linha
                        },
                }]
            },
            options: {
                scales: {
                    y: {
                        beginAtZero: true,
                        suggestedMax: <?php echo esc_attr($max_y); ?> // Set the maximum value for y-axis
                    }
                }
            }
        });
    });
    </script>
    <?php
