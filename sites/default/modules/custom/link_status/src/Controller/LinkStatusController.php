<?php
namespace Drupal\link_status\Controller;
use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Config\ConfigFactoryInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Httpfoundation\RequestStack;
use Drupal\node\Entity\Node;
use Drupal\node\NodeInterface;
use DOMDocument;
/**
 *  Class LinkStatusController.
 */
class LinkStatusController extends ControllerBase {
  /**
   *  Symfony\Component\HttpFoundation\RequestStack Definition.
   *
   * @var \Symfony\Component\Httpfoundation\RequestStack
   */
  protected $requestStack;
  /**
   * Constructs a new object.
   */
  public function __contruct(RequestStack $request_stack) {
     $this->requestStack = $request_stack;
  }
  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('request_stack'),
      $container->get('config.factory')
    );
  }


}


