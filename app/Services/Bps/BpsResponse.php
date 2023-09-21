<?php


namespace App\Services\Bps;


use function array_push;
use function count;
use function implode;
use function is_array;
use function is_object;
use function json_encode;

class BpsResponse
{
    protected $success = false;
    protected $data = null;
    protected $errors = [];

    public function __construct($data)
    {
        $this->parseData($data);
    }

    protected function parseData($data)
    {
        if ( !$data || !is_object($data)){
            array_push($this->errors,"Uknown Error: ".json_encode($data));
            return;
        }

        if ( !$data->data){
            $this->parseErrors($data->errors);
            return;
        }

        $this->success = true;
        $this->data = $data->data;
    }

    protected function parseErrors($errorMessages)
    {
        if (is_array($errorMessages)){
            array_push($this->errors,implode($errorMessages));
            return;
        }

        if ( is_object($errorMessages) && !isset($errorMessages->messages) && count($errorMessages->messages) <=0){
            array_push($this->errors,"Uknown Error");
            return;
        }

        foreach ($errorMessages->messages as $message) {
            array_push($this->errors, $message->message);
        }
    }

    /**
     * @return array
     */
    public function getErrors(): array
    {
        return $this->errors;
    }

    /**
     * @return null|Object
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * @return bool
     */
    public function isSuccess(): bool
    {
        return $this->success;
    }

}
