<?php
    /**
     * Description of GraficoLinhas
     *
     * @author Valter Lorran
     */
    class GraficoLinhas {
        
        private $dados;
        private $eixo;
        private $bd;
        public static $Rows = array();


        public function __construct($lugares, $indicador) {
            $this->bd = new bd();
            $this->consultar($lugares,$indicador);
        }
        
        private function consultar($lugares,$indicador){
            $ParteInicialSQL = "";
            $filtro = "";
            switch ($lugares["e"]) {
                 case Consulta::$ESP_MUNICIPAL:
                    $ParteInicialSQL = "SELECT valor as v, fk_municipio as im,fk_ano_referencia as ka,fk_variavel as iv FROM valor_variavel_mun as vv
                                        WHERE fk_municipio IN ";
                     $filtro = "fk_municipio";
                    break;
                case Consulta::$ESP_ESTADUAL:
                    $ParteInicialSQL = "SELECT valor as v, fk_estado::text||'e' as im,fk_ano_referencia as ka,fk_variavel as iv FROM valor_variavel_estado as vv
                                        INNER JOIN variavel as v ON (vv.fk_variavel = v.id)
                                        INNER JOIN estado as e ON (e.id = vv.fk_estado)
                                        INNER JOIN regiao as r ON (r.id = e.fk_regiao)
                                        WHERE fk_estado IN ";
                     $filtro = "fk_estado";
                    break;
                case Consulta::$ESP_REGIAODEINTERESSE:
                    $ParteInicialSQL = "SELECT valor, fk_regiao_interesse,fk_ano_referencia,fk_variavel, ri.nome FROM valor_variavel_ri as vv
                                        INNER JOIN variavel as v ON (vv.fk_variavel = v.id)
                                        INNER JOIN regiao_interesse as ri ON (vv.fk_regiao_interesse = ri.id)
                                        WHERE ";
                     $filtro = "fk_regiao_interesse";
                    break;
                case Consulta::$ESP_UDH:
                    $ParteInicialSQL = "SELECT valor, fk_udh,fk_ano_referencia,fk_variavel, udh.nome FROM valor_variavel_udh as vv
                                        INNER JOIN variavel as v ON (vv.fk_variavel = v.id)
                                        INNER JOIN udh ON (udh.id = vv.fk_udh)
                                        WHERE ";
                     $filtro = "fk_udh";
                    break;
                case Consulta::$ESP_REGIAOMETROPOLITANA:
                    $ParteInicialSQL = "SELECT valor, fk_rm,fk_ano_referencia,fk_variavel, rm.nome FROM valor_variavel_rm as vv
                                        INNER JOIN variavel as v ON (vv.fk_variavel = v.id)
                                        INNER JOIN rm ON (rm.id = vv.fk_rm)
                                        WHERE ";
                    $filtro = "fk_rm";
                    break;
            }
            if(isset($lugares["l"])){
                $arr = array();
                foreach($lugares["l"] as $key=>$val){
                    $arr[] = $val["id"];
                }
                
                $SQL = "";
                $SQL = $ParteInicialSQL . " (".implode(",", $arr).") and (fk_variavel = $indicador) order by fk_ano_referencia";
                $result = $this->bd->ExecutarSQL($SQL, "im", "GraficoLinhas - 65");
                foreach($result as $val){
                    $temp = $val;
                    unset($temp["im"]);
                    unset($temp["n"]);
                    GraficoLinhas::$Rows[$val['im']]['im'] = $val['im'];
                    GraficoLinhas::$Rows[$val['im']]['vs'][$val['ka']] = $temp;
                }
            }
        }
    }

?>
