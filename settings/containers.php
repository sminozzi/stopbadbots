<?php namespace StopBadBotsWPSettings;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
/**
 * Settings container entity classes.
 */
/**
 * Options page entity.
 */
class Page {
	protected $slug;
	protected $hook;
	protected $page_title;
	protected $menu_title;
	protected $capability;
	protected $icon;
	protected $markup_top;
	protected $markup_bottom;
	protected $type;
	protected $parent_slug;
	public function __construct( $menu_title, $settings = array() ) {
		$this->menu_title = $menu_title;
		$default_settings = array(
			'slug'       => ( isset( $settings['slug'] ) ) ? $settings['slug'] : sanitize_title_with_dashes( $menu_title ),
			'page_title' => ( isset( $settings['page_title'] ) ) ? $settings['page_title'] : $menu_title,
			'capability' => ( isset( $settings['capability'] ) ) ? $settings['capability'] : 'manage_options',
			'icon'       => ( isset( $settings['icon'] ) ) ? $settings['icon'] : 'icon-options-general',
			'type'       => 'theme',
		);
		$settings         = array_merge( $default_settings, $settings );
		// Assign to properties
		foreach ( $settings as $key => $value ) {
			if ( property_exists( $this, $key ) ) {
				$this->$key = $value;
			}
		}
		// Initialize Default Top & Bottom Markup
		$this->set_markup_top();
		$this->set_markup_bottom();
	}
	public function __get( $key ) {
		if ( property_exists( $this, $key ) ) {
			return $this->$key;
		}
	}
	public function set_hook( $value ) {
		$this->hook = $value . $this->slug;
	}
	public function set_markup_top( $markup = false ) {
		if ( ! $markup ) {
			$this->markup_top = '<div class="wrap"><div id="icon-options-general" class="icon32"></div>';
			// $this->markup_top .= '<h2>' . $this->menu_title . '</h2>';
			$this->markup_top .= '<img id="bill_admin_logo" src="' . STOPBADBOTSURL . '/images/logo.png" />';
		} else {
			$this->markup_top = $markup;
		}
	}
	public function set_markup_bottom( $markup = false ) {
		if ( ! $markup ) {
			$this->markup_bottom = '</div>';
		} else {
			$this->markup_bottom = $markup;
		}
	}
}
/**
 * Options tab entity.
 */
class Tab {
	protected $id;
	protected $title;
	protected $page;
	protected $anchor;
	protected $active;
	public function __construct( $title, $id, $page, $section_option_settings = array(), $active_state = false ) {
		$this->title  = $title;
		$this->page   = $page;
		$this->id     = $id;
		$this->anchor = '<a href="?page=' . $this->page->slug . '&tab=' . $this->id . '" class="nav-tab">' . $this->title . '</a>';
		// Check the Query string to set the active state
		$this->set_active( $active_state );
		// Only create sections for active tab
		if ( $this->active ) {
			new SectionFactory( $page, $section_option_settings, $this );
		}
	}
	public function __get( $key ) {
		if ( property_exists( $this, $key ) ) {
			return $this->$key;
		}
	}
	public function set_active( $active_state ) {
		// Determine if this is the active tab
		if ( isset( $_GET['tab'] ) ) {
			if ( $this->id == sanitize_text_field($_GET['tab']) ) {
				$this->active = true;
			}
		} else {
			if ( $active_state ) {
				$this->active = true;
			} else {
				$this->active = false;
			}
		}
	}
	public function get_anchor( $active = false ) {
		if ( $active ) {
			return '<a href="?page=' . $this->page->slug . '&tab=' . $this->id . '" class="nav-tab nav-tab-active">' . $this->title . '</a>';
		}
		return $this->anchor;
	}
}
/**
 * Options section entity.
 */
class Section {
	protected $id;
	protected $title;
	protected $page;
	protected $page_key;
	protected $info;
	protected $settings_factory;
	public function __construct( $id, $title, $info, $page, $page_key, $field_settings = array() ) {
		$this->id               = $id;
		$this->title            = $title;
		$this->info             = $info;
		$this->page             = $page;
		$this->page_key         = $page_key;
		$this->settings_factory = new SettingsFactory( $this, $field_settings );
	}
	public function __get( $key ) {
		if ( property_exists( $this, $key ) ) {
			return $this->$key;
		}
	}
	public function render() {
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

		/*
		$allowed_tags = wp_kses_allowed_html('post');
		wp_kses(stripslashes_deep($input['custom_message']), $allowed_tags);
		*/

		 echo '<p>' . wp_kses( $this->info, $my_allowed ) . '</p>';

		// echo '<p>' . $this->info . '</p>';
	}
}
