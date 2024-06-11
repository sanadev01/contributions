<?php

namespace App\Services\TotalExpress;

use App\Models\Warehouse\Container;

class HandleError
{

    private $response;
    public function __construct($response)
    {
        $this->response = $response;
    }
    function removeLastBR($str)
    {
        $lastSpacePos = strrpos($str, ' ');

        if ($lastSpacePos !== false) { 
            return substr($str, 0, $lastSpacePos);
        } else { 
            return '';
        }
    }
    public function __toString()
    {
        try {
            $decode_response = json_decode($this->response);
                if(!in_array($decode_response->status , [200,422,'ERROR'])){
                    return $decode_response->status. ' '. optional($decode_response)->error;
                }
            $messages = '';  
            foreach ($decode_response->messages as $errors) {

                try{
                    foreach ($errors as $key => $params){
                        foreach ($params as $param){
                            $messages .= $key . ' => ' . $param . ' <br>';
                        }
                    }
                }catch(\Throwable $e){
                    return $errors;
                }
            } 
            return  $this->removeLastBR($messages);;
        } catch (\Throwable $e) {
            return 'An error occurred during ' . $e->getMessage();
        }
    }
}
