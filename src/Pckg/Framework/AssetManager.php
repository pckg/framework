<?php namespace Pckg\Framework;

use Assetic\Asset\AssetCollection;
use Assetic\Asset\FileAsset;
use Pckg\Framework\Helper\Optimize;

class AssetManager
{

    protected $collections;

    protected $types = [
        "css" => '<link rel="stylesheet" type="text/css" href="##LINK##" />',
        "js"  => '<script type="text/javascript" src="##LINK##"></script>',

    ];

    public function __construct()
    {
        /**
         * Libraries, header, footer, for each type ...
         */
        $this->touchCollection('js')->setTargetPath(path('www') . 'cache/js/js.js');
        $this->touchCollection('css')->setTargetPath(path('www') . 'cache/css/css.css');
    }

    public function touchCollection($key, $assets = [], $filters = [])
    {
        if (!isset($this->collections[$key])) {
            $this->collections[$key] = new AssetCollection($assets, $filters, path('cache'));
        }

        return $this->collections[$key];
    }

    public function execute()
    {
        // dependencies
        $this->jQuery();
        $this->jQueryDatetimePicker();
        $this->angularJS();
        $this->vueJS();

        // front framework
        $this->bootstrap();

        // needed
        $this->app();
        $this->maestro();
        $this->theme();

        // decoration
        $this->fontAwesome();
        $this->redactor();
        $this->chosen();
        $this->datatables();
    }

    public function executeCore()
    {
        // dependencies
        $this->jQuery();
        $this->jQueryDatetimePicker();
        $this->angularJS();

        // front framework
        $this->bootstrap();

        // decoration
        $this->fontAwesome();
        $this->chosen();
    }

    public function addAssets($assets)
    {
        if (!is_array($assets)) {
            $assets = [$assets];
        }

        foreach ($assets as $asset) {
            if (mb_strrpos($asset, '.js') == strlen($asset) - strlen('.js')) {
                $this->collections['js']->add(new FileAsset(path('root') . $asset));

            } else if (mb_strrpos($asset, '.css') == strlen($asset) - strlen('.css')) {
                $this->collections['css']->add(new FileAsset(path('root') . $asset));

            } else {
                throw new \Exception('Cannot find asset ' . $asset);

            }
        }
    }

    public function maestro()
    {
        $this->addAssets([
            "js/maestro.js", // should it be on the end?
            "css/maestro.css", // should it be on the end?
        ]);
    }

    public function theme()
    {
        $this->addAssets([
            // sb-admin-2.js
            "js/sb-admin-2.js",
            "js/plugins/metisMenu/metisMenu.min.js",
            "js/jquery-bootstrap-validation/v1.3.6/min.js",
            // sb-admin2
            "css/sb-admin-2.css",
            "css/plugins/dataTables.bootstrap.css",
            "css/plugins/metisMenu/metisMenu.min.css",
            "css/jquery-file-upload/v8.8.5/jquery.fileupload-ui.css",
        ]);

    }

    public function fontAwesome()
    {
        $this->addAssets([
            "vendor/fortawesome/font-awesome/css/font-awesome.min.css",
        ]);

        return $this;
    }

    public function redactor()
    {
        $this->addAssets([
            "app/door/src/Door/Layout/public/assets/css/style-blue-redactor.css",
        ]);

        $this->addAssets([
            "js/redactor/redactor.js",
            "js/redactor/redactor.conf.js",
            "js/redactor/redactor.css",
        ]);

        return $this;
    }

    public function chosen()
    {
        $this->addAssets([
            "vendor/lfw/admin/src/Weblab/Admin/public/js/chosen/v1.0.0/dev.js",
            "vendor/lfw/admin/src/Weblab/Admin/public/js/chosen/v1.0.0/conf.js",
            "vendor/lfw/admin/src/Weblab/Admin/public/css/chosen/v1.0.0/dev.css",
        ]);

        return $this;
    }

    public function datatables()
    {
        $this->addAssets([
            "js/datatables/v1.9.4/dev.js",
            "js/datatables/v1.9.4/conf.js",
            "css/datatables/v1.9.4/conf.css",
        ]);

        return $this;
    }

    public function jQueryDatetimePicker()
    {
        $this->addAssets([
            "vendor/lfw/admin/src/Weblab/Admin/public/js/bootstrap-datetimepicker/v1/min.js",
            "vendor/lfw/admin/src/Weblab/Admin/public/css/bootstrap-datetimepicker/v1/dev.css",
        ]);

        return $this;
    }

    public function jQuery()
    {
        $this->addAssets([
            "components/jquery/jquery.min.js",
        ]);

        return $this;
    }

    public function bootstrap()
    {
        $this->addAssets([
            "vendor/twbs/bootstrap/dist/js/bootstrap.min.js",
            "vendor/bootstrap-select/bootstrap-select/js/bootstrap-select.js",
            "vendor/twbs/bootstrap/dist/css/bootstrap.min.css",
            "vendor/twbs/bootstrap/dist/css/bootstrap-theme.min.css",
            "vendor/bootstrap-select/bootstrap-select/dist/css/bootstrap-select.css",
        ]);

        return $this;
    }

    public function foundation()
    {
        $this->addAssets([
            "bower_components/modernizr/nodernizr.js",
            "bower_components/fastclick/lib/fastclick.js",
            "bower_components/foundation/js/foundation.min.js",
            "bower_components/foundation/css/foundation.min.css",
        ]);
    }

    public function angularJS()
    {
        /*$this->addAssets([
            "https://ajax.googleapis.com/ajax/libs/angularjs/1.4.0-beta.5/angular.min.js",
        ]);*/

        return $this;
    }

    public function vueJS()
    {
        $this->addAssets([
            "vendor/yyx990803/vue/dist/vue.js",
            "vendor/vuejs/vue-resource/dist/vue-resource.js",
        ]);

        return $this;
    }

    public function app()
    {
        $this->addAssets(['js/app.js']);

        return $this;

    }

    public function __toString()
    {
        $return = [];

        foreach ($this->collections as $key => $collection) {
            $lastModified = $collection->getLastModified();
            $targetPath = $collection->getTargetPath();
            $cachePath = $this->str_lreplace('.', '-' . $lastModified . '.', $targetPath);

            if (!is_file($cachePath)) {
                $collection->setTargetPath($cachePath);
                file_put_contents($cachePath, $collection->dump());
            }

            $return[] = str_replace('##LINK##', str_replace(path('www'), '/', $cachePath), $this->types[$key]);
        }

        return implode("\n", $return);
    }

    public function str_lreplace($search, $replace, $subject)
    {
        $pos = strrpos($subject, $search);

        if ($pos !== false) {
            $subject = substr_replace($subject, $replace, $pos, strlen($search));
        }

        return $subject;
    }

}