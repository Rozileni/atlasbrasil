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
            load_cities_data($_POST['extent']);
            break;
        case "load_legenda":
            load_legenda($_POST['indc'],$_POST['spc'],$_POST['ano']);
            break;
    }    
}




//métodos
function laod_cities_shp($cities, $indicador, $ano)
{    
    $cities = str_replace("[", "", $cities);
    $cities = str_replace("]", "", $cities);

    $ocon = new Conexao();
    $link = $ocon->open();

    $sql_leg = "select nome, minimo, maximo, cor_preenchimento as cor from classe where fk_classe_grupo = (select id from classe_grupo where espacialidade = 2 and fk_variavel = $indicador);";
    $result_leg = pg_query($link, $sql_leg) or die("Nao foi possivel executar a consulta!");
    while ($obj = pg_fetch_object($result_leg)) 
    {
       $comp = array($obj->minimo, $obj->maximo,$obj->cor);
       $comps[] = $comp; 
    }
    
    $sql = "SELECT m.id, m.nome, e.uf, v.valor, '' as cor,  ST_AsGeoJSON(m.the_geom,3) as locale FROM municipio m INNER JOIN estado e ON m.fk_estado = e.id INNER JOIN valor_variavel_mun v ON v.fk_municipio = m.id WHERE m.id in ($cities) AND v.fk_ano_referencia = $ano AND fk_variavel = $indicador; ";
    $result = pg_query($link, $sql) or die("Nao foi possivel executar a consulta!");
    $a = array(); 
   
    while ($obj = pg_fetch_object($result)) 
    {
       foreach ($comps as $comp)
       {
           if($obj->valor >= $comp[0] && $obj->valor <= $comp[1])
           {
               $obj->cor = $comp[2];
               break;
           }
       }
        
       $a[] = $obj; 
    }
    
    
    $response = new MapResponse();
    $response->setPlaces($a);
    $response->setLegend($comps);

 
    echo json_encode($response);
    return 1;
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


function load_cities_data($extent)
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

    echo json_encode($a);
    return 1;
}


function load_legenda($indc, $spc, $ano)
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
   
     echo json_encode($json);
    
    return 1;
}


?>


