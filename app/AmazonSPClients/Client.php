<?php

namespace App\AmazonSPClients;
use AmazonSellingPartner\Regions; 
use App\Jobs\BaseJob;
use App\Models\Marketplace;
use App\Models\User; 
use Exception; 

abstract class Client {

    /** @var User */
    protected $user;
    /** @var string|null */
    protected $access_token;
    /** @var string|null */
    protected $region;
    /** @var Configuration */
    protected $config;
    /** @var BaseJob */
    protected $parent_job;

    /**
     * @throws Exception
     */
    public function __construct(User $user, $access_token = null, $region = null) {
        if (!$user->sp_token && !$access_token) {
            throw new Exception('The user is unauthorized for these calls');
        }

        $this->user = $user;
        $this->access_token = $access_token;
        $this->region = $region;
    }

    /**
     * @param $parent_job
     * @return $this
     */
    public function setParentJob($parent_job): self {
        $this->parent_job = $parent_job;
        return $this;
    }

   
    /**
     * @return string
     */
    public function getRegion(): string {
        $region = $this->region ?? $this->user->getRegion();
    
        switch ($region) {
            case Marketplace::REGION_NA:
                return Regions::NORTH_AMERICA;
            case Marketplace::REGION_EU:
                return Regions::EUROPE;
            default:
                return Regions::FAR_EAST;
        }
    }
 
}
