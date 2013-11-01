<?php

function smarty_compiler_head($arrParams,  $smarty){
    $strAttr = '';
    foreach ($arrParams as $_key => $_value) {
        $strAttr .= ' ' . $_key . '="<?php echo ' . $_value . ';?>"';
    }
    return '<head' . $strAttr . '>';
}

function smarty_compiler_headclose($arrParams,  $smarty){
    $strResourceApiPath = preg_replace('/[\\/\\\\]+/', '/', dirname(__FILE__) . '/lib/FISPagelet.class.php');
    $strCode = '<?php ';
    $strCode .= 'if(!class_exists(\'FISPagelet\', false)){require_once(\'' . $strResourceApiPath . '\');}';
    $strCode .= 'echo FISPagelet::cssHook();';
    $strCode .= '?>';
    $strCode .= '</head>';
    return $strCode;
}