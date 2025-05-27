<?php if (! defined('ABSPATH')) {
	exit; // Exit if accessed directly
}
class sbb_List_Table3 extends WP_List_Table
{

	public function __construct()
	{
		// Set parent defaults.
		parent::__construct(
			array(
				'singular' => 'Referer', // Singular name of the listed records.
				'plural'   => 'Referers', // Plural name of the listed records.
				'ajax'     => false, // Does this table support ajax?
			)
		);
	}
	public function get_columns()
	{

		/*
		$columns = array(
			'cb'         => '<input type="checkbox" />', // Render a checkbox instead of text.
			'botname'    => esc_attr__('Referer Name', 'stopbadbots'),
			'botstate'   => esc_attr__('Status',  'stopbadbots'),
			'added'      => esc_attr__('Added by', 'stopbadbots'),
			'botblocked' => esc_attr__('Num Blocked', 'stopbadbots'),
		);
		*/

		$columns = array(
			'cb'          => '<input type="checkbox" />', // Render a checkbox instead of text.
			'botname'    => esc_attr__('Referer Name', 'stopbadbots'),
			'botstate'   => esc_attr__('Status', 'stopbadbots'),
			'added'      => esc_attr__('Added by', 'stopbadbots'),
			'botblocked' => esc_attr__('Num Blocked', 'stopbadbots'),
		);

		return $columns;
	}
	protected function get_sortable_columns()
	{
		/*
		$sortable_columns = array(
			'botname'    => array( __( 'botname', 'stopbadbots' ), true ),
			'botstate'   => array( 'botstate', true ),
			'added'      => array( 'botstate', true ),
			'botblocked' => array( __( 'botblocked', 'stopbadbots' ), true ),
		);
		*/

		/*
		$columns = array(
			
			'cb'         => '<input type="checkbox" />', // Render a checkbox instead of text.
			'botname'    => 'Referer Name',
			'botstate'   => 'Status',
			'added'      => 'Added by',
			'botblocked' => 'Num Blocked',
		);
		*/
		$sortable_columns = array(
			'botname'      => array('Referer Name',  true),
			'botstate'   => array('Status', true),
			'added'      => array('Added by', true),
			'botblocked' => array('Num Blocked', true),
		);
		return $sortable_columns;
	}
	protected function column_default($item, $column_name)
	{
		switch ($column_name) {
			case 'botname':
			case 'botstate':
			case 'botblocked':
			case 'added':
				return $item[$column_name];
			default:
				return print_r($item, true); // Show the whole array for troubleshooting purposes.
		}
	}
	protected function column_cb($item)
	{
		return sprintf(
			'<input type="checkbox" name="%1$s[]" value="%2$s" />',
			$this->_args['singular'], // Let's simply repurpose the table's singular label ("movie").
			$item['id'] // The value of the checkbox should be the record's ID.
		);
	}
	protected function column_nickname($item)
	{
		$page = wp_unslash(sanitize_text_field($_REQUEST['page'])); // WPCS: Input var ok.
		// Build activate row action.
		$edit_query_args     = array(
			'page'   => esc_attr($page),
			'action' => 'activate',
			'bot'    => $item['id'],
		);
		$actions['activate'] = sprintf(
			'<a href="%1$s">%2$s</a>',
			esc_url(
				wp_nonce_url(
					admin_url(add_query_arg($edit_query_args)),
					'editmovie_' . $item['ID']
				)
			),
			esc_attr__(
				'Change Status',
				'List table row action',
				'stopbadbots'
			)
		);
		// Build deactivate row action.
		$delete_query_args     = array(
			'page'   => esc_attr($page),
			'action' => 'deactivate',
			'bot'    => $item['id'],
		);
		$actions['deactivate'] = sprintf(
			'<a href="%1$s">%2$s</a>',
			esc_url(
				wp_nonce_url(admin_url(add_query_arg($delete_query_args)), 'deletemovie_' . $item['ID'])
			),
			esc_attr__('deactivate', 'List table row action', 'stopbadbots')
		);
		// Return the title contents.
		return sprintf(
			'%1$s <span style="color:silver;">(id:%2$s)</span>%3$s',
			$item['nickname'],
			$item['id'],
			$this->row_actions($actions)
		);
	}
	protected function get_bulk_actions()
	{
		$actions = array(
			'activate'   => esc_attr__('Activate', 'List table bulk action', 'stopbadbots'),
			'deactivate' => esc_attr__('deactivate', 'List table bulk action', 'stopbadbots'),
		);
		return $actions;
	}
	protected function process_bulk_action99999()
	{
		// Detect when a bulk action is being triggered.
		global $wpdb;
		if ('activate' === $this->current_action()) {
			if (isset($_GET['referer'])) {
				$ctd = 0;
				foreach (sanitize_text_field($_GET['referer']) as $botid) {
					$ctd++;
					$wpdb->show_errors();
					$result = $wpdb->update(
						$wpdb->prefix . 'sbb_badref',
						array(
							'botstate' =>
							'Enabled',
						),
						array('id' => sanitize_text_field($botid))
					);
					if (gettype($result) != 'integer') {
						if (gettype($result) != 'boolean') {
							if (! result) {
								$wpdb->print_error();
							}
						}
					}
					$wpdb->flush();
				}
				if ($ctd > 0) {
					echo '<h4>' . esc_attr($ctd) . ' updated!</h4>';
				}
			}
		}
		if ('deactivate' === $this->current_action()) {
			if (isset($_GET['referer'])) {
				$ctd = 0;
				foreach (sanitize_text_field($_GET['referer']) as $botid) {
					$ctd++;
					$wpdb->show_errors();
					$result = $wpdb->update(
						$wpdb->prefix . 'sbb_badref',
						array(
							'botstate' =>
							'Disabled',
						),
						array('id' => sanitize_text_field($botid))
					);
					if (gettype($result) != 'integer') {
						if (gettype($result) != 'boolean') {
							if (! result) {
								$wpdb->print_error();
							}
						}
					}
					$wpdb->flush();
				}
				if ($ctd > 0) {
					echo '<h4>' . esc_attr($ctd) . ' updated!</h4>';
				}
			}
		}
	}

