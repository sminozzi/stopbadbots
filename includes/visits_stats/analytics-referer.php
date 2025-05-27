<?php
/*
Description: Creates Referer Table.
Version: 1.0
Author: Bill Minozzi
*/
if (!defined('ABSPATH')) {
	die('Invalid request.');
}
    // Define the default period
    
    // $period = isset($_GET['period']) ? $_GET['period'] : 30;

    $period = isset($_GET['period']) ? intval($_GET['period']) : 30;
    if($period < 1)
       $period = 1;

    
       $current_page2 = isset($_GET['paged2']) ? max(1, absint($_GET['paged2'])) : 1;
    if ($current_page2 === 1 && isset($_GET['page'])) {
        // Verifica se a URL contém "/page/" para extrair o número da página
        $page_url = admin_url('admin.php');
        $page_uri = parse_url($page_url, PHP_URL_QUERY);

        // parse_str($page_uri, $query_args);

        if (!is_null($page_uri) && !empty($page_uri)) {
            parse_str($page_uri, $query_args);
        } 


        if (isset($query_args['page']) && preg_match('/\/page\/(\d+)\//', $query_args['page'], $matches)) {
            if (!empty($matches[1])) {
                $current_page2 = max(1, absint($matches[1]));
            }
        }
    }
    if (isset($_GET['page2']) && is_numeric($_GET['page2']) && $_GET['page2'] > 0) {
        // Verify nonce before accepting user-provided page number
        if (!wp_verify_nonce(sanitize_text_field($_GET['sbb_nonce']), 'visitors_chart_nonce')) {
            die('Security check failed.');
        }
    }
    $total_pages = 1; // Placeholder, will be calculated 
    
    // $period = isset($_GET['period']) ? $_GET['period'] : 30;


$current_domain = parse_url(home_url(), PHP_URL_HOST);
//die($current_domain);
// Prepare the query to fetch referral data without ORDER BY and LIMIT
/*
$query = $wpdb->prepare("
SELECT 
    CASE 
        WHEN referer = '' THEN 'Direct Visits'
        WHEN SUBSTRING_INDEX(SUBSTRING_INDEX(referer, '://', -1), '/', 1) = %s THEN 'Site Navigation'
        ELSE SUBSTRING_INDEX(SUBSTRING_INDEX(referer, '://', -1), '/', 1)
    END AS referrer,
    COUNT(DISTINCT ip) AS total_visits
FROM 
    {$wpdb->prefix}sbb_visitorslog  
WHERE 
    DATE(date) >= CURDATE() - INTERVAL %d DAY 
    AND bot = '0'
    AND referer != 'Direct Visits'  
GROUP BY 
    referrer
", $current_domain, $period);
    $total_count = count($wpdb->get_results($query));
*/
/*
$table_name = $wpdb->prefix . 'sbb_visitorslog';
$query = $wpdb->prepare("
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
", $current_domain, $table_name, $period);

$results = $wpdb->get_results($query);
*/
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
", $current_domain, $table_name, $period));



$total_count = count($results);

    
        $per_page = 10;
        $total_pages = ceil($total_count / $per_page); 
        $current_page2 = min($current_page2, $total_pages); // Ensure page doesn't exceed total pages
    //  >>>>>> acertar page...       'base'      => add_query_arg(array('paged' => '%#%', 'wpmemory_nonce' => wp_create_nonce('sbb_pagination')), admin_url('tools.php?page=wp_memory_admin_page&tab=log')),
    $pagination_args2 = array(
        'base'      => add_query_arg(array('paged2' => '%#%', 'sbb_nonce' => wp_create_nonce('sbb_pagination')), sanitize_text_field($_SERVER['REQUEST_URI'])),
        'format'    => '',
        'prev_text' => '&laquo;',
        'next_text' => '&raquo;',
        'total'     => $total_pages,
        'current'   => $current_page2,
    );
    // Add nonce to pagination links
    $pagination2 = paginate_links($pagination_args2);
    $pagination2 = preg_replace('/href="([^"]*)"/', 'href="$1&amp;anchor=bottom#visitors-table-ref_table"', $pagination2);
    // Display the table
    $nonce = wp_create_nonce('sbb_pagination');
    $offset = ($current_page2 - 1) * $per_page;

    if($offset < 0)
    $offset = 0;


    // Prepare the query to fetch referral data
// Sanitizar o nome da tabela
/*
$table_name_sanitized = esc_sql($wpdb->prefix . 'sbb_visitorslog');

// Preparar e executar a consulta em uma única etapa
$query = $wpdb->prepare("
    SELECT 
        CASE 
            WHEN referer = '' THEN 'Direct Visits'
            WHEN SUBSTRING_INDEX(SUBSTRING_INDEX(referer, '://', -1), '/', 1) = %s THEN 'Site Navigation'
            ELSE SUBSTRING_INDEX(SUBSTRING_INDEX(referer, '://', -1), '/', 1)
        END AS referrer,
        COUNT(DISTINCT ip) AS total_visits
    FROM 
        $table_name_sanitized
    WHERE 
        DATE(date) >= CURDATE() - INTERVAL %d DAY 
        AND bot = '0'
        AND referer != 'Direct Visits'  
    GROUP BY 
        referrer
    ORDER BY 
        total_visits DESC
    LIMIT %d, %d
", $current_domain, $period, $offset, $per_page);

// Execute a consulta
$results = $wpdb->get_results($query);
*/

/*
$query = $wpdb->prepare("
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
", $current_domain,  $table_name, $period, $offset, $per_page);

$results = $wpdb->get_results($query);

*/

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
", $current_domain,  $table_name, $period, $offset, $per_page));

$total_count = count($results);



    // Check if results exist
    if ($results) {
        // Display the period selection dropdown
        echo '<form method="get" action="' . esc_url(admin_url('admin.php')) . '">';
        echo '<input type="hidden" name="page" value="stopbadbots_my-custom-submenu-page-stats">';
        echo '<div style="display: flex; align-items: center;">';
        echo '<label for="period">Select Period:</label>';
        echo '<select name="period" id="period">';
        echo '<option value="1" ' . selected($period, 1, false) . '>1 day</option>';
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
        // Display the table header
        echo '<table class="wp-list-table widefat striped" id="visitors-table-ref_table">';
        echo '<thead><tr><th>Referrer</th><th>Total Visits</th></tr></thead>';
        echo '<tbody>';
        // Loop through the results and display each row in the table
        foreach ($results as $row) {
            echo '<tr>';
            echo '<td>' . esc_attr($row->referrer) . '</td>';
            echo '<td>' . esc_attr( $row->total_visits) . '</td>';
            echo '</tr>';
        }
        // Close the table body and table tags
        echo '</tbody>';
        echo '</table>';
                // Display pagination links using `paginate_links` with total pages and current page
        if ($total_pages > 1) {
            echo '<div class="tablenav bottom">';
            echo '<div class="tablenav" style="margin-left: 0;">';
            // echo '<span class="displaying-num">' . sprintf(_n('%s item', '%s items', $total_items), number_format_i18n($total_items)) . '</span>';
            //echo '<span class="pagination-links">' . esc_html($pagination2) . '</span>';
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
            
            echo '<span class="pagination-links">' . wp_kses($pagination2, $stopbadbots_allowed_tags) . '</span>';
            
            echo '</div>';
            echo '</div>';
        }
        echo '</form>';
    } else {
        // Display a message if no results are found
        echo 'No results found.';
    }
