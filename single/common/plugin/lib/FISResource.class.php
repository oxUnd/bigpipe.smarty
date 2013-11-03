<?php
/**
 * Class FISResource
 * 静态资源的收集，包括查询map表，页面html收集等
 */
class FISResource {

    private static $arrMap = array();
    private static $arrLoaded = array();
    /**
     * array(
     *     js: array(), css: array(), script: array(), async: array()
     * )
     * @var array
     */
    private static $arrStaticCollection = array();
    //收集require.async组件
    private static $arrRequireAsyncCollection = array();
    private static $arrWidgetStatic = array();
    private static $arrWidgetRequireAsync = array();

    private static $arrScriptPool = array();
    private static $arrStylePool = array();

    private static $arrWidgetScript = array();
    private static $arrWidgetStyle = array();

    //标识是否在分析一个widget
    private static $isInnerWidget = false;

    public static $framework = null;

    public static function reset() {
        self::$arrStaticCollection = array();
        self::$arrRequireAsyncCollection = array();
        self::$arrLoaded = array();
        self::$arrScriptPool = array();
        self::$arrStylePool = array();
    }

    public static function widgetStart() {
        self::$isInnerWidget = true;

        self::$arrWidgetStatic = array();
        self::$arrWidgetRequireAsync = array();
        self::$arrWidgetStatic = array();
        self::$arrWidgetStyle = array();
    }

    public static function widgetEnd() {

        self::$isInnerWidget = false;
        $ret = array();
        //{{{ 还原
        if (self::$arrWidgetRequireAsync) {
            foreach (self::$arrWidgetRequireAsync as $key => $val) {
                foreach ($val as $id => $info) {
                    unset(self::$arrLoaded[$id]);
                }
            }
            $ret['async'] = self::getResourceMap(self::$arrWidgetRequireAsync);
        }

        foreach (self::$arrWidgetStatic as $key => $val) {
            foreach ($val as $uri) {
                foreach (array_keys(self::$arrLoaded, $uri) as $id) {
                    unset(self::$arrLoaded[$id]);
                }
            }
        }
        //}}}
        if (self::$arrWidgetStatic['js']) {
            $ret['js'] = self::$arrWidgetStatic['js'];
        }
        if (self::$arrWidgetStatic['css']) {
            $ret['css'] = self::$arrWidgetStatic['css'];
        }
        if (self::$arrWidgetScript) {
            $ret['script'] = self::$arrWidgetScript;
        }
        if (self::$arrWidgetStyle) {
            $ret['style'] = self::$arrWidgetStyle;
        }
        return $ret;
    }

    public static function addStatic($uri, $type) {
        if (self::$isInnerWidget && !in_array($uri, self::$arrStaticCollection[$type])) {
            self::$arrWidgetStatic[$type][] = $uri;
        } else {
            self::$arrStaticCollection[$type][] = $uri;
        }
    }

    public static function addAsync($id, $info, $type) {
        if (self::$isInnerWidget) {
            self::$arrWidgetRequireAsync[$type][$id] = $info;
        } else {
            self::$arrRequireAsyncCollection[$type][$id] = $info;
        }
    }

    public static function delAsync($id, $type) {
        if (self::$isInnerWidget) {
            unset(self::$arrWidgetRequireAsync[$type][$id]);
        } else {
            unset(self::$arrRequireAsyncCollection[$type][$id]);
        }
    }

    public static function getAsync($id, $type) {
        if (self::$isInnerWidget) {
            return self::$arrWidgetRequireAsync[$type][$id];
        }
        return self::$arrRequireAsyncCollection[$type][$id];
    }

    //设置framewok mod.js
    public static function setFramework($strFramework) {
        self::$framework = $strFramework;
    }

    public static function getFramework() {
        return self::$framework;
    }

    public static function addScriptPool($code) {
        if (!self::$isInnerWidget) {
            self::$arrScriptPool[] = $code;
        } else {
            self::$arrWidgetScript[] = $code;
        }
    }

    public static function addStylePool($code) {
        if (!self::$isInnerWidget) {
            self::$arrStylePool[] = $code;
        } else {
            self::$arrWidgetStyle[] = $code;
        }
    }

