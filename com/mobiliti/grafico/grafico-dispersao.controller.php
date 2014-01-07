<?php
    
    if(empty($_SERVER['HTTP_X_REQUESTED_WITH']) || strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) != 'xmlhttprequest') {
        header("Location: {$path_dir}404");
    }

    require_once "../../../config/config_path.php";
    require_once '../../../config/conexao.class.php';
    require_once MOBILITI_PACKAGE.'util/protect_sql_injection.php';
    require_once "../consulta/bd.class.php";
    require_once "../consulta/Consulta.class.php";
    require_once "GraficoDispersao.class.php";
    
    $json_lugares = $_POST['json_lugares'];
    $json_indicadores = $_POST['json_indicadores'];
    
    $grafico = new GraficoDispersao($json_lugares, $json_indicadores);
    $grafico->draw();
?>
