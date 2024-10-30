<?php
/**
* Search
*
* PHP version 5
*
* @package OpenApi_Search
* @author  Christian Krahn <christian@krahn.org>
* @license http://creativecommons.org/licenses/by-nc-nd/3.0/
* @link    www.krahn.org
* @version 1.0
*/
require_once dirname(__FILE__) . '/Utils.php';
require_once dirname(__FILE__) . '/Exception.php';
/**
 * Search
 *
 * PHP version 5
 *
 * @package OpenApi_Search
 * @author  Christian Krahn <christian@krahn.org>
 * @license http://creativecommons.org/licenses/by-nc-nd/3.0/
 * @link    www.krahn.org
 * @version 1.0
 */
class OpenApi_Search
{
	/**
	* The internal instance of the search engine
	* @var OpenApi_Search
	*/
	private static $_instance = NULL;
	
	/**
	 * Key to the klickTel OpenApi
	 * @var string
	 */
	private $key;
	
	/**
	* Get an instance of the search engine
	* 
	* @return OpenApi_Search
	*/
	public static function getInstance()
	{
		if(self::$_instance === NULL)
		{
			self::$_instance = new self();
		}
		return self::$_instance;
	}
	
	/**
	 * Constructor
	 * 
	 * @return void
	 */
	public function __construct()
	{
		$this->key = OpenApi_Utils::getOption(OpenApi_Application::KEY_OPENAPI_KEY);
	}
	
	/**
	* Process search parameters and do search based on them.
	*
	* @param array $params
	*
	* @return string search results string
	*/
	public function doSearch($params)
	{
		$result = '';
		try {
			/*if (empty($params['what']) && empty($params['where']) && empty($params['number'])) {
				throw new OpenApi_Search_Exception('Parameters are missing');
			}*/
			return $this->doTheSearch($params);
		} catch(OpenApi_Search_Exception $e) {
			$result .= '<div>'._e('No Results for this search').'</div>';
			$result .= $e->getMessage();
		}
		return $result;
	}
	
	/**
	 * Builds the OpenAPI URL String
	 * 
	 * @param array $params parameter array
	 * 
	 * @return string url
	 */
	private function buildOpenApiUrl($params)
	{
		$url = "http://openapi.klicktel.de/searchapi/";
		$url .= $params['type'];
		$url .= "?key=".$this->key;
		if (isset($params['what']) && !empty($params['what'])) {
			$url .= "&what={$params['what']}";
		}
		if (isset($params['where']) && !empty($params['where'])) {
			$url .= "&where={$params['where']}";
		}
		if (isset($params['trade']) && !empty($params['trade'])) {
			$url .= "&trade={$params['trade']}";
		}
		if (isset($params['number']) && !empty($params['number'])) {
			$params['number'] = $this->removeObstacles($params['number']);
			$url .= "&number={$params['number']}";
		}
		if (isset($params['name']) && !empty($params['name'])) {
			$url .= "&name={$params['name']}";
		}
		if (isset($params['city']) && !empty($params['city'])) {
			$url .= "&cityname={$params['city']}";
		}
		if (isset($params['street']) && !empty($params['street'])) {
			$url .= "&street={$params['street']}";
		}
		if (isset($params['distance']) && !empty($params['distance'])) {
			$url .= "&distance={$params['distance']}";
		}
		if (isset($params['longitude']) && !empty($params['longitude'])) {
			$url .= "&long={$params['longitude']}";
		}
		if (isset($params['latitude']) && !empty($params['latitude'])) {
			$url .= "&lat={$params['latitude']}";
		}
		if (isset($params['start']) && !empty($params['start'])) {
			$url .= "&start={$params['start']}";
		}
		return $url;
	}
	
	/**
	 * Removes any non numberic chars and spaces from phone number
	 * 
	 * @param string $phoneNumber phone number
	 * @return string phone number
	 */
	private function removeObstacles($phoneNumber)
	{
		$phoneNumber = preg_replace(array('/\s+/', '/\D+/'), '', $phoneNumber);
		return $phoneNumber;
	}
	
	/**
	 * Handles the data retrieval from either cache or URL.
	 * 
	 * @param array $params parameter array
	 * @param string &$result call-by-reference to result string
	 * 
	 * @return mixed $dat the json data object
	 */
	private function getDataFromCacheOrUrl($params, &$result)
	{
		$debug = OpenApi_Utils::getOption(OpenApi_Application::KEY_DEBUG);
		
		$url = $this->buildOpenApiUrl($params);
		
		// checking if there is any content in transient cache that matches the search
		$cache_key = md5(implode('|', $params));
		$dat = get_transient($cache_key);
		
		if (false == $dat || empty($dat)) {
			$dat = json_decode(file_get_contents($url));
			if ($dat->response->type != 'error') {
				set_transient($cache_key, $dat, 60*60);
			}
		}
		
		if ($debug == true) {
			$result .= '<pre>'.$url.'</pre>';
			$result .= '<pre>'.$cache_key.'</pre>';
			$result .= '<pre>'.print_r($dat, true).'</pre>';
		}
		return $dat;
	}
	
