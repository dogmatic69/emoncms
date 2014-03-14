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

/**
 * Return all locale directory from all modules.
 *
 * If one module has a language it will be detected
 *
 * @param string $dir the directory to scan
 *
 * @return array
 */
function directoryLocaleScan($dir) {
    if (!is_dir($dir) || !is_readable($dir)) {
        return array();
    }

    $dir = realpath($dir);

    $objects = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($dir), RecursiveIteratorIterator::SELF_FIRST);

    $dlist = array();
    foreach($objects as $entry => $object){
        $entry = str_replace($dir, '', $entry);
        $baseName = basename($entry);
        if (basename(dirname($entry)) == 'locale' && !in_array($baseName, array('.', '..'))) {
            $dlist[] = $baseName;
        }
    }

    return array_unique($dlist);
}

/**
 * Get languages used in modules
 *
 * @return array
 */
function get_available_languages() {
   return directoryLocaleScan(dirname(__FILE__));
}

/**
 * Figure out the browsers language
 *
 * @return array
 */
function lang_http_accept() {
    $langs = array();

    foreach (explode(',', server('HTTP_ACCEPT_LANGUAGE')) as $lang) {
        $pattern = '/^(?P<primarytag>[a-zA-Z]{2,8})'.
        '(?:-(?P<subtag>[a-zA-Z]{2,8}))?(?:(?:;q=)'.
        '(?P<quantifier>\d\.\d))?$/';

        $splits = array();
        if (!preg_match($pattern, $lang, $splits)) {
            continue;
        }

        $a = $splits['primarytag'];
        if (!empty($splits['subtag'])) {
            $a = $a . "_" . $splits['subtag'];
        }
        $langs[] = $a;
    }
    return $langs;
}
/**
 * set the first browser selected language
 * TODO: iterate to find a suitable available language
 * 
 * Chrome returns different HTTP_ACCEPT_LANGUAGE code than firefox!!!
 * Firefox      Chrome
 * -------------------
 * en_EN         en
 * es_ES         es
 * ... 
 * so translation system does not work in Chrome!!!
 * 
 * lets try to fix quickly
 * 
 * @param string $language the language to configure
 * 
 * @return void
 */
function set_lang($language) {
    if (empty($language[0])) {
        return;
    }

    switch ($language[0]) {
        case 'es':
            $language[0] = 'es_ES';
            break;

        case 'fr':
            $language[0] = 'fr_FR';
            break;
    }

    return set_lang_by_user($language[0]);
}

/**
 * Set the users language
 *
 * @param string $language the language to configure
 *
 * @return void
 */ 
function set_lang_by_user($language) {
    putenv('LC_ALL=' . $language);
    setlocale(LC_ALL, $language);
}

/**
 * Set the language used
 *
 * Defaults to the browser setting if nothing is specified
 *
 * @param string $lang the language to use
 *
 * @return void
 */
function set_emoncms_lang($language = null) {
    if (!empty($language)) {
        return set_lang_by_user($language);
    }

    return set_lang(lang_http_accept());
}

