<?php

namespace App\AmazonSPClients;

use AmazonSellingPartner\AccessToken;
use AmazonSellingPartner\Configuration;
use AmazonSellingPartner\Exception\ApiException;
use AmazonSellingPartner\Regions;
use AmazonSellingPartner\SellingPartnerSDK;
use App\Jobs\BaseJob;
use App\Models\Marketplace;
use App\Models\User;
use Buzz\Client\Curl;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Str;
use JsonException;
use Nyholm\Psr7\Factory\Psr17Factory;
use Psr\Http\Client\ClientExceptionInterface;
use Psr\Log\NullLogger;
use STS\Backoff\Backoff;
use STS\Backoff\Strategies\ExponentialStrategy;

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
     * @return SellingPartnerSDK
     * @throws ApiException
     * @throws ClientExceptionInterface
     * @throws JsonException
     */
    public function getSellingPartnerSDK(): SellingPartnerSDK {
        $factory = new Psr17Factory();
        $client = new Curl($factory);
        $logger = new NullLogger();

        $configuration = new Configuration(
            config('services.sp-api.SP_APP_CLIENT_ID'),
            config('services.sp-api.SP_APP_CLIENT_SECRET')
        );

        return SellingPartnerSDK::create($client, $factory, $factory, $configuration, $logger);
    }

    /**
     * @param SellingPartnerSDK $sdk
     * @return AccessToken
     * @throws ApiException
     * @throws ClientExceptionInterface
     */
    public function getAccessToken(SellingPartnerSDK $sdk): AccessToken {
        if ($this->access_token) {
            return new AccessToken($this->access_token, '', '', 0, 0);
        }

        if (!$this->user->sp_token->expires_at->isFuture()) {
            $access_token = $sdk->oAuth()->exchangeRefreshToken($this->user->sp_token->refresh_token);

            $siblings = $this->user->siblings;

            foreach ($siblings as $sibling) {
                $sibling->sp_token->access_token = $access_token->token();
                $sibling->sp_token->refresh_token = $access_token->refreshToken();
                $sibling->sp_token->expires_at = Carbon::now()->addSeconds(3000); // deliberately kept 600 secs less
                $sibling->sp_token->last_updated_at = Carbon::now();
                $sibling->sp_token->save();
            }

            $this->user = $this->user->fresh();

            return $access_token;
        }

        return new AccessToken($this->user->sp_token->access_token, '', '', 0, 0);
    }

    /**
     * @return string
     */
    public function getRegion(): string {
        return match ($this->region ?? $this->user->getRegion()) {
            Marketplace::REGION_NA => Regions::NORTH_AMERICA,
            Marketplace::REGION_EU => Regions::EUROPE,
            default => Regions::FAR_EAST,
        };
    }

    /**
     * @param $callback
     * @return mixed|null
     * @throws Exception
     */
    protected function sendRequest($callback) {
        return (new Backoff(5, new ExponentialStrategy(10000)))
            ->setDecider(function ($retry, $maxAttempts, $result = null, $exception = null) {
                if ($retry >= $maxAttempts && !is_null($exception)) {
                    throw  $exception;
                }

                $is_throttling = !is_null($exception) && $exception->getCode() === 429;
                $tokens_expired = !is_null($exception) && Str::contains($exception->getMessage(), ['The security token included in the request is expired']);

                return $retry < $maxAttempts && ($is_throttling || $tokens_expired);
            })
            ->setErrorHandler(function ($exception, $attempt, $maxAttempts) {
                console_log('On run ' . $attempt . ' we hit a problem: ' . $exception->getMessage());
            })
            ->run(function () use ($callback) {
                return $callback($this->getSellingPartnerSDK());
            });
    }
}
