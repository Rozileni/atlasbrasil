<?php ob_start(); ?>

<script type="text/javascript">

    $(document).ready(function()
    {
      
      
    });
    
    
    function show_content(obj)
    {
        var buttons = ["#btnTable", "#btnMap"];
        $.each
        ( buttons,
          function( index, element ) 
          {
            $(element).removeClass("blue_button");  
            $(element).addClass("gray_button");
            switch(element)
            {
                case "#btnTable":
                    $(element).html('<img src="./img/icons/table_gray.png">');
                    break;
                case "#btnMap":
                    $(element).html('<img src="./img/icons/brazil_gray.png">');
                    break;        
            }
          }
        );
        
        $("#content .tab-pane").css("display","none");
        $(obj).removeClass("gray_button");  
        $(obj).addClass("blue_button");
        switch($(obj).attr("id"))
        {
            case "btnTable":
                $(obj).html('<img src="./img/icons/table_white.png">');
                load_tabela_content();
                break;
            case "btnMap":
                $(obj).html('<img src="./img/icons/brazil_white.png">');
                load_map_content()
                break;        
         }
         
    }
    
    function load_tabela_content()
    {
        $("#tab_table").css("display","block");
    }
    
    function load_map_content()
    {
       $("#tab_map").show(100,show_tab_map_callback)
    }
    
</script>

<!-- Botões -->
<div id="content">
    <div class="containerPage">
        <div class="containerTitlePage">
            
            <div class="titlePage">
                <div id="id_title_consulta" class="titletopPage"></div>
            </div>
            
            <div class="iconAtlas">
                <button id="btnTable"  type="button" name="" value="" class="gray_button small_bt" style="margin-right: 5px;" data-original-title='Ver na tabela' title data-placement='bottom' onclick="show_content(this);">
                    <img src="./img/icons/table_gray.png">
                </button>
                <button id="btnMap"  type="button" name="" value="" class="gray_button small_bt"  data-original-title='Ver no mapa' title data-placement='bottom'  onclick="show_content(this);" >
                    <img src="./img/icons/brazil_gray.png">
                </button>
            </div>
            
        </div>
        <div id="alertTabela"></div>
    </div>
</div>
<div class="linhaDivisoria"></div>

<!-- conteúdo das regiões metropolitanas -->
<div id="content">
    <div class="containerPageComponentes">
        <div class="tab-content" style="min-height: 500px">
            
            <div class="tab-pane active" id="tab_table">
               tabela
            </div>

            <div class="tab-pane" id="tab_map">
                <?php include("com/mobiliti/rm/map/map_ui.php"); ?>
            </div>
            
        </div>
    </div>   
</div>

 
<?php

$title = $lang_mng->getString("rm_page_title");
$meta_title = $lang_mng->getString("rm_meta_title");
$meta_description = $lang_mng->getString("meta_description"); 

$content = ob_get_contents();
ob_end_clean();
include "web/base.php";
?> 
