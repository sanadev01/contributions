<?php


namespace App\Services\Anjun\Services;

use Exception;

class AnjunError
{
    private $data;
    private $errorCode = [
        '001'    => 'Incorrect or invalid account',
        '002'    => 'Incorrect or invalid token.',
        '003'    => 'The account is locked and cannot be used.',
        '004'    => 'login fail.',
        '005'    => 'Order number already exists.',
        '006'    => 'Tracking number already exists.',
        '007'    => 'Application failed, please contact the docking personnel for handling.',
        '008'    => 'The tracking number has been exhausted. Please wait for supplement',
        '009'    => 'Line code does not exist or has been closed',
        '010'    => 'Failed to submit, please try to submit again',
        '011'    => 'The order has been created. Please do not repeat the operation..',
        '012'    => 'Order does not exist.',
        '013'    => 'lease check the local cookies environment. If there is no problem, please try several times.',
        '014'    => 'System database failure, please wait.',
        '015'    => 'Order data has special characters: #&’.',
        '016'    => 'The order already exists in the warehouse packaging system.',
        '023'    => 'E-system has error..',
        '028'    => 'MS database connection failed, please contact it.',
        '040'    => 'Order number cannot be empty`.',
        '041'    => 'Recipient name cannot be empty.',
        '042'    => 'Recipient phone cannot be empty.',
        '043'    => 'Postal code cannot be empty.',
        '044'    => 'Country cannot be empty.',
        '045'    => 'Province cannot be empty.',
        '046'    => 'City cannot be empty-.',
        '047'    => 'Address cannot be empty.',
        '048'    => 'Weight cannot be empty.',
        '049'    => 'Product value cannot be empty.',
        '050'    => 'Product quantity cannot be empty.',
        '051'    => 'Product english name cannot be empty',
        '052'    => 'Product weights must be string & > 0.',
        '054'    => 'Product value exceeds the range set by the system.',
        '055'    => 'Warehost system connection failed.',
        '057'    => 'Line code closing..',
        '058'    => 'Postal code does not correspond to city.',
        '060'    => 'Product url is cannot be empty.',
        '065'    => 'Getting tracking number is fails,Try again later pls.',
        '066'    => 'Brazilian personal tax No. cannot be empty.',
        '067'    => 'State identification failed.',
        '068'    => 'Products imported from Brazil shall not be less than 7 US dollars.',

    ];

    public function __construct($data)
    {
        $this->data = $data;
    }

    public function getErrors()
    {
        $danhao = null;
        try {
            $danhao = optional($this->datao)->danha;
            return $danhao. ' : ' . $this->errorCode[$this->data->danhao];
        } catch (Exception $e) {
            if($danhao)
            return $danhao. ':' . 'Request Can not proceed.' . $this->data->msg;
            if($this->data->msg){
            return  'Request Can not proceed.' . $this->data->msg;
            }
        }
    }

    public function __toString()
    {
        return $this->getErrors();
    }
}
