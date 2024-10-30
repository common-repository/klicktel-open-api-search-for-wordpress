<?php
/**
 * This file contains a class for loading the presentation layer/files
 *
 * @author Kenny Katzgrau <kenny@oconf.org>
 */

/**
 * This class contains methods for loading views
 */
class OpenApi_View
{
    /**
     * Load a view file. The file should be located in views.
     * 
     * @param string $file The filename of the view without the extension (must be PHP)
     * @param array $data An associative array of data that be be extracted and available to the view
     * 
     * @return void
     */
    public static function load($file, $data = array())
    {
        $file = dirname(__FILE__) . '/views/' . $file . '.php';

        if(!file_exists($file))
        {
            throw new OpenApi_Exception("View '$file' was not found");
        }

        # Extract the variables into the global scope so the views can use them
        extract($data);

        include_once($file);
    }
}