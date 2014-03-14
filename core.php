<?php

/*
    All Emoncms code is released under the GNU Affero General Public License.
    See COPYRIGHT.txt and LICENSE.txt.

    ---------------------------------------------------------------------
    Emoncms - open source energy visualisation
    Part of the OpenEnergyMonitor project:
    http://openenergymonitor.org
*/

// no direct access
defined('EMONCMS_EXEC') or die('Restricted access');

if (!defined('DS')) {
    define('DS', '/');
}

/**
 * debug method wrapper for print_r
 *
 * @return void
 */
function pr($var) {
    echo sprintf('<pre>%s</pre>', print_r($var, true));
}

/**
 * Figure out if the page is running over ssl
 *
 * @return string
 */
function get_protocol() {
    return 'http' . (server('HTTPS') == 'on' || server('HTTP_X_FORWARDED_PROTO') == "https" ? 's' : null);
}

/**
 * figure out the url
 *
 * @return string
 */
function get_application_path() {
    $index = 'HTTP_HOST';
    if(server('HTTP_X_FORWARDED_SERVER')) {
        $index = 'HTTP_X_FORWARDED_SERVER';
    }

    return dirname(get_protocol() . "://" . server($index) . server('SCRIPT_NAME')) . "/";
}

/**
 * check if the database has been initialized
 *
 * @param Object $mysqli the database object
 * @param string $database the database name
 *
 * @return boolean
 */
function db_check($mysqli,$database) {
    $result = $mysqli->query("SELECT count(table_schema) from information_schema.tables WHERE table_schema = '$database'");
    $row = $result->fetch_array();
    
    return $row['0'] > 0;
}

function controller($controller_name) {
    $controller = $controller_name . '_controller';
    $controllerScript = 'Modules' . DS . $controller_name . DS . $controller . '.php';
    if (is_file($controllerScript)) {
        $domain = 'messages';
        bindtextdomain($domain, 'Modules' . DS . $controller_name . DS . 'locale');
        bind_textdomain_codeset($domain, 'UTF-8');

        textdomain($domain);

        require $controllerScript;
        return $controller();
    }
    return array('content'=>'');
}

/**
 * Render a view
 *
 * @param string $filepath the path to render
 * @param array $args the params to pass into the view being rendered
 *
 * @return string
 */
function view($filepath, array $args) {
    extract($args);
    ob_start();
    include $filepath;
    return ob_get_clean();
}

/**
 * wrapper method for getting a get variable from $_GET without having to check it
 * 
 * @param string $index the array index to fetch
 *
 * @return mixed
 */
function get($index) {
    return isset($_GET[$index]) ? $_GET[$index] : null;
}

/**
 * wrapper method for getting a post variable from $_POST without having to check it
 * 
 * @param string $index the array index to fetch
 *
 * @return mixed
 */
function post($index) {
    return isset($_POST[$index]) ? $_POST[$index] : null;
}

/**
 * wrapper method for getting a server variable from $_SERVER without having to check it
 * 
 * @param string $index the array index to fetch
 *
 * @return mixed
 */
function server($index) {
    return isset($_SERVER[$index]) ? $_SERVER[$index] : null;
}

/**
 * load schema files
 *
 * @return array
 */
function load_db_schema() {
    $vars = load_files('schema', array('schema' => array()));
    return $vars['schema'];
}

/**
 * load menu files
 *
 * @return array
 */
function load_menu() {
    $vars = load_files('menu', array('menu_left' => array(), 'menu_right' => array(), 'menu_dropdown' => array()));

    usort($vars['menu_left'], function($a,$b) {
        return $a['order'] > $b['order'];
    });

    return array(
        'left' => $vars['menu_left'], 
        'right' => $vars['menu_right'], 
        'dropdown' => $vars['menu_dropdown'],
    );
}

/**
 * Generic file loader method
 *
 * Initializes variables and then returns the adjusted values after loading the files specified
 *
 * @param string $type the type of files to load (menu / schema etc)
 * @param array $vars the variables to be used
 *
 * @return array
 */
function load_files($type, array $vars = array()) {
    extract($vars, EXTR_OVERWRITE);

    $dir = scandir('Modules');
    for ($i = 2; $i < count($dir); $i++) {
        if (is_dir('Modules' . DS . $dir[$i]) && is_file('Modules' . DS . $dir[$i] . DS . $dir[$i] . '_' . $type . '.php')) {
            require 'Modules' . DS . $dir[$i] . DS . $dir[$i] . '_' . $type . '.php';
        }
    }

    return call_user_func_array('compact', array_keys($vars));
}
