<?php
/**
* This file acts as the 'Controller' of the application. It contains a class
* that will load the required hooks, and the callback functions that those
* hooks execute.
*
* PHP version 5
*
* @package OpenApi_Application
* @author  Christian Krahn <christian@krahn.org>
* @license http://creativecommons.org/licenses/by-nc-nd/3.0/
* @link    www.krahn.org
* @version 1.0
*/

require_once dirname(__FILE__) . '/Exception.php';
require_once dirname(__FILE__) . '/Utils.php';
require_once dirname(__FILE__) . '/Shortcodes.php';
require_once dirname(__FILE__) . '/Search.php';
require_once dirname(__FILE__) . '/View.php';

/**
* This class contains the core code and callback for the behavior of Wordpress.
* It is instantiated and executed directly by the plugin loader file.
*
* PHP version 5
*
* @package OpenApi_Application
* @author  Christian Krahn <christian@krahn.org>
* @license http://creativecommons.org/licenses/by-nc-nd/3.0/
* @link    www.krahn.org
* @version 1.0
*/
class OpenApi_Application
{
	
	CONST KEY_OPENAPI_KEY = 'ck_openapi_key_field';
	const KEY_DEBUG       = 'ck_openapi_activate_debug';
	
	/**
	 * Whether to show debug output or not
	 * @var bool
	 */
	private $debug;
	
	/**
	* The constructor
	* 
	* @return void
	*/
	public function __construct()
	{
		$this->debug = (OpenApi_Utils::getOption(self::KEY_DEBUG, '0') == '1') ? true : false;
		//echo ($this->debug == true) ? "1" : "0";exit;
	}
	
	/**
	* Entry point which starts registration of Wordpress hooks.
	* 
	* @return void
	*/
	public function execute()
	{
		$this->_registerHooks();
	}
	
	/**
	 * This method registers the wordpress hooks.
	 * 
	 * @return void
	 */
	private function _registerHooks()
	{
		add_filter('query_vars', array($this, 'queryVars'));
		add_filter('the_content', array($this, 'checkPostParams'));
		add_filter('home_template', array($this, 'useCustomSearchResultTemplate')) ;
		
		//short codes
		add_shortcode('openapi', array('OpenApi_Shortcodes', 'handleSearchShortcode'));
		add_shortcode('openapiform', array('OpenApi_Shortcodes', 'handleSearchFormShortcode'));
		add_filter('widget_text', 'do_shortcode');
		
		// admin
		register_activation_hook(__FILE__, array($this, 'adminCallback'));
		add_action('admin_menu', array($this, 'adminCallback'));
		
		// css
		add_action('wp_head', array($this, 'addCustomCss'));
	}	
	
	/**
	* Registration for queryvars used by OpenApi. This registers any
	* querystring variables that OpenApi requires so that Wordpress will
	* process them.
	*
	* @param array original array of allowed wordpress query vars
	*
	* @return array $qvars with extra allowed vars added to the array
	*/
	public function queryVars($qvars)
	{
		$qvars[] = 'oaType';
		$qvars[] = 'oaWhat';
		$qvars[] = 'oaWhere';
		$qvars[] = 'oaTrade';
		$qvars[] = 'oaName';
		$qvars[] = 'oaCity';
		$qvars[] = 'oaStreet';
		$qvars[] = 'oaDistance';
		$qvars[] = 'oaNumber';
		$qvars[] = 'oaXGeo';
		$qvars[] = 'oaYGeo';
		$qvars[] = 'oaStart';
		return $qvars;
	}
	
	/**
	 * Check for any query variables that might indicate that we got a call from an OpenApi searchform.
	 * 
	 * @return string the content of the page, maybe enriched with search results
	 */
	public function checkPostParams($content)
	{
		global $wp_query;
		if (isset($wp_query->query_vars['oaType'])) {
		
			$params = array(
					'type' => $wp_query->query_vars['oaType'],
					'what' => $wp_query->query_vars['oaWhat'],
					'where' => $wp_query->query_vars['oaWhere'],
					'trade' => $wp_query->query_vars['oaTrade'],
					'number' => $wp_query->query_vars['oaNumber'],
					'name' => $wp_query->query_vars['oaName'],
					'city' => $wp_query->query_vars['oaCity'],
					'street' => $wp_query->query_vars['oaStreet'],
					'distance' => $wp_query->query_vars['oaDistance'],
					'longitude' => $wp_query->query_vars['oaYGeo'],
					'latitude' => $wp_query->query_vars['oaXGeo'],
					'start' => isset($wp_query->query_vars['oaStart']) ? $wp_query->query_vars['oaStart'] : '0'
			);
			$content = OpenApi_Search::getInstance()->doSearch($params);
		}
		return $content;
	}
	
	/**
	 * This method is being hooked into the home template display and replaces it with our custom
	 * search result template if the situation qualifies.
	 * 
	 * @param string $template path to current tempate
	 * 
	 * @return string path to template to use
	 */
	public function useCustomSearchResultTemplate($template)
	{
		global $wp_query;
		if (isset($wp_query->query_vars['oaType'])) {
			$template = dirname(__FILE__) . '/views/klicktel-open-api-searchresult.php';
			if (file_exists(get_template_directory() . '/klicktel-open-api-searchresult.php'))
			{
				$template = get_template_directory() . '/klicktel-open-api-searchresult.php';
			}
			
		}
		return $template;
	}
	
	/**
	 * This method adds the admin options subpage to the admin area of Wordpress.
	 */
	public function adminCallback()
	{
		add_submenu_page('options-general.php', 'klickTel Open-API', 'klickTel Open-API', 'manage_options', 'klicktel-open-api', array($this, 'adminMenuCallback'));
	}
	
	/**
	 * This method handles the loading of the actual admin option page.
	 * It also saves changes to the options.
	 * 
	 * @return void
	 */
	public function adminMenuCallback()
	{
		// check if we are admin and allowed to access this page
		if (!is_admin()) {
			die('This plugin is only accessible from the WordPress Dashboard.');
		}
		
		// saving keys (TODO: switch to ajax?)
		if (isset($_POST[OpenApi_Application::KEY_OPENAPI_KEY])) {
			OpenApi_Utils::setOption(OpenApi_Application::KEY_OPENAPI_KEY, $_POST[OpenApi_Application::KEY_OPENAPI_KEY]);
		}
		if (isset($_POST[OpenApi_Application::KEY_DEBUG])) {
			OpenApi_Utils::setOption(OpenApi_Application::KEY_DEBUG, $_POST[OpenApi_Application::KEY_DEBUG]);
		}
		
		// inject some variables into view
		$data = array();
		$data['debug'] = OpenApi_Utils::getOption(self::KEY_DEBUG);
		$data['key'] = OpenApi_Utils::getOption(self::KEY_OPENAPI_KEY);
		
		// load view
		OpenApi_View::load('klicktel-openapi-options', $data);
	}
	
	/**
	 * Retrieves the default plugin css file. If a replacement exists in 
	 * stylesheet dir, use that one instead.
	 * 
	 * @return void
	 */
	public function addCustomCss()
	{
		// set the url of default css file
		$css_url = OpenApi_Utils::getBaseURL() . 'css/klicktel-open-api.css';
		
		// instead, set the variable to the current theme's custom css (if it exists)
		if (file_exists(get_stylesheet_directory() . '/klicktel-open-api.css'))
		{
			$css_url = get_bloginfo('stylesheet_directory') . '/klicktel-open-api.css';
		}
		
		// output the css link in the header
		echo "\n" . '<link rel="stylesheet" href="' . $css_url . '" type="text/css" media="screen" />' . "\n";
	}
	
}