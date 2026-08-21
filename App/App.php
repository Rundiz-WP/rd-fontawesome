<?php
/**
 * Main app class.
 * 
 * @package rd-fontawesome
 * @license http://opensource.org/licenses/MIT MIT
 * @since 1.0.0
 */


namespace RdFontAwesome\App;


if (!defined('ABSPATH')) {
    exit();
}


if (!class_exists('\\RdFontAwesome\App\\App')) {
    /**
     * Plugin application main entry class.
     */
    class App
    {


        use AppTrait;


        /**
         * Load text domain. (Language files)
         * 
         * @since 1.0.9
         * @link https://make.wordpress.org/core/2025/03/12/i18n-improvements-6-8/ The load text domain function is not need if requires WP 6.8+
         * @link https://core.trac.wordpress.org/ticket/64249 Follow-up bug fix that auto load translation file not working on multi-site enabled.
         */
        public function loadLanguage()
        {
            load_plugin_textdomain('rd-fontawesome', false, dirname(plugin_basename(RDFONTAWESOME_FILE)) . '/App/languages/');
        }// loadLanguage


        /**
         * Run the WP plugin app.
         */
        public function run()
        {
            add_action('init', function () {
                // @link https://codex.wordpress.org/Function_Reference/load_plugin_textdomain Reference.
                // load language of this plugin.
                $this->loadLanguage();
            });

            // Initialize the loader class.
            $this->Loader = new \RdFontAwesome\App\Libraries\Loader();
            $this->Loader->App = $this;
            $this->Loader->autoLoadFunctions();
            $this->Loader->autoRegisterControllers();
        }// run


    }// App
}
