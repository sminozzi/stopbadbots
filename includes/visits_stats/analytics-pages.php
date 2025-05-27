<?php
/*
Description: Creates a table of page visited
Version: 1.0
Author: Bill Minozzi
2/24
*/
if (!defined('ABSPATH')) {
	die('Invalid request.');
}
    $current_page = isset($_GET['paged']) ? max(1, absint(sanitize_text_field($_GET['paged']))) : 1;
    if ($current_page === 1 && isset($_GET['page'])) {
        // Verifica se a URL contém "/page/" para extrair o número da página
        $page_url = admin_url('admin.php');
        $page_uri = parse_url($page_url, PHP_URL_QUERY);
        // parse_str($page_uri, $query_args);
        if (!is_null($page_uri) && !empty($page_uri)) {
            parse_str($page_uri, $query_args);
        } 
        if (isset($query_args['page']) && preg_match('/\/page\/(\d+)\//', $query_args['page'], $matches)) {
            if (!empty($matches[1])) {
                $current_page = max(1, absint($matches[1]));
            }
        }
    }
    if (isset($_GET['page']) && is_numeric(sanitize_text_field($_GET['page'])) && sanitize_text_field($_GET['page']) > 0) {
        // Verify nonce before accepting user-provided page number
        if (!wp_verify_nonce(sanitize_text_field($_GET['sbb_nonce']), 'visitors_chart_nonce')) {
            die('Security check failed.');
        }
    }
    $total_pages = 1; // Placeholder, will be calculated later
    // $period = isset($_GET['period']) ? $_GET['period'] : 30;
    $period = isset($_GET['period']) ? intval(sanitize_text_field($_GET['period'])) : 30;
    if($period < 1)
       $period = 1;
