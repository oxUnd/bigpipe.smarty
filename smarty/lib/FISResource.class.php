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
    private static $arrScriptPool = array();

    public static $framework = null;

    public static function reset() {
        self::$arrStaticCollection = array();
        self::$arrRequireAsyncCollection = array();
        self::$arrLoaded = array();
        self::$arrScriptPool = array();
    }

    //设置framewok mod.js
    public static function setFramework($strFramework) {
        self::$framework = $strFramework;
    }

    public static function getFramework() {
        return self::$framework;
    }

    public static function addScriptPool($code) {
        self::$arrScriptPool[] = $code;
    }
    public static function getArrStaticCollection() {
        //内嵌脚本
        if (self::$arrScriptPool) {
            self::$arrStaticCollection['script'] = self::$arrScriptPool;
        }
        //异步脚本
        if (self::$arrRequireAsyncCollection) {
            self::$arrStaticCollection['async'] = self::getResourceMap();
        }
        unset(self::$arrStaticCollection['tpl']);
        return self::$arrStaticCollection;
    }

    //获取异步js资源集合，变为json格式的resourcemap
    public static function getResourceMap() {
        $ret = '';
        $arrResourceMap = array();
        if (isset(self::$arrRequireAsyncCollection['res'])) {
            foreach (self::$arrRequireAsyncCollection['res'] as $id => $arrRes) {
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
        if (isset(self::$arrRequireAsyncCollection['pkg'])) {
            foreach (self::$arrRequireAsyncCollection['pkg'] as $id => $arrRes) {
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
        $arrRes = self::$arrRequireAsyncCollection['res'][$strName];
        if ($arrRes['pkg']) {
            $arrPkg = &self::$arrRequireAsyncCollection['pkg'][$arrRes['pkg']];
            if ($arrPkg) {
                self::$arrStaticCollection['js'][] = $arrPkg['uri'];
                unset(self::$arrRequireAsyncCollection['pkg'][$arrRes['pkg']]);
                foreach ($arrPkg['has'] as $strHas) {
                    if (isset(self::$arrRequireAsyncCollection['res'][$strHas])) {
                        self::delAsyncDeps($strHas);
                    }
                }
            }
        } else {
            //已经分析过的并且在其他文件里同步加载的组件，重新收集在同步输出组
            self::$arrStaticCollection['js'][] = self::$arrRequireAsyncCollection['res'][$strName]['uri'];
            unset(self::$arrRequireAsyncCollection['res'][$strName]);
        }
        if ($arrRes['deps']) {
            foreach ($arrRes['deps'] as $strDep) {
                if (isset(self::$arrRequireAsyncCollection['res'][$strDep])) {
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
            if (!$async && isset(self::$arrRequireAsyncCollection['res'][$strName])) {
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
                            self::$arrRequireAsyncCollection['pkg'][$arrRes['pkg']] = $arrPkg;
                            self::$arrRequireAsyncCollection['res'] = array_merge((array)self::$arrRequireAsyncCollection['res'], $arrPkgHas);
                        } else {
                            self::$arrRequireAsyncCollection['res'][$strName] = $arrRes;
                        }
                    } else {
                        self::$arrStaticCollection[$arrRes['type']][] = $strURI;
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