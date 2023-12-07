<?php

namespace App\Console\Commands;

use AmazonSellingPartner\Exception\ApiException;
use App\ApiClients\SellingPartner\SellersApiClient;
use App\Models\User;
use Exception;
use Illuminate\Console\Command;
use Psr\Http\Client\ClientExceptionInterface;

class GetAPITokens extends Command {

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:tokens {user_id}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This command helps fetch tokens for SP-API test calls';

	/** @var User */
	protected $user;

	/**
	 * Execute the console command.
	 *
	 * @return void
	 * @throws ClientExceptionInterface
	 * @throws ApiException
	 * @throws \JsonException
	 * @throws Exception
	 */
    public function handle() {
		$this->user = User::query()->find($this->argument('user_id'));

//		if ($this->option('sp')) {
			$client = new SellersApiClient($this->user);
			$sdk = $client->getSellingPartnerSDK();
			$config = $sdk->configuration();

			console_log('Access Key: ', $config->accessKey());
			console_log('Secret Key: ', $config->secretKey());
			console_log('AWS Region: ', $client->getRegion());
			console_log('Service Name: ', 'execute-api');
			console_log('Session Token: ', $config->securityToken());
			console_log('Access Token: ', $client->getAccessToken($sdk)->token()); // x-amz-access-token
//		}

    }

}
