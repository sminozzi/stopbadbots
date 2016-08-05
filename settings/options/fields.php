<?php

$fields = array();

// Text Input
$fields[] = array(
	'type' 	=> 'text',
	'name' 	=> 'my_textfield',
	'label' => 'My Text Field',
	'id' => 'my_textfield', // (optional, will default to name)
	'value' => 'Default Value' // (optional, will default to '')
	);

// Color Input
$fields[] = array(
	'type' 	=> 'color',
	'name' 	=> 'my_colorfield',
	'label' => 'My Color Field',
	'id' => 'my_colorfield', // (optional, will default to name)
	'value' => '#FFFFFF' // (optional, will default to '')
	);

// Textarea Input
$fields[] = array(
	'type' 	=> 'textarea',
	'name' 	=> 'my_textarea',
	'label' => 'My Textarea',
	'id' => 'my_textarea', // (optional, will default to name)
	'value' => 'Default Value' // (optional, will default to '')
	);

// Checkbox Input
$fields[] = array(
	'type' 	=> 'checkbox',
	'name' 	=> 'my_checkbox',
	'label' => 'My Checkbox',
	'id' => 'my_checkbox', // (optional, will default to name)
	'value' => 1 // (optional, 1 is checked, will default to 0)
	);

// Select List
$fields[] = array(
	'type' 	=> 'select',
	'name' 	=> 'my_select',
	'label' => 'My Select',
	'id' => 'my_select', // (optional, will default to name)
	'value' => 'red', // (optional, will default to '')
	'select_options' => array(
		array('value'=>'red', 'label' => 'Red'),
		array('value'=>'blue', 'label' => 'Blue'),
		array('value'=>'green', 'label' => 'Green')
		)			
	);

// Radio List
$fields[] = array(
	'type' 	=> 'radio',
	'name' 	=> 'my_radio',
	'label' => 'My Radio',
	'id' => 'my_radio', // (optional, will default to name)
	'value' => 'red', // (optional, will default to '')
	'radio_options' => array(
		array('value'=>'red', 'label' => 'Red'),
		array('value'=>'blue', 'label' => 'Blue'),
		array('value'=>'green', 'label' => 'Green')
		)			
	);

// Upload Field
$fields[] = array(
	'type' 	=> 'upload',
	'name' 	=> 'my_upload',
	'label' => 'My Upload',
	'id' => 'my_upload', // (optional, will default to name)
	'value' => 'Default Value' // (optional, will default to '')
	);

// Wordpress Editor
$fields[] = array(
	'type' 	=> 'editor',
	'name' 	=> 'my_editor',
	'label' => 'My Editor',
	'id' => 'my_editor', // (optional, will default to name)
	'value' => 'Default Value', // (optional, will default to '')
	'editor_settings' => array('media_buttons' => false) // (optional, settings found in wp_editor arguments)
	);

// Multi Field
$my_multi_fields = array();

$my_multi_fields[] = array(
	'type' 	=> 'radio',
	'name' 	=> 'my_multi_radio',
	'label' => 'My Multi Radio',
	'radio_options' => array(
		array('value'=>'red', 'label' => 'Red'),
		array('value'=>'blue', 'label' => 'Blue'),
		array('value'=>'green', 'label' => 'Green')
		)			
	);

$my_multi_fields[] = array(
	'type' 	=> 'text',
	'name' 	=> 'my_multi_text',
	'label' => 'My Multi Text'		
	);

$fields[] = array(
	'type' 	=> 'multi',
	'name' 	=> 'my_multi',
	'label' => 'My Multi',
	'id' => 'my_multi', // (optional, will default to name)
	'limit' => 3, // (optional, will default to unlimited)
	'fields' => $my_multi_fields
	);
