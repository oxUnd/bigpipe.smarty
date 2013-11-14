<?php

function smarty_compiler_html($arrParams,  $smarty){
    $strResourceApiPath = preg_replace('/[\\/\\\\]+/', '/', dirname(__FILE__) . '/lib/FISPagelet.class.php');
    $strFramework = $arrParams['framework'];
    $strMode = $arrParams['mode'];

    unset($arrParams['framework']);
    unset($arrParams['mode']);

    $strAttr = '';
    $strCode = '<?php ';
    $strCode .= 'if(!class_exists(\'FISPagelet\', false)){require_once(\'' . $strResourceApiPath . '\');}';
    if (isset($strFramework)) {
        $strCode .= 'FISResource::setFramework(FISResource::load('.$strFramework.', $_smarty_tpl->smarty));';
    }
    $strCode .= 'FISPagelet::init('.$strMode.');';
    $strCode .= ' ?>';
    foreach ($arrParams as $_key => $_value) {
        if (is_numeric($_key)) {
            $strAttr .= ' <?php echo ' . $_value .';?>';
        } else {
            $strAttr .= ' ' . $_key . '="<?php echo ' . $_value . ';?>"';
        }
    }
    return $strCode . "<html{$strAttr}>";
}

function smarty_compiler_htmlclose($arrParams,  $smarty){
    $strCode = '<?php ';
    $strCode .= '$_smarty_tpl->registerFilter(\'output\', array(\'FISPagelet\', \'renderResponse\'));';
    $strCode .= '?>';
    $strCode .= '</html>';
    return $strCode;
}
