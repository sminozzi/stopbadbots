<?php  namespace StopBadBotsWPSettings;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
/**
 * Page builder classes that register and render the settings pages.
 *
 * @author Laura Dobkins <lauradobkins@paintedcloud.us>
 */
/**
 * Base class for optons page builders.
 */
class OptionPageBuilder {
	protected $page;
	protected $tabs;
	protected $scripts;
	protected $styles;
	public function __construct( $page, $scripts = array(), $styles = array() ) {
		// Initialize page and register page action
		$this->page = $page;
		add_action( 'admin_menu', array( $this, 'register_page' ) );
		// Add user supplied scripts for this page
		$this->scripts = $scripts;
		// Add user supplied stylesheets
		$this->styles = $styles;
		global $stopbadbots_settings_config;
		// Load PCS Settings stylesheet.

				// 'src'=> $stopbadbots_settings_config['base_uri'] . 'styles/admin-settings.css',

		$this->styles[] = array(
			'handle'  => 'pcs-admin-settings',
			'src'     => STOPBADBOTSURL . 'settings/styles/admin-settings.css',
			'enqueue' => true,
		);
		// add_action('admin_enqueue_scripts', array($this, 'admin_enqueue_scripts'));
		// moved to Stop Bad Bots.php
	}
	public function register_page() {
		switch ( $this->page->type ) {
			case 'menu':
				// TODO: Add icon url and postion configuration values
				add_menu_page(
					$this->page->title,
					$this->page->menu_title,
					$this->page->capability,
					$this->page->slug,
					array( $this, 'render' )
				);
				$this->page->set_hook( 'toplevel_page_' );
				break;
			case 'submenu2':
				add_submenu_page(
					'stop_bad_bots_plugin', // parent slug
					'Settings', // title
					'Settings1', // menu title
					$this->page->capability,
					$this->page->slug, // menu slug stop-bad-bots
					// 'stopbadbots_settings', // menu slug
					array( $this, 'render' )
				);
				break;
			case 'submenu':
				add_submenu_page(
					$this->page->parent_slug,
					$this->page->title,
					'Settings', // $this->page->menu_title,
					$this->page->capability,
					$this->page->slug,
					array( $this, 'render' ),
					1
				);
				break;
			case 'settings':
				add_options_page( $this->page->title, $this->page->menu_title, $this->page->capability, $this->page->slug, array( $this, 'render' ) );
				$this->page->set_hook( 'settings_page_' );
				break;
			default:
				add_theme_page( $this->page->title, $this->page->menu_title, $this->page->capability, $this->page->slug, array( $this, 'render' ) );
				$this->page->set_hook( 'appearance_page_' );
				break;
		}
	}
	public function admin_enqueue_scripts( $page_hook ) {
		// Only load our scripts on our page
		if ( $this->page->hook == $page_hook ) {
			// Process the Scripts
			foreach ( $this->scripts as $script ) {
				$deps = ( isset( $script['deps'] ) ) ? $script['deps'] : array();
				if ( isset( $script['enqueue'] ) && $script['enqueue'] ) {
					if ( isset( $script['src'] ) && ! wp_script_is( $script['handle'], 'registered' ) ) {
						wp_register_script( $script['handle'], $script['src'], $deps );
					}
					if ( ! wp_script_is( $script['handle'], 'enqueued' ) ) {
						wp_enqueue_script( $script['handle'] );
					}
				} else {
					if ( isset( $script['src'] ) && ! wp_script_is( $script['handle'], 'registered' ) ) {
						wp_register_script( $script['handle'], $script['src'], $script['deps'] );
					}
				}
			}
			// Process the Styles
			foreach ( $this->styles as $style ) {
				$deps = ( isset( $style['deps'] ) ) ? $style['deps'] : array();
				if ( isset( $style['enqueue'] ) && $style['enqueue'] ) {
					if ( isset( $style['src'] ) && ! wp_style_is( $style['handle'], 'registered' ) ) {
						wp_register_style( $style['handle'], $style['src'], $deps );
					}
					if ( ! wp_style_is( $style['handle'], 'enqueued' ) ) {
						wp_enqueue_style( $style['handle'] );
					}
				} else {
					if ( isset( $style['src'] ) && ! wp_style_is( $style['handle'], 'registered' ) ) {
						wp_register_style( $style['handle'], $style['src'], $style['deps'] );
					}
				}
			}
		}
	}
	public function render() {
		do_action( 'pcs_render_option_page' );
		echo esc_attr( $this->page->markup_top );
		echo '<form method="post" action="options.php">';
		// TODO: only output errors on custom pages
		// settings_errors();
		settings_fields( $this->page->slug );
		do_settings_sections( $this->page->slug );
		submit_button();
		echo '</form>';
		$this->render_reset_form();
		echo esc_attr( $this->page->markup_bottom );
	}
	public function render_reset_form( $active_tab = null ) {
		// echo reset form
		echo '<form method="post" action="' . esc_attr(str_replace( '&settings-updated=true', '', esc_url( sanitize_text_field($_SERVER['REQUEST_URI'] )) ) . '" class="reset-form">');
		// Reset nonce
		wp_nonce_field( 'pcs_reset_options', 'pcs_reset_options_nonce' );
		echo '<input type="hidden" name="action" value="reset" />';
		if ( ! is_null( $active_tab ) ) {
			echo '<button type="submit" class="button secondary reset-settings" title="Reset ' . esc_attr( $active_tab->title ) . '">Reset ' . esc_attr( $active_tab->title ) . '</button>';
		} else {
			echo '<button type="submit" class="button secondary reset-settings" title="Reset Options">Reset Options</button>';
		}
		echo '</form>';
	}
}
/**
 * Single options page builder
 */
