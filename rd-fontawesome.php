<?php
/**
 * Plugin Name: Rundiz Font Awesome
 * Plugin URI: https://rundiz.com/?p=319
 * Description: Use Font Awesome from your host and update from GitHub.
 * Version: 1.0.6
 * Requires at least: 5.0
 * Requires PHP: 7.0
 * Author: Vee Winch
 * Author URI: https://rundiz.com
 * License: MIT
 * License URI: https://opensource.org/licenses/MIT
 * Text Domain: rd-fontawesome
 * Domain Path: /App/languages
 * 
 * @package rd-fontawesome
 */


if (!defined('ABSPATH')) {
    exit();
}


// define this plugin main file path.
if (!defined('RDFONTAWESOME_FILE')) {
    define('RDFONTAWESOME_FILE', __FILE__);
}


if (!defined('RDFONTAWESOME_VERSION')) {
    // if not defined constant version.
    $rd_fontawesome_pluginData = (function_exists('get_file_data') ? get_file_data(__FILE__, ['Version' => 'Version']) : null);
    $rd_fontawesome_pluginVersion = (isset($rd_fontawesome_pluginData['Version']) ? $rd_fontawesome_pluginData['Version'] : date('Ym')); // phpcs:ignore WordPress.DateTime.RestrictedFunctions.date_date
    unset($rd_fontawesome_pluginData);

    define('RDFONTAWESOME_VERSION', $rd_fontawesome_pluginVersion);

    unset($rd_fontawesome_pluginVersion);
}


// include this plugin's autoload.
require __DIR__ . '/autoload.php';


// initialize plugin app main class.
$rd_fontawesome_App = new \RdFontAwesome\App\App();
$rd_fontawesome_App->run();
unset($rd_fontawesome_App);
