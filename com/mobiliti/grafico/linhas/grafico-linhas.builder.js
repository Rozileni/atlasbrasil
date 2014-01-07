/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

function GraficoLinhas(){
    this.data;
    this.consultar = function (eixoX){
        loadingHolder.show("Carregando dados...");
        $.ajax({
            type: 'post',
            url:'com/mobiliti/grafico/linhas/grafico-linhas.controller.php',
            data:{'json_lugares':geral.getLugares(),'indicador' : eixoX},
            success: function(retorno){
                this.data = jQuery.parseJSON(retorno);
                dataGraficoDispersao = this.data;
                drawChartLinha(dataGraficoDispersao);
                loadingHolder.dispose();
            }
        });
    }
}

//==============================================================================
//cast
//==============================================================================
    var graficoLinhas = new GraficoLinhas();