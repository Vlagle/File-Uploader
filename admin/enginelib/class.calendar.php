<?php
// +----------------------------------------------------------------------+
// | EngineLib - Calendar Class                                           |
// +----------------------------------------------------------------------+
// | Copyright (c) 2003,2004 AlexScriptEngine - e-Visions                 |
// +----------------------------------------------------------------------+
// | This code is not freeware. Please read our licence condition care-   |
// | fully to find out more. If there are any doubts please ask at the    |
// | Support Forum                                                        |
// |                                                                      |
// +----------------------------------------------------------------------+
// | Author: Alex Höntschel <info@alexscriptengine.de>                    |
// | Web: http://www.alexscriptengine.de                                  |
// | IMPORTANT: No email support, please use the support forum at         |
// |            http://www.alexscriptengine.de                            |
// +----------------------------------------------------------------------+
// $Id: class.calendar.php 6 2005-10-08 10:12:03Z alex $

define('NEWLINE', "\n");

class DHTML_Calendar {
    var $calendar_lib_path;

    var $calendar_file;
    var $calendar_lang_file;
    var $calendar_setup_file;
    var $calendar_theme_file;
    var $calendar_options;

    function DHTML_Calendar($calendar_lib_path = '/calendar/',$lang = 'en',$theme = 'calendar-system',$stripped = true) {
        if ($stripped) {
            $this->calendar_file = 'calendar_stripped.js';
            $this->calendar_setup_file = 'calendar-setup_stripped.js';
        } else {
            $this->calendar_file = 'calendar.js';
            $this->calendar_setup_file = 'calendar-setup.js';
        }
        $this->calendar_lang_file = 'lang/calendar-' . $lang . '.js';
        $this->calendar_theme_file = $theme.'.css';
        $this->calendar_lib_path = preg_replace('/\/+$/', '/', $calendar_lib_path);
        $this->calendar_options = array('ifFormat' => '%Y/%m/%d','daFormat' => '%Y/%m/%d');
    }

    function set_option($name, $value) {
        $this->calendar_options[$name] = $value;
    }

    function load_files() {
        echo $this->get_load_files_code();
    }

    function get_load_files_code() {
        $code  = ( '<link rel="stylesheet" type="text/css" media="all" href="' .
                   $this->calendar_lib_path . $this->calendar_theme_file .
                   '" />' . NEWLINE );
        $code .= ( '<script type="text/javascript" src="' .
                   $this->calendar_lib_path . $this->calendar_file .
                   '"></script>' . NEWLINE );
        $code .= ( '<script type="text/javascript" src="' .
                   $this->calendar_lib_path . $this->calendar_lang_file .
                   '"></script>' . NEWLINE );
        $code .= ( '<script type="text/javascript" src="' .
                   $this->calendar_lib_path . $this->calendar_setup_file .
                   '"></script>' );
        return $code;
    }

    function _make_calendar($other_options = array()) {
        $js_options = $this->_make_js_hash(array_merge($this->calendar_options, $other_options));
        $code  = ( '<script type="text/javascript">Calendar.setup({' .
                   $js_options .
                   '});</script>' );
        return $code;
    }

    function make_input_field($cal_options = array(), $field_attributes = array(),$title="") {
		global $config,$_ENGINE;
        $id = $this->_gen_id();
        $attrstr = $this->_make_html_attr(array_merge($field_attributes,
                                                      array('id'   => $this->_field_id($id),
                                                            'type' => 'text')));

		$input = '<input ' . $attrstr .'/>';
        $input .= '&nbsp;<a href="#" id="'. $this->_trigger_id($id) . '">' .
            '<img width="16" height="16" border="0" align="absmiddle" border="0" src="'.$_ENGINE['main_url'].'/admin/images/calender.gif" alt="" /></a>';
        buildStandardRow($title, $input);
        $options = array_merge($cal_options,
                               array('inputField' => $this->_field_id($id),
                                     'button'     => $this->_trigger_id($id)));
        echo $this->_make_calendar($options);
    }

    /// PRIVATE SECTION

    function _field_id($id) { 
		return 'f-calendar-field-' . $id; 
	}
	
    function _trigger_id($id) { 
		return 'f-calendar-trigger-' . $id; 
	}
	
    function _gen_id() { 
		static $id = 0; return ++$id; 
	}

    function _make_js_hash($array) {
        $jstr = '';
        reset($array);
        while (list($key, $val) = each($array)) {
            if (is_bool($val))
                $val = $val ? 'true' : 'false';
            else if (!is_numeric($val))
                $val = '"'.$val.'"';
            if ($jstr) $jstr .= ',';
            $jstr .= '"' . $key . '":' . $val;
        }
        return $jstr;
    }

    function _make_html_attr($array) {
        $attrstr = '';
        reset($array);
        while (list($key, $val) = each($array)) {
            $attrstr .= $key . '="' . $val . '" ';
        }
        return $attrstr;
    }
};

?>