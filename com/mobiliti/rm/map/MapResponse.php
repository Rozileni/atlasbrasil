<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of MapResponse
 *
 * @author atwork
 */
class MapResponse {
    
    
    public $places;
    public $legend;
    
    
    public function setPlaces($places) {
        $this->places = $places;
    }
    
    public function getPlaces() {
        return $this->places;
    }
    
    public function setLegend($legend) {
        $this->legend = $legend;
    }
    
    public function getLegend() {
        return $this->legend;
    }
}

?>
