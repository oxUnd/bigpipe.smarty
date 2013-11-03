<?php

function smarty_compiler_uri($arrParams,  $smarty){
    $strName = $arrParams['name'];
    $strCode = '';
    if($strName){
        $strResourceApiPath = preg_replace('/[\\/\\\\]+/', '/', dirname(__FILE__) . '/lib/FISPagelet.class.php');
        $strCode .= '<?php if(!class_exists(\'FISPagelet\', false)){require_once(\'' . $strResourceApiPath . '\');}';
        $strCode .= 'echo FISPagelet::getUri(' . $strName . ',$_smarty_tpl->smarty);';
        $strCode .= '?>';
    }
    return $strCode;
}