	protected function process_bulk_action()
	{
		global $wpdb;

		// Detect when a bulk action is being triggered.
		if ('activate' === $this->current_action() || 'deactivate' === $this->current_action()) {
			if (isset($_GET['referer']) && is_array($_GET['referer'])) {
				$ctd = 0;
				foreach ($_GET['referer'] as $referer_id) {
					$referer_id = absint($referer_id); // Garante que $referer_id seja um número inteiro positivo

					// Determine o estado do botstate com base na ação
					$botstate = 'Disabled';
					if ('activate' === $this->current_action()) {
						$botstate = 'Enabled';
					}

					$wpdb->show_errors();
					$result = $wpdb->update(
						$wpdb->prefix . 'sbb_badref',
						array(
							'botstate' => $botstate,
						),
						array('id' => $referer_id),
						array('%s'),
						array('%d')
					);

					if (false === $result) {
						$wpdb->print_error();
					} else {
						$ctd++;
					}

					$wpdb->flush();
				}

				if ($ctd > 0) {
					echo '<h4>' . esc_html($ctd) . ' updated!</h4>';
				}
			}
		}
	}

	function sbb_prepare_items3()
	{
		global $wpdb;
		global $option;
		$user          = get_current_user_id();
		$screen        = get_current_screen();
		$screen_option = $screen->get_option('stopbadbots_per_page', 'option');
		$aper_page     = get_user_meta($user, $screen_option, false);
		if (isset($aper_page['stopbadbots_per_page'][0])) {
			$per_page = $aper_page['stopbadbots_per_page'][0];
		} else {
			$per_page = 50;
		}
		if (empty($per_page) || $per_page < 1) {
			$per_page = 50;
		}
		$columns               = $this->get_columns();
		$hidden                = array();
		$sortable              = $this->get_sortable_columns();
		$this->_column_headers = array(
			$columns,
			$hidden,
			$sortable,
		);
		$this->process_bulk_action();
		$current_table = $wpdb->prefix . 'sbb_badref';
		if (isset($_GET['order'])) {
			$order = sanitize_text_field($_GET['order']);
		} else {
			$order = 'asc';
		}
		if (isset($_GET['orderby'])) {
			$orderby = sanitize_text_field($_GET['orderby']);
		} else {
			$orderby = 'botname';
		}

		if (strlen($order) > 5) {
			$order = 'asc';
		}

		if (strlen($orderby) > 13) {
			$orderby = 'botname';
		}

		// extra-sanitize
		$order   = sanitize_sql_orderby($order);
		$orderby = str_replace(' ', '', $orderby);

		if (isset($_GET['s'])) {

			$my_search = sanitize_text_field($_GET['s']);
			$my_search = str_replace(' ', '', $my_search);

			/*
			$my_search = sanitize_text_field($_GET['s']);
			$results = $wpdb->get_results("SELECT * FROM $current_table  WHERE
			`botname` LIKE  '%" . $my_search . "%'
			 order by " . $orderby . " " . $order);
			*/

			/*
			$results = $wpdb->get_results(
				$wpdb->prepare(
					"SELECT * FROM %i  
					WHERE 
					`botname` LIKE %s  
					ORDER BY $orderby $order",
					$current_table,
					'%' . $my_search . '%'
				)

				);
				*/

			$order   = sanitize_sql_orderby($order);
			$orderby = str_replace(' ', '', $orderby);
			$my_search = sanitize_text_field($_GET['s']);


			$valid_columns = array('botname', 'botstate', 'added', 'botblocked');
			$orderby = in_array($orderby, $valid_columns) ? $orderby : 'botname'; // Valor padrão se inválido
			$order = strtoupper($order) === 'DESC' ? 'DESC' : 'ASC'; // Valor padrão se inválido


			$results = $wpdb->get_results("SELECT * FROM $current_table  WHERE
				`botname` LIKE  '%" . $my_search . "%'
				 order by " . $orderby . " " . $order);


			/*
			$results = $wpdb->get_results(
				$wpdb->prepare(
					"SELECT * FROM %i WHERE botname LIKE %s ORDER BY $orderby $order",
					$current_table,
					'%' . $wpdb->esc_like($my_search) . '%'
				)
			);
			*/
		} else {


			$order   = sanitize_sql_orderby($order);
			$orderby = str_replace(' ', '', $orderby);

			$valid_columns = array('botname', 'botstate', 'added', 'botblocked');
			$orderby = in_array($orderby, $valid_columns) ? $orderby : 'botname'; // Valor padrão se inválido
			$order = strtoupper($order) === 'DESC' ? 'DESC' : 'ASC'; // Valor padrão se inválido



			$results = $wpdb->get_results("SELECT * FROM $current_table  
			 order by " . $orderby . " " . $order);


			/*
			$results = $wpdb->get_results(
				$wpdb->prepare(
					"SELECT * FROM %i ORDER BY %s %s",
					$current_table,
					$orderby,
					$order
				)
			);
			*/
		}

		$data = array();
		$i    = 0;
		foreach ($results as $querydatum) {
			array_push($data, (array) $querydatum);
		}
		$current_page = $this->get_pagenum();
		$total_items  = count($data);
		$data         = array_slice($data, (($current_page - 1) * $per_page), $per_page);
		$this->items  = $data;
		$this->set_pagination_args(
			array(
				'total_items' => $total_items, // WE have to calculate the total number of items.
				'per_page'    => $per_page, // WE have to determine how many items to show on a page.
				'total_pages' => ceil($total_items / $per_page), // WE have to calculate the total number of pages.
			)
		);
	}
	protected function usort_reorder($a, $b)
	{
		// If no sort, default to title.
		$orderby = ! empty($_REQUEST['orderby']) ? wp_unslash(sanitize_text_field($_REQUEST['orderby'])) :
			'botname'; // WPCS: Input var ok.
		// If no order, default to asc.
		$order = ! empty($_REQUEST['order']) ? wp_unslash(sanitize_text_field($_REQUEST['order'])) : 'asc'; // WPCS: Input var ok.
		// Determine sort order.

		if (strlen($order) > 5) {
			$order = 'asc';
		}

		if (strlen($orderby) > 13) {
			$orderby = 'botname';
		}

		$result = strcmp($a[$orderby], $b[$orderby]);
		return ('asc' === $order) ? $result : -$result;
	}
}
