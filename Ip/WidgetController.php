<?php
/**
 * @package ImpressPages
 *
 *
 */
namespace Ip;


use Ip\Module\Content\Exception;
use Ip\Module\Content\Model;

class WidgetController
{
    var $name;
    var $moduleName;

    /**
     * @var boolean - true if widget is installed by default
     */
    var $core;
    const LAYOUT_DIR = 'layout';

    //TODOX
    const MANAGEMENT_DIR = 'admin';

    //TODOX
    const SNIPPET_VIEW = 'snippet.php';

    private $widgetDir;
    private $widgetAssetsDir;

    public function __construct($name, $moduleName, $core = false)
    {
        $this->name = $name;
        $this->moduleName = $moduleName;
        $this->core = $core;

        $ds = '/';

        $this->widgetDir = $moduleName . $ds . Model::WIDGET_DIR . $ds . $this->name . $ds;
        $this->widgetAssetsDir = $moduleName . $ds . \ip\Application::ASSET_DIR . $ds . Model::WIDGET_DIR . $ds . $this->name . $ds;
    }

    public function getTitle()
    {
        return self::getName();
    }

    public function getName()
    {
        return $this->name;
    }

    //TODOX remove
    public function getModuleGroup()
    {
        return $this->moduleGroup;
    }

    public function getModuleName()
    {
        return $this->moduleName;
    }

    public function isCore()
    {
        return $this->core;
    }

    public function getIcon()
    {
        if ($this->core) {
            if (file_exists(ipConfig()->coreModuleFile($this->widgetAssetsDir . 'icon.png'))) {
                return ipConfig()->coreModuleUrl($this->widgetAssetsDir . 'icon.png');
            }
        } else {
            if (file_exists(ipConfig()->pluginFile($this->widgetAssetsDir . 'icon.png'))) {
                return ipConfig()->pluginUrl($this->widgetAssetsDir . 'icon.png');
            }

        }

        return ipConfig()->coreModuleUrl('Content/assets/img/icon_widget.png');
    }

    public function defaultData()
    {
        return array();
    }

    public function getLayouts()
    {

        $views = array();


        //collect default view files
        if ($this->core) {
            $layoutsDir = ipConfig()->coreModuleFile($this->widgetDir . self::LAYOUT_DIR . '/');
        } else {
            $layoutsDir = ipConfig()->pluginFile($this->widgetDir . self::LAYOUT_DIR . '/');
        }


        if (!is_dir($layoutsDir)) {
            throw new Exception('Layouts directory does not exist. ' . $layoutsDir, Exception::NO_LAYOUTS);
        }

        $availableViewFiles = scandir($layoutsDir);
        foreach ($availableViewFiles as $viewFile) {
            if (is_file($layoutsDir . $viewFile) && substr($viewFile, -4) == '.php') {
                $views[substr($viewFile, 0, -4)] = 1;
            }
        }
        //collect overridden theme view files
        $themeViewsFolder = ipConfig()->themeFile(\Ip\View::OVERRIDE_DIR . '/' . $this->moduleName . '/' . Model::WIDGET_DIR . '/' . $this->name . '/' . self::LAYOUT_DIR);
        if (is_dir($themeViewsFolder)){
            $availableViewFiles = scandir($themeViewsFolder);
            foreach ($availableViewFiles as $viewFile) {
                if (is_file($themeViewsFolder . '/' . $viewFile) && substr($viewFile, -4) == '.php') {
                    $views[substr($viewFile, 0, -4)] = 1;
                }
            }
        }

        $layouts = array();
        foreach ($views as $viewKey => $view) {
            $translation = __('Layout_' . $viewKey, $this->getModuleName(), false);
            $layouts[] = array('name' => $viewKey, 'title' => $translation);
        }

        if (empty($layouts)) {
            throw new Exception('No layouts', Exception::NO_LAYOUTS);
        }

        return $layouts;
    }

    /**
     * Return true if you like to hide widget in administration panel.
     * You will be able to access widget in your code.
     */
    public function getUnderTheHood()
    {
        return false; //by default all widgets are visible; 
    }

    /**
     *
     *
     * @param $widgetId
     * @param $postData
     * @param $currentData
     * @return array data to be stored to the database
     */
    public function update ($widgetId, $postData, $currentData)
    {
        return $postData;
    }

