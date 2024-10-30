<?php
 
/**************************************************************************
ADMIN INITIALIZE
**************************************************************************/
 
if ('ck_openapi-options.php' == basename($_SERVER['SCRIPT_FILENAME']))
{
    die ('Please do not access this admin file directly. Thanks!');
}
 
 
if (!is_admin()) {die('This plugin is only accessible from the WordPress Dashboard.');}
 
 
if (!defined('WP_CONTENT_DIR')) define('WP_CONTENT_DIR', ABSPATH . 'wp-content');
if (!defined('WP_CONTENT_URL')) define('WP_CONTENT_URL', get_option('siteurl') . '/wp-content');
if (!defined('WP_ADMIN_URL')) define('WP_ADMIN_URL', get_option('siteurl') . '/wp-admin');
if (!defined('WP_PLUGIN_DIR')) define('WP_PLUGIN_DIR', WP_CONTENT_DIR . '/plugins');
if (!defined('WP_PLUGIN_URL')) define('WP_PLUGIN_URL', WP_CONTENT_URL . '/plugins');

if (isset($_POST['ck_openapi_key_field'])) {
	update_option("ck_openapi_key_field", $_POST['ck_openapi_key_field']);
}
if (isset($_POST['ck_openapi_activate_debug'])) {
	update_option("ck_openapi_activate_debug", $_POST['ck_openapi_activate_debug']);
}
?>

<div class="wrap">
    
 
        <h2>&nbsp;About this Plug-in</h2>
        <div style="margin-left:20px; line-height:150%; font-size:14px;">
        This plugin will give your blog access to the klickTel Open-API. <a target="_blank" href="http://www.klicktel.de">klickTel</a> is a brand of <a target="_blank" href="http://www.telegate-media.de">telegate MEDIA</a> in germany, which provides free and paid directory listing to small and medium companies in germany. You can look up several kinds of information e.g. phone book entries and trade name based searches for businesses.<br/><br/>

		The intention of the plugin is to extend the existing search capabilities of WordPress to include the klickTel Open-API. That way you can look up directory listings for a specific search word or get the nearest restaurant to order some food.
        
        <br/><br/>
        </div>
 
 
 
        <h2>&nbsp;Links Related to this Plug-in</h2>
        <div style="margin-left:20px; line-height:150%; font-size:14px;">
        <ol style="margin-left:40px;">
            <li>Visit my web site, <a target="_blank" href="http://www.krahn.org">krahn.org</a>, for lots of WordPress tips, tweaks, shortcodes, and plugins.</li>
            <li>Did you find this plug-in useful? A small <a target="_blank" href="https://www.paypal.com/cgi-bin/webscr?cmd=_donations&currency_code=EUR&business=christian%40krahn.org&item_name=WP%20Open-API%20donation&amount=0.00">donation</a> will support my continued WordPress development efforts. Enter any amount.</li>
        </ol>
        <br/>
        </div>
 
 
        <h2>&nbsp;Shortcode Usage Information</h2>
        <div style="margin-left:20px; line-height:150%; font-size:14px;">
        Upon activation of this plug-in, you'll have access to a <b>new shortcode</b> that returns a search result for a given query. You can use the shortcode anywhere on a page/post or in a text widget.<br/><br/>
 
        <b>Basic Search Form Syntax:</b><br/>
        <div style="margin-left:25px;">[openapiform]</div>
        <br/>
 
        <b>Example Search Result Shortcode Usage:</b><br/>
        <div style="margin-left:25px;">
            [openapi <b>type=</b>'meta' <b>what=</b>'klicktel' <b>where=</b>'essen' <b>start</b>='0']<br/>
        </div>
        <br/>
 
        <b>Available Attributes:</b><br/>
        <ol style="margin-left:40px;">
            <li><b>type</b> defines the type of search, currently only "meta" is supported</li>
            <li><b>what</b> what to search for, e.g. name of a person or business, or a trade name</li>
            <li><b>where</b> location string, can be a combination of zipcode, cityname, street, housenumber<br/></li>
            <li><b>start</b> pagination index, starting with 0 (use increments of 20)<br/></li>
        </ol>
        <br/>
 
        <b>klickTel Open-API Details:</b><br/>
        <div style="margin-left:25px;">
            This plug-in utilizes the <a href="http://openapi.klicktel.de" target="_blank">klickTel Open-API</a>.
        </div>
        <br/>
 
 		<b>Your Open-API Key</b><br/>
 		<div class="wrap">
			You need to provide your Open-API key. Apply for a key on the <a href="http://openapi.klicktel.de" target="_blank">klickTel Open-API page</a>.
			<form name="form1" method="post" action="">
			<input type="hidden" name="ck_openapi_activate_debug" value="0"/>
			<input size="60" name="ck_openapi_key_field" value="<?php echo get_option('ck_openapi_key_field');?>" type="text" /><br/>
			Debug? (currently <?php echo get_option('ck_openapi_activate_debug');?>)<input type="checkbox" name="ck_openapi_activate_debug" value="1" <?php echo (get_option('ck_openapi_activate_debug') == '1') ? 'checked="checked"' : ''; ?>/>
			<input type="submit" value="Save" />
			</form>
		</div>

</div>
<br/><br/>