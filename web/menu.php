<?php
    if(!isset($_SESSION)) 
   { 
        session_start(); 
   }
   ob_start(); 
    
    
    include ('./config/config_home.php');
    $url = str_replace(strrchr($_SERVER["REQUEST_URI"], "?"), "", $_SERVER["REQUEST_URI"]);
    $gets = explode("/",$_GET["cod"]);
    
    if($gets[0] == "pt" || $gets[0] == "en" || $gets[0] == "es")
    {
        array_shift ( $gets );
    } 

	if(sizeof($base_expl)== 1){
                $pag = $gets[0];
                if(sizeof($gets)> 1){
                    $pagNext = $gets[1];
                }
                else{
                    $pagNext = '';
                }
                if(sizeof($gets) > 2){
                    $pagNext2 = $gets[2];
                }
                else{
                    $pagNext2 = '';
                }
            }
?>

<?php
if($pag == 'perfil' || $pag == 'perfil_print'){
?>
<script src="js/charts/function_format.js" type="text/javascript"></script> 
<script src="js/charts/charts_perfil.js" type="text/javascript"></script>
<?php
}
?>

<div class="contentCenterMenu">
    
    
    <style type="text/css">
        
        #div_lang_selector 
        {
            display: <?php echo(HIDE_INTER)?'none;':'block;'; ?>
        }
        
        div.link_inter a
        {
           color: black;
        }
        
        a.<?php echo 'link_' . $_SESSION['lang']; ?>
        {
            font-weight: bold;
        }
       
    </style>
        
    <div id="div_lang_selector" style="float:right; margin-right: 25px" class="link_inter" >
        <img src="img/flag/bra.png" /><a class="link_pt" href="pt/" > Português </a>&nbsp;
        <img src="img/flag/eua.png" /><a class="link_en" href="en/"> English </a>&nbsp;
        <img src="img/flag/es.png" /><a class="link_es" href="es/"> Español </a>&nbsp;
    </div>
    
               
    <div class="mainMenuTop">   
        <img src="./img/setaMenu.png" id="setaMenu" style="display: none;position: absolute; width: 80px" alt=""/>
        <div class="imgLogo">
            <a href="<?php echo $path_dir.$_SESSION["lang"].'/' ?>"><img src=<?php echo "./img/logos/branca_".$_SESSION["lang"].".png";?> alt=""/></a>
            <?php
                if($tarja_EmDesenvolvimento == true){
                    echo "<div class='tarja_EmDesenvolvimento'>
                        <img src='./img/em_desenvolvimento.png' />
                    </div>";
                }
            ?>
        </div>
        <ul class="mainMenuTopUl">
            <li><a href="<?php echo $path_dir;?><?php echo $_SESSION["lang"];?>/home/" <?php if(($pag == 'home' || $pag == '') && $pagNext == '') {echo 'class="ativo"';} ?> id="menu_home"></a></li>
           
            <?php 
               if(atlas_has_lang($_SESSION["lang"])){
            ?>
                <li><a href="<?php echo $path_dir; ?><?php echo $_SESSION["lang"];?>/o_atlas/o_atlas_/" <?php if($pag == "o_atlas") {echo 'class="ativo"';} ?> id="menu_oAtlas"></a></li>
            <?php
                }
            ?>
            
            <?php 
                if(destaque_has_lang(@$_SESSION["lang"])){
            ?>
                <li><a href="<?php echo $path_dir; ?><?php echo $_SESSION["lang"];?>/destaques/" <?php if($pag == "destaques") {echo 'class="ativo"';} ?> id="menu_destaques"></a></li> 
            <?php 
                }
            ?>
                
            <?php 
                if(perfil_has_lang(@$_SESSION["lang"])){
            ?>
                <li><a href="<?php echo $path_dir; ?><?php echo $_SESSION["lang"];?>/perfil/" <?php if($pag == 'perfil' && $pagNext2 == '') {echo 'class="ativo"';} ?> id="menu_perfil"></a></li>
            <?php 
                }
            ?>   
                
            <?php 
                if(consulta_has_lang(@$_SESSION["lang"])){
            ?>
                <li><a href="<?php echo $path_dir; ?><?php echo $_SESSION["lang"];?>/consulta/" <?php if($pag == 'consulta' && $pagNext2 == '') {echo 'class="ativo"';} ?> id="menu_consulta"></a></li>
            <?php 
                }
            ?>
                
            <?php 
                if(arvore_has_lang(@$_SESSION["lang"])){
            ?>
                <li><a href="<?php echo $path_dir; ?><?php echo $_SESSION["lang"];?>/arvore/" <?php if($pag == 'arvore' && ($pagNext == '' || $pagNext == 'estado' || $pagNext == 'municipio' || $pagNext == 'aleatorio')) {echo 'class="ativo"';} ?> id='menu_arvore'></a></li>
            <?php 
                }
            ?>  
                
            <?php 
                if(ranking_has_lang(@$_SESSION["lang"])){
            ?>
                <li><a href="<?php echo $path_dir; ?><?php echo $_SESSION["lang"];?>/ranking" <?php if($pag == 'ranking' && $pagNext == '') {echo 'class="ativo"';} ?> id="menu_ranking"></a></li>
            <?php 
                }
            ?>  
            
            <?php 
                if(download_has_lang(@$_SESSION["lang"])){
            ?>
                <li><a href="<?php echo $path_dir; ?><?php echo $_SESSION["lang"];?>/download/"<?php echo $_SESSION["lang"];?> <?php if($pag == "download" && $pagNext == '') {echo 'class="ativo"';} ?> id="menu_download"></a></li>
            <?php 
                }
            ?>

        </ul>
    </div> 
