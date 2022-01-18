<?php
/**
 * Handle enqueue/dequeue styles and scripts.
 * 
 * @package rd-fontawesome
 * @license http://opensource.org/licenses/MIT MIT
 * @since 1.0.0
 */


namespace RdFontAwesome\App\Controllers\Front\Hooks;


if (!class_exists('\\RdFontAwesome\\App\\Controllers\\Front\\Hooks\\EnqueueDequeue')) {
    class EnqueueDequeue extends \RdFontAwesome\App\Controllers\BaseController
    {


        /**
         * Get all settings.
         * 
         * @return array
         */
        protected function getSettings(): array
        {
            $Settings = new \RdFontAwesome\App\Libraries\Settings();
            $allSettings = $Settings->getAllSettings();
            unset($Settings);
            return $allSettings;
        }// getSettings


        /**
         * Dequeue scripts.
         */
        public function dequeueScripts()
        {
            $allSettings = $this->getSettings();

            if (isset($allSettings['dequeue_js']) && !empty($allSettings['dequeue_js'])) {
                $handles = explode(',', $allSettings['dequeue_js']);
                foreach ($handles as $handle) {
                    wp_dequeue_script(trim($handle));
                }// endforeach;
                unset($handle, $handles);
            }
        }// dequeueScripts


        /**
         * Dequeue styles.
         */
        public function dequeueStyles()
        {
            $allSettings = $this->getSettings();

            if (isset($allSettings['dequeue_css']) && !empty($allSettings['dequeue_css'])) {
                $handles = explode(',', $allSettings['dequeue_css']);
                foreach ($handles as $handle) {
                    wp_dequeue_style(trim($handle));
                }// endforeach;
                unset($handle, $handles);
            }
        }// dequeueStyles


        /**
         * Enqueue this plugin's assets.
         */
        public function enqueueAssets()
        {
            $allSettings = $this->getSettings();

            if (!isset($allSettings['donot_enqueue']) || $allSettings['donot_enqueue'] !== '1') {
                $faVersion = ($allSettings['fontawesome_version'] ?? false);
                $pluginUrlBase = ($this->getStaticPluginData())['targetDistURLBase'];

                wp_enqueue_style('rd-fontawesome-allmin', $pluginUrlBase . '/css/all.min.css', [], $faVersion);
            }
        }// enqueueAssets


        /**
         * {@inheritDoc}
         */
        public function registerHooks()
        {
            $allSettings = $this->getSettings();

            if (isset($allSettings['dequeue_css']) && !empty($allSettings['dequeue_css'])) {
                add_action('wp_enqueue_scripts', [$this, 'dequeueStyles'], 100);
            }
            if (isset($allSettings['dequeue_js']) && !empty($allSettings['dequeue_js'])) {
                add_action('wp_enqueue_scripts', [$this, 'dequeueScripts'], 100);
            }
            if (!isset($allSettings['donot_enqueue']) || $allSettings['donot_enqueue'] !== '1') {
                add_action('wp_enqueue_scripts', [$this, 'enqueueAssets']);
            }
        }// registerHooks


    }// EnqueueDequeue
}
