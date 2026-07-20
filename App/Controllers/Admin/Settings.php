<?php
/**
 * Add settings sub menu and page into the Settings menu.
 * 
 * @package rd-fontawesome
 * @license http://opensource.org/licenses/MIT MIT
 * @since 1.0.0
 */


namespace RdFontAwesome\App\Controllers\Admin;


if (!defined('ABSPATH')) {
    exit();
}


if (!class_exists('\\RdFontAwesome\\App\\Controllers\\Admin\\Settings')) {
    /**
     * Admin settings page.
     */
    class Settings extends \RdFontAwesome\App\Controllers\BaseController
    {


        /**
         * @var string Settings menu slug. This class constant visibility must be public.
         */
        const MENU_SLUG = 'rd-fontawesome-settings';


        /**
         * @var string The current admin page.
         */
        private $hookSuffix = '';


        /**
         * Allow code/WordPress to call hook `admin_enqueue_scripts` 
         * then `wp_register_script()`, `wp_localize_script()`, `wp_enqueue_script()` functions will be working fine later.
         * 
         * @link https://wordpress.stackexchange.com/a/76420/41315 Original source code.
         * @since 2025-10-14 On Rundiz Plugin Template
         * @since 1.0.8
         */
        public function callEnqueueHook()
        {
            add_action('admin_enqueue_scripts', [$this, 'registerScripts']);
        }// callEnqueueHook


        /**
         * Get current settings.
         * 
         * @return array|object
         */
        protected function getCurrentSettings()
        {
            $Settings = new \RdFontAwesome\App\Libraries\Settings();
            return $Settings->getAllSettings();
        }// getCurrentSettings


        /**
         * Get server info.
         * 
         * @return array
         */
        protected function getServerInfo(): array
        {
            $output = [];

            // WordPress version.
            global $wp_version;
            $output['wpVersion'] = $wp_version;

            // plugin version.
            $output['pluginVersion'] = RDFONTAWESOME_VERSION;
            // paths writable.
            $pathsToCheck = [
                WP_CONTENT_DIR . '/uploads',
                WP_CONTENT_DIR . '/uploads/rd-fontawesome',
            ];
            $output['writable'] = [];
            foreach ($pathsToCheck as $path) {
                $pathNormalized = str_replace(['\\', '/'], DIRECTORY_SEPARATOR, $path);
                if (!file_exists($path)) {
                    $output['writable'][$pathNormalized] = 'filenotexists';
                } else {
                    $output['writable'][$pathNormalized] = wp_is_writable($path);
                }
            }// endforeach;
            unset($path, $pathNormalized, $pathsToCheck);
            // end check paths writable.

            // execution timeout
            $output['phpExecTimeout'] = ini_get('max_execution_time');
            // memory limit
            $output['phpMemoryLimit'] = ini_get('memory_limit');
            $output['wpMemoryLimit'] = (defined('WP_MEMORY_LIMIT') ? WP_MEMORY_LIMIT : null);

            return $output;
        }// getServerInfo


        /**
         * The plugin settings sub menu to go to settings page.
         */
        public function pluginSettingsMenu()
        {
            $hook_suffix = add_options_page(__('Rundiz Font Awesome settings', 'rd-fontawesome'), __('Rundiz Font Awesome', 'rd-fontawesome'), 'manage_options', static::MENU_SLUG, [$this, 'pluginSettingsPage']);
            if (is_string($hook_suffix)) {
                $this->hookSuffix = $hook_suffix;
                add_action('load-' . $hook_suffix, [$this, 'callEnqueueHook']);
            }
            unset($hook_suffix);
        }// pluginSettingsMenu


        /**
         * Display plugin settings page.
         */
        public function pluginSettingsPage()
        {
            // check permission.
            if (!current_user_can('manage_options')) {
                wp_die(esc_html__('You do not have permission to access this page.', 'rd-fontawesome'));
            }

            $output = [];
            // list selectable of major versions for the form
            $output['allMajorVersions'] = ($this->getStaticPluginData())['majorVersions'];
            // load settings from config json file.
            $output['settings'] = $this->getCurrentSettings();
            // server info data.
            $output['serverinfo'] = $this->getServerInfo();
            // set default form value if not exists.
            if (!isset($output['settings']['major_version'])) {
                $output['settings']['major_version'] = ($this->getStaticPluginData())['defaultMajorVersion'];
            }

            // phpcs:ignore WordPress.Security.NonceVerification.Missing
            if (isset($_POST) && !empty($_POST)) {
                // if method POST.
                // save via AJAX only, this process should just die.
                wp_die(esc_html__('Invalid request.', 'rd-fontawesome'));
                exit();
            }// endif $_POST

            $this->Loader->loadView('admin/settings_v', $output);
            unset($output);
        }// pluginSettingsPage


        /**
         * {@inheritDoc}
         */
        public function registerHooks()
        {
            add_action('admin_menu', [$this, 'pluginSettingsMenu']);
        }// registerHooks


        /**
         * Enqueue scripts and styles here.
         * 
         * @param string $hook_suffix The current admin page.
         */
        public function registerScripts(string $hook_suffix = '')
        {
            if ($hook_suffix !== $this->hookSuffix) {
                return;
            }

            // enqueue style.
            wp_enqueue_style('rd-fontawesome-handle-settings', plugin_dir_url(RDFONTAWESOME_FILE) . 'assets/css/admin/settings.css', [], RDFONTAWESOME_VERSION);

            // enqueue script.
            $handleName = 'rd-fontawesome-handle-settings';
            wp_register_script($handleName, plugin_dir_url(RDFONTAWESOME_FILE) . 'assets/js/admin/settings.js', ['jquery'], RDFONTAWESOME_VERSION, true);
            wp_localize_script(
                $handleName,
                'RdFontAwesomeSettingsObject', 
                [
                    'nonce' => wp_create_nonce(SettingsAjax::AJAX_NONCE),
                    'txtDismissNotice' => __('Dismiss this notice.', 'rd-fontawesome'),
                    'txtLoading' => __('Loading', 'rd-fontawesome'),
                ]
            );
            wp_enqueue_script($handleName);
            unset($handleName);
        }// registerScripts


    }// Settings
}