	/**
	* The Search
	*
	* @param array $params parameters for The search
	*
	* @throws OpenApi_Search_Exception
	*
	* @return string html for the The search result
	*/
	private function doTheSearch($params)
	{
		$debug = OpenApi_Utils::getOption(OpenApi_Application::KEY_DEBUG);
		
		$result = '';
		
		$dat = $this->getDataFromCacheOrUrl($params, $result);
		
		// throw exception if api returns an error
		if ($dat->response->type == "error") {
			throw new OpenApi_Search_Exception($dat->response->type->error->message);
		}
		
		if ($dat->response->type == "list") {
			$entries = $dat->response->results[0]->entries;
		
			if (is_array($entries)) {
				$result .= $this->_createPagination($dat, $params);
				$result .= '<ul class="entrylisting">';
				foreach ($entries as $entry) {
		
					$result .= '<li class="entry clearfix" id="entry-' . $entry->id . '">';
					$result .= '<div class="headline"><a href="http://www.klicktel.de/homepage/detail?id='.$entry->id.'&WT.mc_id=34028000" target="_blank"><strong>' . $entry->displayname . '</strong></a></div>';
					$result .= '<div class="location">';
					$result .= '<div class="address">' . trim($entry->location->street . " " . $entry->location->streetnumber) . '<br/>';
					$result .= '' . trim($entry->location->zipcode . " " . $entry->location->city) . '</div>';
					$result .= '<div class="contacts phone">';
					foreach ($entry->phonenumbers as $phoneNumber) {
						switch ($phoneNumber->type) {
							case 'mobil':
								$result .= 'Mobil.: ';
								break;
							case 'fax':
								$result .= 'Fax: ';
								break;
							case 'phone':
							default:
								$result .= 'Tel.: ';
								break;
						}
						$result .= $phoneNumber->displayphone.'<br/>';
					}
					$result .= '</div>';
					$result .= '<div class="contacts email">';
					foreach ($entry->emails as $email) {
						$result .= '<a href="mailto:'.$email->email.'">'.$email->email.'</a><br/>';
					}
					$result .= '</div>';
					$result .= '</div>';
					$result .= '</li>';
				}
				$result .= '</ul>';
				$result .= $this->_createPagination($dat, $params);
				$result .= '<div class="tgcopyright clearfix">';
				$result .= 'Powered by telegate MEDIA';
				$result .= '</div>';
				
			} else {
				throw new OpenApi_Search_Exception('No entries');
			}
			if ($dat->response->results[1]->type == 'locations') {
				$locations = $dat->response->results[1]->locations;
			}
		}
		
		return $result;
	}
	
	/**
	* Add the pagination to the output
	*
	* @param object $dat    openapi data
	* @param array  $params form parameters
	*
	* @return string html with pagination information
	*/
	private function _createPagination($dat, $params) {
		$result = '<div class="pagination">';
		if ($dat->response->results[0]->first > 1) {
			$result .= '<div class="pagileft"><a href="'.$_SERVER['SCRIPT_NAME '].'?oaType='.$params['type'];
			$result .= '&oaWhat='.$params['what'];
			$result .= '&oaWhere='.$params['where'];
			$result .= '&oaTrade='.$params['trade'];
			$result .= '&oaName='.$params['name'];
			$result .= '&oaDistance='.$params['distance'];
			$result .= '&oaNumber='.$params['number'];
			$result .= '&oaStart='.($dat->response->results[0]->first - 21);
			$result .= '">'.__('previous').'</a></div>';
		}
		if ($dat->response->results[0]->last < $dat->response->results[0]->total) {
			$result .= '<div class="pagiright"><a href="'.$_SERVER['SCRIPT_NAME '].'?oaType='.$params['type'];
			$result .= '&oaWhat='.$params['what'];
			$result .= '&oaWhere='.$params['where'];
			$result .= '&oaTrade='.$params['trade'];
			$result .= '&oaDistance='.$params['distance'];
			$result .= '&oaNumber='.$params['number'];
			$result .= '&oaStart='.$dat->response->results[0]->last.'">'.__('next');
			$result .= '</a></div>';
		}
		$result .= '</div>';
		return $result;
	}
	
}

/**
* This class contains the exception class for OpenApi_Search.
*
* PHP version 5
*
* @package OpenApi_Search
* @author  Christian Krahn <christian@krahn.org>
* @license http://creativecommons.org/licenses/by-nc-nd/3.0/
* @link    www.krahn.org
* @version 1.0
*/
class OpenApi_Search_Exception extends OpenApi_Exception {}