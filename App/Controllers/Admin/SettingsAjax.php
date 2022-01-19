<?php
/**
 * AJAX for settings page.
 * 
 * @package rd-fontawesome
 * @license http://opensource.org/licenses/MIT MIT
 * @since 1.0.0
 */


namespace RdFontAwesome\App\Controllers\Admin;


if (!class_exists('\\RdFontAwesome\\App\\Controllers\\Admin\\SettingsAjax')) {
        class SettingsAjax extends \RdFontAwesome\App\Controllers\BaseController
        {


            /**
             * @var \\RdFontAwesome\\App\\Libraries\\Url
             */
            protected $Url;


            /**
             * Class constructor.
             */
            public function __construct()
            {
                $this->Url = new \RdFontAwesome\App\Libraries\Url($this->getStaticPluginData());
            }// __construct


            /**
             * {@inheritDoc}
             */
            public function registerHooks()
            {
                if (is_admin()) {
                    add_action('wp_ajax_rdfontawesome_retrievelatestversion', [$this, 'retrieveLatestFAVersion']);
                    add_action('wp_ajax_rdfontawesome_installlatestversion', [$this, 'installLatestFAVersion']);
                    add_action('wp_ajax_rdfontawesome_savesettings', [$this, 'saveSettings']);
                }
            }// registerHooks


            /**
             * Download and install latest Font Awesome version.
             */
            public function installLatestFAVersion()
            {
                $output = [];
                check_ajax_referer('rdfontawesome_ajaxnonce', 'nonce');
                $downloadType = (strip_tags($_REQUEST['download_type']) ?? '');

                $Settings = new \RdFontAwesome\App\Libraries\Settings();
                $allSettings = $Settings->getAllSettings();

                $latestInfo = $this->Url->retrieveLatestVersion($downloadType);
                $downloadLink = ($latestInfo['downloadLink'] ?? null);
                $latestVersion = ($latestInfo['tagVersion'] ?? null);
                unset($latestInfo);

                $statusCode = 200;
                if (is_null($downloadLink) || is_null($latestVersion)) {
                    $statusCode = 404;
                } elseif (is_string($downloadLink) && !empty($downloadLink) && is_string($latestVersion)) {
                    if (
                        empty($allSettings) || 
                        (
                            isset($allSettings['fontawesome_version']) &&
                            version_compare($allSettings['fontawesome_version'], $latestVersion, '<')
                        ) ||
                        !is_dir(($this->getStaticPluginData())['targetDistDir'])
                    ) {
                        // if never installed before OR older that latest released.
                        $dlResult = $this->Url->downloadFile($downloadType, $downloadLink);
                        if (is_wp_error($dlResult)) {
                            $statusCode = 500;
                            $output['formResult'] = 'error';
                            $output['formResultMessage'] = [];
                            foreach ($dlResult->get_error_messages() as $eMessage) {
                                $output['formResultMessage'][] = $eMessage;
                            }// endforeach;
                            unset($eMessage);
                        } else {
                            $output['downloadResult'] = $dlResult;
                            if (false === $dlResult) {
                                // if there are some errors but can continue.
                                $output['tempDir'] = $this->Url->tempDir;
                                $output['tempDirExists'] = is_dir($this->Url->tempDir);
                            }
                        }

                        if (!is_wp_error($dlResult)) {
                            $Settings->saveSettings([
                                'download_type' => $downloadType,
                                'fontawesome_version' => strip_tags($latestVersion),
                            ]);
                            $output['allSettings'] = $Settings->getAllSettings();
                            $output['downloadLink'] = $downloadLink;
                            $output['tagVersion'] = ($output['allSettings']['fontawesome_version'] ?? null);
                            $output['formResult'] = 'success';
                            $output['formResultMessage'] = [
                                __('Success! You have installed latest version of Font Awesome.', 'rd-fontawesome'),
                            ];
                        }
                        unset($dlResult);
                    } else {
                        $output['skippedDownload'] = [
                            'result' => true,
                            'latestVersion' => $latestVersion,
                            'currentVersion' => ($allSettings['fontawesome_version'] ?? null),
                        ];
                        $output['downloadLink'] = $downloadLink;
                        $output['tagVersion'] = ($allSettings['fontawesome_version'] ?? null);
                        $output['formResult'] = 'success';
                        $output['formResultMessage'] = [
                            __('Success! Your Font Awesome is already latest version.', 'rd-fontawesome'),
                        ];
                    }
                }

                unset($allSettings, $downloadType, $downloadLink, $latestVersion, $Settings);
                wp_send_json($output, $statusCode);
            }// installLatestFAVersion


            /**
             * Retrieve latest Font Awesome version.
             */
            public function retrieveLatestFAVersion()
            {
                $output = [];
                check_ajax_referer('rdfontawesome_ajaxnonce', 'nonce');
                $downloadType = (strip_tags($_REQUEST['download_type']) ?? '');

                $output = array_merge($output, $this->Url->retrieveLatestVersion($downloadType));

                unset($downloadType);
                wp_send_json($output);
            }// retrieveLatestFAVersion


            /**
             * Save settings.
             */
            public function saveSettings()
            {
                $output = [];
                check_ajax_referer('rdfontawesome_ajaxnonce', 'nonce');

                $data = [];
                $data['download_type'] = (isset($_POST['download_type']) && !empty(trim($_POST['download_type'])) ? trim(strip_tags($_POST['download_type'])) : 'github');
                $data['dequeue_css'] = trim(strip_tags($_POST['dequeue_css']));
                $data['dequeue_js'] = trim(strip_tags($_POST['dequeue_js']));
                $data['donot_enqueue'] = (isset($_POST['donot_enqueue']) && $_POST['donot_enqueue'] === '1' ? '1' : '0');

                $Settings = new \RdFontAwesome\App\Libraries\Settings();
                $Settings->saveSettings($data);
                unset($data, $Settings);

                $output['formResult'] = 'success';
                $output['formResultMessage'] = [
                    __('Saved successfully.', 'rd-fontawesome'),
                ];

                wp_send_json($output);
            }// saveSettings


        }
}
