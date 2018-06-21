<?php
namespace App\Providers;

use App\Utilities\SignatureHelper;
use Illuminate\Support\ServiceProvider;

class SignaturehelperServiceProvider extends ServiceProvider {

	public function register() {
		$this->app->bind('signaturehelper', function ($app) {
			return new SignatureHelper();
		});
	}
}
