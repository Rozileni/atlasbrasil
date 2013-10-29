<style type="text/css">
    .leg_block 
    {
        position: relative;
        height: 50px;
        width: 100%;
    }
    
    .leg_color 
    {
        position: absolute;
        background-color: white;
        height: 18px;
        width: 32px;
        top: 10px;
        border: 2px solid black;
        left: 10px;
    }
    
    .leg_label
    {
        font-weight: bold;
        position: absolute;
        left: 50px;
        top: 9px;
    }
    
    /* css do menu*/
    #menu-rm ul 
    {
	padding:0px;
	margin:0px;
	background-color:white;
	list-style:none;
    }
    
    #menu-rm ul li { display: inline; }
    
    #menu-rm ul li a 
    {
	padding: 2px 10px;
	display: inline-block;
 
	/* visual do link */
	background-color:white;
	color: #777;
	text-decoration: none;
	border-bottom:3px solid white;
     }
     
     #menu-rm ul li a:hover 
     {
	background-color:#D6D6D6;
	color: #6D6D6D;
	border-bottom:3px solid rgba(123,210,250,0.5);
     }
     
     
     /* checklist */
     .locais-list{ }
     .locais-list li
     {
        list-style-type: none;  
     }
     
</style>

<script type="text/javascript" src="https://maps.googleapis.com/maps/api/js?sensor=false"></script>
<script type="text/javascript" src="com/mobiliti/rm/map/RmMap.js"></script>
<script type="text/javascript" src="com/mobiliti/rm/selector/IndicadorRM.js"></script>
<script>
    
    var rm_map;
    var indicadorRm;
    var indicadorList;
    var geral = new Geral();
    
    function show_tab_map_callback()
    {
        if(rm_map == null)init_map_rm();
        return 0;
    }
    
    
    function init_map_rm()
    {
        rm_map = new RmMap("google-map-canvas");
        $("#menu-rm a").click(menu_handler);
        
        try 
        {
            indicadorRm = new IndicadorRM(seletor_indicador_evt);
            
        }catch (e) 
        {
            console.log(e.message);
        }
        
        
        try 
        {
            $('#ui-indicador-list').load('com/mobiliti/rm/selector/indicador_rm.html', function()
            {
                indicadorList = new SeletorIndicador();
                indicadorList.startLocal(indicadorList_evt, "ui-indicador-list", false);
            });
            
        }
        catch (e) 
        {
            console.log(e.message);
        }
       
        
        return 0;
    }   
    
    function menu_handler()
    {
        var menu = $(this).attr("href");
        
        if($(this).attr("status") == "close")
        {
            $("#ui-mnu-" + menu).show().animate({height: "315px"}, "slow");
            $(this).attr("status","open");
        }
        else
        {
            $("#ui-mnu-" + menu).animate({height: "0px"}, "slow", function (){$("#ui-mnu-" + menu).hide();});
            $(this).attr("status","close");
        }       
        return false;
    }
    
    
    function btn_select_indicador_handler()
    {
        indicadorRm.show();
        return 0;
    }
    
    function seletor_indicador_evt(obj)
    {
        geral.setIndicadores(obj);
        indicadorRm.refresh();
        indicadorList.refresh();
    }
    
    function indicadorList_evt(obj)
    {
        geral.setIndicadores(obj);
        indicadorList.refresh();
    }
</script>


<!-- Componente de seleção de indicadores -->
<div id="indicador-holder" class="divCallOut" style='position:absolute; z-index: 2; background-color: white; display: none; border: 1px solid black; opacity: 1; height: 360px; width: 713px;' >
    

    <div class="dimensao box dim">
        <h6 class="title_box" id="dim_title">Dimensão</h6>
        <ul class="nav nav-list list_menu_indicador dim"> 
            
        </ul>
    </div>

    <div class="tema box">
       <h6 class="title_box" id="tem_title">Tema</h6>
       <ul class="nav nav-list list_menu_indicador">
       </ul>
    </div>

     <div class="indicador box">
         <h6 class="title_box" id="ind_title">Indicador</h6>
         <ul class="nav nav-list list_menu_indicador">
         </ul>
     </div>

     <div class="itens_selecionados box">
         <a class="close indicador">&times;</a>
         <h6 class="title_box" id="sel_title">Selecionados</h6>
         <ul class="nav nav-list list_menu_indicador"></ul>
     </div>

     <div class="btn_select" style="width: 699px">
            <div class="messages"></div>
            <div class="buttons">
                <button class="blue_button big_bt btn_ok" type="button" style="float: right; font-size: 14px; height: 30px; padding: 5px 10px;">Ok</button>
                <button class="gray_button big_bt btn_clean" id="limpar_title" type="button" style="width: 162px; font-size: 14px; height: 30px; margin-left: 20px;">limpar sel</button>
                <div></div>
            </div> 
      </div>
</div>
<!-- // Componente de seleção de indicadores -->

<!-- menu -->
<nav id="menu-rm">
    <ul>
        <li><a href="locais" status="close" >Locais &nbsp;<img src="img/seta.gif" /></a></li>
        <li><a href="indicadores" status="close" >Indicadores &nbsp;<img src="img/seta.gif" /></a></li>
        <li><a href="legenda" status="close">Legenda</a></li>
    </ul>
</nav>
<div style='position:relative; height: 500px; width: 100%'>
    
  
     <!-- canvas do mapa -->
     <div id="google-map-canvas" style='margin: 0; padding: 0; top:0px; left:0px; height: 100%;  width: 100%; ' >
     </div>

     <div id="loader" style='position:absolute; top:50%; left:49%; display: none'>
         <img  src="img/loader.gif" />
     </div>
     
     <div id="ui-mnu-locais" class="ui-mnu" style='position:absolute; top:0px; left:2px; height: 0px; width: 300px; background-color: white; display: none; border: 1px solid #ccc;' >
         
         <!-- lista de espacialidades -->
         <ul class="locais-list" >
           <li><input type="checkbox" value="mun">Municípios</li>
           <li><input type="checkbox" value="udh">Unidade de Desenvolvimento Humano</li>
           <li><input type="checkbox" value="udh">Regiões do OP</li>
         </ul>
         
     </div>
     
     <div id="ui-mnu-indicadores" class="ui-mnu" style='position:absolute; top:0px; left:70px; height: 0px; width: 300px; background-color: white; display: none; border: 1px solid #ccc;' >
         <br/> 
         <div id="ui-indicador-list"></div> 
         <div id="btn_select_indicador" style='position:absolute; top:275px; left:10px;' onclick="btn_select_indicador_handler();" class="btn">Alterar Lista</div>
     </div>
     
     
      <!-- Local para a legenda-->
      <div id="ui_legend" style='position:absolute; top:0px; left:759px; display: none; height: 100%; width: 200px; background-color: white; border-left: 1px solid #ccc;' >
      
          <div style="height: 50px; width: 100%;">
             <div style='position:absolute; top:7px; left:50px; font-size: 16px; font-weight: bold'> Legenda </div>
          </div>
          
          <div id="ui_area_leg">
          </div>
                    
      </div>

</div>
