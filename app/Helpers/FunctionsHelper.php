<?php

function multipleEmpty(){
    $response = false;
    foreach(func_get_args() as $arg){
        if(empty($arg)){
            $response = true;
        }
    }
    return $response;
}

?>
