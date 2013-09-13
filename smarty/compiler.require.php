<?php

function smarty_compiler_require($arrParams,  $smarty){
    $strName = $arrParams['name'];
    $strCode = '';
    if($strName){
        $strResourceApiPath = preg_replace('/[\\/\\\\]+/', '/', dirname(__FILE__) . '/lib/FISPagelet.class.php');
        $strCode .= '<?php if(!class_exists(\'FISPagelet\')){require_once(\'' . $strResourceApiPath . '\');}';
        $strCode .= 'FISPagelet::load(' . $strName . ',$_smarty_tpl->smarty);';
        $strCode .= '?>';
    }
    return $strCode;
}
