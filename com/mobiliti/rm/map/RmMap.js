function RmMap(map_div)
{
    //membros privados
    var _map;
    var _map_div = map_div;
    
    var _rm_data_table;
    var _rm_markers;
    var _rm_markers_info;
    var _is_marker_visible = false;
    var _rm_shapes;
    
    var _rm_cities = new Object();
    
    var RM_LIMIT_VIEW = 7;
    var RM_DISPLAY_CITY = 11;
    
   
    //costrutor
    _iniciar_mapa();
    //fim construtor
    
    //métodos privados
    function _iniciar_mapa()
    {
        //starta o mapa com suas opções
        var mapOptions = {
            zoom: 5,
            center: new google.maps.LatLng(-14.68, -50.36),
            mapTypeId: google.maps.MapTypeId.ROADMAP
        };
      
        _map = new google.maps.Map(document.getElementById(_map_div), mapOptions); 
        
        google.maps.event.addListener(_map, 'zoom_changed', _zoom_evt);
        //google.maps.event.addListener(_map, 'dragend', _dragend_evt);
        
        
        
        loadingHolder.show("Carregando Regiões Metropolitanas...");
        $.ajax({type: "POST",dataType: "json",url: "com/mobiliti/rm/map/regmet_controller.php",
            data: {method:'laod_rm_data'}
        }).done(_load_rm_data);
        
        return 1;
    }
    
    
   function _zoom_evt()
   {
       
       if(this.zoom >= RM_LIMIT_VIEW && _is_marker_visible)
       {
            _display_rm_pin(!_is_marker_visible);
            _display_rm_shp(true);
       }
       else if(this.zoom < RM_LIMIT_VIEW && !_is_marker_visible)
       {
             _display_rm_pin(!_is_marker_visible);
             _display_rm_shp(false);
       }
       
       
       if(this.zoom >= RM_DISPLAY_CITY)_check_for_cities();
       
       return 1;
   }
   
   function _dragend_evt()
   {
       if(this.zoom >= RM_DISPLAY_CITY)_check_for_cities();
       return 1;
   }
   
   function _load_rm_data(data)
   {
       _rm_data_table = data;
       _create_rm_pin();
       _create_rm_shp();
       _display_rm_pin(true);
       loadingHolder.dispose();
       return 1;
   }
   
   //exibe os marcadores sobre as rm's
   function _create_rm_pin()
   {  
      _rm_markers = new Array();
      _rm_markers_info = new Array();
      for (var i = 0, len = _rm_data_table.length; i < len; i++) 
      {
        //rm
        var rm = _rm_data_table[i];
        //positions
        var pos = new google.maps.LatLng(rm.lat, rm.lon);
          
        // To add the marker to the map, use the 'map' property
        var marker = new google.maps.Marker({
            position: pos,
            title: " "+ i
        });
        
        var contentString = '<div id="content">'+
        '<div id="siteNotice">'+
        '</div>'+
        '<h1 id="firstHeading" class="firstHeading">'+ rm.nome +'</h1>'+
        '<div id="bodyContent">'+
        '<p><b>Uluru</b>, also referred to as <b>Ayers Rock</b>, is a large ' +
        'sandstone rock formation in the southern part of the '+
        'Northern Territory, central Australia. It lies 335&#160;km (208&#160;mi) '+
        'south west of the nearest large town, Alice Springs; 450&#160;km '+
        '(280&#160;mi) by road. Kata Tjuta and Uluru are the two major '+
        'features of the Uluru - Kata Tjuta National Park. Uluru is '+
        'sacred to the Pitjantjatjara and Yankunytjatjara, the '+
        'Aboriginal people of the area. It has many springs, waterholes, '+
        'rock caves and ancient paintings. Uluru is listed as a World '+
        'Heritage Site.</p>'+
        '<p>Attribution: Uluru, <a href="http://en.wikipedia.org/w/index.php?title=Uluru&oldid=297882194">'+
        'http://en.wikipedia.org/w/index.php?title=Uluru</a> '+
        '(last visited June 22, 2009).</p>'+
        '</div>'+
        '</div>';
        
        
        
        var infowindow = new google.maps.InfoWindow({
            content: contentString
        });
        
        _rm_markers.push(marker);
        _rm_markers_info.push(infowindow);
        
      }
      _make_markers_clickable();
      
      return  1;
   }
   
   
   function _make_markers_clickable()
   {
       
      for (var i = 0, len = _rm_markers.length; i < len; i++) 
      {
          google.maps.event.addListener(_rm_markers[i], 'click', function(obj) {
             _rm_markers_info[parseInt(this.title)].open(_map, this);
          });
            
      }
       return 1;
   }
   
   
   function _check_for_cities()
   {
       _display_rm_shp(false);
       
       var max  = _map.getBounds().getNorthEast();
       var min  = _map.getBounds().getSouthWest();
       
       var obj_extent = {minx: min.lng(), miny: min.lat(), maxx: max.lng(), maxy: max.lat() };
       
       $.ajax({type: "POST",dataType: "json",url: "com/mobiliti/rm/map/regmet_controller.php",
            data: {method:'load_cities_data', extent: obj_extent }
        }).done(_load_cities_data);
       
       return 1;
   }
   
   
   function _load_cities_data(data)
   {
      for (var i = 0, len = data.length; i < len; i++) 
      {
        var city = data[i];
        
        console.log(city.rm_id)
           
           
        var geo_info = 
        {
              type: "Feature",
              properties: {"nome": city.nome, "id": city.id},
              geometry: $.parseJSON(city.geo_json)
        };
         
        var color = "#CCFF66";
        var geo_style = 
        {
              strokeColor: "#000",
              strokeOpacity: 1,
              strokeWeight: 1,
              fillColor: color,
              fillOpacity: 0.5
        };
          
        var geo_json = new GeoJSON(geo_info, geo_style);

        if (geo_json.type && geo_json.type == "Error") 
        {
            console.log("Erro ao gerar objeto GeoJson!");
            return false; 
        }
        
        
        geo_json[0].setMap(_map);
      }

      return 1;
   }
   
   //exibe os marcadores sobre as rm's
   function _display_rm_pin(flag)
   { 
      _is_marker_visible = flag;
      for (var i = 0, len = _rm_markers.length; i < len; i++) 
      {
          var mk = _rm_markers[i];
          if(flag)
              mk.setMap(_map);
          else
              mk.setMap(null);  
      }
      return _is_marker_visible;
   }
   
   
   //cria as shapes das rms
   function _create_rm_shp()
   { 
      _rm_shapes = new Array(); 
      for (var i = 0, len = _rm_data_table.length; i < len; i++) 
      {
        var rm = _rm_data_table[i];
         
        var geo_info = 
        {
              type: "Feature",
              properties: {"nome": rm.nome, "id": rm.id},
              geometry: $.parseJSON(rm.geo_json)
        };
         
        var color = "#CCFF66";
        var geo_style = 
        {
              strokeColor: "#000000",
              strokeOpacity: 1,
              strokeWeight: 1,
              fillColor: color,
              fillOpacity: 0.5
        };
          
        var geo_json = new GeoJSON(geo_info, geo_style);

        if (geo_json.type && geo_json.type == "Error") 
        {
            console.log("Erro ao gerar objeto GeoJson!");
            return false; 
        }
        
        _rm_shapes.push(geo_json[0]);
      }
      
      return 1;
   }
   
   
   
   //exibe os shapes sobre as rm's
   function _display_rm_shp(flag)
   { 
      for (var i = 0, len = _rm_shapes.length; i < len; i++) 
      {
          var shp = _rm_shapes[i];
          if(flag)
              shp.setMap(_map);
          else
              shp.setMap(null);  
      }
      return 1;      
   }
   
    
   //membros publicos
   return {
        adicionaLivro: function(livro) {
            _livros.push(livro);
            //return this;
        },
        meusLivros: function(){
            return _ordenaLivros(_livros).join(", ");
        }
    };
}
