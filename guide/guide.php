<?php
/**
 * @author William Sergio Minossi
 * @copyright 2016
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
$ah_help  = '<p style="font-family:arial; font-size:16px;">';
$ah_help .= '1) ' . __( 'Open the General Settings Tab and click over Yes  (to begin to block).', 'stopbadbots' );
$ah_help .= '<br>';
$ah_help .= '<br>';
$ah_help .= '2) ' . __( 'You can also add  Bad Bots at bad bots table. Dashboard => Stop Bad Bots => Add Bad Bot to Table.', 'stopbadbots' );
$ah_help .= '<br>';
$ah_help .= '<br>';
$ah_help .= '3) ' . __( 'You can also add  Bad IPs at bad IPs table. Dashboard => Stop Bad Bots => Add Bad IP to Table.', 'stopbadbots' );


$ah_help .= '<br>';
$ah_help .= '<br>';
$ah_help .= '4) ' . __( 'You can also add  Bad Referer at bad Referer table. Dashboard => Stop Bad Bots => Add Bad Referer to Table.', 'stopbadbots' );
$ah_help .= '<br>';
$ah_help .= '<br>';

$ah_help .= '5) ' . __( 'You can go to Limit Bots Visits tab and block bots by visit number.', 'stopbadbots' );
$ah_help .= '<br>';
$ah_help .= '<br>';


$ah_help .= '6) ' . __( 'You can go to Block HTTP tools tab and manage it.', 'stopbadbots' );
$ah_help .= '<br>';
$ah_help .= '<br>';


$ah_help .= '7) ' . __( 'You can go to WhiteList tab and manage  String and IP table.', 'stopbadbots' );
$ah_help .= '<br>';
$ah_help .= '<br>';





$ah_help .= '8) ' . __( 'At eMail and Notifications tab, you can customize your contact email or left blank to use your WordPress eMail.', 'stopbadbots' );
$ah_help .= '<br>';

$ah_help .= __( 'You can record your option by receive or not email alerts about bots attempts and firewall blocks.', 'stopbadbots' );
$ah_help .= '<br>';
$ah_help .= '<br>';


$ah_help .= '9) ' . __( 'Look our Go Pro tab about how to get weekly updates, more features and Firewall protection.', 'stopbadbots' );
$ah_help .= '<br>';
$ah_help .= '<br>';


$ah_help .= '10) ' .__( "To perform an analytical and detailed analysis of the visits received on your website, visit the page StopBadBots => Visits Log. There, you can easily add visitors to the blacklist or whitelist with just one click.", 'stopbadbots' );

$ah_help .= '<br>';
$ah_help .= '<br>';



$ah_help .= '<span style="background-color: #FFFF00">';
$ah_help .= '<big><b>';
$ah_help .= __( 'Please, read this:', 'stopbadbots' );
$ah_help .= '</b></big>';
$ah_help .= '<br><b>';
$ah_help .= __( 'Because not all bots are bad, you need manage the bots and whitelist tables.', 'stopbadbots' );
$ah_help .= '</b><br>';
$ah_help .= __( 'Open the page Bad Bots Table  (under Stop Bad Bots Menu) and take a look at Default Bad Bots List. If you wish, you can turn off some. (Just check, Bulk Actions, Apply). Our plugin will create a table with more than 2500 Bots.', 'stopbadbots' );
$ah_help .= __( 'You can see how many times each bot was blocked at the column Num Blocked. Click the title (Num Blocked) to order by.', 'stopbadbots' );
$ah_help .= '<br><b>';
$ah_help .= '<br>';
$ah_help .= __( "Check the bot's table frequently, especially in the first days.", 'stopbadbots' );
$ah_help .= __( 'Confirm if you want block all that bots. Maybe you want unblock Baidu, DuckDuck, Yandex, Seznam or another search engine in your language or some social sites or some useful bot for you.', 'stopbadbots' );
$ah_help .= '<br>';
$ah_help .= __( 'If you use RSS FEED services, probably they have their bot to read your feeds.', 'stopbadbots' );
$ah_help .= __( 'Remember to deactivate their bot.', 'stopbadbots' );
$ah_help .= __( 'Same thing if you create some smartphone APP.', 'stopbadbots' );
$ah_help .= '<br>';
$ah_help .= __( 'We have also a table of bad IPs. Many bad bots use fake or blank User Agent. Then, we need block them by IP.', 'stopbadbots' );
$ah_help .= '<br>';
$ah_help .= __(
	'Some search engine or social media, like Telegram, Whatsapp, Qwant, Mail.ru, LinkedIn, bitlybot, Applebot, AppleNewsBot, SkypeUriPreview, FacebookBot, twitterbot, vkShare
for example, sometimes send bots with empty user agent (or another bad practice) and our system catch them.',
	'stopbadbots'
);
$ah_help .= ' ' . __( "Check also the IP's table frequently, especially in the first days. Confirm if you want block all that IPs.", 'stopbadbots' );
$ah_help .= '<br>';
$ah_help .= __( 'If you need more info about each bot or IP, visit our site www.StopBadBots.com (page Bots Table and Boats Table by IP)', 'stopbadbots' );
$ah_help .= '<br>';
$ah_help .= '<br>';
$ah_help .= __( 'Note: If you use the plugin WPFastestCache, deactivate the bot named "test" at Bad Bots Table. (Dashboard => Stop Bad Bots => Bad Bots Table)', 'stopbadbots' );
$ah_help .= '<br>';
$ah_help .= '<br>';
$ah_help .= __( 'StopBadBots it is a powerfull tool. Then, like all powerfull tools it is necessary to use carefully.', 'stopbadbots' );
$ah_help .= '<br>';
$ah_help .= __( 'It is up to you determine what bot is beneficial or detrimental.', 'stopbadbots' );
$ah_help .= '<br>';
$ah_help .= __( 'Unfortunately the amount of bots is growing vertiginously. They can overload your site. You need invest time to manage this.', 'stopbadbots' );
$ah_help .= '</span>';
$ah_help .= '</b><br>';
$ah_help .= '<br>';
$ah_help .= __( 'Remember to click Save Changes before to left each tab.', 'stopbadbots' );
$ah_help .= '<br>';
$ah_help .= '<br>';
$ah_help .= __( "You don't need create any robots.txt or htaccess file. ", 'stopbadbots' );
$ah_help .= '<br>';
$ah_help .= '<br>';
$ah_help .= __( "The Plugin doesn't block main Google, Yahoo, Bing (Microsoft), Twitter and Facebook bots.", 'stopbadbots' );
$ah_help .= '<br>';
$ah_help .= '<br>';


$ah_help .=  __('You have also the option to deactivate Yandex bot.','stopbadbots');
$ah_help .= '<br>';
$ah_help .= __('Dashboard => Stop Bad Bots => Bad Bots Table.','stopbadbots');
$ah_help .= '<br>';
$ah_help .= __('Deactivate this 3 boots:','stopbadbots');
$ah_help .= '<br>';
$ah_help .= __('1) Yandex','stopbadbots');
$ah_help .= '<br>';
$ah_help .= __('2) Yandexbot','stopbadbots');
$ah_help .= '<br>';
$ah_help .= __('3) Exbot','stopbadbots');

$ah_help .= '<br>';
$ah_help .= '<br>';


$ah_help .= esc_attr__( 'Visit the plugin site for more details, video, online guide, FAQ and Troubleshooting page and bot\'s and IP\'s details.', 'stopbadbots' );
$ah_help .= '<br>';
$ah_help .= '<br>';
$ah_help .= '<a href="https://stopbadbots.com/help/" class="button button-primary">' . esc_attr__( 'OnLine Guide', 'stopbadbots' ) . '</a>';
$ah_help .= '&nbsp;&nbsp;';
$ah_help .= '<a href="https://stopbadbots.com/faq/" class="button button-primary">' . esc_attr__( 'Faq Page', 'stopbadbots' ) . '</a>';
$ah_help .= '&nbsp;&nbsp;';
$ah_help .= '<a href="https://billminozzi.com/dove/" class="button button-primary">' . esc_attr__( 'Support Page', 'stopbadbots' ) . '</a>';
$ah_help .= '&nbsp;&nbsp;';
$ah_help .= '<a href="https://siterightaway.net/troubleshooting/" class="button button-primary">' . __( 'Troubleshooting Page', 'stopbadbots' ) . '</a>';
$ah_help .= '&nbsp;&nbsp;';
$ah_help .= '<a href="https://stopbadbots.com/premium/" class="button button-primary">' . esc_attr__( 'Go Pro', 'stopbadbots' ) . '</a>';
$ah_help .= '<br>';
$ah_help .= '<br>';

/*
$ah_help .= __('That is all. Enjoy it.','stopbadbots');
$ah_help .= '<br>';
$ah_help .= __( 'If you like this product, please write a few words about it. It will help other people find this useful plugin more quickly.', 'stopbadbots');
$ah_help .= '<br>';
$ah_help .= '<a href="https://stopbadbots.com/share/" class="button button-primary">'.__("Share","stopbadbots").'</a>';
*/
$ah_help .= '</p>';
