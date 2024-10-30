<?php
/*
Plugin Name: klickTel Open-API for Wordpress
Plugin URI: http://wordpress.org/extend/plugins/klicktel-open-api-search-for-wordpress/
Description: WordPress Plugin to embedd klickTel Open API in your Wordpress blog
Version: 0.2
Author: Christian Krahn
Author URI: http://www.krahn.org/
License: GPL2
*/
/*
This program is free software; you can redistribute it and/or modify it
under the terms of the GNU General Public License as published by the
Free Software Foundation; either version 2 of the License, or (at your
option) any later version.

This program is distributed in the hope that it will be useful, but WITHOUT
ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or
FITNESS FOR A PARTICULAR PURPOSE.  See the GNU General Public License for
more details.

You should have received a copy of the GNU General Public License along
with this program, LICENSE.txt. If not, write to the Free Software Foundation, Inc.,
51 Franklin Street, Fifth Floor, Boston, MA 02110-1301, USA. Or, visit this web address:
http://www.gnu.org/licenses/gpl.html OR http://www.gnu.org/licenses/gpl-2.0.html.
*/

// prevent file from being accessed directly
$ck_openapi_filename = 'klicktel-open-api.php';
if (basename($_SERVER['SCRIPT_FILENAME']) == $ck_openapi_filename)
{
	die ("Please do not access this file, $ck_openapi_filename, directly. Thanks!");
}

require dirname(__FILE__) . '/php/Application.php';

$app = new OpenApi_Application();
$app->execute();