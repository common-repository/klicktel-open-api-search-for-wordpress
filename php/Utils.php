<?php
/**
* This file contains a class for utility methods and/or wrappers for built-in
* Wordpress API calls
*
* PHP version 5
*
* @package OpenApi_Utils
* @author  Christian Krahn <christian@krahn.org>
* @license http://creativecommons.org/licenses/by-nc-nd/3.0/
* @link    www.krahn.org
* @version 1.0
*/

/**
* This class contains a number of utility methods that may be needed.
*
* PHP version 5
*
* @package OpenApi_Utils
* @author  Christian Krahn <christian@krahn.org>
* @license http://creativecommons.org/licenses/by-nc-nd/3.0/
* @link    www.krahn.org
* @version 1.0
*/
class OpenApi_Utils
{
	
	/**
	* Sets a Wordpress option
	* @param string $name The name of the option to set
	* @param string $value The value of the option to set
	*/
	public static function setOption($name, $value)
	{
		if($value != '')
		{
			if (get_option($name) !== FALSE)
			{
				update_option($name, $value);
			}
			else
			{
				$deprecated = ' ';
				$autoload   = 'no';
				add_option($name, $value, $deprecated, $autoload);
			}
		}
	}
	
	/**
	 * Gets a Wordpress option
	 * @param string    $name The name of the option
	 * @param mixed     $default The default value to return if one doesn't exist
	 * @return string   The value if the option does exist
	 */
	public static function getOption($name, $default = FALSE)
	{
		$value = get_option($name);
		if( $value !== FALSE ) return $value;
		return $default;
	}
	
	/**
	* Get the base URL of the plugin installation
	* @return string the base URL
	*/
	public static function getBaseURL()
	{
		return (get_bloginfo('url') . '/wp-content/plugins/klicktel-open-api-search-for-wordpress/');
	}
	
	
}