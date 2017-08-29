<?php
/**
 * Description of config
 *
 * @author Cristiano
 */

session_start();

class Controller {    
    public $conectar;

    public function __construct() {
        return $this->conectar = mysqli_connect("localhost", "root", "", "hcode_shop");
    }
    
    public function query($string_query){
        return mysqli_query($this->conectar, $string_query);
    }

    public function select($string_query){
        $result = $this->query($string_query);
        
        $dados = array();
    
        while ($row = mysqli_fetch_array($result)) {
            //Nota nº 9: dev-guide
            foreach($row as $key => $value){
                $row[$key] = utf8_encode($value);
            }
            
            //A função array_push adiciona todo conteúdo 
            //de uma variável em um array declarado
            array_push($dados, $row);
        }
        
        //Remove a variável result da memória
        unset($result);
        
        return $dados;
    }
    
    public function __destruct() {
        mysqli_close($this->conectar);
    }
}
