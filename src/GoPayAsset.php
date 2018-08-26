<?php
/**
 * @author Martin Ludvik <matolud@gmail.com>
 * @copyright Copyright (c) 2018 Martin Ludvik
 * @license https://opensource.org/licenses/MIT MIT License
 */

namespace mludvik\gopay;

use yii\web\AssetBundle;

class GoPayAsset extends AssetBundle {

  public $js = [
    YII_ENV_PROD ?
      'https://gate.gopay.cz/gp-gw/js/embed.js' :
      'https://gw.sandbox.gopay.com/gp-gw/js/embed.js',
  ];
}
