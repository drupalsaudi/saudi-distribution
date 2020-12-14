<?php

namespace Drupal\flexslider_fields\Plugin\Field\FieldFormatter;

use Drupal\Core\Field\FieldDefinitionInterface;
use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\responsive_image\Plugin\Field\FieldFormatter\ResponsiveImageFormatter;

/**
 * Plugin implementation of the '<flexslider_responsive>' formatter.
 *
 * @FieldFormatter(
 *   id = "flexslider_responsive",
 *   label = @Translation("FlexSlider Responsive"),
 *   field_types = {
 *     "image"
 *   }
 * )
 */
class FlexsliderResponsiveFormatter extends ResponsiveImageFormatter {
  use FlexsliderFormatterTrait;

  /**
   * {@inheritdoc}
   */
  public static function defaultSettings() {
    return self::getDefaultSettings() + parent::defaultSettings();
  }

  /**
   * {@inheritdoc}
   */
  public function settingsSummary() {
    $summary = $this->buildSettingsSummary($this);

    // Add the image settings summary and return.
    return array_merge($summary, parent::settingsSummary());

  }

  /**
   * {@inheritdoc}
   */
  public function settingsForm(array $form, FormStateInterface $form_state) {
    // Add the optionset setting.
    $element = $this->buildSettingsForm($this);

    // Add the image settings.
    $element = array_merge($element, parent::settingsForm($form, $form_state));
    // We don't need the link setting.
    $element['image_link']['#access'] = FALSE;

    // Add the caption setting.
    if (!empty($this->getSettings())) {
      $element += $this->captionSettings($this, $this->fieldDefinition);
    }

    return $element;
  }

  /**
   * {@inheritdoc}
   */
  public function viewElements(FieldItemListInterface $items, $langcode) {

    $images = parent::viewElements($items, $langcode);

    return $this->viewImages($images, $this->getSettings());

  }

  /**
   * {@inheritdoc}
   */
  public static function isApplicable(FieldDefinitionInterface $field_definition) {
    // This formatter only applies to multi-image fields when Responsive Image
    // module is loaded.
    if (\Drupal::moduleHandler()->moduleExists('responsive_image')) {
      return parent::isApplicable($field_definition) && $field_definition->getFieldStorageDefinition()->isMultiple();
    }
    return FALSE;
  }

  /**
   * {@inheritdoc}
   */
  public function calculateDependencies() {
    $dependencies = parent::calculateDependencies();
    return $dependencies + $this->getOptionsetDependencies($this);
  }

  /**
   * {@inheritdoc}
   */
  public function onDependencyRemoval(array $dependencies) {
    $changed = parent::onDependencyRemoval($dependencies);

    if ($this->optionsetDependenciesDeleted($this, $dependencies)) {
      $changed = TRUE;
    }
    return $changed;
  }

}
