<?php
/**
 * @package rd-fontawesome
 * @license http://opensource.org/licenses/MIT MIT
 * @since 1.0.0
 */


namespace RdFontAwesome\App;


if (!class_exists('\\RdFontAwesome\App\\App')) {
    /**
     * Main app class.
     */
    class App
    {


        use AppTrait;


        /**
         * Run the application.
         */
        public function run()
        {
            // Initialize the loader class.
            $this->Loader = new \RdFontAwesome\App\Libraries\Loader();
            $this->Loader->App = $this;
            $this->Loader->autoLoadFunctions();
            $this->Loader->autoRegisterControllers();
        }// run


    }// App
}