class OptionPageBuilderSingle extends OptionPageBuilder {
	public function __construct( $page, $section_settings = array(), $scripts = array(), $styles = array() ) {
		parent::__construct( $page, $scripts, $styles );
		new SectionFactory( $page, $section_settings );
	}
}
/**
 * Tabbed options page builder.
 */
class OptionPageBuilderTabbed extends OptionPageBuilder {
	protected $tabs;
	public function __construct( $page, $options_settings = array(), $scripts = array(), $styles = array() ) {
		parent::__construct( $page, $scripts, $styles );
		$this->tabs = array();
		$counter    = 0;
		// Runs when posting to option.php
		// Only create the active tab so the other page sections
		// Do not get overwritten
		$action   = ( isset( $_POST['action'] ) ) ? sanitize_text_field( $_POST['action'] ) : false;
		$page_key = ( isset( $_POST['option_page'] ) ) ? sanitize_text_field( $_POST['option_page'] ) : false;
		if ( $page_key == $page->slug && $action == 'update' ) {
			// Extract the tab id from the referer post
			$referrer = ( isset( $_POST['_wp_http_referer'] ) ) ? sanitize_text_field( $_POST['_wp_http_referer'] ) : '';
			$matches  = array();
			preg_match( '/tab=([^&]*)/', $referrer, $matches );
			// Build the Tab Sections for the submitted tab
			foreach ( $options_settings as $title => $section_settings ) {
				$id = str_replace( '-', '_', sanitize_title_with_dashes( $title ) );
				if ( isset( $matches[1] ) && $matches[1] == $id ) {
					// Tab submitted was determined
					$this->tabs[] = new Tab( $title, $id, $this->page, $section_settings, true );
					break;
				}
				// Cache first id for use if no tab match is found
				if ( $counter == 0 ) {
					$first = array(
						'id'       => $id,
						'title'    => $title,
						'settings' => $section_settings,
					);
				}
				$counter++;
			}
			// If no tab was created
			// create the default tab with the first id
			if ( empty( $this->tabs ) ) {
				$this->tabs[] = new Tab( $first['title'], $first['id'], $this->page, $first['settings'], true );
			}
		} else {
			// Runs when displaying the options page
			// Show the first tab as active by default
			foreach ( $options_settings as $title => $section_settings ) {
				$id = str_replace( '-', '_', sanitize_title_with_dashes( $title ) );
				// Each Key Is Tab
				// Set first one to active by default
				if ( $counter == 0 ) {
					$this->tabs[] = new Tab( $title, $id, $this->page, $section_settings, true );
				} else {
					$this->tabs[] = new Tab( $title, $id, $this->page, $section_settings );
				}
				$counter++;
			}
		}
	}
	public function render() {
		global $stopbadbots_checkversion;

		$allowed_atts = array(
			'align'      => array(),
			'class'      => array(),
			'type'       => array(),
			'id'         => array(),
			'dir'        => array(),
			'lang'       => array(),
			'style'      => array(),
			'xml:lang'   => array(),
			'src'        => array(),
			'alt'        => array(),
			'href'       => array(),
			'rel'        => array(),
			'rev'        => array(),
			'target'     => array(),
			'novalidate' => array(),
			'type'       => array(),
			'value'      => array(),
			'name'       => array(),
			'tabindex'   => array(),
			'action'     => array(),
			'method'     => array(),
			'for'        => array(),
			'width'      => array(),
			'height'     => array(),
			'data'       => array(),
			'title'      => array(),

			'checked'    => array(),
			'selected'   => array(),

		);

		$my_allowed['form']   = $allowed_atts;
		$my_allowed['select'] = $allowed_atts;
		// select options
		$my_allowed['option']   = $allowed_atts;
		$my_allowed['style']    = $allowed_atts;
		$my_allowed['label']    = $allowed_atts;
		$my_allowed['input']    = $allowed_atts;
		$my_allowed['textarea'] = $allowed_atts;

		// more...future...
		$my_allowed['form']     = $allowed_atts;
		$my_allowed['label']    = $allowed_atts;
		$my_allowed['input']    = $allowed_atts;
		$my_allowed['textarea'] = $allowed_atts;
		$my_allowed['iframe']   = $allowed_atts;
		$my_allowed['script']   = $allowed_atts;
		$my_allowed['style']    = $allowed_atts;
		$my_allowed['strong']   = $allowed_atts;
		$my_allowed['small']    = $allowed_atts;
		$my_allowed['table']    = $allowed_atts;
		$my_allowed['span']     = $allowed_atts;
		$my_allowed['abbr']     = $allowed_atts;
		$my_allowed['code']     = $allowed_atts;
		$my_allowed['pre']      = $allowed_atts;
		$my_allowed['div']      = $allowed_atts;
		$my_allowed['img']      = $allowed_atts;
		$my_allowed['h1']       = $allowed_atts;
		$my_allowed['h2']       = $allowed_atts;
		$my_allowed['h3']       = $allowed_atts;
		$my_allowed['h4']       = $allowed_atts;
		$my_allowed['h5']       = $allowed_atts;
		$my_allowed['h6']       = $allowed_atts;
		$my_allowed['ol']       = $allowed_atts;
		$my_allowed['ul']       = $allowed_atts;
		$my_allowed['li']       = $allowed_atts;
		$my_allowed['em']       = $allowed_atts;
		$my_allowed['hr']       = $allowed_atts;
		$my_allowed['br']       = $allowed_atts;
		$my_allowed['tr']       = $allowed_atts;
		$my_allowed['td']       = $allowed_atts;
		$my_allowed['p']        = $allowed_atts;
		$my_allowed['a']        = $allowed_atts;
		$my_allowed['b']        = $allowed_atts;
		$my_allowed['i']        = $allowed_atts;

		$active_tab_id = ( isset( $_GET['tab'] ) ) ? sanitize_text_field( $_GET['tab'] ) : $this->tabs[0]->id;
		do_action( 'pcs_render_option_page' );
		// echo $this->page->markup_top;
		echo wp_kses( $this->page->markup_top, $my_allowed );
		// echo esc_attr($this->page->markup_top);

		echo '<div id="containerleft">';

		echo '<form method="post" action="options.php">';
		settings_errors();
		// Output all tab headings
		echo '<h2 class="nav-tab-wrapper">';
		foreach ( $this->tabs as $tab ) {
			// Outbut Tabs
			if ( $tab->active ) {
				// echo $tab->get_anchor(true);
				echo wp_kses( $tab->get_anchor( true ), $my_allowed );
				// Cache active tab to reneder sections later
				$active_tab = $tab;
			} else {
				echo wp_kses( $tab->get_anchor( true ), $my_allowed );
			}
		}
		echo '</h2>';
		settings_fields( $this->page->slug );
		do_settings_sections( $this->page->slug );

		/*
		if ($active_tab_id <> 'startup_guide' and $active_tab_id <> 'memory_checkup'
		and $active_tab_id <> 'anti_hacker' and $active_tab_id <> 'recaptcha' and $active_tab_id <> 'useful_tools')
		submit_button();
		*/

		// submit_button();

		// echo '</form>';

		// $this->render_reset_form( $active_tab );
		// echo esc_attr($this->page->markup_bottom);
		// echo wp_kses($this->page->markup_bottom, $my_allowed);

		submit_button();
		 echo '</form>';
		// $this->render_reset_form( $active_tab );
		echo '</div>'; // containerleft

		if ( $active_tab_id <> 'startup_guide' and $active_tab_id <> 'go_premium' ) {
			// require_once 'mybanners.inc';
		} else {
			// submit_button();
			// echo '</form>';
			// $this->render_reset_form( $active_tab );
			// echo '</div>'; //containerleft

		}

		echo wp_kses( $this->page->markup_bottom, $my_allowed );
	}
}
