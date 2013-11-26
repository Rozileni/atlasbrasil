<?php
//bloquer acesso direto
if(empty($_SERVER['HTTP_X_REQUESTED_WITH']) || strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) != 'xmlhttprequest') 
{
    header("Location: {$path_dir}404");
}

//libs
include_once("../../../../config/conexao.class.php");
include_once("MapResponse.php");

//chamar método
if(isset($_POST)) 
{
    switch ($_POST['method']) 
    {
        case "laod_rm_data":
            laod_rm_data();
            break;
        case "load_cities_data":
            load_cities_data($_POST['extent'], $_POST['indc'],$_POST['spc'],$_POST['ano']);
            break;
        case "load_legenda":
            load_legenda($_POST['indc'],$_POST['spc'],$_POST['ano']);
            break;
    }    
}

function laod_rm_data()
{

    $ocon = new Conexao();
    $link = $ocon->open();

    $sql = "SELECT id, nome, lat, lon, ST_AsGeoJSON(rm.the_geom,3)as geo_json FROM rm;";
    $result = pg_query($link, $sql) or die("Nao foi possivel executar a consulta!");
    $a = array(); 
   
    while ($obj = pg_fetch_object($result)) 
    {   
       $a[] = $obj; 
    }

    echo json_encode($a);
    return 1;
}


function load_cities_data($extent, $indc, $spc, $ano)
{
    
    $ocon = new Conexao();
    $link = $ocon->open();

    $sql = "select rm.id as rm_id, m.id as mun_id, m.nome, ST_AsGeoJSON(m.the_geom,3)as geo_json from rm " .
           " INNER JOIN municipio m ON rm.id = m.fk_rm " .
           " where ST_Intersects(ST_MakeEnvelope(" . $extent["minx"]  . "," .  $extent["miny"] . "," . $extent["maxx"]  . "," . $extent["maxy"]  . ", 4326), rm.the_geom) ORDER BY rm.id ASC;";

    
    $result = pg_query($link, $sql) or die("Nao foi possivel executar a consulta!");
    $a = array(); 
   
    while ($obj = pg_fetch_object($result)) 
    {   
       $a[] = $obj; 
    }
    
    $leg_and_data = load_legenda_and_data($indc, $spc, $ano);    
    $json = (object)Array('shapes'=> $a, 'legenda'=> $leg_and_data->legenda,'dados'=> $leg_and_data->dados);
    
    echo json_encode($json);
    return 1;
}

function load_legenda($indc, $spc, $ano)
{
    $leg_and_data = load_legenda_and_data($indc, $spc, $ano);
    echo json_encode($leg_and_data);
    return 1; 
}


function load_legenda_and_data($indc, $spc, $ano)
{
    $ocon = new Conexao();
    $link = $ocon->open();

    $sql = "SELECT cg.id as cg_id,c.id as c_id,c.nome,c.minimo,c.maximo,c.cor_preenchimento FROM classe_grupo cg INNER JOIN classe c " . 
           " ON c.fk_classe_grupo = cg.id WHERE fk_ano_referencia = $ano AND fk_variavel = $indc AND espacialidade = $spc ORDER BY c_id DESC;";
    
    
    $result = pg_query($link, $sql) or die("Nao foi possivel executar a consulta!");
    $a = array(); 

    while ($obj = pg_fetch_object($result)) 
    {   
       $a[] = $obj; 
    }
    
    if(sizeof($a) == 0)
    {
        $sql = "SELECT cg.id as cg_id,c.id as c_id,c.nome,c.minimo,c.maximo,c.cor_preenchimento FROM classe_grupo cg INNER JOIN classe c " . 
           " ON c.fk_classe_grupo = cg.id WHERE fk_ano_referencia IS NULL AND fk_variavel = $indc AND espacialidade = $spc ORDER BY c_id DESC;";
        
        $result = pg_query($link, $sql) or die("Nao foi possivel executar a consulta!");
        while ($obj = pg_fetch_object($result)) 
        {   
           $a[] = $obj; 
        }
    }

    $result = array();
    $result["legenda"] = $a;
    
    
    switch($spc)
    {
        //RM
        case 6:
            $sql = "SELECT fk_rm as id, valor FROM valor_variavel_rm WHERE fk_ano_referencia = $ano AND fk_variavel = $indc;";
            break;
        //Municipal
        case 2:
            $sql = "SELECT fk_municipio as id, valor FROM valor_variavel_mun WHERE fk_ano_referencia = $ano AND fk_variavel = $indc;";
            break;
    }
    
    $result = pg_query($link, $sql) or die("Nao foi possivel executar a consulta!");
    $dados = array();
    while ($obj = pg_fetch_object($result)) 
    {   
      $dados[$obj->id] = $obj->valor; 
    }

    //var_dump($dados);
    //$result["dados"] = $dados;
    
     $json = new stdClass();
     $json = (object)Array('legenda'=> $a,'dados'=> $dados);
   
     return $json;
}


?>


