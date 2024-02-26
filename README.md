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
# If you want override default migrations
php artisan vendor:publish --provider="GoCardlessPayment\ServiceProvider" --tag="migrations"
```

Place required credentials:

```dotenv
GOCARDLESS_ACCESS_TOKEN="sandbox_XxxxXXXxxxxXXXXxx-xXxxxXXx_XX-xxxX"
GOCARDLESS_WEBHOOK_ENDPOINT_SECRET="XXXXxxxxXXXXXxxxXXXXxxxXXXXxx"
```

### Customize default configuration

Amends in any app service provider

```php

public function register()
{
    // Do not run default migrations
    \GoCardlessPayment\GoCardlessPayment::ignoreMigrations();
    // Do not use default routes provided by package
    \GoCardlessPayment\GoCardlessPayment::ignoreRoutes();
    
    // Override repository to get local customer
    $this->app->singleton(LocalCustomerRepository::class, function (Application $app) {
        return new MyCustomLocalCustomerRepository();
    });
    
    // Override Api functionality
    $this->app->bind(Api::class, function (Application $app) {
        return new CustomApi();
    });
}
```

## Usage

**Note**: to use api requests your server IP should be in country covered by GoCardless service, in other case api
requests will fail (you can try use VPN for local test/development)

## Create mandate checkput url

As first step server should
generate [mandate](https://developer.gocardless.com/billing-requests/setting-up-a-dd-mandate) url and redirect user
to this url.

```php
use GoCardlessPayment\MandateCheckout\BillingRequest;
use GoCardlessPayment\MandateCheckout\BillingRequestFlow;
use GoCardlessPayment\MandateCheckout\MandateCheckoutPage;
use GoCardlessPayment\MandateCheckout\MandateRequest;
use GoCardlessPayment\MandateCheckout\Metadata;
use GoCardlessPayment\MandateCheckout\ReturnUrls;

/** @var \GoCardlessPayment\Contracts\GoCardlessCustomer $user */
$user = getCurrentUser();

$url = MandateCheckoutPage::make(
            BillingRequest::make()
                ->mandateRequest(
                    MandateRequest::make()
                        ->scheme('bacs')
                        ->verifyWhenAvailable()
                ),
            BillingRequestFlow::make()
                ->returnUrls(ReturnUrls::make(route('example.route')))
        )->useCustomer($user)->requestCheckoutUrl();

return Redirect::to($url);
```

Or fully managed request:

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
                )->metadata(
                    Metadata::make()
                        ->add('crm_user', $user->getKey())
                )->links(Links::make()->addCustomer($user->gocardlessKey())),
            BillingRequestFlow::make()
                ->prefilledCustomer(
                    PrefilledCustomer::make()
                        ->givenName($user->first_name)
                        ->familyName($user->last_name)
                        ->email($user->email)
                        ->postalCode($user->postalcode)
                        ->addressLine1($user->street)
                        ->addressLine2($user->locality)
                        ->city($user->town)
                        ->region($user->county)
                        ->countryCode($user->country_code)
                )
                ->returnUrls(ReturnUrls::make(route('example.route')))
        )->requestCheckoutUrl();

return Redirect::to($url);
```

### Webhook installation

Receive [webhook](https://developer.gocardless.com/resources/testing-webhooks-cli) about created mandate and store it in
database.

#### Local server webhooks

Firstly install [cli](https://developer.gocardless.com/developer-tools/gc-cli) on your laptop.

Then you can run listener with forwarding to your local site. Example:

```shell
gc listen --forward http://localhost/gocardless/webhook
# Or to jus preview webhooks content without real processing by app you can use "simple" listen method:
# gc listen
```

## Credits

- [![Think Studio](https://yaroslawww.github.io/images/sponsors/packages/logo-think-studio.png)](https://think.studio/) 
