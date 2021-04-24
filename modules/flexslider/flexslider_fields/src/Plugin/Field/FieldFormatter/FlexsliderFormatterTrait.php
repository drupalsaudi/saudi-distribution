<?php

namespace Drupal\flexslider_fields\Plugin\Field\FieldFormatter;

use Drupal\Core\Field\FieldDefinitionInterface;
use Drupal\Core\Field\FormatterBase;
use Drupal\Core\Url;
use Drupal\Core\Cache\Cache;
use Drupal\Component\Utility\Xss;
use Drupal\flexslider\Entity\Flexslider;

/**
 * A common Trait for flexslider formatters.
 *
 * Currently, only image based formatters exist for flexslider but this trait
 * could apply to any type formatter.
 *
 * @see Drupal\Core\Field\FormatterBase
 */
trait FlexsliderFormatterTrait {

  /**
   * Returns the flexslider specific default settings.
   *
   * @return array
   *   An array of default settings for the formatter.
   */
  protected static function getDefaultSettings() {
    return [
      'optionset' => 'default',
      'caption' => '',
    ];
  }

  /**
   * Builds the flexslider settings summary.
   *
   * @param \Drupal\Core\Field\FormatterBase $formatter
   *   The formatter having this trait.
   *
   * @return array
   *   The settings summary build array.
   */
  protected function buildSettingsSummary(FormatterBase $formatter) {
    $summary = [];

    // Load the selected optionset.
    $optionset = $this->loadOptionset($formatter->getSetting('optionset'));

    // Build the optionset summary.
    $os_summary = $optionset ? $optionset->label() : $formatter->t('Default settings');
    $summary[] = $formatter->t('Option set: %os_summary', ['%os_summary' => $os_summary]);

    return $summary;
  }

  /**
   * Builds the flexslider settings form.
   *
   * @param \Drupal\Core\Field\FormatterBase $formatter
   *   The formatter having this trait.
   *
   * @return array
   *   The render array for Optionset settings.
   */
  protected function buildSettingsForm(FormatterBase $formatter) {

    // Get list of option sets as an associative array.
    $optionsets = flexslider_optionset_list();

    $element['optionset'] = [
      '#title' => $formatter->t('Option Set'),
      '#type' => 'select',
      '#default_value' => $formatter->getSetting('optionset'),
      '#options' => $optionsets,
    ];

    $element['links'] = [
      '#theme' => 'links',
      '#links' => [
        [
          'title' => $formatter->t('Create new option set'),
          'url' => Url::fromRoute('entity.flexslider.add_form', [], ['query' => \Drupal::destination()->getAsArray()]),
        ],
        [
          'title' => $formatter->t('Manage option sets'),
          'url' => Url::fromRoute('entity.flexslider.collection', [], ['query' => \Drupal::destination()->getAsArray()]),
        ],
      ],
      '#access' => \Drupal::currentUser()->hasPermission('administer flexslider'),
    ];

    return $element;
  }

  /**
   * The flexslider formatted view for images.
   *
   * @param array $images
   *   Images render array from the (Responsive)Image Formatter.
   * @param array $formatter_settings
   *   Render array of settings.
   *
   * @return array
   *   Render of flexslider formatted images.
   */
  protected function viewImages(array $images, array $formatter_settings) {

    // Bail out if no images to render.
    if (empty($images)) {
      return [];
    }

    // Get cache tags for the option set.
    if ($optionset = $this->loadOptionset($formatter_settings['optionset'])) {
      $cache_tags = $optionset->getCacheTags();
    }
    else {
      $cache_tags = [];
    }

    $items = [];

    foreach ($images as $delta => &$image) {

      // Merge in the cache tags.
      if ($cache_tags) {
        $image['#cache']['tags'] = Cache::mergeTags($image['#cache']['tags'], $cache_tags);
      }

      // Prepare the slide item render array.
      $item = [];

      // Check caption settings.
      if ($formatter_settings['caption'] == 1) {
        $item['caption'] = ['#markup' => Xss::filterAdmin($image['#item']->title)];
      }
      elseif ($formatter_settings['caption'] == 'alt') {
        $item['caption'] = ['#markup' => Xss::filterAdmin($image['#item']->alt)];
      }

      // @todo Should find a way of dealing with render arrays instead of the actual output
      $item['slide'] = render($image);

      $items[$delta] = $item;
    }

    $images['#theme'] = 'flexslider';
    $images['#flexslider'] = [
      'settings' => $formatter_settings,
      'items' => $items,
    ];

    return $images;
  }

