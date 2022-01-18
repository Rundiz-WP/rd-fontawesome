<?php
/**
 * URL class.
 * 
 * @package rd-fontawesome
 * @license http://opensource.org/licenses/MIT MIT
 * @since 1.0.0
 */


namespace RdFontAwesome\App\Libraries;


if (!class_exists('\\RdFontAwesome\\App\\Libraries\\Url')) {
    class Url
    {


        /**
         * @var array
         */
        protected $data;


        /**
         * @var string
         */
        protected $tempDir;


        /**
         * URL class constructor.
         * 
         * @param array $data The data that is in `AppTrait`.
         */
        public function __construct(array $data = [])
        {
            $this->data = $data;
        }// __construct


        /**
         * Magic get
         * 
         * @param string $name
         * @return mixed
         */
        public function __get(string $name)
        {
            if (property_exists($this, $name)) {
                return $this->{$name};
            }
            return null;
        }// __get


        /**
         * Download file from target URL and extract/move to distribute folder with these steps.
         * 
         * 1. Download file from `$downloadURL`.
         * 2. Prepare temp folder for extract files.
         * 3. Extract zip files to temp folder.
         * 4. Check that downloaded file is valid Font Awesome file(s).
         * 5. Move valid files to distribute folder (assets/vendor/fontawesome).
         * 
         * @global \WP_Filesystem_Base $wp_filesystem WordPress filesystem subclass.
         * @param string $downloadType The download type.
         * @param string $downloadUrl The download URL.
         * @return true|false|\WP_Error Return `true` on success, `false` on failure but be able to continue, `\WP_Error` on completely failure and need to be fixed.
         */
        public function downloadFile(string $downloadType, string $downloadUrl)
        {
            $tmp = download_url($downloadUrl);
            if (is_wp_error($tmp)) {
                return $tmp;
            } elseif (stripos($tmp, '.zip') === false) {
                return new \WP_Error(
                    'RDFA_NOTZIP', 
                    /* translators: %1$s the downloaded file path. */
                    sprintf(__('The downloaded file is not zip file. (%1$s).', 'rd-fontawesome'), $tmp)
                );
            }
            $this->tempDir = plugin_dir_path(RDFONTAWESOME_FILE) . '.temparchive';
            $targetDistributeDir = $this->data['targetDistDir'];

            // prepare new temp folder.
            global $wp_filesystem;
            WP_Filesystem();
            $wp_filesystem->delete($this->tempDir, true);
            $result = wp_mkdir_p($this->tempDir);
            if (false === $result) {
                return new \WP_Error(
                    'RDFA_CANTCREATEDIR', 
                    /* translators: %1$s the plugin directory path. */
                    sprintf(__('Unable to create directory at %1$s', 'rd-fontawesome'), plugin_dir_path(RDFONTAWESOME_FILE))
                );
            }
            unset($result);

            // extract files to new temp folder.
            $result = unzip_file($tmp, $this->tempDir);
            $wp_filesystem->delete($tmp);
            if (is_wp_error($result)) {
                return $result;
            }

        // validate Font Awesome files. -----
        $filesInNewtemp = $wp_filesystem->dirlist($this->tempDir);
        $validFontAwesomeFiles = false;
        $newVersionDir = null;
        $validFiles = [
            'css/all.min.css',
            'js/all.min.js',
            'sprites',
            'webfonts',
        ];
        if (is_array($filesInNewtemp)) {
            $newVersionDir = array_key_first($filesInNewtemp);// the extracted will be FontAwesome-x.x.x/css This will be get FontAwesome-x.x.x folder name.
        }
        unset($filesInNewtemp);

        foreach ($validFiles as $validFile) {
            if (file_exists($this->tempDir . DIRECTORY_SEPARATOR . $newVersionDir . DIRECTORY_SEPARATOR . $validFile)) {
                $validFontAwesomeFiles = true;
            } else {
                $validFontAwesomeFiles = false;
                break;
            }
        }// endforeach;
        unset($validFile, $validFiles);
        // end validate Font Awesome files. -----

        // move valid files to distribute folder (vendor/fontawesome).
        if (true === $validFontAwesomeFiles) {
            // if valid Font Awesome files.
            // prepare distributed folder
            $wp_filesystem->delete($targetDistributeDir, true);
            wp_mkdir_p($targetDistributeDir);
            // move target folders and files.
            $moveFiles = [
                'css',
                'js',
                'sprites',
                'svgs',
                'webfonts',
                'LICENSE.txt',
            ];
            if ('githubapi' === $downloadType) {
                $moveFiles[] = 'attribution.js';
            }
            $results = [];
            $movedSuccess = [];
            foreach ($moveFiles as $moveFile) {
                $result = rename($this->tempDir . '/' . $newVersionDir . '/' . $moveFile, $targetDistributeDir . '/' . $moveFile);
                $results[$this->tempDir . '/' . $newVersionDir . '/' . $moveFile] = $result;
                if (true === $result) {
                    $movedSuccess[] = $moveFile;
                }
                unset($result);
            }// endforeach;
            unset($moveFile);

            if (count($movedSuccess) === count($moveFiles)) {
                // if all files and folders moved successfully.
                $return = true;

                // delete new temp folder. (if not all success, keep it to showing what left behind.)
                $wp_filesystem->delete($this->tempDir, true);
            } else {
                $return = false;
                // it's PHP error, show in the log, no need to translate.
                trigger_error(sprintf('Unable to move some files (%s)', var_export($results, true)), E_USER_WARNING);
            }
            unset($moveFiles, $movedSuccess, $results);

            return $return;
        } else {
            $wp_filesystem->delete($this->tempDir, true);
            return new \WP_Error(
                'RDFA_INVALID_FAFILES',
                __('Invalid Font Awesome files.', 'rd-fontawesome')
            );
        }
        unset($newVersionDir, $validFontAwesomeFiles);
        }// downloadFile


        /**
         * Retrieve latest version.
         * 
         * @param string $downloadType The download type.
         * @return array Return associative array.<br>
         *              `tagVersion` (string) Tag version (if avaialble).<br>
         *              `downloadLink` (string) Download link (if available).<br>
         *              `isZipball` (bool) Optional. If `true` means it is using GitHub zipball auto URL.<br>
         */
        public function retrieveLatestVersion(string $downloadType): array
        {
            $output = [];
            if ($downloadType === 'githubapi') {
                $targetUrl = $this->data['repoURLs']['latestAPIURL'];
            } else {
                $targetUrl = $this->data['repoURLs']['latestURL'];
            }
            $response = wp_remote_get($targetUrl, [
                'redirection' => 0,
            ]);
            $location = wp_remote_retrieve_header($response, 'Location');
            $responseCode = (int) wp_remote_retrieve_response_code($response);

            if ($downloadType === 'githubapi') {
                $body = wp_remote_retrieve_body($response);
                $bodyObj = json_decode($body);
                unset($body);

                if (is_object($bodyObj)) {
                    $output['tagVersion'] = ($bodyObj->tag_name ?? null);
                    if (isset($bodyObj->assets) && (is_array($bodyObj->assets) || is_object($bodyObj->assets))) {
                        foreach ($bodyObj->assets as $asset) {
                            if (isset($asset->browser_download_url) && stripos($asset->browser_download_url, 'web') !== false) {
                                $output['downloadLink'] = $asset->browser_download_url;
                                break;
                            }
                        }// endforeach;
                        unset($asset);
                    }
                    if (!isset($output['downloadLink'])) {
                        // if still not found download link.
                        $output['downloadLink'] = ($bodyObj->zipball_url ?? null);
                        $output['isZipball'] = true;
                    }
                }
                unset($bodyObj);
            } else {
                if (
                    (301 === $responseCode || 302 === $responseCode) && 
                    is_string($location) && 
                    !empty($location)
                ) {
                    $location = trim($location, " \n\r\t\v\0/");
                    $expUrl = explode('/', $location);
                    $output['tagVersion'] = $expUrl[(count($expUrl) -1)];
                    $output['downloadLink'] = 'https://github.com/' . 
                        $this->data['reponame'] . 
                        '/archive/refs/tags/' . $output['tagVersion'] . '.zip';// https://stackoverflow.com/a/68446460/128761
                    unset($expUrl);
                }
            }

            unset($location, $response, $responseCode);
            return $output;
        }// retrieveLatestVersion


    }
}
