function Geral(listenerReady)
{
	lugares = new Array();
	indicadores = new Array();

	listenerLugares = null;
	listenerIndicadores = null;
        
        var areas_tematicas = new Array();

	var ready = listenerReady;

	this.listenerReady = function(value)
	{
		ready = value;
	}

	this.dispatchListeners = function(event)
	{
		listenerIndicadores(event, indicadores);
		listenerLugares(event, lugares);
	}

	/**
	* Retorna a posição do elemento adicionado
	* */
	this.addLugar = function(value)
	{
		var obj = lugares.push(value);
		
		if(listenerLugares)
                    listenerLugares('check',obj);
                else
                    alert('listener não esta definido!');
                
                
		return lugares.length - 1;
	}

	this.getLugaresPorEspacialidadeAtiva = function()
	{
		for(var i = 0; i < lugares.length; i++)
		{
			var item = lugares[i];
			
			if(item.ac == true)
				return item;
		}
	}

	this.removeLugar = function(espacialidade, id)
	{
		for(var i = 0; i < lugares.length; i++)
		{
			var item = lugares[i];

			if(item.e == espacialidade)
			{
				var locais = item.l;
				for(var k = 0 ; k < locais.length; k++)
				{
					var local = locais[k];
					if(local.id == id)
					{
						var obj = locais.splice(k,1);
						if(listenerLugares)listenerLugares('nocheck',obj);
					}
				}
			}
		}
	}
        
    this.removerIndicadoresTodos = function(){
        indicadores = new Array();
    }
    this.removeLugarTodos = function(){
        lugares = new Array();
    }
      
    this.removeTodosIndicadores = function(){
        indicadores = new Array();
    }
        
	this.getLugares = function()
	{
		return lugares;
	}
        
        this.getTotalLugares = function(){
            var total = 0;
            var instance = this;
            $.each(lugares, function(index, value) {
                  if(value.e == 7)
                  {
                      $.each(value.l, function(index , value) 
                      {
                          var at = instance.getAreaTematica(value.id);
                          total = parseInt(total) + parseInt(at.getSize());  
                      });
       
                  }
                  else
                  {
                    total += value.l.length;    
                  }
            });
            return total;
        }

        this.getLugaresString = function(){
            ob = new Array();
            c = 0;
            for(var i in lugares){
                temp = new Array();
                for(var j in lugares[i].l){
                    temp.push(lugares[i].l[j].id);
                }
                temp_ob = new Object();
                temp_ob.e = lugares[i].e;
                temp_ob.ids = temp.join(',');
                ob.push(temp_ob);
            }
            return ob;
        }
        
	this.setLugares = function(value)
	{
		lugares = value;
		if(listenerLugares)listenerLugares('reloadList',value);
	}
	
	/**
	* Retorna a posição do elemento adicionado
	* */
	this.addIndicador = function(value)
	{
		var obj = indicadores.push(value);
		if(listenerIndicadores)listenerIndicadores('check',obj);
		
		return indicadores.length - 1;
	}

	this.getIndicadores = function()
	{
		return indicadores;
	}
        
	this.getIndicadoresString = function()
	{
            temp = new Array();
            for(var i in indicadores){
                temp.push(indicadores[i].a+";"+indicadores[i].id);
            }
            return temp.join(",");
	}

	this.setIndicadores = function(value)
	{
		indicadores = value;
		if(listenerIndicadores)listenerIndicadores('reloadList',value);
	}

	this.removeIndicador = function(index)
	{
		for(var i = 0;i<indicadores.length;i++)
		{
			var item = indicadores[i];
			if(i == index)
			{
				var obj = indicadores.splice(i,1);
				if(listenerIndicadores)listenerIndicadores('nocheck',obj);
			}
		}
	}

	this.updateIndicador = function (index, ano)
	{
		for(var i = 0; i < indicadores.length; i++)
		{
			var item = indicadores[i];
			if(i == index)
				item.a = ano;
		}
	}

	this.setListenerLugares = function(listener)
	{
		listenerLugares = listener;
	}

	this.setListenerIndicadores = function(listener)
	{
		listenerIndicadores = listener;
	}

	/**
	* Retorna os indicadores da consulta, desprezando o ano e retirando os indicadores duplicados
	*/
	this.getIndicadoresDistintos = function()
	{
		var indicadoresDistintos = new Array();

		for(var i = 0; i < indicadores.length; i++)
		{
			var item = indicadores[i];
			if(indicadoresDistintos.indexOf(item.id) == -1)
				indicadoresDistintos.push(item.id);
		}

		return indicadoresDistintos;
	}

	this.removeIndicadoresExtras = function()
	{
		var novosIndicadores = indicadores.slice();
	 	var hasCheck = false;

	 	for(var i = 0; i < novosIndicadores.length; i++)
	 	{
	 		var item = novosIndicadores[i];
			
	 		if(item.c == true)
	 		{
	 			hasCheck = true;
	 		}
	 	}
	 	if(hasCheck == false)
	 	{
	 		for(var i = 0; i < novosIndicadores.length; i++)
	 		{
	 			var item = novosIndicadores[i];
			
	 			if(i == 0)
	 			{
	 				item.c = true;
	 				break;
	 			}
	 		}	
	 	}
 		indicadores = novosIndicadores;
	};

	this.removeIndicadoresDuplicados = function()
	{
		var novosIndicadores = new Array();

		for(var i = 0; i < indicadores.length; i++)
		{
			var item = indicadores[i];
			
			if(containsInArray(novosIndicadores,item) == false)
			{
				var indicador = new IndicadorPorAno();
				indicador.id = item.id;
				indicador.a = 1;
				indicador.c = false;
				indicador.desc = item.desc;
				indicador.nc = item.nc;

				if(novosIndicadores.length == 0)
					indicador.c = true;

				novosIndicadores.push(indicador);
			}
		}
		indicadores = novosIndicadores;
	}

	function containsInArray(array,value)
    {
        for(var i = 0; i < array.length; i++)
        {
            if(array[i].id == value.id)
                return true;
        } 
        return false;
    }
    
    
    
    this.getAreaTematica = function (id)
    {
        var area = null;
        for(var i = 0; i < areas_tematicas.length; i++)
        {
            if(areas_tematicas[i].getId() == id){
                area = areas_tematicas[i];
                break;
            }
	}
        return area;
    };
    
    this.AddOrUpdateAreaTematica = function (id,nome,size)
    {
        var area = null;

        
        for(var i = 0; i < areas_tematicas.length; i++)
        {
            if(areas_tematicas[i].getId() == id){
                area = areas_tematicas[i].setNome(nome).setSize(size);
                break;
            }
	}
        
        if(area == null)
        {
            area = new AreaTematica();
            area.setId(id).setNome(nome).setSize(size);
            areas_tematicas.push(area);
        }
        return area;
    };
}

function IndicadorPorAno()
{
	this.id; //indicadoor
	this.a; //ano
	this.c; //checked
	this.desc; //nome_longo
	this.nc; //nome_curto

	this.setIndicador = function(id,a,c,desc,nc)
	{
		this.id = id;
		this.a = a; 
		this.c = c; 
		this.desc = desc;
		this.nc  = nc;
	}
}

function Lugar()
{
	this.e; //espacialidade;
	this.ac; //ativo
	this.l = new Array(); //array de locais
}

function Local()
{	
	this.id;
	this.n; //nome
	this.c; //checado
	this.s; //item selecionado
}

function AreaTematica()
{
    //atributos
    var _id = 0;
    var _nome = "";
    var _size = 0;   
    
    
    //metodos
    this.setId = function(id)
    {
        _id = id;
        return this;
    };
    
    this.getId = function()
    {
       return _id;
    };
    
    this.setNome = function(nome)
    {
        _nome = nome;
        return this;
    };
    
    this.getNome = function()
    {
        return _nome;
    };
    
    this.setSize = function(size)
    {
        _size = size;
        return this;
    };
    
    this.getSize = function()
    {
        return _size;
    };
}
