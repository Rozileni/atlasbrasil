function RmMap(map_div)
{
    
    var _this;
    //membros privados
    var _map;
    var _map_div = map_div;
    
    
    
    var _rm_data_table;
    var _rm_markers;
    var _rm_markers_info;
    var _is_marker_visible = false;
    
    
    
    var _rm_shapes;
    var _rm_shapes_idx;
    
    var _rm_cities;
    var _rm_cities_idx;
    
    var RM_LIMIT_VIEW = 8;

    var _NO_COLOR = "#ccc";
    
    var _espac = -1;
    var _indicador  = -1;
    var _ano = 1;
    
    //listeners
    var _display_rm_listener = null;
    var _legenda_listener = null;
    
    
    var _legenda = new Array();
    var _dados = null;
   
    //costrutor
    _iniciar_mapa();
    //fim construtor
    
    //mÃ©todos privados
    function _iniciar_mapa()
    {
        //starta o mapa com suas opÃ§Ãµes
        var mapOptions = {
            zoom: 5,
            center: new google.maps.LatLng(-14.68, -50.36),
            mapTypeId: google.maps.MapTypeId.ROADMAP
        };
      
        _map = new google.maps.Map(document.getElementById(_map_div), mapOptions); 
        
        google.maps.event.addListener(_map, 'zoom_changed', _zoom_evt);
        //google.maps.event.addListener(_map, 'dragend', _dragend_evt);

        return 1;
    }
    
    
   function _zoom_evt()
   {  
       if(this.zoom >= RM_LIMIT_VIEW && _is_marker_visible)
       {
            //mostra os shapes das RM
            _display_rm_pin(!_is_marker_visible);
            _this.displayRM();
            if(_display_rm_listener !== null) _display_rm_listener(true);
            _espac = ESP_REGIAOMETROPOLITANA;
       }
       else if(this.zoom < RM_LIMIT_VIEW && !_is_marker_visible)
       {
           //mostra os marcadores das RM
           _display_rm_pin(!_is_marker_visible);
           _clear_map();
           if(_display_rm_listener !== null)_display_rm_listener(false);
           _espac = -1;
       }
       return 1;
   }
   
   function _clear_map()
   {
     _display_rm_shp(false);
     _display_mun_shp(false);
   }
   
   function _load_rm_shp(data)
   {
       _rm_data_table = data;
       _create_rm_pin();
       _create_rm_shp();
       _display_rm_pin(true);
       loadingHolder.dispose();
       return 1;
   }
   
   //cria os alfinestes sobre as regiÃµes metropolitas 
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
        
        var contentString = '<div id="content">'+ rm.nome +'</div>';
        
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

       loadingHolder.show("Carregando municípios...");
       var max  = _map.getBounds().getNorthEast();
       var min  = _map.getBounds().getSouthWest();
       
       var obj_extent = {minx: min.lng(), miny: min.lat(), maxx: max.lng(), maxy: max.lat() };
       
       $.ajax({type: "POST",dataType: "json",url: "com/mobiliti/rm/map/regmet_controller.php",
            data: {method:'load_cities_data', extent: obj_extent, indc: _indicador, spc: _espac, ano: _ano }
        }).done(_load_cities_shp);
       
       return 1;
   }
   
   
   function _load_cities_shp(data)
   {
      _rm_cities = new Array();
      _rm_cities_idx = new Array();
      var _shapes = data.shapes;

      for (var i = 0, len = _shapes.length; i < len; i++) 
      {
            var city = _shapes[i];

            var geo_info = 
            {
                  type: "Feature",
                  properties: {"nome": city.nome, "rmid" : city.rm_id, "id": city.mun_id, valor: 0},
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

            if (geo_json.type && geo_json.type === "Error") 
            {
                console.log("Erro ao gerar objeto GeoJson!");
                return false; 
            }

            _rm_cities[city.mun_id] = geo_json[0];
            _rm_cities_idx.push(city.mun_id);
            
       }

        _build_legend({dados: data.dados, legenda: data.legenda});
       _display_mun_shp(true);
       loadingHolder.dispose();
       return 1;
   }
   
   
   //exibe os shapes dos municÃ­pios
   function _display_mun_shp(flag)
   { 
      var rmid_b = -1, rmid_a = -1;
      
      if(_rm_cities === undefined || _rm_cities === null) return 0;
      for (var i = 0, len = _rm_cities_idx.length; i < len; i++) 
      {
          var city = _rm_cities[_rm_cities_idx[i]];

          if(flag)
          {
              //exibe as cidades de acordo com os seus respectivos valores
              city.fillColor = "#ccc"; 
              city.strokeColor = "#00f";
              city.strokeOpacity = 1;
              city.strokeWeight =  1;
              city.fillOpacity = 0.5;
              
              for (var k = 0, klen = _legenda.length; k < klen; k++) 
              { 
                  var valor = city.geojsonProperties.valor;
                  var leg = _legenda[k];
                
                  
                  if(valor >= leg.minimo && valor <= leg.maximo)
                  {
                      city.fillColor = leg.cor_preenchimento;
                      break;
                  }
              }

              city.setMap(null);
              city.setMap(_map);
              //------------------------------------------------- 
              
              //exibir borda da rm
              rmid_a = parseInt(city.geojsonProperties.rmid);
              if(rmid_a !== rmid_b)
              {
                _rm_shapes[rmid_a].strokeColor = "#000";
                _rm_shapes[rmid_a].strokeOpacity = 1;
                _rm_shapes[rmid_a].strokeWeight =  2;
                _rm_shapes[rmid_a].fillOpacity = 0;
                _rm_shapes[rmid_a].setMap(_map);
              }
              rmid_b = rmid_a;
              //------

          }
          else
              city.setMap(null);  
      }
      return 1;
   }
   
   
   //exibe os marcadores sobre as rm's
   function _display_rm_pin(flag)
   { 
      _is_marker_visible = flag;
      if(_rm_markers === undefined || _rm_markers === null) return 0;
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
      _rm_shapes_idx = new Array(); 
      
      for (var i = 0, len = _rm_data_table.length; i < len; i++) 
      {
        var rm = _rm_data_table[i];
         
        var geo_info = 
        {
              type: "Feature",
              properties: {"nome": rm.nome, "id": rm.id, valor: 0},
              geometry: $.parseJSON(rm.geo_json)
        };
         
        var geo_style = 
        {
              strokeColor: "#00f",
              strokeOpacity: 1,
              strokeWeight: 1,
              fillColor: _NO_COLOR,
              fillOpacity: 0.5
        };
          
        var geo_json = new GeoJSON(geo_info, geo_style);

        if (geo_json.type && geo_json.type === "Error") 
        {
            console.log("Erro ao gerar objeto GeoJson!");
            return false; 
        }
        
        _rm_shapes[rm.id] = geo_json[0];
        _rm_shapes_idx.push(rm.id);
      }
      
      return 1;
   }
   
   
   
   //exibe os shapes sobre as rm's
   //e atualiza suas respectivas cores
   function _display_rm_shp(flag)
   { 
      if(_rm_shapes === undefined || _rm_shapes === null) return 0;
      for (var i = 0, len = _rm_shapes_idx.length; i < len; i++) 
      {

          var shp = _rm_shapes[_rm_shapes_idx[i]];
          if(flag)
          {
              shp.fillColor = "#ccc"; 
              shp.strokeColor = "#00f";
              shp.strokeOpacity = 1;
              shp.strokeWeight =  1;
              shp.fillOpacity = 0.5;
              
              for (var k = 0, klen = _legenda.length; k < klen; k++) 
              { 
                  var valor = shp.geojsonProperties.valor;
                  var leg = _legenda[k];

                  console.log(valor);
                  if(valor >= leg.minimo && valor <= leg.maximo)
                  {
                      shp.fillColor = leg.cor_preenchimento;
                      break;
                  }
              }

              shp.setMap(null);
              shp.setMap(_map);
          }
          else
              shp.setMap(null);  
      }
      return 1;      
   }
   
   
   function _build_legend(data)
   {
       _dados = data.dados;
       _legenda = data.legenda;
      
       switch (_espac)
       {
            case ESP_REGIAOMETROPOLITANA:
                for (var i = 0, len = _rm_shapes_idx.length; i < len; i++) 
                {
                   var shp = _rm_shapes[_rm_shapes_idx[i]];
                   shp.geojsonProperties.valor = _dados[shp.geojsonProperties.id];
                }
                break;
            case ESP_MUNICIPAL:
                for (var i = 0, len = _rm_cities_idx.length; i < len; i++) 
                {
                   var shp = _rm_cities[_rm_cities_idx[i]];
                   shp.geojsonProperties.valor = _dados[shp.geojsonProperties.id];
                }
                break; 
       } 
       loadingHolder.dispose();   
       if(_legenda_listener !== null )_legenda_listener(_legenda);
   }
   
   function _update_indicador()
   { 
        if(_espac === -1)
        {
           //alert('Nenhuma espacialidade selecionada');
           return 0;
        }
        loadingHolder.show("Carregando Legenda...");
        $.ajax({type: "POST",dataType: "json",url: "com/mobiliti/rm/map/regmet_controller.php",
            data: {method:'load_legenda',indc: _indicador, spc: _espac, ano: _ano}
        }).done(_build_legend);

       return 1;
   }
   

   //membros publicos
   _this =  {
        setIndicador: function(value){
            _indicador = value;
            _update_indicador();
        },
        getIndicador: function() {
            return _indicador;
        },   
        setAno: function(value) {
            _ano = value;
        },
        getEspacialidade: function() {
            return _espac;
        },
        getAno: function() {
            return _ano;
        },
        getAnoString: function() {
            var ano_t = "1991";
            switch (_ano)
            {
                case 2:
                    ano_t = "2000";
                    break;
                case 3: 
                    ano_t = "2010";
                    break;
            }
            
            return ano_t;
        },
        setUpdateLegendaListener: function(listener) {
            _legenda_listener = listener;
        },
        setDisplayRMListener: function(listener) {
            _display_rm_listener = listener;
        },   
        loadAndDisplayCities: function() {
            _espac = ESP_MUNICIPAL;
            _clear_map();
            _check_for_cities();
        },
                
        laodAndDisplayRM: function() {
             _espac = ESP_REGIAOMETROPOLITANA;
             _clear_map();

            if(_rm_shapes == undefined)
            {
                loadingHolder.show("Carregando regiões metropolitanas...");
                $.ajax({type: "POST",dataType: "json",url: "com/mobiliti/rm/map/regmet_controller.php",
                   data: {method:'laod_rm_data'}
                }).done(_load_rm_shp);
            }
            else
            {
                _display_rm_shp(true);
            }
        },
                
                
        displayCities: function() {
            _espac = ESP_MUNICIPAL;
            _clear_map();
            _display_mun_shp(true);
        },
        displayRM: function() {
            
             _espac = ESP_REGIAOMETROPOLITANA;
             _clear_map();
             _display_rm_shp(true);
        },
        loadAndDisplayUDH: function() {
            _espac = ESP_UDH;
            _clear_map();
        },
        loadAndDisplayOP: function() {
            _espac = -1;
            _clear_map();
        },
        changeMapType: function(type) {
            if(type === "ROADMAP")
                _map.setMapTypeId(google.maps.MapTypeId.ROADMAP);
            else if(type === "SATELLITE")
                 _map.setMapTypeId(google.maps.MapTypeId.SATELLITE);
        }  
    };
    
    return _this;
}
