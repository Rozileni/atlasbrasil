<?php
    /**
     * Description of GraficoDispersao
     *
     * @author Valter Lorran
     */
    class GraficoDispersao {
        
        private $dados;
        private $eixo;
        private $bd;
        
        public function __construct($lugares, $indicadores) {
            $this->eixo_x = $eixoX;
            $this->eixo_y = $eixoY;
            $this->eixo_size = $eixoSize;
            $this->eixo_color = $eixoColor;
            $this->bd = new bd();
            $this->consultar($lugares,$indicadores);
        }
        
        public function draw(){
            $draw = array();
            $counter = 0;
            $draw[$counter][] = 'Lugar';
            $draw[$counter][] = $this->eixo["X"];
            $draw[$counter][] = $this->eixo["Y"];
            $draw[$counter][] = $this->eixo["Color"];
            $draw[$counter][] = $this->eixo["Size"];
            foreach($this->dados as $d){
                $counter++;
                $draw[$counter] = $d->draw();
            }
            echo json_encode($draw);
        }
        
        private function consultar($lugares,$indicadores){
            $ParteInicialSQL = "";
            $filtro = "";
            switch ($lugares["e"]) {
                 case Consulta::$ESP_MUNICIPAL:
                    $ParteInicialSQL = "SELECT valor, fk_municipio,fk_ano_referencia as id_a,fk_variavel as id_v, m.nome as nome,e.uf as uf FROM valor_variavel_mun as vv
                                        INNER JOIN variavel as v ON (vv.fk_variavel = v.id)
                                        INNER JOIN municipio as m ON (m.id = vv.fk_municipio)
                                        INNER JOIN estado as e ON (e.id = m.fk_estado)
                                        INNER JOIN regiao as r ON (r.id = e.fk_regiao)
                                        WHERE ";
                     $filtro = "fk_municipio";
                    break;
                case Consulta::$ESP_ESTADUAL:
                    $ParteInicialSQL = "SELECT valor, fk_estado,fk_ano_referencia as id_a,fk_variavel as id_v, e.nome FROM valor_variavel_estado as vv
                                        INNER JOIN variavel as v ON (vv.fk_variavel = v.id)
                                        INNER JOIN estado as e ON (e.id = vv.fk_estado)
                                        INNER JOIN regiao as r ON (r.id = e.fk_regiao)
                                        WHERE ";
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
            $vars = array();
            $identify = array();
            $variaveis = array();
            foreach($indicadores as $key=>$val){
                $vars[] = "(fk_variavel = {$val['id']} and fk_ano_referencia = {$val['ano']})";
                $identify["{$val['id']}_{$val['ano']}"] = $val['eixo'];
                $variaveis["id"][] = $val['id'];
                $variaveis["eixo"][] = $val['eixo'];
            }
            $whereVars = '('.implode(' OR ',$vars).')';
            
            $lugs = array();
            foreach($lugares["l"] as $key=>$val){
                $lugs[] = "($filtro = {$val['id']})";
            }
            $whereLugs = '('.implode(' OR ',$lugs).')';
            
            $ParteInicialSQL .= $whereVars . " AND " . $whereLugs . " ORDER BY nome, fk_variavel, fk_ano_referencia";
            
            $Resposta = pg_query($this->bd->getConexaoLink(), $ParteInicialSQL) or die ("Nao foi possivel executar a consulta! ");
            
            while ($Linha = pg_fetch_assoc($Resposta))
            {
                if(isset($this->dados[$Linha[$filtro]])){
                    $this->dados[$Linha[$filtro]]->addEixo($Linha['valor'], $identify["{$Linha['id_v']}_{$Linha['id_a']}"]);
                }else{
                    $lugar = new Lugar($Linha["nome"]);
                    $this->dados[$Linha[$filtro]] = new Data($lugar);
                    $this->dados[$Linha[$filtro]]->addEixo($Linha['valor'], $identify["{$Linha['id_v']}_{$Linha['id_a']}"]);
                }
            }
            
            $SQL = "select nomecurto, id from variavel where id IN (".implode(',',$variaveis['id']).")";
            $Resposta = pg_query($this->bd->getConexaoLink(), $SQL) or die ("Nao foi possivel executar a consulta! ");
            
            
            while ($Linha = pg_fetch_assoc($Resposta))
            {
                foreach($indicadores as $key=>$v){
                    if($Linha["id"] == $v['id']){
                        $this->eixo[$v['eixo']] = $Linha["nomecurto"];
                    }
                }
            }
        }
    }
    
    class Lugar{
        
        private $nome;
        
        public function __construct($nome){
            $this->nome = $nome;
        }
        
        public function getNome(){
            return $this->nome;
        }
    }
    
    class Data{
        
        private $lugar;
        private $eixo;
        
        public function __construct($lugar){
            $this->lugar = $lugar;
        }
        
        public function draw(){
            $draw = array();
            $draw[] = $this->lugar->getNome();
            $draw[] = (float)$this->eixo["X"];
            $draw[] = (float)$this->eixo["Y"];
            $draw[] = (float)$this->eixo["Color"];
            $draw[] = (float)$this->eixo["Size"];
            return $draw;
        }
        
        public function addEixo($valor,$eixo){
            $this->eixo[$eixo] = $valor;
        }
    }

?>
