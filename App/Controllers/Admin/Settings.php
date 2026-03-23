<?php
/**
 * Settings page.
 * 
 * @package rd-fontawesome
 * @license http://opensource.org/licenses/MIT MIT
 * @since 1.0.0
 */


namespace RdFontAwesome\App\Controllers\Admin;


if (!class_exists('\\RdFontAwesome\\App\\Controllers\\Admin\\Settings')) {
    /**
     * Settings class.
     */
    class Settings extends \RdFontAwesome\App\Controllers\BaseController
    {


        /**
         * Enqueue scripts and styles here.
         */
        public function enqueueScriptsStyles()
        {
            // enqueue style.
            wp_enqueue_style('rd-fontawesome-settings', plugin_dir_url(RDFONTAWESOME_FILE) . 'assets/css/admin/settings.css', [], RDFONTAWESOME_VERSION);

            // enqueue script.
            wp_register_script('rd-fontawesome-settings', plugin_dir_url(RDFONTAWESOME_FILE) . 'assets/js/admin/settings.js', ['jquery'], RDFONTAWESOME_VERSION, true);
            wp_localize_script(
                'rd-fontawesome-settings',
                'RdFontAwesomeSettingsObject', 
                [
                    'nonce' => wp_create_nonce('rdfontawesome_ajaxnonce'),
                    'txtDismissNotice' => __('Dismiss this notice.', 'rd-fontawesome'),
                    'txtLoading' => __('Loading', 'rd-fontawesome'),
                ]
            );
            wp_enqueue_script('rd-fontawesome-settings');
        }// enqueueScriptsStyles


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
         * Setup settings menu to go to settings page.
         */
        public function pluginSettingsMenu()
        {
            $hook_suffix = add_options_page(__('Rundiz Font Awesome settings', 'rd-fontawesome'), __('Rundiz Font Awesome', 'rd-fontawesome'), 'manage_options', 'rd-fontawesome-settings', [$this, 'pluginSettingsPage']);
            add_action('load-' . $hook_suffix, [$this, 'enqueueScriptsStyles']);
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
                exit();
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
            if (is_admin()) {
                add_action('admin_menu', [$this, 'pluginSettingsMenu']);
            }
        }// registerHooks


    }// Settings
}
