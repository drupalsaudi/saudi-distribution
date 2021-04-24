<?php

namespace Drupal\views_bootstrap\Plugin\views\style;

use Drupal\Core\Form\FormStateInterface;
use Drupal\views\Plugin\views\style\StylePluginBase;
use Drupal\Component\Utility\Html;
use Drupal\views_bootstrap\ViewsBootstrap;

/**
 * Style plugin to render each item in an ordered or unordered list.
 *
 * @ingroup views_style_plugins
 *
 * @ViewsStyle(
 *   id = "views_bootstrap_grid",
 *   title = @Translation("Bootstrap Grid"),
 *   help = @Translation("Displays rows in a Bootstrap Grid layout"),
 *   theme = "views_bootstrap_grid",
 *   theme_file = "../views_bootstrap.theme.inc",
 *   display_types = {"normal"}
 * )
 */
class ViewsBootstrapGrid extends StylePluginBase {
  /**
   * Overrides \Drupal\views\Plugin\views\style\StylePluginBase::usesRowPlugin.
   *
   * @var bool
   */
  protected $usesRowPlugin = TRUE;

  /**
   * Overrides \Drupal\views\Plugin\views\style\StylePluginBase::usesRowClass.
   *
   * @var bool
   */
  protected $usesRowClass = TRUE;

  /**
   * Definition.
   */
  protected function defineOptions() {
    $options = parent::defineOptions();

    foreach (ViewsBootstrap::getBreakpoints() as $breakpoint) {
      $breakpoint_option = "col_$breakpoint";
      $options[$breakpoint_option] = ['default' => 'none'];
    }
    $options['col_class_custom'] = ['default' => ''];
    $options['col_class_default'] = ['default' => TRUE];
    $options['row_class_custom'] = ['default' => ''];
    $options['row_class_default'] = ['default' => TRUE];
    $options['default'] = ['default' => ''];
    $options['info'] = ['default' => []];
    $options['override'] = ['default' => TRUE];
    $options['sticky'] = ['default' => FALSE];
    $options['order'] = ['default' => 'asc'];
    $options['caption'] = ['default' => ''];
    $options['summary'] = ['default' => ''];
    $options['description'] = ['default' => ''];
    return $options;
  }

  /**
   * {@inheritdoc}
   */
  public function buildOptionsForm(&$form, FormStateInterface $form_state) {
    parent::buildOptionsForm($form, $form_state);

    foreach (ViewsBootstrap::getBreakpoints() as $breakpoint) {
      $breakpoint_option = "col_$breakpoint";
      $prefix = 'col' . ($breakpoint != 'xs' ? '-' . $breakpoint : '');
      $form[$breakpoint_option] = [
        '#type' => 'select',
        '#title' => $this->t("Column width of items at '$breakpoint' breakpoint"),
        '#default_value' => isset($this->options[$breakpoint_option]) ? $this->options[$breakpoint_option] : NULL,
        '#description' => $this->t("Set the number of columns each item should take up at the '$breakpoint' breakpoint and higher."),
        '#options' => [
          'none' => 'None (or inherit from previous)',
          $prefix => 'Equal',
          $prefix . '-auto' => 'Fit to content',
        ],
      ];
      foreach ([1, 2, 3, 4, 6, 12] as $width) {
        $form[$breakpoint_option]['#options'][$prefix . "-$width"] = $this->formatPlural(12 / $width, '@width (@count column per row)', '@width (@count columns per row)', ['@width' => $width]);
      }
    }
  }

}
