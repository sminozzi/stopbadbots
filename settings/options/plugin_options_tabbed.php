<?php

namespace StopBadBotsWPSettings;

// $mypage = new Page('Stop Bad Bots', array('type' => 'menu'));
$mypage   = new Page(
	'Settings Stop Bad Bots',
	array(
		'type'        => 'submenu',
		'parent_slug' => 'stop_bad_bots_plugin',
	)
);
$settings = array();
require_once STOPBADBOTSPATH . 'guide/guide.php';

if (! empty($stopbadbots_checkversion)) {
	$pro_enabled = '[Pro enabled]';
} else {
	$pro_enabled = '';
}



$settings['Startup Guide']['Startup Guide'] = array('info' => $ah_help);
$fields                                     = array();
$settings['Startup Guide']['Startup Guide']['fields'] = $fields;
$msg2  = '<b>' . __('Block all Bots included at Bad Bots Table?', 'stopbadbots') . '</b>';
$msg2 .= '<br />';
$msg2 .= __('You need only check yes or no below. All Bad Bots enabled at Bad Bots Table will be blocked.', 'stopbadbots');
$msg2 .= '<br />';
$msg2 .= __('To manage the bots individually, go to Bad Bots Table (Dashboard=> Stop Bad Bots =>Bad Bots Table).', 'stopbadbots');
$msg2 .= '<br />';
$msg2 .= __('If you check "test mode", the system don\'t block but update statistics and send email notifications to you.', 'stopbadbots');

$msg2 .= '<br />';
$msg2 .= __('Then click SAVE CHANGES.', 'stopbadbots');
$msg2 .= '<br />';
$msg2 .= '<br />';
$msg2 .= '<b>' . __('Block all Bots included at Bad IPs Table?', 'stopbadbots') . '</b>';
$msg2 .= '<br />';
$msg2 .= __('You need only check yes or no below. All Bad IPs enabled at Bad IPs Table will be blocked.', 'stopbadbots');
$msg2 .= '<br />';
$msg2 .= __('To manage the IPs individually, go to Bad IPs Table (Dashboard=> Stop Bad Bots =>Bad IPs Table).', 'stopbadbots');
$msg2 .= '<br />';
$msg2 .= __('Then click SAVE CHANGES.', 'stopbadbots');



$msg2 .= '<br />';
$msg2 .= '<br />';
$msg2 .= '<b>' . __('Enable Block Bad Referer?', 'stopbadbots') . '</b>';
$msg2 .= '<br />';
$msg2 .= __('Enabling Block Bad Referer the plugin will blocks  websites that use Referer Spam to promote their.', 'stopbadbots');


$msg2 .= '<br />';
$msg2 .= '<br />';
$msg2 .= '<b>' . __('Enable Firewall?', 'stopbadbots') . '</b>';
$msg2 .= '<br />';
$msg2 .= __('Enabling Firewall the plugin will blocks malicious requests also from hackers. 100% Plug-n-play, no configuration required.', 'stopbadbots');
$msg2 .= '<br />';


$msg2 .= '<br />';
$msg2 .= '<b>' . __('Participate in the Real-Time Bad Bots Security Network?', 'stopbadbots') . '</b>';
$msg2 .= '<br />';
$msg2 .= __('Enabling this feature causes your site to anonymously share data with Stop Bad Bots on Bad Bots visits. In return your WordPress site receives updates at your Bad Bots Table with new Bad Bots Names, IPs and bad referer.', 'stopbadbots');
$msg2 .= '<br />';
$msg2 .= __('No personally identifiable data is sent by this option and we also do not associate any of the data we do receive with your specific website. The data is aggregated on a real-time platform to determine which Bots are currently engaged in negative activity and need to be blocked by our community.', 'stopbadbots');
$msg2 .= '<br />';

$msg2 .= '<br />';
$msg2 .= '<b>' . __('Block all with Blank User Agent?', 'stopbadbots') . '</b>';
$msg2 .= '<br />';
$msg2 .= __('This can reduce abuse against your site. Look your metrics menu in cPanel or request support to your hosting company for details about your visitors, if necessary.', 'stopbadbots');
$msg2 .= '<br />';
$msg2 .= '<br />';
$msg2 .= '<b>' . __('Block User Enumeration?', 'stopbadbots') . '</b>';
$msg2 .= '<br />';
$msg2 .= __('This can block bad bots scan by user id and user and login name. This happens a lot and is often a pre-cursor to very nasty activities and attacks.', 'stopbadbots');
$msg2 .= '<br />';