  /**
   * Loads the selected option set.
   *
   * @param string $id
   *   This option set id.
   *
   * @returns \Drupal\flexslider\Entity\Flexslider
   *   The option set selected in the formatter settings.
   */
  protected function loadOptionset($id) {
    return Flexslider::load($id);
  }

  /**
   * Returns the form element for caption settings.
   *
   * @param \Drupal\Core\Field\FormatterBase $formatter
   *   The formatter having this trait.
   * @param \Drupal\Core\Field\FieldDefinitionInterface $field_definition
   *   The image field definition.
   *
   * @return array
   *   The caption settings render array.
   */
  protected function captionSettings(FormatterBase $formatter, FieldDefinitionInterface $field_definition) {
    $field_settings = $field_definition->getSettings();

    // Set the caption options.
    $caption_options = [
      0 => $formatter->t('None'),
      1 => $formatter->t('Image title'),
      'alt' => $formatter->t('Image ALT attribute'),
    ];

    $default_value = $formatter->getSetting('caption');

    // Remove the options that are not available.
    $action_fields = [];
    if ($field_settings['title_field'] === FALSE) {
      unset($caption_options[1]);
      // User action required on the image title.
      $action_fields[] = 'title';
      if ($default_value == 1) {
        $default_value = '';
      }
    }
    if ($field_settings['alt_field'] === FALSE) {
      unset($caption_options['alt']);
      // User action required on the image alt.
      $action_fields[] = 'alt';
      if ($default_value == 'alt') {
        $default_value = '';
      }
    }

    // Create the caption element.
    $element['caption'] = [
      '#title' => $formatter->t('Choose a caption source'),
      '#type' => 'select',
      '#options' => $caption_options,
      '#default_value' => $default_value,
    ];

    // If the image field doesn't have all of the suitable caption sources,
    // tell the user.
    if ($action_fields) {
      $action_text = $formatter->t('enable the @action_field field', ['@action_field' => implode(' and/or ', $action_fields)]);
      /* This may be a base field definition (e.g. in Views UI) which means it
       * is not associated with a bundle and will not have the toUrl() method.
       * So we need to check for the existence of the method before we can
       * build a link to the image field edit form.
       */
      if (method_exists($field_definition, 'toUrl')) {
        // Build the link to the image field edit form for this bundle.
        $rel = "{$field_definition->getTargetEntityTypeId()}-field-edit-form";
        $action = $field_definition->toLink($action_text, $rel,
          [
            'fragment' => 'edit-settings-alt-field',
            'query' => \Drupal::destination()->getAsArray(),
          ]
        )->toRenderable();
      }
      else {
        // Just use plain text if we can't build the field edit link.
        $action = ['#markup' => $action_text];
      }
      $element['caption']['#description']
        = $formatter->t('You need to @action for this image field to be able to use it as a caption.',
        ['@action' => render($action)]);

      // If there are no suitable caption sources, disable the caption element.
      if (count($action_fields) >= 2) {
        $element['caption']['#disabled'] = TRUE;
      }
    }

    return $element;
  }

  /**
   * Return the currently configured option set as a dependency array.
   *
   * @param \Drupal\Core\Field\FormatterBase $formatter
   *   The formatter having this trait.
   *
   * @return array
   *   An array of option set dependencies
   */
  protected function getOptionsetDependencies(FormatterBase $formatter) {
    $dependencies = [];
    $option_id = $formatter->getSetting('optionset');
    if ($option_id && $optionset = $this->loadOptionset($option_id)) {
      // Add the optionset as dependency.
      $dependencies[$optionset->getConfigDependencyKey()][] = $optionset->getConfigDependencyName();
    }
    return $dependencies;
  }

  /**
   * If a dependency is going to be deleted, set the option set to default.
   *
   * @param \Drupal\Core\Field\FormatterBase $formatter
   *   The formatter having this trait.
   * @param array $dependencies_deleted
   *   An array of dependencies that will be deleted.
   *
   * @return bool
   *   Whether or not option set dependencies changed.
   */
  protected function optionsetDependenciesDeleted(FormatterBase $formatter, array $dependencies_deleted) {
    $option_id = $formatter->getSetting('optionset');
    if ($option_id && $optionset = $this->loadOptionset($option_id)) {
      if (!empty($dependencies_deleted[$optionset->getConfigDependencyKey()]) && in_array($optionset->getConfigDependencyName(), $dependencies_deleted[$optionset->getConfigDependencyKey()])) {
        $formatter->setSetting('optionset', 'default');
        return TRUE;
      }
    }
    return FALSE;
  }

}