/*
$total_count_query = $wpdb->prepare("
    SELECT 
        COUNT(DISTINCT page_visited) AS total_count
    FROM (
        SELECT 
            SUBSTRING_INDEX(url, '?', 1) AS page_visited
        FROM 
            {$wpdb->prefix}sbb_visitorslog 
        WHERE 
            DATE(date) >= CURDATE() - INTERVAL %d DAY 
            AND bot = '0'
            AND SUBSTRING_INDEX(url, '?', 1) NOT LIKE '/wp-content/%' 
            AND SUBSTRING_INDEX(url, '?', 1) NOT LIKE '/wp-includes/%'
            AND SUBSTRING_INDEX(url, '?', 1) NOT LIKE '/wp-admin/%'
        GROUP BY 
            SUBSTRING_INDEX(url, '?', 1)
    ) AS sub_query
", $period);
    $total_count = $wpdb->get_var(sanitize_text_field($total_count_query));
    */
    $table_name = $wpdb->prefix . 'sbb_visitorslog';
    $total_count = $wpdb->get_var($wpdb->prepare("
        SELECT 
            COUNT(DISTINCT page_visited) AS total_count
        FROM (
            SELECT 
                SUBSTRING_INDEX(url, '?', 1) AS page_visited
            FROM 
                %i 
            WHERE 
                DATE(date) >= CURDATE() - INTERVAL %d DAY 
                AND bot = '0'
                AND SUBSTRING_INDEX(url, '?', 1) NOT LIKE '/wp-content/%' 
                AND SUBSTRING_INDEX(url, '?', 1) NOT LIKE '/wp-includes/%'
                AND SUBSTRING_INDEX(url, '?', 1) NOT LIKE '/wp-admin/%'
            GROUP BY 
                SUBSTRING_INDEX(url, '?', 1)
        ) AS sub_query
    ", esc_sql($table_name), $period));
        $per_page = 10;
        $total_pages = ceil($total_count / $per_page); 
        $current_page = min($current_page, $total_pages); // Ensure page doesn't exceed total pages
$pagination_args = array(
    'base'      => add_query_arg(array('paged' => '%#%', 'sbb_nonce' => wp_create_nonce('sbb_pagination')), sanitize_text_field($_SERVER['REQUEST_URI'])),
    'format'    => '',
    'prev_text' => '&laquo;',
    'next_text' => '&raquo;',
    'total'     => $total_pages,
    'current'   => $current_page,
);
$pagination = paginate_links($pagination_args);
$pagination = paginate_links($pagination_args);
$pagination = preg_replace('/href="([^"]*)"/', 'href="$1&amp;anchor=bottom#visitors-table"', $pagination);
    // Display the table
    $nonce = wp_create_nonce('sbb_pagination');
    $offset = ($current_page - 1) * $per_page;
    //$offset = ($current_page - 1) * $per_page;
    if($offset < 0)
      $offset = 0;

    // Prepare the main query with pagination and period parameter
    /*
    $query = $wpdb->prepare("
    SELECT 
        SUBSTRING_INDEX(url, '?', 1) AS page_visited,
        COUNT(*) AS unique_visitors
    FROM 
        {$wpdb->prefix}sbb_visitorslog 
    WHERE 
        DATE(date) >= CURDATE() - INTERVAL %d DAY 
        AND bot = '0'
        AND SUBSTRING_INDEX(url, '?', 1) NOT LIKE '/wp-content/%' 
        AND SUBSTRING_INDEX(url, '?', 1) NOT LIKE '/wp-includes/%'
        AND SUBSTRING_INDEX(url, '?', 1) NOT LIKE '/wp-admin/%'
    GROUP BY 
        page_visited
    ORDER BY 
        unique_visitors DESC
    LIMIT %d, %d
    ", $period, $offset, $per_page); // Escape period for security
    */
/*
    $query = $wpdb->prepare("
    SELECT 
        SUBSTRING_INDEX(url, '?', 1) AS page_visited,
        COUNT(*) AS unique_visitors
    FROM 
        {$wpdb->prefix}sbb_visitorslog 
    WHERE 
        DATE(date) >= CURDATE() - INTERVAL %d DAY 
        AND bot = '0'
        AND SUBSTRING_INDEX(url, '?', 1) NOT LIKE '/wp-content/%' 
        AND SUBSTRING_INDEX(url, '?', 1) NOT LIKE '/wp-includes/%'
        AND SUBSTRING_INDEX(url, '?', 1) NOT LIKE '/wp-admin/%'
    GROUP BY 
        page_visited
    ORDER BY 
        unique_visitors DESC
    LIMIT %d, %d
    ", $wpdb->esc_sql($period), $offset, $per_page); // Escape period, offset, and per_page
*/
    //$results = $wpdb->get_results($query);
/*
    $query = $wpdb->prepare("
    SELECT 
        SUBSTRING_INDEX(url, '?', 1) AS page_visited,
        COUNT(*) AS unique_visitors
    FROM 
        %s 
    WHERE 
        DATE(date) >= CURDATE() - INTERVAL %d DAY 
        AND bot = '0'
        AND SUBSTRING_INDEX(url, '?', 1) NOT LIKE '/wp-content/%' 
        AND SUBSTRING_INDEX(url, '?', 1) NOT LIKE '/wp-includes/%'
        AND SUBSTRING_INDEX(url, '?', 1) NOT LIKE '/wp-admin/%'
    GROUP BY 
        page_visited
    ORDER BY 
        unique_visitors DESC
    LIMIT %d, %d
    ", $wpdb->prefix . 'sbb_visitorslog', $period, $offset, $per_page);
    */
    $query = $wpdb->prepare("
    SELECT 
        SUBSTRING_INDEX(url, '?', 1) AS page_visited,
        COUNT(*) AS unique_visitors
    FROM 
        %i 
    WHERE 
        DATE(date) >= CURDATE() - INTERVAL %d DAY 
        AND bot = '0'
        AND SUBSTRING_INDEX(url, '?', 1) NOT LIKE '/wp-content/%' 
        AND SUBSTRING_INDEX(url, '?', 1) NOT LIKE '/wp-includes/%'
        AND SUBSTRING_INDEX(url, '?', 1) NOT LIKE '/wp-admin/%'
    GROUP BY 
        page_visited
    ORDER BY 
        unique_visitors DESC
    LIMIT %d, %d
    ", $wpdb->prefix . 'sbb_visitorslog', $period, $offset, $per_page);
    $results = $wpdb->get_results($query);

    
    // Check if results exist

    /*
    if ($results) {
        echo '<form method="get" action="' . esc_url(admin_url('admin.php')) . '">';
        echo '<input type="hidden" name="page" value="stopbadbots_my-custom-submenu-page-stats">';
        echo '<div style="display: flex; align-items: center;">';
        echo '<label for="period">Select Period:</label>';
        echo '<select name="period" id="period">';
        echo '<option value="1" ' . selected($period, 1, false) . '>Today</option>';
        echo '<option value="7" ' . selected($period, 7, false) . '>Last 7 Days</option>';
        echo '<option value="30" ' . selected($period, 30, false) . '>Last 30 Days</option>';
        echo '<option value="90" ' . selected($period, 90, false) . '>Last 90 Days</option>';
        echo '<option value="365" ' . selected($period, 365, false) . '>Last 365 Days</option>';
        echo '</select>';
        echo '&nbsp;';
        echo '<input type="submit" value="Submit" class="button">';
        echo '</div>';
        echo '</form>';
        echo '<br>';
        echo wp_nonce_field('visitors_chart_nonce', 'sbb_nonce'); // Include nonce field
        echo '<table class="wp-list-table widefat striped" id="visitors-table_table">';
        echo '<thead><tr><th>Page Visited</th><th>Visits</th></tr></thead>';
        echo '<tbody>';
        // Loop through the results and display each row in the table
        foreach ($results as $row) {
            echo '<tr>';
            echo '<td>' . $row->page_visited . '</td>';
            echo '<td>' . $row->unique_visitors . '</td>';
            echo '</tr>';
        }
        // Close the table body and table tags
        echo '</tbody></table>';
        // Display pagination links using `paginate_links` with total pages and current page
        if ($total_pages > 1) {
            echo '<div class="tablenav bottom">';
            echo '<div class="tablenav" style="margin-left: 0;">';
            // echo '<span class="displaying-num">' . sprintf(_n('%s item', '%s items', $total_items), number_format_i18n($total_items)) . '</span>';
            echo '<span class="pagination-links">' . $pagination . '</span>';
            echo '</div>';
            echo '</div>';
        }
        echo '</form>';
    } else {
        // Display a message if no results are found
        echo 'No results found.';
    }
    */
        // Check if results exist
        if ($results) {
            echo '<form method="get" action="' . esc_url(admin_url('admin.php')) . '">';
            echo '<input type="hidden" name="page" value="stopbadbots_my-custom-submenu-page-stats">';
            echo '<div style="display: flex; align-items: center;">';
            echo '<label for="period">Select Period:</label>';
            echo '<select name="period" id="period">';
            echo '<option value="1" ' . selected($period, 1, false) . '>Today</option>';
            echo '<option value="7" ' . selected($period, 7, false) . '>Last 7 Days</option>';
            echo '<option value="30" ' . selected($period, 30, false) . '>Last 30 Days</option>';
            echo '<option value="90" ' . selected($period, 90, false) . '>Last 90 Days</option>';
            echo '<option value="365" ' . selected($period, 365, false) . '>Last 365 Days</option>';
            echo '</select>';
            echo '&nbsp;';
            echo '<input type="submit" value="Submit" class="button">';
            echo '</div>';
            echo '</form>';
            echo '<br>';
            //
            echo wp_nonce_field('visitors_chart_nonce', 'sbb_nonce'); // Include nonce field
            echo '<table class="wp-list-table widefat striped" id="visitors-table_table">';
            echo '<thead><tr><th>Page Visited</th><th>Visits</th></tr></thead>';
            echo '<tbody>';
            // Loop through the results and display each row in the table
            foreach ($results as $row) {
                echo '<tr>';
                echo '<td>' . esc_attr($row->page_visited) . '</td>';
                echo '<td>' . esc_attr($row->unique_visitors). '</td>';
                echo '</tr>';
            }
            // Close the table body and table tags
            echo '</tbody></table>';
            // Display pagination links using `paginate_links` with total pages and current page
            if ($total_pages > 1) {
                echo '<div class="tablenav bottom">';
                echo '<div class="tablenav" style="margin-left: 0;">';
                // echo '<span class="displaying-num">' . sprintf(_n('%s item', '%s items', $total_items), number_format_i18n($total_items)) . '</span>';
                //echo '<span class="pagination-links">' . $pagination . '</span>';
                $stopbadbots_allowed_tags = array(
                    'a' => array(
                        'href' => array(),
                        'title' => array(),
                    ),
                    'span' => array(
                        'class' => array(),
                    ),
                    'div' => array(
                        'class' => array(),
                    ),
                    'ul' => array(
                        'class' => array(),
                    ),
                    'li' => array(
                        'class' => array(),
                    ),
                    // Add more tags and attributes as needed
                );
                
                echo '<span class="pagination-links">' . wp_kses($pagination, $stopbadbots_allowed_tags) . '</span>';
                


                echo '</div>';
                echo '</div>';
            }
            echo '</form>';
        } else {
            // Display a message if no results are found
            echo 'No results found.';
        }
