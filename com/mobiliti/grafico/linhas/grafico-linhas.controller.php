<?php
    require_once "../../../../config/config_path.php";
    require_once '../../../../config/conexao.class.php';
    require_once MOBILITI_PACKAGE.'util/protect_sql_injection.php';
    require_once "../../consulta/bd.class.php";
    require_once "../../consulta/Consulta.class.php";
    require_once "GraficoLinhas.class.php";
    
    $json_lugares = $_POST['json_lugares'];
    $indicador = $_POST['indicador'];
    foreach($json_lugares as $key=>$val){
        $grafico = new GraficoLinhas($val, $indicador);
    }
    
    $linha1 = array();
    $linha2 = array();
    $linha3 = array();
    $linha4 = array();
    
    $linha1[] = "Ano";
    $linha2[] = "1991";
    $linha3[] = "2000";
    $linha4[] = "2010";
    foreach(GraficoLinhas::$Rows as $key=>$val){
        $linha1[] = "{$val['im']}";
        $linha2[] = (float)$val['vs'][1]['v'];
        $linha3[] = (float)$val['vs'][2]['v'];
        $linha4[] = (float)$val['vs'][3]['v'];
    }
    
    $arr = array($linha1,$linha2,$linha3,$linha4);
    
    echo json_encode($arr);
?>
