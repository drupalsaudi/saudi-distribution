<?php

namespace Drupal\flexslider\Entity;

use Drupal\Core\Config\Entity\ConfigEntityBase;
use Drupal\flexslider\FlexsliderDefaults;
use Drupal\flexslider\FlexsliderInterface;

/**
 * Defines the Flexslider entity.
 *
 * @ConfigEntityType(
 *   id = "flexslider",
 *   label = @Translation("FlexSlider optionset"),
 *   handlers = {
 *     "list_builder" = "Drupal\flexslider\Controller\FlexsliderListBuilder",
 *     "form" = {
 *       "add" = "Drupal\flexslider\Form\FlexsliderForm",
 *       "edit" = "Drupal\flexslider\Form\FlexsliderForm",
 *       "delete" = "Drupal\flexslider\Form\FlexsliderDeleteForm"
 *     }
 *   },
 *   config_prefix = "optionset",
 *   admin_permission = "administer flexslider",
 *   entity_keys = {
 *     "id" = "id",
 *     "label" = "label",
 *     "status" = "status"
 *   },
 *   links = {
 *     "canonical" = "/admin/config/media/flexslider/{flexslider}",
 *     "edit-form" = "/admin/config/media/flexslider/{flexslider}/edit",
 *     "enable" = "/admin/config/media/flexslider/{flexslider}/enable",
 *     "disable" = "/admin/config/media/flexslider/{flexslider}/disable",
 *     "delete-form" = "/admin/config/media/flexslider/{flexslider}/delete",
 *     "collection" = "/admin/config/media/flexslider"
 *   },
 *   config_export = {
 *     "id",
 *     "label",
 *     "options",
 *   }
 * )
 */
class Flexslider extends ConfigEntityBase implements FlexsliderInterface {
  /**
   * The Flexslider optionset ID.
   *
   * @var string
   */
  protected $id;

  /**
   * The Flexslider optionset label.
   *
   * @var string
   */
  protected $label;

  /**
   * The Flexslider optionset options.
   *
   * @var array
   */
  protected $options = [];

  /**
   * {@inheritdoc}
   */
  public function getOptions($strict = FALSE) {
    if ($strict) {
      $options = $this->options;
      if (isset($options['controlNav']) && $options['controlNav'] != 'thumbnails') {
        $options['controlNav'] = boolval($options['controlNav']);
      }
      return $options;
    }
    else {
      return $this->options;
    }
  }

  /**
   * {@inheritdoc}
   */
  public function setOptions(array $options) {
    $this->options = $options;
  }

  /**
   * {@inheritdoc}
   */
  public function getOption($name) {
    return isset($this->options[$name]) ? $this->options[$name] : NULL;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(array $values = []) {
    $flexslider = parent::create($values);
    // Merge options with default options.
    $default_options = FlexsliderDefaults::defaultOptions();
    $flexslider->setOptions($flexslider->getOptions() + $default_options);
    return $flexslider;
  }

}
