<?php

namespace Drupal\private_message_adjust\Controller;

use Symfony\Component\HttpFoundation\JsonResponse;

// use for token testing
use Drupal\Core\Url;


class PrivateMessageAdjustController {

  public function privateMessageAdjust() {

    return new JsonResponse(
      [
        'data' => $this->getResults(),
        'method' => 'GET'
      ]
    );
  }

  private function getResults(){
    $items = [
      ['name' => 'Article 1'],
      ['name' => 'Article 2'],
      ['name' => 'Article 3'],
      ['name' => 'Article 4'],
      ['name' => 'Article 5'],
    ];
    return $items;
  }
}
?>