    /**
     * 
     * You can make posts directly to your widget. If you will pass following parameters:
     * sa=Content.widgetPost
     * securityToken=actualSecurityToken
     * instanceId=actualWidgetInstanceId
     * 
     * then that post request will be redirected to this method.
     * 
     * Use return new \Ip\Response\Json($jsonArray) to return json.
     *
     * Be careful. This method is accessible for website visitor without admin login.
     *
     * @param int $instanceId
     * @param array $data widget data
     */
    public function post ($instanceId, $data)
    {

    }

    /**
     * 
     * Duplicate widget action. This function is executed after the widget is being duplicated.
     * All widget data is duplicated automatically. This method is used only in case a widget
     * needs to do some maintenance tasks on duplication.
     * @param int $oldId old widget id
     * @param int $newId duplicated widget id
     * @param array $data data that has been duplicated from old widget to the new one
     */
    public function duplicate($oldId, $newId, $data)
    {

    }

    /**
     * 
     * Delete widget. This method is executed before actual deletion of widget.
     * It is used to remove widget data (photos, files, additional database records and so on).
     * Standard widget data is being deleted automatically. So you don't need to extend this method
     * if your widget does not upload files or add new records to the database manually.
     * @param int $widgetId
     * @param array $data data that is being stored in the widget
     */
    public function delete($widgetId, $data)
    {

    }


    public function adminSnippets()
    {

        //TODOX scandir Model::SNIPPET_DIR and return snippets as an array
//        $answer = '';
//        try {
//            if ($this->core ) {
//                $adminView = ipConfig()->coreModuleFile($this->moduleName . '/' . Model::WIDGET_DIR . '/' . $this->name . '/' . self::SNIPPET_VIEW);
//            } else {
//                $adminView = ipConfig()->pluginFile($this->moduleName . '/' . Model::WIDGET_DIR . '/' . $this->name . '/' . self::SNIPPET_VIEW);
//            }
//            if (is_file($adminView)) {
//                $answer = \Ip\View::create($adminView)->render();
//            }
//        } catch (\Ip\CoreException $e){
//            return $e->getMessage();
//        }
//        return $answer;
        return array();
    }

    //TODOX remove
    public function managementHtml($instanceId, $data, $layout)
    {
        $answer = '';
        try {
            if ($this->core ) {
                $adminView = ipConfig()->coreModuleFile($this->moduleName . '/' . Model::WIDGET_DIR . '/' . $this->name . '/' . self::MANAGEMENT_DIR.'/default.php');
            } else {
                $adminView = ipConfig()->pluginFile($this->moduleName . '/' . Model::WIDGET_DIR . '/' . $this->name . '/' . self::MANAGEMENT_DIR.'/default.php');
            }
            if (is_file($adminView)) {
                $answer = \Ip\View::create($adminView, $data)->render();
            }
        } catch (\Ip\CoreException $e){
            return $e->getMessage();
            //do nothing. Administration view does not exist
        }
        return $answer;
    }

    //TODOX rename to generateHtml or something.
    public function previewHtml($instanceId, $data, $layout)
    {
        $answer = '';
        try {
            if ($this->core) {
                $answer = \Ip\View::create(ipConfig()->coreModuleFile($this->moduleName . '/' . Model::WIDGET_DIR . '/' . $this->name . '/' . self::LAYOUT_DIR.'/'.$layout.'.php'), $data)->render();
            } else {
                $answer = \Ip\View::create(ipConfig()->pluginFile($this->moduleName . '/' . Model::WIDGET_DIR . '/' . $this->name . '/' . self::LAYOUT_DIR.'/'.$layout.'.php'), $data)->render();
            }
        } catch (\Ip\CoreException $e) {
            if (\Ip\ServiceLocator::content()->isManagementState()) {
                $tmpData = array(
                    'widgetName' => $this->name,
                    'layout' => $layout
                );
                $answer = \Ip\View::create(ipConfig()->coreModuleFile('Content/view/unknown_widget_layout.php'), $tmpData)->render();
            } else {
                $answer = '';
            }
        }
        return $answer;
    }

    public function dataForJs($data)
    {
        return $data;
    }

    /**
     * This method is called when widget options has been changed.
     * Do any maintenance job needed.
     * Eg. if widget has cropped images, they need to be cropped once again, because cropping options
     * might be changed.
     */
    public function recreate($widgetId, $data)
    {
        return $data;
    }
}
