<?php

namespace Drupal\fontawesome\Controller;

use Drupal\Core\Controller\ControllerBase;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Drupal\Component\Utility\Tags;
use Drupal\Component\Render\FormattableMarkup;
use Drupal\fontawesome\FontAwesomeManagerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Defines a route controller for entity autocomplete form elements.
 */
class AutocompleteController extends ControllerBase {

  /**
   * Drupal Font Awesome manager service.
   *
   * @var \Drupal\fontawesome\FontAwesomeManagerInterface
   */
  protected $fontAwesomeManager;

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    $fontAwesomeManager = $container->get('fontawesome.font_awesome_manager');
    return new static($fontAwesomeManager);
  }

  /**
   * {@inheritdoc}
   */
  public function __construct(FontAwesomeManagerInterface $fontAwesomeManager) {
    $this->fontAwesomeManager = $fontAwesomeManager;
  }

  /**
   * Handler for autocomplete request.
   */
  public function handleAutocomplete(Request $request) {
    $results = [];

    // Get the typed string from the URL, if it exists.
    if ($input = $request->query->get('q')) {
      $typed_string = Tags::explode($input);
      $typed_string = mb_strtolower(array_pop($typed_string));

      // Load the icon data so we can check for a valid icon.
      $iconData = $this->fontAwesomeManager->getIconsWithCategories();

      // Check each icon to see if it starts with the typed string.
      foreach ($iconData as $thisIcon) {
        // If the string is found.
        if (strpos($thisIcon['name'], $typed_string) === 0 || in_array($typed_string, $thisIcon['search_terms'])) {
          $iconRenders = [];
          // Loop over each style.
          foreach ($thisIcon['styles'] as $style) {

            // Determine the prefix.
            switch ($style) {

              case 'brands':
                $iconPrefix = 'fab';
                break;

              case 'light':
                $iconPrefix = 'fal';
                break;

              case 'regular':
                $iconPrefix = 'far';
                break;

              case 'duotone':
                $iconPrefix = 'fad';
                break;

              default:
              case 'solid':
                $iconPrefix = 'fas';
                break;
            }
            // Render the icon.
            $iconRenders[] = new FormattableMarkup('<i class=":prefix fa-:icon fa-fw fa-2x"></i> ', [
              ':prefix' => $iconPrefix,
              ':icon' => $thisIcon['name'],
            ]);
          }

          $results[] = [
            'value' => $thisIcon['name'],
            'label' => implode('', $iconRenders) . $thisIcon['name'],
          ];
        }
      }
    }

    return new JsonResponse($results);
  }

}
