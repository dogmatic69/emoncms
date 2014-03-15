<?php
/*

All Emoncms code is released under the GNU Affero General Public License.
See COPYRIGHT.txt and LICENSE.txt.

---------------------------------------------------------------------
Emoncms - open source energy visualisation
Part of the OpenEnergyMonitor project:
http://openenergymonitor.org

*/

define('MODULE_PATH', 'Modules');
define('MODULE_PATH_EXT', MODULE_PATH . DS);
define('WIDGETS_PATH', MODULE_PATH_EXT . 'dashboard' . DS  . 'Views' . DS . 'js' . DS . 'widgets');
define('WIDGETS_PATH_EXT', WIDGETS_PATH . DS);

$widgets = array();
$dir = scandir(WIDGETS_PATH);
for ($i = 2; $i < count($dir); $i++) {
    if (filetype(WIDGETS_PATH_EXT.$dir[$i]) != 'dir') {
        continue;
    }

    $file = WIDGETS_PATH_EXT . $dir[$i] . DS . $dir[$i] . '_widget.php';
    if (is_file($file)) {
        require_once $file;
        $widgets[] = $dir[$i];
        continue;
    }

    $file = WIDGETS_PATH_EXT . $dir[$i] . DS . $dir[$i] . '_render.js';
    if (is_file($file)) {
        echo sprintf('<script type="text/javascript" src="%s_render.js"></script>', $path . WIDGETS_PATH_EXT . $dir[$i] . DS . $dir[$i]);
        $widgets[] = $dir[$i];
    }
}

/**
 * Load module specific widgets
 */
$dir = scandir(MODULE_PATH);
for ($i=2; $i<count($dir); $i++) {
    if (filetype(MODULE_PATH_EXT.$dir[$i]) != 'dir') {
        continue;
    }

    $file = MODULE_PATH_EXT . $dir[$i] . DS . 'widget' . DS . $dir[$i] . '_widget.php';
    if (is_file($file)) {
        require_once $file;
        $widgets[] = $dir[$i];
        continue;
    }

    $file = MODULE_PATH_EXT . $dir[$i] . DS . 'widget' . DS . $dir[$i] . '_render.js';
    if (is_file($file)) {
        echo sprintf('<script type="text/javascript" src="%s/widget/%s_render.js"></script>', $path . MODULE_PATH_EXT . $dir[$i], $dir[$i]);
        $widgets[] = $dir[$i];
    }
}
