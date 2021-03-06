<?php


namespace Drupal\product_redirect\EventSubscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Drupal\Core\Url;
/**
 * Redirect .html pages to corresponding Node page.
 */
class ProductRedirectSubscriber implements EventSubscriberInterface {

  /** @var int */
  private $redirectCode = 301;

  /**
   * Redirect pattern based url
   * @param GetResponseEvent $event
   */
  public function customRedirection(GetResponseEvent $event) {

    $request = \Drupal::request();
    $requestUrl = $request->server->get('REQUEST_URI', null);

    $parse = explode('/', $requestUrl);

    if ($parse[1] == 'machines' &&
      preg_match('/[0-9]/', $parse[2])) {
      print '<pre>';
      print $parse[2] . "<br>";

        $nid = db_select('node__field_stock_number', 'w')
            ->fields('w', array('entity_id'))
            ->condition('field_stock_number_value', $parse[2], '=')
            ->execute()
            ->fetchObject();

      print $nid->entity_id . "<br>";
      $alias = \Drupal::service('path.alias_manager')->getAliasByPath('/node/' . $nid->entity_id);
      print $alias . "<br>";

      // $response = new RedirectResponse($alias, $this->redirectCode);
      // $response->send();
      // exit(0);

      print '</pre>';
    }



  }

  /**
   * Listen to kernel.request events and call customRedirection.
   * {@inheritdoc}
   * @return array Event names to listen to (key) and methods to call (value)
   */
  public static function getSubscribedEvents() {
    $events[KernelEvents::REQUEST][] = array('customRedirection');
    return $events;
  }
}
