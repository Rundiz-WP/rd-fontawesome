<?php
/**
 * Loader class. This class will load anything for example: views, template, configuration file.
 * 
 * @package rd-fontawesome
 * @license http://opensource.org/licenses/MIT MIT
 * @since 1.0.0
 */


namespace RdFontAwesome\App\Libraries;


if (!class_exists('\\RdFontAwesome\\App\\Libraries\\Loader')) {
    /**
     * Loader class.
     */
    class Loader
    {


        /**
         * @var \RdFontAwesome\App\App
         */
        public $App;


        /**
         * Automatic `require_once` all files in App/functions folder.
         */
        public function autoLoadFunctions()
        {
            $this_plugin_dir = dirname(RDFONTAWESOME_FILE);
            $di = new \RecursiveDirectoryIterator($this_plugin_dir . DIRECTORY_SEPARATOR . 'App' . DIRECTORY_SEPARATOR . 'functions', \RecursiveDirectoryIterator::SKIP_DOTS);
            $it = new \RecursiveIteratorIterator($di);
            unset($di);

            foreach ($it as $file) {
                if (is_file($file)) {
                    require_once $file;
                }
            }// endforeach;

            unset($file, $it, $this_plugin_dir);
        }// autoloadFunctions


        /**
         * Automatic look into those controllers and register to the main App class to make it works.<br>
         * The controllers that will be register must implement `RdFontAwesome\App\Controllers\ControllerInterface` to have `registerHooks()` method in it, otherwise it will be skipped.
         * 
         * The controllers that will be register must extended `\RdFontAwesome\App\Controllers\BaseController`
         * that is implemented `\RdFontAwesome\App\Controllers\ControllerInterface` to have registerHooks() method in it, 
         * otherwise it will be skipped.
         */
        public function autoRegisterControllers()
        {
            $this_plugin_dir = dirname(RDFONTAWESOME_FILE);
            $file_list = $this->getClassFileList($this_plugin_dir . DIRECTORY_SEPARATOR . 'App' . DIRECTORY_SEPARATOR . 'Controllers');

            if (is_array($file_list)) {
                foreach ($file_list as $file) {
                    $this_file_classname = '\\RdFontAwesome' . str_replace([$this_plugin_dir, '.php', '/'], ['', '', '\\'], $file);
                    if (class_exists($this_file_classname)) {
                        $TestClass = new \ReflectionClass($this_file_classname);
                        if (
                            !$TestClass->isAbstract() && 
                            !$TestClass->isTrait() && 
                            $TestClass->implementsInterface('\\RdFontAwesome\\App\\Controllers\\ControllerInterface')
                        ) {
                            $ControllerClass = new $this_file_classname();
                            $ControllerClass->Loader = $this->App->Loader;
                            if (
                                $ControllerClass instanceof \RdFontAwesome\App\Controllers\BaseController && 
                                method_exists($ControllerClass, 'registerHooks')
                            ) {
                                $ControllerClass->registerHooks();
                            }
                            unset($ControllerClass);
                        }
                        unset($TestClass);
                    }
                    unset($this_file_classname);
                }// endforeach;
                unset($file);
            }

            unset($file_list, $this_plugin_dir);
        }// autoRegisterControllers


        /**
         * Get file list that may contain class in specific path.
         *
         * @param string $path The full path without trailing slash.
         * @return array Return indexed array of file list.
         */
        protected function getClassFileList(string $path): array
        {
            $Di = new \RecursiveDirectoryIterator($path, \RecursiveDirectoryIterator::SKIP_DOTS);
            $It = new \RecursiveIteratorIterator($Di);
            unset($Di);

            $file_list = [];
            foreach ($It as $file) {
                $file_list[] = $file;
            }// endforeach;
            unset($file, $It);
            natsort($file_list);

            return $file_list;
        }// getClassFileList


        /**
         * Load views.
         *
         * @param string $view_name View file name, refer from app/Views folder.
         * @param array $data For send data variable to view.
         * @param bool $require_once Set to `true` to use `include_once`, `false` to use `include`. Default is `false`.
         * @return bool Return `true` if success loading.
         * @throws \Exception Throws the error if views file was not found.
         */
        public function loadView(string $view_name, array $data = [], bool $require_once = false): bool
        {
            $view_dir = dirname(__DIR__) . '/Views/';
            $templateFile = $view_dir . $view_name . '.php';
            unset($view_dir);

            if ('' !== $view_name && file_exists($templateFile) && is_file($templateFile)) {
                // if views file was found.
                if (is_array($data)) {
                    extract($data, EXTR_PREFIX_SAME, 'dupvar_');// phpcs:ignore WordPress.PHP.DontExtract.extract_extract
                }

                if (true === $require_once) {
                    include_once $templateFile;
                } else {
                    include $templateFile;
                }

                unset($templateFile);
                return true;
            } else {
                // if views file was not found.
                // Throw the exception to notice the developers. Without translation.
                throw new \Exception(
                    esc_html(
                        sprintf(
                            'The views file was not found (%s).', 
                            str_replace(['\\', '/'], '/', $templateFile)
                        )
                    )
                );
            }
        }// loadView


    }// Loader
}