    public static function getArrStaticCollection() {
        //内嵌脚本
        if (self::$arrScriptPool) {
            self::$arrStaticCollection['script'] = self::$arrScriptPool;
        }

        if (self::$arrStylePool) {
            self::$arrStaticCollection['style'] = self::$arrStylePool;
        }

        //异步脚本
        if (self::$arrRequireAsyncCollection) {
            self::$arrStaticCollection['async'] = self::getResourceMap(self::$arrRequireAsyncCollection);
        }
        unset(self::$arrStaticCollection['tpl']);
        return self::$arrStaticCollection;
    }

    //获取异步js资源集合，变为json格式的resourcemap
    public static function getResourceMap($arr) {
        $ret = '';
        $arrResourceMap = array();
        if (isset($arr['res'])) {
            foreach ($arr['res'] as $id => $arrRes) {
                $deps = array();
                if (!empty($arrRes['deps'])) {
                    foreach ($arrRes['deps'] as $strName) {
                        if (preg_match('/\.js$/i', $strName)) {
                            $deps[] = $strName;
                        }
                    }
                }

                $arrResourceMap['res'][$id] = array(
                    'url' => $arrRes['uri'],
                );

                if (!empty($arrRes['pkg'])) {
                    $arrResourceMap['res'][$id]['pkg'] = $arrRes['pkg'];
                }

                if (!empty($deps)) {
                    $arrResourceMap['res'][$id]['deps'] = $deps;
                }
            }
        }
        if (isset($arr['pkg'])) {
            foreach ($arr['pkg'] as $id => $arrRes) {
                $arrResourceMap['pkg'][$id] = array(
                    'url'=> $arrRes['uri']
                );
            }
        }
        if (!empty($arrResourceMap)) {
            $ret = $arrResourceMap;
        }
        return  $ret;
    }

    //获取命名空间的map.json
    public static function register($strNamespace, $smarty){
        if($strNamespace === '__global__'){
            $strMapName = 'map.json';
        } else {
            $strMapName = $strNamespace . '-map.json';
        }
        $arrConfigDir = $smarty->getConfigDir();
        foreach ($arrConfigDir as $strDir) {
            $strPath = preg_replace('/[\\/\\\\]+/', '/', $strDir . '/' . $strMapName);
            if(is_file($strPath)){
                self::$arrMap[$strNamespace] = json_decode(file_get_contents($strPath), true);
                return true;
            }
        }
        return false;
    }

    public static function getUri($strName, $smarty) {
        $intPos = strpos($strName, ':');
        if($intPos === false){
            $strNamespace = '__global__';
        } else {
            $strNamespace = substr($strName, 0, $intPos);
        }
        if(isset(self::$arrMap[$strNamespace]) || self::register($strNamespace, $smarty)) {
            $arrMap = &self::$arrMap[$strNamespace];
            $arrRes = &$arrMap['res'][$strName];
            if (isset($arrRes)) {
                return $arrRes['uri'];
            }
        }
    }

    /**
     * 分析组件依赖
     * @param array $arrRes  组件信息
     * @param Object $smarty  smarty对象
     * @param bool $async   是否异步
     */
    private static function loadDeps($arrRes, $smarty, $async) {
        //require.async
        if (isset($arrRes['extras']) && isset($arrRes['extras']['async'])) {
            foreach ($arrRes['extras']['async'] as $uri) {
                self::load($uri, $smarty, true);
            }
        }
        if(isset($arrRes['deps'])){
            foreach ($arrRes['deps'] as $strDep) {
                self::load($strDep, $smarty, $async);
            }
        }
    }

