<?php
/**
 * Settings class.
 * 
 * @package rd-fontawesome
 * @license http://opensource.org/licenses/MIT MIT
 * @since 1.0.0
 */


namespace RdFontAwesome\App\Libraries;


if (!class_exists('\\RdFontAwesome\\App\\Libraries\\Settings')) {
    class Settings
    {


        /**
         * @var string
         */
        protected $settingsFile;


        /**
         * Class constructor.
         */
        public function __construct()
        {
            $this->settingsFile = plugin_dir_path(RDFONTAWESOME_FILE) . 'settings.json';
        }// __construct


        /**
         * Get all settings from [plugin]/settings.json file.
         * 
         * @return array
         */
        public function getAllSettings(): array
        {
            $output = [];

            if (is_file($this->settingsFile)) {
                $settingsContent = file_get_contents($this->settingsFile);
                $settingsObj = json_decode($settingsContent);
                unset($settingsContent);

                if (is_object($settingsObj)) {
                    $output = (array) $settingsObj;
                }
                unset($settingsObj);
            }

            return $output;
        }// getAllSettings


        /**
         * Save settings to file.
         * 
         * @param array $settings The array key => value pair of settings. Example:
         *      <pre>array(
         *          'download_type' => 'github',
         *          'fontawesome_version' => '5.14.5',
         *      )</pre>
         * @return bool
         */
        public function saveSettings(array $settings): bool
        {
            $allSettings = $this->getAllSettings();

            if (!array_key_exists('last_update', $settings)) {
                $settings['last_update'] = get_date_from_gmt(gmdate('Y-m-d H:i:s'));
            }
            if (!array_key_exists('last_update_gmt', $settings)) {
                $settings['last_update_gmt'] = gmdate('Y-m-d H:i:s');;
            }

            $allSettings = array_merge($allSettings, $settings);
            $settingsJSON = json_encode($allSettings, JSON_PRETTY_PRINT);
            unset($allSettings);

            $result = file_put_contents($this->settingsFile, $settingsJSON);

            return ($result !== false);
        }// saveSettings


    }// Settings
}