$msg2 .= '<br />';
$msg2 .= '<b>' . __('Block PingBack Request?', 'stopbadbots') . '</b>';
$msg2 .= '<br />';
$msg2 .= __('PingBack request happens a lot and is often a pre-cursor to brute-force password attacks and others nasty things.', 'stopbadbots');
$msg2 .= '<br />';



$msg2 .= '<br />';
$msg2 .= '<b>' . __('Find the balance between our software and server power with Block Engine Management, which lets you control user agent filtering, server CPU utilization, and visitor behavior.', 'stopbadbots') . '</b>';
$msg2 .= '<br />';
$msg2 .= __('Maximum Block option: may result in false positives if your users use proxies, VPNs, or block cookies and/or JavaScript.', 'stopbadbots');
$msg2 .= '<br />';
$msg2 .= __('Conservative option:  may allow some advanced bots but avoids false positives. Conservative (and Maximum) also considers the behavior of bots 
.', 'stopbadbots');
$msg2 .= '<br />';
$msg2 .= __('Standard option:  maintains a balance between Conservative and Maximum Block and also considers the behavior of bots.', 'stopbadbots');
$msg2 .= '<br />';
$msg2 .= __('Minimal option: only blocks bots included in our database but maybe smart bots can overload your server.', 'stopbadbots');


$msg2 .= '<br />';
$msg2 .= __('The default option is Conservative.', 'stopbadbots');
$msg2 .= '<br />';
$msg2 .= __('The free version includes only the Minimal and Conservative options.', 'stopbadbots');


/*
$msg2 .= '<br />';


$msg2 .= '<br />';
$msg2 .= '<b>'.__('Block all Comments?','stopbadbots').'</b>';
$msg2 .= '<br />';
$msg2 .= __('This can block all bots (and users) from post comments directly to file wp-comments-post.php and save bandwidth.','stopbadbots');
*/


$settings['General Settings'][__('Instructions')] = array('info' => $msg2);
$fields   = array();
$fields[] = array(
	'type'          => 'radio',
	'name'          => 'stop_bad_bots_active',
	'label'         => __('Block all Bots included at Bad Bots Table?', 'stopbadbots'),
	'radio_options' => array(
		array(
			'value' => 'yes',
			'label' => __('yes', 'stopbadbots'),

		),
		array(
			'value' => 'no',
			'label' => __('no', 'stopbadbots'),

		),
		array(
			'value' => 'test',
			'label' => __('test mode', 'stopbadbots'),

		),
	),
);
$fields[] = array(
	'type'          => 'radio',
	'name'          => 'stop_bad_bots_ip_active',
	'label'         => __('Block all IPs included at Bad IPs Table?', 'stopbadbots'),
	'radio_options' => array(
		array(
			'value' => 'yes',
			'label' => __('yes', 'stopbadbots'),
		),
		array(
			'value' => 'no',
			'label' => __('no', 'stopbadbots'),

		),
	),
);

$fields[] = array(
	'type'          => 'radio',
	'name'          => 'stop_bad_bots_referer_active',
	'label'         => __('Block all bots included at Bad Referer Table?', 'stopbadbots'),
	'radio_options' => array(
		array(
			'value' => 'yes',
			'label' => __('yes', 'stopbadbots'),

		),
		array(
			'value' => 'no',
			'label' => __('no', 'stopbadbots'),

		),
	),
);

$fields[] = array(
	'type'          => 'radio',
	'name'          => 'stopbadbots_engine_option',
	'label'         => __('Block Engine Management (Only Minimal and Conservative options available in the free version of the plugin).', 'stopbadbots') . '  ' . $pro_enabled,
	'radio_options' => array(
		array(
			'value' => 'minimal',
			'label' => __('Minimal', 'stopbadbots'),
		),
		array(
			'value' => 'conservative',
			'label' => __('Conservative', 'stopbadbots'),
		),
		array(
			'value' => 'normal',
			'label' => __('Standard', 'stopbadbots'),
		),
		array(
			'value' => 'maximum',
			'label' => __('Maximum Block', 'stopbadbots'),
		),
	),
);

$fields[] = array(
	'type'          => 'radio',
	'name'          => 'stopbadbots_firewall',
	'label'         => __('Enable Firewall? (available only in pro version)', 'stopbadbots') . '  ' . $pro_enabled,
	'radio_options' => array(
		array(
			'value' => 'yes',
			'label' => __('yes', 'stopbadbots'),

		),
		array(
			'value' => 'no',
			'label' => __('no', 'stopbadbots'),

		),
	),
);


$fields[] = array(
	'type'          => 'radio',
	'name'          => 'stopbadbots_firewall',
	'label'         => __('Enable Firewall? (available only in pro version)', 'stopbadbots') . '  ' . $pro_enabled,
	'radio_options' => array(
		array(
			'value' => 'yes',
			'label' => __('yes', 'stopbadbots'),

		),
		array(
			'value' => 'no',
			'label' => __('no', 'stopbadbots'),

		),
	),
);


$fields[] = array(
	'type'          => 'radio',
	'name'          => 'stop_bad_bots_network',
	'label'         => __('Receive bot\'s table updates by participate in the Real-Time Bad Bots Security Network?', 'stopbadbots'),
	'radio_options' => array(
		array(
			'value' => 'yes',
			'label' => __('yes', 'stopbadbots'),
		),
		array(
			'value' => 'no',
			'label' => __('no', 'stopbadbots'),
		),
	),
);


$fields[] = array(
	'type'          => 'radio',
	'name'          => 'stop_bad_bots_blank_ua',
	'label'         => __('Block all with Blank User Agent?', 'stopbadbots'),
	'radio_options' => array(
		array(
			'value' => 'yes',
			'label' => __('yes', 'stopbadbots'),
		),
		array(
			'value' => 'no',
			'label' => __('no', 'stopbadbots'),
		),
	),
);
/*
	$fields[] = array(
		'type'          => 'radio',
		'name'          => 'stop_bad_bots_automatic_updates',
		'label'         => __( 'Enable Stop Bad Bots Plugin Automatic Updates?','stopbadbots' ),
		'radio_options' => array(
			array(
				'value' => 'yes',
				'label' => __( 'yes (default)','stopbadbots' ),
			),
			array(
				'value' => 'no',
				'label' => __( 'no (unsafe)','stopbadbots'  ),
			),
		),
	);
*/
$fields[] = array(
	'type'          => 'radio',
	'name'          => 'stopbadbots_block_enumeration',
	'label'         => __('Block User enumeration to improve security?', 'stopbadbots'),
	'radio_options' => array(
		array(
			'value' => 'yes',
			'label' => __('yes', 'stopbadbots'),
		),
		array(
			'value' => 'no',
			'label' => __('no', 'stopbadbots'),
		),
	),
);

$fields[] = array(
	'type'          => 'radio',
	'name'          => 'stopbadbots_block_pingbackrequest',
	'label'         => __('Block PingBack Request?', 'stopbadbots'),
	'radio_options' => array(
		array(
			'value' => 'yes',
			'label' => __('yes', 'stopbadbots'),
		),
		array(
			'value' => 'no',
			'label' => __('no', 'stopbadbots'),
		),
	),
);




$fields[] = array(
	'type'          => 'radio',
	'name'          => 'stopbadbots_block_spam_contacts',
	'label'         => __('Protect Contact Form 7 and WP Forms?', 'stopbadbots'),
	'radio_options' => array(
		array(
			'value' => 'yes',
			'label' => __('yes', 'stopbadbots'),
		),
		array(
			'value' => 'no',
			'label' => __('no', 'stopbadbots'),
		),
	),
);

$fields[] = array(
	'type'          => 'radio',
	'name'          => 'stopbadbots_block_spam_comments',
	'label'         => __("Protect Comments Form by check external spammer's databases (only when comment form is submited)?", 'stopbadbots'),
	'radio_options' => array(
		array(
			'value' => 'yes',
			'label' => __('yes', 'stopbadbots'),
		),
		array(
			'value' => 'no',
			'label' => __('no', 'stopbadbots'),
		),
	),
);


$fields[] = array(
	'type'          => 'radio',
	'name'          => 'stopbadbots_block_spam_login',
	'label'         => __('Protect Login Form against bots?', 'stopbadbots'),
	'radio_options' => array(
		array(
			'value' => 'yes',
			'label' => __('yes', 'stopbadbots'),
		),
		array(
			'value' => 'no',
			'label' => __('no', 'stopbadbots'),
		),
	),
);



$fields[] = array(
	'type'          => 'radio',
	'name'          => 'stopbadbots_block_false_google',
	'label'         => __('Block False Googlebot and Msnbot & Bingbot?  (available only in pro version)', 'stopbadbots') . '  ' . $pro_enabled,
	'radio_options' => array(
		array(
			'value' => 'yes',
			'label' => __('yes', 'stopbadbots'),
		),
		array(
			'value' => __('no', 'stopbadbots'),
			'label' => __('no', 'stopbadbots'),
		),
	),
);

$fields[] = array(
	'type'          => 'radio',
	'name'          => 'stopbadbots_block_china',
	'label'         => __('Block Traffic from China, Cuba and North Korea?  (available only in pro version)', 'stopbadbots') . '  ' . $pro_enabled,
	'radio_options' => array(
		array(
			'value' => 'yes',
			'label' => __('yes', 'stopbadbots'),
		),
		array(
			'value' => 'no',
			'label' => __('no', 'stopbadbots'),
		),
	),
);


$fields[] = array(
	'type'           => 'select',
	'name'           => 'stopbadbots_keep_log',
	'label'          => __('How long keep the visitors log file? If you have a very heavy traffic, select 1 day. Your choices can impact the Visits Log and Visits Chart Pages.', 'stopbadbots'),
	'id'             => 'my_select', // (optional, will default to name)
	'value'          => 'red', // (optional, will default to '')
	'select_options' => array(
		array(
			'value' => '30',
			'label' => __('Select', 'stopbadbots'),
		),
		array(
			'value' => '1',
			'label' => '1 ' . __('day', 'stopbadbots'),
		),
		array(
			'value' => '3',
			'label' => '3 ' . __('days', 'stopbadbots'),
		),
		array(
			'value' => '7',
			'label' => '7 ' . __('days', 'stopbadbots'),
		),
		array(
			'value' => '14',
			'label' => '14 ' . __('days', 'stopbadbots'),
		),
		array(
			'value' => '21',
			'label' => '21 ' . __('days', 'stopbadbots'),
		),
		array(
			'value' => '30',
			'label' => '30 ' . __('days', 'stopbadbots'),
		),
		array(
			'value' => '90',
			'label' => '90 ' . __('days', 'stopbadbots'),
		),
		array(
			'value' => '180',
			'label' => '180 ' . __('days', 'stopbadbots'),
		),
		array(
			'value' => '360',
			'label' => '360 ' . __('days', 'stopbadbots'),
		),
	),
);
/*
		$fields[] = array(
			'type'          => 'radio',
			'name'          => 'stopbadbots_install_anti_hacker',
			'label'         => __( 'Block Bots From Hackers (look the Anti Hacker Tab for details)', 'stopbadbots' ),
			'radio_options' => array(
				array(
					'value' => 'yes',
					'label' => __( 'yes', 'stopbadbots' ),
				),
				array(
					'value' => 'no',
					'label' => __( 'no', 'stopbadbots' ),
				),
			),
		);
		*/

/*
		$fields[] = array(
			'type'          => 'radio',
			'name'          => 'stopbadbots_install_recaptcha',
			'label'         => __( 'Enable invisible reCAPTCHA extension. (look the reCAPTCHA Tab for details)', 'stopbadbots' ),
			'radio_options' => array(
				array(
					'value' => 'yes',
					'label' => __( 'yes', 'stopbadbots' ),
				),
				array(
					'value' => 'no',
					'label' => __( 'no', 'stopbadbots' ),
				),
			),
		);
		*/





/*
		$fields[] = array(
			'type' 	=> 'radio',
			'name' 	=> 'stop_bad_bots_block_comments',
			'label' => __('Block all Comments?'),
			'radio_options' => array(
				array('value'=>'yes', 'label' => __('yes')),
				array('value'=>'no', 'label' => __('no'))
				)
			);
		*/

$settings['General Settings']['']['fields'] = $fields;


$msg2 = '<br />';
$msg2        .= '<b>' . __('This page works only in Pro Version.', 'stopbadbots') . ' ' . $pro_enabled . '</b>';
$msg2        .= '<br />';
$msg2        .= __('We can\'t block all bots. We need, for example, allow google, uptime, WordPress, stripe and others.', 'stopbadbots');

$msg2 .= '<br />';
$msg2 .= __('We can limit a number of bot\'s visits (non humans), just choose the options below.', 'stopbadbots') . '</b>';
$msg2 .= '<br />';
$msg2 .= __('You can also whitelist bots, look the Whitelist tab.', 'stopbadbots') . '</b>';
$msg2 .= '<br />';
$msg2 .= __('Search engine crawlers has unlimited access (look Whitelist tab) but you can block them (remove from Whitelist) and include new ones on User Agent Tables.', 'stopbadbots') . '</b>';
$msg2 .= '<br />';

$settings['Limit Bot Visits'][__('Instructions')] = array('info' => $msg2);


$fields = array();

$fields[] = array(
	'type'          => 'radio',
	'name'          => 'stopbadbots_limit_visits',
	'label'         => __('Enable the Rate Limiting for non humans?', 'stopbadbots'),
	'radio_options' => array(
		array(
			'value' => 'yes',
			'label' => __('yes', 'stopbadbots'),
		),
		array(
			'value' => 'no',
			'label' => __('no', 'stopbadbots'),
		),
	),
);

/*
		// Checkbox Input
		$fields[] = array(
		'type'  => 'checkbox',
		'name'  => 'stopbadbots_limit visits',
		'label' => 'My Checkbox',
		'id' => 'my_checkbox', // (optional, will default to name)
		'value' => 1 // (optional, 1 is checked, will default to 0)
		);
		*/



// Select List
$fields[] = array(
	'type'           => 'select',
	'name'           => 'stopbadbots_rate_limiting',
	'label'          => __('If a bot requests exceed', 'stopbadbots'),
	'id'             => 'rate_limiting', // (optional, will default to name)
	'value'          => 'red', // (optional, will default to '')
	'select_options' => array(
		array(
			'value' => 'unlimited',
			'label' => __('Unlimited', 'stopbadbots'),
		),
		array(
			'value' => '1',
			'label' => __('1 per minute', 'stopbadbots'),
		),
		array(
			'value' => '2',
			'label' => __('2 per minute', 'stopbadbots'),
		),
		array(
			'value' => '3',
			'label' => __('3 per minute', 'stopbadbots'),
		),
		array(
			'value' => '4',
			'label' => __('4 per minute', 'stopbadbots'),
		),
		array(
			'value' => '5',
			'label' => __('5 per minute', 'stopbadbots'),
		),
		array(
			'value' => '10',
			'label' => __('10 per minute', 'stopbadbots'),
		),
		array(
			'value' => '20',
			'label' => __('20 per minute', 'stopbadbots'),
		),
		array(
			'value' => '50',
			'label' => __('50 per minute', 'stopbadbots'),
		),

	),
);

$fields[] = array(
	'type'           => 'select',
	'name'           => 'stopbadbots_rate_limiting_day',
	'label'          => __('Or if a bot requests exceed', 'stopbadbots'),
	'id'             => 'rate_limiting_day', // (optional, will default to name)
	'value'          => 'red', // (optional, will default to '')
	'select_options' => array(
		array(
			'value' => 'unlimited',
			'label' => __('Unlimited', 'stopbadbots'),
		),
		array(
			'value' => '1',
			'label' => __('5 per hour', 'stopbadbots'),
		),
		array(
			'value' => '2',
			'label' => __('10 per hour', 'stopbadbots'),
		),
		array(
			'value' => '3',
			'label' => __('20 per hour', 'stopbadbots'),
		),
		array(
			'value' => '4',
			'label' => __('50 per hour', 'stopbadbots'),
		),
		array(
			'value' => '5',
			'label' => __('100 per hour', 'stopbadbots'),
		),

	),
);





$fields[] = array(
	'type'           => 'select',
	'name'           => 'stopbadbots_rate404_limiting',
	'label'          => __('If IP made only 404 requests exceed', 'stopbadbots'),
	'id'             => 'stopbadbots_rate404_limiting', // (optional, will default to name)
	'value'          => 'red', // (optional, will default to '')
	'select_options' => array(
		array(
			'value' => 'unlimited',
			'label' => __('Unlimited', 'stopbadbots'),
		),
		array(
			'value' => '5',
			'label' => __('five 404 pages', 'stopbadbots'),
		),
		array(
			'value' => '10',
			'label' => __('ten 404 pages', 'stopbadbots'),
		),
		array(
			'value' => '15',
			'label' => __('fifteen 404 pages', 'stopbadbots'),
		),
		array(
			'value' => '20',
			'label' => __('twenty 404 pages', 'stopbadbots'),
		),
		array(
			'value' => '50',
			'label' => __('fifty 404 pages', 'stopbadbots'),
		),
	),
);

$fields[] = array(
	'type'           => 'select',
	'name'           => 'stopbadbots_rate_penalty',
	'label'          => __('How long is an IP address blocked when it breaks a rule', 'stopbadbots'),
	'id'             => 'my_select', // (optional, will default to name)
	'value'          => 'red', // (optional, will default to '')
	'select_options' => array(
		array(
			'value' => '2',
			'label' => __('5 minutes', 'stopbadbots'),
		),
		array(
			'value' => '3',
			'label' => __('30 minutes', 'stopbadbots'),
		),
		array(
			'value' => '4',
			'label' => __('1 Hour', 'stopbadbots'),
		),
		array(
			'value' => '5',
			'label' => __('2 Hours', 'stopbadbots'),
		),
		array(
			'value' => '6',
			'label' => __('6 Hours', 'stopbadbots'),
		),
		array(
			'value' => '7',
			'label' => __('24 Hours', 'stopbadbots'),
		),

	),
);



$settings['Limit Bot Visits']['']['fields'] = $fields;
// $settings['General Settings']['']['fields'] = $fields;
//





$msg2 = '<br />';
$msg2            .= '<b>' . __('This page works only in Pro Version.', 'stopbadbots') . '  ' . $pro_enabled . '</b>';
$msg2            .= '<br />';
$msg2            .= '<b>' . __('HTTP Tools are tools to do HTTP request, used for not humans.', 'stopbadbots') . '</b>';

$msg2 .= '<br />';
$msg2 .= __('To Block HTTP Tools, just add one for each line.', 'stopbadbots') . '</b>';
$msg2 .= '<br />';
$msg2 .= __('To Manage, you can also remove one or more lines. Then, click Save Changes.', 'stopbadbots') . '</b>';
$msg2 .= '<br />';
$msg2 .= __('Activate eMail notification for some days and manage the Whitelist tables.', 'stopbadbots') . '</b>';
$msg2 .= '<br />';

$settings['Block HTTP Tools'][__('Instructions about to block HTTP tools (Only Pro)', 'stopbadbots')] = array('info' => $msg2);

$fields = array();

$fields[] = array(
	'type'          => 'radio',
	'name'          => 'stopbadbots_block_http_tools',
	'label'         => __('Block HTTP tools?', 'stopbadbots'),
	'radio_options' => array(
		array(
			'value' => 'yes',
			'label' => __('yes', 'stopbadbots'),
		),
		array(
			'value' => 'no',
			'label' => __('no', 'stopbadbots'),
		),
	),
);

$fields[] = array(
	'type'          => 'radio',
	'name'          => 'stopbadbots_update_http_tools',
	'label'         => __('Update HTTP tools each new plugin version or when plugin is activated? Maybe you will need remove that tools you don\'t want to block again.', 'stopbadbots'),
	'radio_options' => array(
		array(
			'value' => 'yes',
			'label' => __('yes', 'stopbadbots'),
		),
		array(
			'value' => 'no',
			'label' => __('no', 'stopbadbots'),
		),
	),
);



$fields[] = array(
	'type'  => 'textarea',
	'name'  => 'stopbadbots_http_tools',
	'label' => __('HTTP tools to block:', 'stopbadbots'),
);



$settings['Block HTTP Tools']['']['fields'] = $fields;




$msg2 = '<br />';
$msg2            .= '<b>' . __('This page works only in Pro Version.', 'stopbadbots') . '</b>';
$msg2            .= '<br />';
// $msg2 .= '<br />';
$msg2 .= '<b>' . __('This tables are very usefull if you want to block HTTP tools or use Rate Limiting.You can create 2 whitelist in this page: String and IP.', 'stopbadbots') . '</b>';
$msg2 .= '<br />';

$msg2 .= __('Just add one string to unblock all User Agent that contain that string.', 'stopbadbots');
$msg2 .= '<br />';
$msg2 .= __('For IP withelist, just add the IP to unblock it.', 'stopbadbots');
$msg2 .= '<br />';
$msg2 .= __('Add only one for each line.', 'stopbadbots');
$msg2 .= '<br />';
$msg2 .= __('Your current ip is: ', 'stopbadbots');
$msg2 .= ' ' . sbb_findip();

$settings['Whitelist'][__('Instructions about User Agent String and IP Whitelist. (Only Pro)')] = array('info' => $msg2);

$fields   = array();
$fields[] = array(
	'type'          => 'radio',
	'name'          => 'stopbadbots_enable_whitelist',
	'label'         => __('Enable Both Withelist?', 'stopbadbots'),
	'radio_options' => array(
		array(
			'value' => 'yes',
			'label' => __('yes', 'stopbadbots'),
		),
		array(
			'value' => 'no',
			'label' => __('no', 'stopbadbots'),
		),
	),
);


$fields[] = array(
	'type'  => 'textarea',
	'name'  => 'stopbadbots_string_whitelist',
	'label' => __('String whitelist (no case sensitive)', 'stopbadbots'),
);


$fields[] = array(
	'type'  => 'textarea',
	'name'  => 'stopbadbots_ip_whitelist',
	'label' => __('IP whitelist.', 'stopbadbots') . ' ' . __('Your Current IP:', 'stopbadbots') . ' ' . sbb_findip(),

);

$settings['Whitelist']['Whitelist Tables']['fields'] = $fields;


// $stopbadbots_admin_email = get_option( 'admin_email' );
$msg_email  = __('Fill out the email address to send messages.', 'stopbadbots');
$msg_email .= '<br />';
$msg_email .= __('Left Blank to use your default WordPress email. Then, click save changes.', 'stopbadbots');


$fields   = array();
$fields[] = array(
	'type'  => 'text',
	'name'  => 'stopbadbots_my_email_to',
	'label' => 'email',
);


$notificatin_msg  = __('Do you want receive email alerts for each bot attempt?', 'stopbadbots');
$notificatin_msg .= '<br />';
$notificatin_msg .= __('If you under brute force attack, you will receive a lot of emails.', 'stopbadbots');
$notificatin_msg .= '<br />';
$notificatin_msg .= __('You can see the bots attacks info at Bad Bots Table. (column Num Blocked).', 'stopbadbots');


$settings['Notifications'][__('Notifications')] = array('info' => $notificatin_msg);


// $fields = array();
$fields[] = array(
	'type'          => 'radio',
	'name'          => 'stopbadbots_my_radio_report_all_visits',
	'label'         => __('Alert me by email each Bots Attempts', 'stopbadbots'),
	'radio_options' => array(
		array(
			'value' => 'yes',
			'label' => __('Yes.', 'stopbadbots'),
		),
		array(
			'value' => 'no',
			'label' => __('No.', 'stopbadbots')
		),
	),
);

$fields[] = array(
	'type'          => 'radio',
	'name'          => 'stopbadbots_Blocked_Firewall',
	'label'         => __('Alert me All Times Firewall Block Something. (available only in pro version)', 'stopbadbots') . '  ' . $pro_enabled,
	'radio_options' => array(
		array(
			'value' => 'yes',
			'label' => __('Yes', 'stopbadbots'),
		),
		array(
			'value' => 'no',
			'label' => __('No', 'stopbadbots'),
		),
	),
);
/*
			$fields[] = array(
			'type'  => 'radio',
			'name'  => 'stopbadbots_Blocked_userenum',
			'label' => __('Alert me All Times Plugin Blocks User Enumeration?', "stopbadbots"),
			'radio_options' => array(
			array('value'=>'yes', 'label' => __('Yes', "stopbadbots")),
			array('value'=>'no', 'label' => __('No', "stopbadbots")),
			)
			);
			*/

$settings['Notifications']['Notifications']['fields'] = $fields;

/*


			$fields = array();

			$msg = '<big>';

			$msg .= __( 'Hackers use bots and brute force attacks and can slow down your site.', 'stopbadbots' );
			$msg .= '<br />';

			$msg .= __( 'Install the Free Anti Hacker Plugin extension and you can also:', 'stopbadbots' );
			$msg .= '<br />';

			// $msg .= __('Just install this free plugin: ', "stopbadbots");
			// $msg .= '&nbsp;<a href="https://wordpress.org/plugins/antihacker/" target="_self">';
			// $msg .= 'Anti Hacker Plugin</a>';
			// $msg .= '<br />';
			$msg .= __( 'Scan All Your files, Pages, Posts and Comments against Malware!', 'stopbadbots' );
			$msg .= '<br />';

			$msg .= __( 'block bots to search for vulnerabilities on themes and plugins.', 'stopbadbots' );
			$msg .= '<br />';


			$msg .= __( 'block bots to access Login Form againt bruteforce attack.', 'stopbadbots' );
			$msg .= '<br />';

			$msg .= __( 'block bots to access the WordPress xml-rpc - xmlrpc - or disable only Pingaback API', 'stopbadbots' );
			$msg .= '<br />';
			$msg .= __( 'block bots to access Json WordPress Rest API ', 'stopbadbots' );
			$msg .= '<br />';
			$msg .= __( 'block bots to post comments in media page.', 'stopbadbots' );
			$msg .= '<br />';
			$msg .= __( 'block bots to steal data from Feeds (Optional).', 'stopbadbots' );
			$msg .= '<br />';

			$msg .= __( 'Block Traffic from TOR (The Onion Router).', 'stopbadbots' );
			$msg .= '<br />';
			$msg .= __( 'Block Hackers from include new administrators.', 'stopbadbots' );
			$msg .= '<br />';



			$msg .= __( 'And a lot more tools.', 'stopbadbots' );
			$msg .= '<br />';

			$msg .= __( 'No DNS API (EndPoint) or cloud traffic redirection. No slow down your site. No Google penalties.', 'stopbadbots' );
			$msg .= '<br />';

			$msg .= __( 'Just Mark Yes at General Settings Tab (Block Bots From Hackers) and the system will try to install the free Anti Hacker Plugin extension.', 'stopbadbots' );
			$msg .= '<br />';

			$msg .= __( 'To install by hand, download it from', 'stopbadbots' );
			$msg .= '&nbsp;<a href="https://wordpress.org/plugins/antihacker/">WordPress Repository</a>';






			$msg                                   .= '</big>';
			$settings['Anti Hacker']['Anti Hacker'] = array( 'info' => $msg );
			$fields                                 = array();
			$settings['Anti Hacker']['Anti Hacker']['fields'] = $fields;



						$fields = array();
			$msg                = '<big>';
			$msg               .= __( ' reCAPTCHA For All Pages protect, for free, against current and future bots and SPAMMERS.', 'stopbadbots' );
			$msg               .= '<br />';
			$msg               .= '<br />';
			$msg               .= __(
				'Every day, hundreds of pieces of  malicious code and bots are creating
and are trying 
to mess up the day of many fine people who are just trying to work.',
				'stopbadbots'
			);
			$msg               .= '<br />';
			$msg               .= __(
				'We just created a new extension to protect against current and future bots
using invisible reCAPTCHA Google technology in all pages of your site.',
				'stopbadbots'
			);
			$msg               .= '<br />';
			$msg               .= __(
				'To install it, just go to General Settings tab on this page and mark
Enable reCAPTCHA extension reCAPTCHA For all.',
				'stopbadbots'
			);
			$msg               .= '<br />';
			// $msg .= __('(look the reCAPTCHA Tab for details)' , 'stopbadbots');
			$msg                               .= '<br />';
			$msg                               .= __( 'To install by hand, download it from', 'stopbadbots' );
			$msg                               .= '&nbsp;<a href="https://wordpress.org/plugins/recaptcha-for-all/">WordPress Repository</a>';
			$msg                               .= '</big>';
			$settings['reCAPTCHA']['reCAPTCHA'] = array( 'info' => $msg );

*/




/*
			$fields = array();

			if ( is_multisite() ) {
				$msg  = '<big>';
				$msg .= __( ' Free Useful tools to help you to manage memory, server load, errors and more:', 'stopbadbots' );
				$msg .= '<br />';
				$msg .= '<br />';
				$msg .= __( 'WP memory plugin to help you to manage and check the free memory and the PHP and WodPress memory Limits.', 'stopbadbots' );
				$msg .= '<br />';
				$msg .= __( 'To install visit', 'stopbadbots' );
				$msg .= '&nbsp;<a href="https://wordpress.org/plugins/wp-memory/">WordPress Plugin Page</a>';
				$msg .= '<br />';
				$msg .= '<br />';
				$msg .= __( 'WP tools plugin to help you to check the site errors, server load/usage, Server stats and more 34 tools.', 'stopbadbots' );
				$msg .= '<br />';
				$msg .= __( 'To install visit', 'stopbadbots' );
				$msg .= '&nbsp;<a href="https://wordpress.org/plugins/wptools/">WordPress Plugin Page</a>';
				$msg .= '<br />';
				$msg .= '<br />';

				$msg .= '</big>';
			} else {
				$msg  = '<script>';
				$msg .= 'window.location.replace("' . esc_url_raw( STOPBADBOTSHOMEURL ) . '/admin.php?page=stopbadbots_new_more_plugins");';
				// $msg .= 'window.location.replace("'.esc_url_raw(STOPBADBOTSHOMEURL).'plugin-install.php?s=sminozzi&tab=search&type=author");';
				$msg .= '</script>';
			}
			$settings['Useful Tools']['Useful Tools'] = array( 'info' => $msg );

*/











// require_once (STOPBADBOTSPATH. "guide/memory.php");
// $settings['Memory Checkup'][__('Memory Checkup')] = array('info' => $sbb_memory_msg );
$fields = array();
// $settings['Memory Checkup'][__('Memory Checkup')]['fields'] = $fields;
//
$gopro  = '<span style="font-size: 24pt; color: #CC3300;">Pro Features<font color="#000000">';
$gopro .= '<br />';
$gopro .= '</span>';
$gopro .= '<span style="font size: 16px;color: #000000;">';

if (empty($pro_enabled)) {
	$gopro .= __('Get Pro and receive, automatically, weekly updates, Firewall and more Protection.', 'stopbadbots');
	// $gopro .= '<br />';
	$gopro .= __('Help us to keep the bot database updated and the plugin stronger.', 'stopbadbots');
	$gopro .= '<br />';
	$gopro .= '<a href="https://stopbadbots.com/premium" >' . __('Visit our Pro Page for more details.', 'stopbadbots') . '</a>';
	$gopro .= '<br />';
	$gopro .= '<br />';
	$gopro .= __('Paste below the Item Purchase Code received by email from us when you bought the premium version.', 'stopbadbots');
	$gopro .= __("You don't need reinstall the plugin.", 'stopbadbots');
	$gopro .= '</span>';
} else {
	$gopro .= $pro_enabled;
}
// Form
$settings['Go Pro']['Go Pro'] = array('info' => $gopro);
// $fields = array();
$fields[]                               = array(
	'type'  => 'text',
	'name'  => 'stopbadbots_checkversion',
	'label' => __('Purchase Code (just paste here to activate your product)', 'stopbadbots') . ':',
);
$settings['Go Pro']['Go Pro']['fields'] = $fields;



new OptionPageBuilderTabbed($mypage, $settings);