    /**
     * 已经分析到的组件在后续被同步使用时在异步组里删除。
     * @param $strName
     */
    private static function delAsyncDeps($strName) {
        $arrRes = self::getAsync($strName, 'res');
        if ($arrRes['pkg']) {
            $arrPkg = self::getAsync($arrRes['pkg'], 'pkg');
            if ($arrPkg) {
                self::addStatic($arrPkg['uri'], 'js');
                self::delAsync($arrRes['pkg'], 'pkg');
                foreach ($arrPkg['has'] as $strHas) {
                    if (self::getAsync($strHas, 'res')) {
                        self::delAsyncDeps($strHas);
                    }
                }
            } else {
                self::delAsync($strName, 'res');
            }
        } else {
            //已经分析过的并且在其他文件里同步加载的组件，重新收集在同步输出组
            $res = self::getAsync($strName, 'res');
            self::addStatic($res['uri'], 'js');
            self::delAsync($strName, 'res');
        }
        if ($arrRes['deps']) {
            foreach ($arrRes['deps'] as $strDep) {
                //if (isset(self::$arrRequireAsyncCollection['res'][$strDep])) {
                if (self::getAsync($strDep, 'res')) {
                    self::delAsyncDeps($strDep);
                }
            }
        }
    }

    /**
     * 加载组件以及组件依赖
     * @param $strName      id
     * @param $smarty       smarty对象
     * @param bool $async   是否为异步组件（only JS）
     * @return mixed
     */
    public static function load($strName, $smarty, $async = false){
        if(isset(self::$arrLoaded[$strName])) {
            //同步组件优先级比异步组件高
            if (!$async && self::getAsync($strName, 'res')) {
                self::delAsyncDeps($strName);
            }
            return self::$arrLoaded[$strName];
        } else {
            $intPos = strpos($strName, ':');
            if($intPos === false){
                $strNamespace = '__global__';
            } else {
                $strNamespace = substr($strName, 0, $intPos);
            }
            if(isset(self::$arrMap[$strNamespace]) || self::register($strNamespace, $smarty)){
                $arrMap = &self::$arrMap[$strNamespace];
                $arrRes = &$arrMap['res'][$strName];
                $arrPkg = null;
                $arrPkgHas = array();
                if(isset($arrRes)) {
                    if(!array_key_exists('fis_debug', $_GET) && isset($arrRes['pkg'])){
                        $arrPkg = &$arrMap['pkg'][$arrRes['pkg']];
                        $strURI = $arrPkg['uri'];
                        foreach ($arrPkg['has'] as $strResId) {
                            self::$arrLoaded[$strResId] = $strURI;
                        }
                        foreach ($arrPkg['has'] as $strResId) {
                            $arrHasRes = &$arrMap['res'][$strResId];
                            if ($arrHasRes) {
                                $arrPkgHas[$strResId] = $arrHasRes;
                                self::loadDeps($arrHasRes, $smarty, $async);
                            }
                        }
                    } else {
                        $strURI = $arrRes['uri'];
                        self::$arrLoaded[$strName] = $strURI;
                        self::loadDeps($arrRes, $smarty, $async);
                    }

                    if ($async && $arrRes['type'] === 'js') {
                        if ($arrPkg) {
                            self::addAsync($arrRes['pkg'], $arrPkg, 'pkg');
                            foreach ($arrPkgHas as $id => $val) {
                                self::addAsync($id, $val, 'res');
                            }
                        } else {
                            self::addAsync($strName, $arrRes, 'res');
                        }
                    } else {
                        self::addStatic($strURI, $arrRes['type']);
                    }
                    return $strURI;
                } else {
                    self::triggerError($strName, 'undefined resource "' . $strName . '"', E_USER_NOTICE);
                }
            } else {
                self::triggerError($strName, 'missing map file of "' . $strNamespace . '"', E_USER_NOTICE);
            }
        }
        self::triggerError($strName, 'unknown resource "' . $strName . '" load error', E_USER_NOTICE);
    }

    /**
     * 用户代码自定义js组件，其没有对应的文件
     * 只有有后缀的组件找不到时进行报错
     * @param $strName       组件ID
     * @param $strMessage    错误信息
     * @param $errorLevel    错误level
     */
    private static function triggerError($strName, $strMessage, $errorLevel) {
        $arrExt = array(
            'js',
            'css',
            'tpl',
            'html',
            'xhtml',
        );
        if (preg_match('/\.('.implode('|', $arrExt).')$/', $strName)) {
            trigger_error(date('Y-m-d H:i:s') . '   ' . $strName . ' ' . $strMessage, $errorLevel);
        }
    }

}
