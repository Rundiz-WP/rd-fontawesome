<?php
/**
 * Activation class is the class that will be working on activate, deactivate, delete WordPress plugin.
 * 
 * @package rundiz-oauth
 * @license http://opensource.org/licenses/MIT MIT
 * @since 1.0.0
 */


namespace RdFontAwesome\App\Controllers\Admin;


if (!class_exists('\\RdFontAwesome\\App\\Controllers\\Activation')) {
    class Activation extends \RdFontAwesome\App\Controllers\BaseController
    {


        /**
         * add links to plugin actions area
         * 
         * @param array $actions current plugin actions. (including deactivate, edit).
         * @param string $plugin_file the plugin file for checking.
         * @return array return modified links
         */
        public function actionLinks(array $actions, string $plugin_file): array
        {
            static $plugin;
            
            if (!isset($plugin)) {
                $plugin = plugin_basename(RDFONTAWESOME_FILE);
            }
            
            if ($plugin == $plugin_file) {
                $link['settings'] = '<a href="'.  esc_url(get_admin_url(null, 'options-general.php?page=rd-fontawesome-settings')).'">'.__('Settings').'</a>';
                $actions = array_merge($link, $actions);
            }
            
            return $actions;
        }// actionLinks


        /**
         * {@inheritDoc}
         */
        public function registerHooks()
        {
            // register uninstall hook. this hook will be work on delete plugin.
            // * register uninstall hook MUST be static method or function.
            register_uninstall_hook(RDFONTAWESOME_FILE, ['\\RdFontAwesome\\App\\Controllers\\Admin\\Activation', 'uninstall']);

            // add filter action links. this will be displayed in actions area of plugin page. for example: xxxbefore | Activate | Edit | Delete | xxxafter
            add_filter('plugin_action_links', [$this, 'actionLinks'], 10, 5);
        }// registerHooks


        /**
         * delete the plugin.
         * 
         * @global \wpdb $wpdb
         */
        public static function uninstall()
        {
            // do something that will be happens on delete plugin.
            global $wpdb;
            $wpdb->show_errors();

            // this plugin keep settings into file base.
            // so, it is no need to `delete_option()` here because all files in this plugin will be delete on uninstall by WordPress core anyway.
        }// uninstall


    }
}