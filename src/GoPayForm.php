<?php
/**
 * @author Martin Ludvik <matolud@gmail.com>
 * @copyright Copyright (c) 2018 Martin Ludvik
 * @license https://opensource.org/licenses/MIT MIT License
 */

namespace mludvik\gopay;

use Yii;
use yii\web\Response;
use yii\bootstrap\ActiveForm;

class GoPayForm extends ActiveForm {

  public static function response($response) {
    if($response !== null && $response->hasSucceed()) {
      $gatewayUrl = $response->json['gw_url'];
      return self::asJson(200, ['gatewayUrl' => $gatewayUrl]);
    } else {
      return self::asJson(500, null);
    }
  }

  private static function asJson($statusCode, $data) {
    $response = Yii::$app->getResponse();
    $response->format = Response::FORMAT_JSON;
    $response->statusCode = $statusCode;
    $response->data = $data;
    return $response;
  }

  public function init() {
    parent::init();
    $this->validateOnSubmit = false;
  }

  public function run() {
    $this->registerGoPayClientScript();
    return parent::run();
  }

  private function registerGoPayClientScript() {
    $id = $this->options['id'];
    $view = $this->getView();
    $errorMessage = 'Error occured while processing payment.'; // TODO: Yii::t('gopay', ...)

    GoPayAsset::register($view);

    $view->registerJs("jQuery('#{$id}').submit(function(evt) {
      evt.preventDefault();

      const el = jQuery(this);
      const action = el.attr('action');

      jQuery.ajax({
        url  : action,
        type : 'POST',
        data : el.serialize()

      }).done(function(createResult) {
        _gopay.checkout({gatewayUrl: createResult.gatewayUrl, inline: true}, function(checkoutResult) {
          const returnUrl = checkoutResult.url;
          window.location.replace(returnUrl);
        });

      }).fail(function() {
        alert('{$errorMessage}');
      });
    });");
  }
}
