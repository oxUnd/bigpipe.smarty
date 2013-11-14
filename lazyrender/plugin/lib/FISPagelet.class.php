<?php
if (!class_exists('FISResource')) require_once(dirname(__FILE__) . '/FISResource.class.php');
/**
 * Class FISPagelet
 * DISC:
 * 构造pagelet的html以及所需要的静态资源json
 */
class FISPagelet {

    const CSS_LINKS_HOOK = '<!--[FIS_CSS_LINKS_HOOK]-->';
    const JS_SCRIPT_HOOK = '<!--[FIS_JS_SCRIPT_HOOK]-->';

    const MODE_NOSCRIPT = 0;
    const MODE_QUICKLING = 1;
    const MODE_BIGPIPE = 2;

    /**
     * 收集widget内部使用的静态资源
     * array(
     *  0: array(), 1: array(), 2: array()
     * )
     * @var array
     */
    static protected $inner_widget = array(
        array(),
        array(),
        array()
    );
    /**
     * array(
     *     js: array(), css: array(), script: array(), async: array()
     * )
     * @var array
     */
    static private $_collection = array();
    static private $_session_id = 0;
    static private $_context = array();
    static private $_contextMap = array();
    static private $_pagelets = array();
    static private $_title = '';
    static private $_pagelet_group = array();
    /**
     * 解析模式
     * @var number
     */
    static protected $mode = null;

    static protected $force_mode = null;

    static protected $default_mode = null;

    /**
     * 某一个widget使用那种模式渲染
     * @var number
     */
    static protected  $widget_mode;

    static protected  $filter;

    static public $cp;
    static public $arrEmbeded = array();

