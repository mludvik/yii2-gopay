# yii2-gopay

Yii2 integration for official [GoPay's PHP SDK for Payments REST API](https://github.com/gopaycommunity/gopay-php-api).

## Installation

The preferred way to install this extension is through [composer](https://getcomposer.org/download/).

Either run

```
php composer.phar require mludvik/yii2-gopay:"~1.0"
```

or add

```
"mludvik/yii2-gopay": "~1.0"
```

to the require section of your `composer.json`.

## Usage

Before you start using this extension, it is recommended to read [GoPay REST API documentation](https://doc.gopay.com/).

### GoPayComponent

`GoPayComponent` is thin wrapper over GoPay PHP SDK.

Enable it by adding respective component configuration.

```
'components' => [

  ...

  'goPay' => [
    'class' => 'mludvik\gopay\GoPayComponent',
    'config' => [
      'goid' => '...',
      'clientId' => '...',
      'clientSecret' => '...',
      'isProductionMode' => YII_ENV_PROD,
      'scope' => TokenScope::ALL,
      'language' => Language::ENGLISH,
      'timeout' => 30,
    ],
  ],

  ...
]
```

Then you can call any method you would call on [Payments](https://github.com/gopaycommunity/gopay-php-api/blob/3a9eb4b0480599fec687bdc594973b62bb9f8c33/src/Payments.php) object.

```
use Yii;

Yii::$app->goPay->createPayment([...]);
Yii::$app->goPay->getStatus(...);
...
```

### GoPayAsset

`GoPayAsset` imports front-end GoPay dependencies to your pages.
It is sensitive to `YII_EVN` constant — loads production version when `YII_ENV === 'prod'`, test version otherwise.
There are multiple ways how to use it.

1. To load it on all pages, register it in `AppAsset`.

```
class AppAsset extends AssetBundle {

  ...

  public $depends = [
    ...
    'mludvik\gopay\GoPayAsset',
  ];
}
```

2. To load it on specific page, register it in respective view.

```
use mludvik\gopay\GoPayAsset;

GoPayAsset::register($this);
```

3. When using `GoPayForm` described below, you do not need to register `GoPayAsset` at all. It will be registered automatically on pages where `GoPayForm` is rendered.

### GoPayForm

`GoPayForm` is widget created to help you build checkout page. Use it if you like or develop your own widget with the help of `GoPayComponent` and `GoPayAsset`.

`GoPayForm` is basically `ActiveForm` with 2 differences.

- It uses asynchronous POST request to submit data to controller and then redirects user to payment gateway.
- It has static method `response`, which can be used in controller to build expected response to form submission.

## Example

First of all, you will need form to collect user payment preferences (eg. preferred payment method). It is also a good place to put some business logic in.

```
namespace app\models;

use Yii;
use yii\base\Model;

class CheckoutForm extends Model {

  public $paymentMethod;

  ...

  public function createPayment() {
    // Build request based on form's data.
    $request = [...];

    // Establish payment on GoPay side.
    return Yii::$app->goPay->createPayment($request);
  }
}
```

Then create checkout action.

```
namespace app\controllers;

use Yii;
use yii\web\Controller;
use mludvik\gopay\GoPayForm;
use app\models\CheckoutForm;

class PaymentController extends Controller {

  ...

  public function actionCheckout() {
    $model = CheckoutForm;

    if(Yii::$app->request->isAjax) {
      if($model->load(Yii::$app->request->post()) && $model->validate()) {
        $response = $model->createPayment();
        return GoPayForm::response($response);
      } else {
        return GoPayForm::response(null);
      }
    }

    return $this->render('checkout', ['model' => $model]);
  }
}

```

Finally, create the view.

```
<?php use mludvik\gopay\GoPayForm; ?>

<?php $form = GoPayForm::begin(); ?>

  ...

  <?= Html::submitButton('Pay') ?>
<?php GoPayForm::end(); ?>
```
