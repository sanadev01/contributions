<?php

namespace App\Models;

use App\Models\Commune;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;

class Recipient extends Model
{
    protected $guarded = [];
    use LogsActivity;
    protected static $logAttributes = ['*'];
    protected static $logOnlyDirty = true;
    protected static $submitEmptyLogs = false;
    public function country()
    {
        return $this->belongsTo(Country::class);
    }

    public function state()
    {
        return $this->belongsTo(State::class);
    }

    public function getFullName()
    {
        return $this->first_name.' '.$this->last_name;
    }

    public function getDocumentType()
    {
        return $this->account_type == 'individual' ? 'CPF' : 'CNPJ';
    }
    public function fullName()
    {
        return $this->first_name.' '.$this->last_name;
    }

    public function getRecipientInfo()
    {
        return $this->getFullName(). '/' .$this->tax_id;
    }

    public function getAddress()
    {
        return $this->address. ' '. $this->address2;
    }

    public function commune()
    {
        return $this->belongsTo(Commune::class);
    }
}
