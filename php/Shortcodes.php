<?php
/**
* This file contains a class for the short code logic.
*
* PHP version 5
*
* @package OpenApi_Shortcodes
* @author  Christian Krahn <christian@krahn.org>
* @license http://creativecommons.org/licenses/by-nc-nd/3.0/
* @link    www.krahn.org
* @version 1.0
*/
require_once dirname(__FILE__) . '/Utils.php';
require_once dirname(__FILE__) . '/Search.php';
/**
 * This class contains the short code logic.
 *
 * PHP version 5
 *
 * @package OpenApi_Shortcodes
 * @author  Christian Krahn <christian@krahn.org>
 * @license http://creativecommons.org/licenses/by-nc-nd/3.0/
 * @link    www.krahn.org
 * @version 1.0
 */
class OpenApi_Shortcodes
{
	
	/**
	 * This method handles the search short codes which immediately does a search
	 * based on short code parameters and returns the result.
	 * 
	 * @param array $atts short code attributes
	 * 
	 * @return string search results string
	 */
	public static function handleSearchShortcode($atts)
	{
		$result = '';
		$params = shortcode_atts(array(
				'type' => 'meta', 	// can be "meta", "invers", "whitepages", "yellowpages" or "geo"
				'what' => '',		// used in types "meta", "geo"
				'name' => '',		// used in type "whitepages"
				'trade' => '',		// used in type "yellowpages"
				'where' => '',		// used in "meta", "yellowpages", "whitepages"
				'street' => '',
				'cityname' => '',
				'number' => '',		// used in tyoe "invers"
				'distance' => '',	// used in type "geo"
				'start' => '0'
		), $atts);
		return OpenApi_Search::getInstance()->doSearch($params);
	}
	
	/**
	 * This method handles the searchform short codes which returns a html form
	 * for a specific search type.
	 * 
	 * @param array $atts short code attributes
	 * 
	 * @return string search form string
	 */
	public static function handleSearchFormShortcode($atts)
	{
		global $wp_query;
		
		$result = '';
		$result .= '<form method="GET" action="">';
		if ($atts['type'] == 'geo') {
			$result .= '<input type="hidden" name="oaType" value="geo"/><table>';
			$result .= '<tr><td><label for="oaWhat">'.__('What').'</label></td><td><input type="text" id="oaWhat" name="oaWhat" value="'.$wp_query->query_vars['oaWhat'].'"/></td></tr>';
			$result .= '<tr><td><label for="oaWhere">'.__('Where').'</label></td><td><input type="text" id="oaWhere" name="oaWhere" value="'.$wp_query->query_vars['oaWhere'].'"/></td></tr>';
			$result .= '<tr><td><label for="oaDistance">'.__('Distance in km').'</label></td><td><input type="text" id="oaDistance" name="oaDistance" value="'.$wp_query->query_vars['oaDistance'].'"/></td></tr>';
			//$result .= '<tr><td><label for="oaXGeo">'.__('XGeo').'</label></td><td><input type="text" id="oaXGeo" name="oaXGeo"></td></tr>';
			//$result .= '<tr><td><label for="oaYGeo">'.__('YGeo').'</label></td><td><input type="text" id="oaYGeo" name="oaYGeo"></td></tr>';
		} else if ($atts['type'] == 'invers') {
			$result .= '<input type="hidden" name="oaType" value="invers"/><table>';
			$result .= '<tr><td><label for="oaNumber">'.__('Number').'</label></td><td><input type="text" id="oaNumber" name="oaNumber" value="'.$wp_query->query_vars['oaNumber'].'"/></td></tr>';
		} else if ($atts['type'] == 'whitepages') {
			$result .= '<input type="hidden" name="oaType" value="whitepages"/><table>';
			$result .= '<tr><td><label for="oaName">'.__('Name').'</label></td><td><input type="text" id="oaName" name="oaName" value="'.$wp_query->query_vars['oaName'].'"/></td></tr>';
			$result .= '<tr><td><label for="oaCity">'.__('City').'</label></td><td><input type="text" id="oaCity" name="oaCity" value="'.$wp_query->query_vars['oaCity'].'"/></td></tr>';
			$result .= '<tr><td><label for="oaStreet">'.__('Street').'</label></td><td><input type="text" id="oaStreet" name="oaStreet" value="'.$wp_query->query_vars['oaStreet'].'"/></td></tr>';
		} else if ($atts['type'] == 'yellowpages') {
			$result .= '<input type="hidden" name="oaType" value="yellowpages"/><table>';
			$result .= '<tr><td><label for="oaTrade">'.__('Trade').'</label></td><td><input type="text" id="oaTrade" name="oaTrade" value="'.$wp_query->query_vars['oaTrade'].'"/></td></tr>';
			$result .= '<tr><td><label for="oaWhere">'.__('Where').'</label></td><td><input type="text" id="oaWhere" name="oaWhere" value="'.$wp_query->query_vars['oaWhere'].'"/></td></tr>';
		}
			 else {
			$result .= '<input type="hidden" name="oaType" value="meta"/><table>';
			$result .= '<tr><td><label for="oaWhat">'.__('What').'</label></td><td><input type="text" id="oaWhat" name="oaWhat" value="'.$wp_query->query_vars['oaWhat'].'"/></td></tr>';
			$result .= '<tr><td><label for="oaWhere">'.__('Where').'</label></td><td><input type="text" id="oaWhere" name="oaWhere" value="'.$wp_query->query_vars['oaWhere'].'"/></td></tr>';
		}
		$result .= '<tr><td>&nbsp;</td><td><button type="submit">'.__('Search').'</button></td></tr>';
		$result .= '</table></form>';
		return $result;
	}
	
}