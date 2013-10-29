<?php 
    ob_start(); 
    
    require_once "web/home.php";
    $title = $lang_mng->getString("home_title");
    $meta_title = $lang_mng->getString("home_metaTitle");
    $meta_description = $lang_mng->getString("home_metaDescricao");
    $content = ob_get_contents();
    ob_end_clean();
    include "web/base.php";
?>
