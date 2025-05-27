<?php
/*
Description: Creates a chart of visits over the last 12 months using Chart.js.
Version: 1.0
Author: Bill Minozzi
*/

if (!defined('ABSPATH')) {
	die('Invalid request.');
}

$table_name = $wpdb->prefix.'sbb_visitorslog';

/*
    // Query for total unique visits
    $total_visits_results = $wpdb->get_results("SELECT DATE_FORMAT(date, '%Y-%m') AS visit_month, COUNT(DISTINCT ip) AS total_visits FROM {$wpdb->prefix}sbb_visitorslog  WHERE date >= DATE_FORMAT(CURDATE() - INTERVAL 12 MONTH, '%Y-%m-01') GROUP BY DATE_FORMAT(date, '%Y-%m')");
    // Query for bot visits
    $bot_visits_results = $wpdb->get_results("SELECT DATE_FORMAT(date, '%Y-%m') AS visit_month, COUNT(DISTINCT ip) AS bot_visits FROM {$wpdb->prefix}sbb_visitorslog  WHERE date >= DATE_FORMAT(CURDATE() - INTERVAL 12 MONTH, '%Y-%m-01') AND bot = '1' GROUP BY DATE_FORMAT(date, '%Y-%m')");
    // Query for human visits
    $human_visits_results = $wpdb->get_results("SELECT DATE_FORMAT(date, '%Y-%m') AS visit_month, COUNT(DISTINCT ip) AS human_visits FROM {$wpdb->prefix}sbb_visitorslog  WHERE date >= DATE_FORMAT(CURDATE() - INTERVAL 12 MONTH, '%Y-%m-01') AND human = 'Human' GROUP BY DATE_FORMAT(date, '%Y-%m')");
   
*/

    // Query for total unique visits
$total_visits_results = $wpdb->get_results($wpdb->prepare("SELECT DATE_FORMAT(date, '%Y-%m') AS visit_month, COUNT(DISTINCT ip) AS total_visits FROM %i WHERE date >= DATE_FORMAT(CURDATE() - INTERVAL 12 MONTH, '%Y-%m-01') GROUP BY DATE_FORMAT(date, '%Y-%m')", $table_name));

// Query for bot visits
$bot_visits_results = $wpdb->get_results($wpdb->prepare("SELECT DATE_FORMAT(date, '%Y-%m') AS visit_month, COUNT(DISTINCT ip) AS bot_visits FROM %i WHERE date >= DATE_FORMAT(CURDATE() - INTERVAL 12 MONTH, '%Y-%m-01') AND bot = '1' GROUP BY DATE_FORMAT(date, '%Y-%m')", $table_name));

// Query for human visits
$human_visits_results = $wpdb->get_results($wpdb->prepare("SELECT DATE_FORMAT(date, '%Y-%m') AS visit_month, COUNT(DISTINCT ip) AS human_visits FROM %i WHERE date >= DATE_FORMAT(CURDATE() - INTERVAL 12 MONTH, '%Y-%m-01') AND human = 'Human' GROUP BY DATE_FORMAT(date, '%Y-%m')", $table_name));

  
  // Merge results into a single array 
  
    $data = [];
    foreach ($total_visits_results as $row) {
        $data[$row->visit_month] = [
            'visit_month' => $row->visit_month,
            'total_visits' => $row->total_visits,
            'bot_visits' => 0,
            'human_visits' => 0,
        ];
    }
    foreach ($bot_visits_results as $row) {
        $data[$row->visit_month]['bot_visits'] = $row->bot_visits;
    }
    foreach ($human_visits_results as $row) {
        $data[$row->visit_month]['human_visits'] = $row->human_visits;
    }
    // Convert associative array to indexed array
    $data = array_values($data);
    ?>
    <!-- Checkboxes for showing different types of visits -->
    <label>
        <input type="checkbox" name="show_total_visits" id="showTotalVisits" checked> Total Visits
    </label>
    <label>
        <input type="checkbox" name="show_human_visits" id="showHumanVisits" checked> Human Visits
    </label>
    <label>
        <input type="checkbox" name="show_bot_visits" id="showBotVisits" checked> Bot Visits
    </label>
    <canvas id="visitors-chart" style="width:600px;max-height:300px;"></canvas>
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        var ctx = document.getElementById('visitors-chart').getContext('2d');
        var myChart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: <?php echo wp_json_encode(array_column($data, 'visit_month')); ?>,
                datasets: [
                    {
                        label: 'Total Visits',
                        data: <?php echo wp_json_encode(array_column($data, 'total_visits')); ?>,
                        borderColor: 'rgb(255, 99, 132)',
                        tension: 0.1,
                        fill: {
                            target: 'origin',
                            above: 'rgba(255, 99, 132, 0.2)', // Cor de preenchimento mais clara acima da linha
                        },
                        hidden: false // Mostrar por padrão
                    },
                    {
                        label: 'Human Visits',
                        data: <?php echo wp_json_encode(array_column($data, 'human_visits')); ?>,
                        borderColor: 'rgb(75, 192, 192)',
                        tension: 0.1,
                        fill: {
                            target: 'origin',
                            above: 'rgba(75, 192, 192, 0.2)', // Cor de preenchimento mais clara acima da linha
                        },
                        hidden: false // Mostrar por padrão
                    },
                    {
                        label: 'Bot Visits',
                        data: <?php echo wp_json_encode(array_column($data, 'bot_visits')); ?>,
                        borderColor: 'rgb(0, 0, 0)',
                        tension: 0.1,
                        fill: {
                            target: 'origin',
                            above: 'rgba(0, 0, 0, 0.2)', // Cor de preenchimento mais clara acima da linha
                        },
                        hidden: false // Mostrar por padrão
                    }
                ]
            },
            options: {
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });
        // Add event listeners to the checkboxes
        document.getElementById('showTotalVisits').addEventListener('change', function() {
            myChart.data.datasets[0].hidden = !this.checked;
            myChart.update();
        });
        document.getElementById('showHumanVisits').addEventListener('change', function() {
            myChart.data.datasets[1].hidden = !this.checked;
            myChart.update();
        });
        document.getElementById('showBotVisits').addEventListener('change', function() {
            myChart.data.datasets[2].hidden = !this.checked;
            myChart.update();
        });
    });
</script>
<?php
