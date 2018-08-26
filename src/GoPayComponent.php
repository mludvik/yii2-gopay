<?php
/**
 * @author Martin Ludvik <matolud@gmail.com>
 * @copyright Copyright (c) 2018 Martin Ludvik
 * @license https://opensource.org/licenses/MIT MIT License
 */

namespace mludvik\gopay;

use yii\base\Component;
use GoPay;

class GoPayComponent extends Component {

  private $goPay;

  public $config;

  public function init() {
    $this->goPay = GoPay\payments($this->config);
  }

  function __call($name, $arguments) {
    return $this->goPay->$name(...$arguments);
  }
}