    static public function init() {
        self::$default_mode = self::MODE_NOSCRIPT;
        if ($_GET['force_mode']) {
            self::$force_mode = $_GET['force_mode'];
            self::setMode(self::$default_mode);
        } else {
            $is_ajax = isset($_SERVER['HTTP_X_REQUESTED_WITH'])
                && (strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest');
            if ($is_ajax) {
                self::setMode(self::MODE_QUICKLING);
            } else {
                self::setMode(self::$default_mode);
            }
        }
        self::setFilter($_GET['pagelets']);
    }

    static public function setMode($mode){
        if (self::$mode === null) {
            self::$mode = isset($mode) ? intval($mode) : 1;
        }
    }

    static public function setFilter($ids) {
        if (!is_array($ids)) {
            $ids = array($ids);
        }
        foreach ($ids as $id) {
            self::$filter[$id] = true;
        }
    }

    static public function getUri($strName, $smarty) {
        return FISResource::getUri($strName, $smarty);
    }

    static public function addScript($code) {
        if(self::$_context['hit'] || self::$mode == self::$default_mode){
            FISResource::addScriptPool($code);
        }
    }

    static public function addStyle($code) {
        if(self::$_context['hit'] || self::$mode == self::$default_mode){
            FISResource::addStylePool($code);
        }
    }

    public static function cssHook() {
        return self::CSS_LINKS_HOOK;
    }

    public static function jsHook() {
        return self::JS_SCRIPT_HOOK;
    }

    static function load($str_name, $smarty, $async = false) {
        if(self::$_context['hit'] || self::$mode == self::MODE_NOSCRIPT){
            FISResource::load($str_name, $smarty, $async);
        }
    }

    static private function _parseMode($str_mode) {
        $str_mode = strtoupper($str_mode);
        $mode = self::$mode;
        switch($str_mode) {
            case 'BIGPIPE':
                $mode = self::MODE_BIGPIPE;
                break;
            case 'QUICKLING':
                $mode = self::MODE_QUICKLING;
                break;
            case 'NOSCRIPT':
                $mode = self::MODE_NOSCRIPT;
                break;
        }
        return $mode;
    }
    /**
     * WIDGET START
     * 解析参数，收集widget所用到的静态资源
     * @param $id
     * @param $mode
     * @return bool
     */
    static public function start($id, $mode = null, $group = null) {
        $has_parent = !empty(self::$_context);
        $special_flag = false;
        if ($mode !== null) {
            $special_flag = true;
        }

        if ($has_parent) {
            //keep
            self::$widget_mode = self::$widget_mode;
        } else {
            if ($mode) {
                self::$widget_mode = self::_parseMode($mode);
            } else {
                self::$widget_mode = self::$mode;
            }
        }

        $parent_id = $has_parent ? self::$_context['id'] : '';
        $id = empty($id) ? '__elm_' . $parent_id . '_' . self::$_session_id ++ : $id;

        //widget是否命中，默认命中
        $hit = true;
        switch(self::$widget_mode) {
            case self::MODE_NOSCRIPT:
                $context = array( 'id' => $id );
                $parent = self::$_context;
                if(!empty($parent)){
                    self::$_contextMap[$parent_id] = $parent;
                    $context['parent_id'] = $parent['id'];
                }
                self::$_context = $context;
                break;
            case self::MODE_QUICKLING:
                $hit = self::$filter[$id];
                if (!$group) {
                    //widget调用时mode='quickling'，so，打出异步加载代码
                    if ($special_flag && !$has_parent) {
                        echo '<textarea class="g_fis_bigrender" style="visibility: hidden;">'
                            .'BigPipe.asyncLoad({id: "'.$id.'"});'
                            .'</textarea>';
                    }
                } else {
                    if (isset(self::$_pagelet_group[$group])) {
                        self::$_pagelet_group[$group][] = $id;
                    } else {
                        self::$_pagelet_group[$group] = array($id);
                        echo "<!--" . $group . "-->";
                    }
                }
            case self::MODE_BIGPIPE:
                $context = array( 'id' => $id );
                $parent = self::$_context;
                if(!empty($parent)){
                    $parent_id = $parent['id'];
                    self::$_contextMap[$parent_id] = $parent;
                    $context['parent_id'] = $parent_id;
                    if($parent['hit']) {
                        $hit = true;
                    } else if($hit && self::$mode === self::MODE_QUICKLING){
                        unset($context['parent_id']);
                    }
                }
                $context['hit'] = $hit;
                self::$_context = $context;

                if (empty($parent) && $hit) {
                    FISResource::widgetStart();
                } else if (!empty($parent) && !$parent['hit'] && $hit) {
                    FISResource::widgetStart();
                }

                echo '<div id="' . $id . '">';
                ob_start();
                break;
        }
        return $hit;
    }

    /**
     * WIDGET END
     * 收集html，收集静态资源
     */
    static public function end() {
        $ret = true;
        $html = '';
        if (self::$widget_mode !== self::MODE_NOSCRIPT) {
            $html = ob_get_clean();
            $pagelet = self::$_context;
            //end
            if (isset($pagelet['parent_id'])) {
                $parent = self::$_contextMap[$pagelet['parent_id']];
                if (!$parent['hit'] && $pagelet['hit']) {
                    self::$inner_widget[self::$widget_mode][] = FISResource::widgetEnd();
                }
            } else {
                if ($pagelet['hit']) {
                    self::$inner_widget[self::$widget_mode][] = FISResource::widgetEnd();
                }
            }

            if($pagelet['hit']){
                if (self::$force_mode) {
                    if (self::$force_mode == self::$widget_mode) {
                        unset($pagelet['hit']);
                        $pagelet['html'] = $html;
                        self::$_pagelets[] = &$pagelet;
                        unset($pagelet);
                    }
                } else {
                    unset($pagelet['hit']);
                    $pagelet['html'] = $html;
                    self::$_pagelets[] = &$pagelet;
                    unset($pagelet);
                }

            } else {
                $ret = false;
            }
            $parent_id = self::$_context['parent_id'];
            if(isset($parent_id)){
                self::$_context = self::$_contextMap[$parent_id];
                unset(self::$_contextMap[$parent_id]);
            } else {
                self::$_context = null;
            }
            //收集
            echo '</div>';
            //reset
        } else {
            //删除上下文
            if (isset(self::$_context['parent_id'])) {
                self::$_context = self::$_contextMap[self::$_context['parent_id']];
                unset(self::$_contextMap[self::$_context['parent_id']]);
            } else {
                self::$_context = null;
            }
        }
        return $ret;
    }

    /**
     * 渲染静态资源
     * @param $html
     * @param $arr
     * @param bool $clean_hook
     * @return mixed
     */
    static public function renderStatic($html, $arr, $clean_hook = false) {
        if (!empty($arr)) {
            $code = '';
            $resource_map = $arr['async'];
            $loadModJs = (FISResource::getFramework() && ($arr['js'] || $resource_map));
            if ($loadModJs) {
                foreach ($arr['js'] as $js) {
                    $code .= '<script type="text/javascript" src="' . $js . '"></script>';
                    if ($js == FISResource::getFramework()) {
                        if ($resource_map) {
                            $code .= '<script type="text/javascript">';
                            $code .= 'require.resourceMap('.json_encode($resource_map).');';
                            $code .= '</script>';
                        }
                    }
                }
            }

            if (!empty($arr['script'])) {
                $code .= '<script type="text/javascript">'. PHP_EOL;
                foreach ($arr['script'] as $inner_script) {
                    $code .= '!function(){'.$inner_script.'}();'. PHP_EOL;
                }
                $code .= '</script>';
            }
            $html = str_replace(self::JS_SCRIPT_HOOK, $code . self::JS_SCRIPT_HOOK, $html);
            $code = '';
            if (!empty($arr['css'])) {
                $code = '<link rel="stylesheet" type="text/css" href="'
                    . implode('" /><link rel="stylesheet" type="text/css" href="', $arr['css'])
                    . '" />';
            }
            if (!empty($arr['style'])) {
                $code .= '<style type="text/css">';
                foreach ($arr['style'] as $inner_style) {
                    $code .= $inner_style;
                }
                $code .= '</style>';
            }
            //替换
            $html = str_replace(self::CSS_LINKS_HOOK, $code . self::CSS_LINKS_HOOK, $html);
        }
        if ($clean_hook) {
            $html = str_replace(array(self::CSS_LINKS_HOOK, self::JS_SCRIPT_HOOK), '', $html);
        }
        return $html;
    }

    /**
     * @param $html string html页面内容
     * @return mixed
     */
    static public function insertPageletGroup($html) {
        if (empty(self::$_pagelet_group)) {
            return $html;
        }
        $search = array();
        $replace = array();
        foreach (self::$_pagelet_group as $group => $ids) {
            $search[] = '<!--' . $group . '-->';
            $replace[] = '<textarea class="g_fis_bigrender" style="display: none">BigPipe.asyncLoad([{id: "'.
                implode('"},{id:"', $ids)
            .'"}])</textarea>';
        }
        return str_replace($search, $replace, $html);
    }

    static public function display($html) {
        $html = self::insertPageletGroup($html);
        $pagelets = self::$_pagelets;
        $mode = self::$force_mode ? self::$force_mode : self::$mode;
        $res = array(
            'js' => array(),
            'css' => array(),
            'script' => array(),
            'style' => array(),
            'async' => array(
                'res' => array(),
                'pkg' => array()
            )
        );
        //{{{
        foreach (self::$inner_widget[$mode] as $item) {
            foreach ($res as $key => $val) {
                if (isset($item[$key]) && is_array($item[$key])) {
                    if ($key != 'async') {
                        $arr = array_merge($res[$key], $item[$key]);
                        //sort by key of array
                        $arr = array_merge(array_unique($arr));
                    } else {
                        $arr = array(
                            'res' => array_merge($res['async']['res'], (array)$item['async']['res']),
                            'pkg' => array_merge($res['async']['pkg'], (array)$item['async']['pkg'])
                        );
                    }
                    //合并收集
                    $res[$key] = $arr;
                }
            }
        }
        //if empty, unset it!
        foreach ($res as $key => $val) {
            if (empty($val)) {
                unset($res[$key]);
            }
        }
        //}}}

        //tpl信息没有必要打到页面
        switch($mode) {
            case self::MODE_NOSCRIPT:
                //渲染widget以外静态文件
                $all_static = FISResource::getArrStaticCollection();
                $html = self::renderStatic(
                    $html,
                    $all_static,
                    true
                );
                break;
            case self::MODE_QUICKLING:
                header('Content-Type: text/json;');
                if ($res['script']) {
                    $res['script'] = implode("\n", $res['script']);
                }
                if ($res['style']) {
                    $res['style'] = implode("\n", $res['style']);
                }
                $html = json_encode(array(
                    'title' => '',
                    'pagelets' => $pagelets,
                    'resource_map' => $res
                ));
                break;
            case self::MODE_BIGPIPE:
                $external = FISResource::getArrStaticCollection();
                $html = self::renderStatic(
                    $html,
                    $external,
                    true
                );
                $html .= '<script type="text/javascript">';
                $html .= "\n";
                if(isset($res['script'])){
                    $html .= 'BigPipe.onPageReady(function(){';
                    if(isset($res['script'])){
                        $html .= "\n";
                        $html .= implode("\n", $res['script']);
                    }
                    $html .= '});';
                    unset($res['script']);
                }
                $html .= '</script>';
                $html .= "\n";
                foreach($pagelets as $index => $pagelet){
                    $id = '__cnt_' . $index;
                    $html .= '<code style="display:none" id="' . $id . '"><!-- ';
                    $html .= str_replace(
                        array('\\', '-->'),
                        array('\\\\', '--\\>'),
                        $pagelet['html']
                    );
                    unset($pagelet['html']);
                    $pagelet['html_id'] = $id;
                    $html .= ' --></code>';
                    $html .= "\n";
                    $html .= '<script type="text/javascript">';
                    $html .= "\n";
                    $html .= 'BigPipe.onPageletArrived(';
                    $html .= json_encode($pagelet);
                    $html .= ');';
                    $html .= "\n";
                    $html .= '</script>';
                    $html .= "\n";
                }
                $html .= "\n";
                $html .= '</script>';
                $html .= '<script type="text/javascript">';
                $html .= "\n";
                $html .= 'BigPipe.register(';
                if(empty($res)){
                    $html .= '{}';
                } else {
                    $html .= json_encode($res);
                }
                $html .= ');';
                $html .= "\n";
                $html .= '</script>';
                break;
        }

        return $html;
    }

    //smarty output filter
    static function renderResponse($content, $smarty) {
        return self::display($content);
    }
}
