# GoCardless payment implementation for laravel

Highly targeted package created for specific usage case and not support/maintain all options of GoCardless. Package is
based on official php [package](https://github.com/gocardless/gocardless-pro-php)

## Installation

```shell
composer require think.studio/laravel-gocardless-payment
```

Optionally you can publish the config file with:

```shell
php artisan vendor:publish --provider="GoCardlessPayment\ServiceProvider" --tag="config"
```

```dotenv
GOCARDLESS_ACCESS_TOKEN="sandbox_XxxxXXXxxxxXXXXxx-xXxxxXXx_XX-xxxX"
```

## Usage

**Note**: to use api requests your server IP should be in country covered by GoCardless service, in other case api
requests will fail (you can try use VPN for local test/development)

1. As first step server should
   generate [mandate](https://developer.gocardless.com/billing-requests/setting-up-a-dd-mandate) url and redirect user
   to this url.

```php
use GoCardlessPayment\MandateCheckout\BillingRequest;
use GoCardlessPayment\MandateCheckout\BillingRequestFlow;
use GoCardlessPayment\MandateCheckout\MandateCheckoutPage;
use GoCardlessPayment\MandateCheckout\MandateRequest;
use GoCardlessPayment\MandateCheckout\Metadata;
use GoCardlessPayment\MandateCheckout\ReturnUrls;

$url = MandateCheckoutPage::make(
            BillingRequest::make()
                ->mandateRequest(
                    MandateRequest::make()
                        ->scheme('bacs')
                        ->verifyWhenAvailable()
                        ->metadata(Metadata::make()->add('site_user_id', '222')),
                ),
            BillingRequestFlow::make()
                ->returnUrls(ReturnUrls::make('https://company.com/success', 'https://company.com/cancel'))
        )->requestCheckoutUrl();
```

## Credits

- [![Think Studio](https://yaroslawww.github.io/images/sponsors/packages/logo-think-studio.png)](https://think.studio/) 
