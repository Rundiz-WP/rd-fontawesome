<?php
/**
 * App trait.
 * 
 * @package rd-fontawesome
 * @license http://opensource.org/licenses/MIT MIT
 * @since 1.0.0
 */


namespace RdFontAwesome\App;


if (!trait_exists('\\RdFontAwesome\\App\\AppTrait')) {
    trait AppTrait
    {


            /**
             * @var \RdFontAwesome\App\Libraries\Loader
             */
            protected $Loader;


            /**
             * Magic get
             * 
             * @param string $name
             */
            public function __get(string $name)
            {
                if (property_exists($this, $name)) {
                    return $this->{$name};
                }
                return null;
            }// __get


            /**
             * Get static plugin data such as Font Awesome repository name, URL, plugin folder, plugin URL.
             * 
             * @return array Return associative array with keys:<br>
             *              'reponame' (string) Repository name. Example: name/repo<br>
             *              'repoURLs' (array) Repository URLs.<br>
             *                      'latestURL' (string) Latest release URL.<br>
             *                      'latestAPIURL' (string) Latest release as API URL.<br>
             *              'targetDistDir' (string) The plugin assets/vendor/fontawesome folder path.<br>
             *              'targetDistURLBase' (string) The plugin assets/vendor/fontawesome URL base.<br>
             */
            public function getStaticPluginData(): array
            {
                $reponame = 'FortAwesome/Font-Awesome';
                return [
                    'reponame' => $reponame,
                    'repoURLs' => [
                        'latestURL' => 'https://github.com/' . $reponame . '/releases/latest',
                        'latestAPIURL' => 'https://api.github.com/repos/' . $reponame . '/releases/latest',
                    ],
                    'targetDistDir' => plugin_dir_path(RDFONTAWESOME_FILE) . 'assets/vendor/fontawesome',
                    'targetDistURLBase' => plugin_dir_url(RDFONTAWESOME_FILE) . 'assets/vendor/fontawesome',
                ];
            }// getStaticPluginData


    }
}
