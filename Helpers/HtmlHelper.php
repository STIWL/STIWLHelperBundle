<?php

namespace Esolving\HelperBundle\Helpers;

class HtmlHelper {
    /**
     * CodeIgniter
     *
     * An open source application development framework for PHP 5.1.6 or newer
     *
     * @package		CodeIgniter
     * @author		ExpressionEngine Dev Team
     * @copyright	Copyright (c) 2008 - 2011, EllisLab, Inc.
     * @license		http://codeigniter.com/user_guide/license.html
     * @link		http://codeigniter.com
     * @since		Version 1.0
     * @filesource
     */
// ------------------------------------------------------------------------

    /**
     * CodeIgniter HTML Helpers
     *
     * @package		CodeIgniter
     * @subpackage	Helpers
     * @category	Helpers
     * @author		ExpressionEngine Dev Team
     * @link		http://codeigniter.com/user_guide/helpers/html_helper.html
     */
// ------------------------------------------------------------------------

    /**
     * Heading
     *
     * Generates an HTML heading tag.  First param is the data.
     * Second param is the size of the heading tag.
     *
     * @access	public
     * @param	string
     * @param	integer
     * @return	string
     */
    function heading($data = '', $h = '1', $attributes = '') {
        $attributes = ($attributes != '') ? ' ' . $attributes : $attributes;
        return "<h" . $h . $attributes . ">" . $data . "</h" . $h . ">";
    }

// ------------------------------------------------------------------------

    /**
     * Unordered List
     *
     * Generates an HTML unordered list from an single or multi-dimensional array.
     *
     * @access	public
     * @param	array
     * @param	mixed
     * @return	string
     */
    function ul($list, $attributes = '') {
        return _list('ul', $list, $attributes);
    }

// ------------------------------------------------------------------------

    /**
     * Ordered List
     *
     * Generates an HTML ordered list from an single or multi-dimensional array.
     *
     * @access	public
     * @param	array
     * @param	mixed
     * @return	string
     */
    function ol($list, $attributes = '') {
        return _list('ol', $list, $attributes);
    }

// ------------------------------------------------------------------------

    /**
     * Generates the list
     *
     * Generates an HTML ordered list from an single or multi-dimensional array.
     *
     * @access	private
     * @param	string
     * @param	mixed
     * @param	mixed
     * @param	integer
     * @return	string
     */
    function _list($type = 'ul', $list, $attributes = '', $depth = 0) {
        // If an array wasn't submitted there's nothing to do...
        if (!is_array($list)) {
            return $list;
        }

        // Set the indentation based on the depth
        $out = str_repeat(" ", $depth);

        // Were any attributes submitted?  If so generate a string
        if (is_array($attributes)) {
            $atts = '';
            foreach ($attributes as $key => $val) {
                $atts .= ' ' . $key . '="' . $val . '"';
            }
            $attributes = $atts;
        } elseif (is_string($attributes) AND strlen($attributes) > 0) {
            $attributes = ' ' . $attributes;
        }

        // Write the opening list tag
        $out .= "<" . $type . $attributes . ">\n";

        // Cycle through the list elements.  If an array is
        // encountered we will recursively call _list()

        static $_last_list_item = '';
        foreach ($list as $key => $val) {
            $_last_list_item = $key;

            $out .= str_repeat(" ", $depth + 2);
            $out .= "<li>";

            if (!is_array($val)) {
                $out .= $val;
            } else {
                $out .= $_last_list_item . "\n";
                $out .= _list($type, $val, '', $depth + 4);
                $out .= str_repeat(" ", $depth + 2);
            }

            $out .= "</li>\n";
        }

        // Set the indentation for the closing tag
        $out .= str_repeat(" ", $depth);

        // Write the closing list tag
        $out .= "</" . $type . ">\n";

        return $out;
    }

// ------------------------------------------------------------------------

    /**
     * Generates HTML BR tags based on number supplied
     *
     * @access	public
     * @param	integer
     * @return	string
     */
    function br($num = 1) {
        return str_repeat("<br />", $num);
    }

// ------------------------------------------------------------------------

    /**
     * Image
     *
     * Generates an <img /> element
     *
     * @access	public
     * @param	mixed
     * @return	string
     */
    function img($src = '', $index_page = FALSE) {
        if (!is_array($src)) {
            $src = array('src' => $src);
        }

        // If there is no alt attribute defined, set it to an empty string
        if (!isset($src['alt'])) {
            $src['alt'] = '';
        }

        $img = '<img';
        $img .= 'src="' . $src . '"';

        $img .= '/>';

        return $img;
    }

// ------------------------------------------------------------------------

    /**
     * Doctype
     *
     * Generates a page document type declaration
     *
     * Valid options are xhtml-11, xhtml-strict, xhtml-trans, xhtml-frame,
     * html4-strict, html4-trans, and html4-frame.  Values are saved in the
     * doctypes config file.
     *
     * @access	public
     * @param	string	type	The doctype to be generated
     * @return	string
     */
    function doctype($type = 'xhtml1-strict') {
        global $_doctypes;

        if (!is_array($_doctypes)) {
            if (defined('ENVIRONMENT') AND is_file(APPPATH . 'config/' . ENVIRONMENT . '/doctypes.php')) {
                include(APPPATH . 'config/' . ENVIRONMENT . '/doctypes.php');
            } elseif (is_file(APPPATH . 'config/doctypes.php')) {
                include(APPPATH . 'config/doctypes.php');
            }

            if (!is_array($_doctypes)) {
                return FALSE;
            }
        }

        if (isset($_doctypes[$type])) {
            return $_doctypes[$type];
        } else {
            return FALSE;
        }
    }

// ------------------------------------------------------------------------

    /**
     * Generates meta tags from an array of key/values
     *
     * @access	public
     * @param	array
     * @return	string
     */
    function meta($name = '', $content = '', $type = 'name', $newline = "\n") {
        // Since we allow the data to be passes as a string, a simple array
        // or a multidimensional one, we need to do a little prepping.
        if (!is_array($name)) {
            $name = array(array('name' => $name, 'content' => $content, 'type' => $type, 'newline' => $newline));
        } else {
            // Turn single array into multidimensional
            if (isset($name['name'])) {
                $name = array($name);
            }
        }

        $str = '';
        foreach ($name as $meta) {
            $type = (!isset($meta['type']) OR $meta['type'] == 'name') ? 'name' : 'http-equiv';
            $name = (!isset($meta['name'])) ? '' : $meta['name'];
            $content = (!isset($meta['content'])) ? '' : $meta['content'];
            $newline = (!isset($meta['newline'])) ? "\n" : $meta['newline'];

            $str .= '<meta ' . $type . '="' . $name . '" content="' . $content . '" />' . $newline;
        }

        return $str;
    }

// ------------------------------------------------------------------------

    /**
     * Generates non-breaking space entities based on number supplied
     *
     * @access	public
     * @param	integer
     * @return	string
     */
    function nbs($num = 1) {
        return str_repeat("&nbsp;", $num);
    }

}

/* End of file html_helper.php */
/* Location: ./system/helpers/html_helper.php */