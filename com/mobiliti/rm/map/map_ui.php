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
    
    var rm_map = null;
    var indicadorRm;
    var indicadorList;
    var geral = new Geral();
    
    function show_tab_map_callback()
    {
        if(rm_map === null)init_map_rm();
        return 0;
    }
    
    
    function init_map_rm()
    {
        
        
        $("#ui-mnu-locais :input").attr('disabled', true);
        $("#ui-mnu-locais :input").click(on_locais_check_evt);
        $("#ui_rm_map_year_slider").bind("slider:changed", year_changed_evt);
        rm_map = new RmMap("google-map-canvas");
        
        rm_map.setDisplayRMListener(on_display_rm);
        rm_map.setResetLocais(on_reset_locais);
        rm_map.setUpdateLegendaListener(on_update_legenda);
        rm_map.setAno(1);
        
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
       
        
        
        $(".opt-view").click(optViewHandler);
        return 0;
    }   
    
    
    
    function year_changed_evt(event, data)
    {
        if (data.value === 1991)
            rm_map.setAno(1);
        else if (data.value === 2000)
            rm_map.setAno(2);
        else if (data.value === 2010)
            rm_map.setAno(3);
        
        update_ind_display();
    }
    
    function menu_handler()
    {
        var menu = $(this).attr("href");
        
        if($(this).attr("status") === "close")
        {
            $("#ui-mnu-" + menu).show().animate({height: "400px"}, "slow");
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
        return 1;
    }
    
    function seletor_indicador_evt(arr)
    {
        geral.setIndicadores(arr);
        indicadorRm.refresh();
        indicadorList.refresh();
        update_ind_display();
        
        return 1;
    }
    
    
    
    function indicadorList_evt(arr)
    {
        geral.setIndicadores(arr);
        indicadorList.refresh();
        update_ind_display();
        
        return 1;
    }
    
    
    function update_ind_display()
    {
        $("#ui_ind_label").html('');
        
        arr = geral.getIndicadores();
        $.each(arr,function(i,item)
	{
            if(item.c === true)
            {
               $("#ui_ind_label").html(item.nc + ' (' + rm_map.getAnoString() + ')'); 
               rm_map.setIndicador(item.id);
               return 1;
            }
	 });
        return 1;
    }
    
    
    function on_display_rm(display)
    {
        if(display)
            $("#ui-mnu-locais :input").attr('disabled', false);
        else
            $("#ui-mnu-locais :input").attr('disabled', true);
        
        return 1;
    }
    
    function on_locais_check_evt()
    {
        switch($(this).val())
        {
             case "reg":
                rm_map.loadAndDisplayRM();
                break;
            case "mun":
                rm_map.loadAndDisplayCities();
                break;
            case "udh":
                rm_map.loadAndDisplayUDH();
                break;
            case "rop":
                rm_map.loadAndDisplayOP();
                break;
        }
        return 1;
    }
    
    
    function on_reset_locais()
    {
        $("#rm_first_opt").attr("checked","checked");
        return 1;
    }
    
    
    function on_update_legenda(leg)
    {
        $("#ui-area-leg").html('');
        
        $.each(leg,function(i,item)
	{
             htm = "<div>" 
              + "  <div style='float: left; background-color:" + item.cor_preenchimento + "; border: 1px solid black; height: 20px; width: 20px;'></div>&nbsp; " 
              +  "  <span>"+ item.nome + "</span>  " 
              +  " </div>  <br/>";
              
              $("#ui-area-leg").append(htm);
        });
        
        return 1;
    }
    
    function optViewHandler()
    {
        $(".opt-view").removeClass("active");
        $(this).addClass("active");
        
        rm_map.changeMapType($(this).attr("map-type"));
        
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
        <li><a href="legenda" status="close">Legenda &nbsp;<img src="img/seta.gif" /></a></li>
        <li>| <span id="ui_ind_label"></span></li>
    </ul>
</nav>

<div style='position:relative; height: 500px; width: 100%'>
    
  
     <!-- canvas do mapa -->
     <div id="google-map-canvas" style='margin: 0; padding: 0; top:0px; left:0px; height: 100%;  width: 100%; ' >
     </div>

     <div id="loader" style='position:absolute; top:50%; left:49%; display: none'>
         <img  src="img/loader.gif" />
     </div>
     
     <!-- lista de espacialidades -->
     <div id="ui-mnu-locais" class="ui-mnu" style='position:absolute; top:0px; left:2px; height: 0px; width: 300px; background-color: white; display: none; border: 1px solid #ccc;' >
         
         <ul class="locais-list" >
             <li><input id="rm_first_opt" type="radio" name="local-tipo" checked="checked" value="reg">Regiões Metropolitanas</li>
           <li><input type="radio" name="local-tipo" value="mun">Municípios</li>
           <li><input type="radio" name="local-tipo" value="udh">Unidade de Desenvolvimento Humano</li>
           <li><input type="radio" name="local-tipo" value="rop">Regiões do OP</li>
         </ul>
         
     </div>
     
     <div id="ui-mnu-indicadores" class="ui-mnu" style='position:absolute; top:0px; left:80px; height: 0px; width: 300px; background-color: white; display: none; border: 1px solid #ccc;' >
         <br/> 
         <div id="ui-indicador-list"></div> 
         <div id="btn_select_indicador" style='position:absolute; top:275px; left:10px;' onclick="btn_select_indicador_handler();" class="btn">Alterar Lista</div>
         <div style='position:absolute; top:320px; left:15px;'>
             <span id="ui_rm_ano" style="font-weight: bold; display:block; margin-left:24px; width:44px">ANO</span>
                <div>
                    <div class='labels'>
                        <span class="one">1991</span>
                        <span class="two">2000</span>
                        <span class="tree">2010</span>
                    </div>
                </div>
                <div class="sliderDivFather">
                    <div class="sliderDivIn">
                        <input type='text' id="ui_rm_map_year_slider" data-slider="true" data-slider-values="1991,2000,2010" data-slider-equal-steps="true" data-slider-snap="true" data-slider-theme="volume" />
                    </div>    
                </div>
         </div>
     
     </div>
     
     
      <!-- Local para a legenda-->
      <div id="ui-mnu-legenda" class="ui-mnu" style='position:absolute; top:0px; left:190px; height: 0px; width: 300px; background-color: white; display: none; border: 1px solid #ccc;' >
       
          <div id="ui-area-leg" style='position: relative; top:10px; left:10px; height: 100%; width: 290px;' >
              
              Nenhuma legenda disponível.
              
             
          </div>         
      </div>
      
      
      <div class="btn-group" style='position:absolute; top:10px; left:820px;'>
        <button type="button" map-type="ROADMAP"  class="btn btn-default opt-view active">Mapa</button>
        <button type="button" map-type="SATELLITE" class="btn btn-default opt-view">Satélite</button>
      </div>

</div>