</div>

<!--&& (pagNext == '' || pagNext2 == 'municipio' || pagNext2 == 'estado' || pagNext2 == 'aleatorio' || pagNext == lang-->


<script type="text/javascript">
        $(document).ready(function(){
            rez();
        })
        
        function rez(){
            pag = '<?=$pag?>';
        pagNext = '<?=$pagNext?>';
        pagNext2 = '<?=$pagNext2?>';
        if(
        (pag == 'destaques')
            | (pag == 'consulta' && pagNext == 'padrao' && pagNext2 == '') | (pag == 'perfil' && pagNext2 == '') | (pag == 'download' | pag == 'ranking') | 
            (pag == 'arvore' && (pagNext == '' || pagNext == 'municipio' || pagNext == 'estado' || pagNext == 'aleatorio'))  | 
            (pag == 'o_atlas' && (pagNext == '' || pagNext == 'o_atlas_' || pagNext == 'quem_faz' || pagNext == 'para_que' || pagNext == 'processo' || pagNext == 'desenvolvimento_humano' || pagNext == 'idhm' || pagNext == 'metodologia' && (pagNext2 == 'idhm_longevidade' || pagNext2 == 'idhm_educacao' || pagNext2 == 'idhm_renda') || pagNext == 'glossario' || pagNext == 'perguntas_frequentes' || pagNext == 'tutorial' || pagNext == ''))){
            document.getElementById("setaMenu").style.display = 'block';
            var pos = $(".mainMenuTopUl .ativo").position();
            var largura = $(".mainMenuTopUl .ativo").css("width");
            tamanho = parseInt(largura.length);
            tamanho2 = tamanho - 2;
            numberLargura = parseInt(largura.substr(0,tamanho2));
            pos.top = pos.top + 46;
            metLargura = numberLargura / 2;
            pos.left = parseInt(pos.left + metLargura - 37) ;

            $("#setaMenu").css("left",pos.left+"px");
            $("#setaMenu").css("top",pos.top+"px");
            }
            };

        $("#menu_home").html(lang_mng.getString("menu_home"));
        $("#menu_oAtlas").html(lang_mng.getString("menu_oAtlas"));
        $("#menu_destaques").html(lang_mng.getString("menu_destaques"));
        $("#menu_perfil").html(lang_mng.getString("menu_perfil"));
        $("#menu_consulta").html(lang_mng.getString("menu_consulta"));
        $("#menu_arvore").html(lang_mng.getString("menu_arvore"));
        $("#menu_ranking").html(lang_mng.getString("menu_ranking"));
        $("#menu_download").html(lang_mng.getString("menu_download"));
     
</script>

