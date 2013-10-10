<?php

function smarty_compiler_title($arrParams,  $smarty){
    return "<title>" . '<?php ob_start(); ?>';
}

function smarty_compiler_titleclose($arrParams,  $smarty){
    $strResourceApiPath = preg_replace('/[\\/\\\\]+/', '/', dirname(__FILE__) . '/lib/FISPagelet.class.php');
    $strCode = '<?php ';
    $strCode .= '$title = ob_get_clean();';
    $strCode .= 'if(!class_exists(\'FISPagelet\', false)){require_once(\'' . $strResourceApiPath . '\');}';

    $strCode .= 'if ($title) {';
    $strCode .=     'FISPagelet::setTitle($title);';
    $strCode .=     'echo $title;';
    $strCode .= '}';
    $strCode .= ' ?>';
    return $strCode . '</title>';
}
