<?php

namespace Drupal\animate_any\Form;

use Drupal\Core\Database\Connection;
use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Provides the Animate Any form.
 */
class AnimateAnyForm extends FormBase {

  /**
   * @var \Drupal\Core\Database\Connection
   */
  protected $database;

  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('database')
    );
  }

  /**
   * Class constructor.
   */
  public function __construct(Connection $database) {
    $this->database = $database;
  }

  /**
   * Function to get Form ID.
   */
  public function getFormId() {
    return 'animate_any_form';
  }

  /**
   * Build Animate Any Setting Form.
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
//    $page_cache = \Drupal::config('animate_any.settings')->get('animate.data');
//    kint($page_cache);
    // Fetch animate.css from library.
    $animate_css = DRUPAL_ROOT . '/libraries/animate_any/animate.css';
    // Check animate.css file exists.
    if (!file_exists($animate_css)) {
      $this->messenger()->addMessage($this->t('animate.css library is missing.'), 'warning');
    }
    // Building add more form element to add animation.
    $form['#attached']['library'][] = 'animate_any/animate';

    $form['parent_class'] = [
      '#title' => $this->t('Add Parent Class/ID'),
      '#description' => $this->t('You can add parent class like <em>body.classname OR div.classname</em>, for class with dot(.) prefix and for Id with hash(#) prefix.'),
      '#type' => 'textfield',
    ];

    $form['#tree'] = TRUE;

    $form['animate_fieldset'] = [
      '#prefix' => '<div id="item-fieldset-wrapper">',
      '#suffix' => '</div>',
      '#tree' => TRUE,
      '#theme' => 'table',
      '#header' => [],
      '#rows' => [],
      '#attributes' => ['class' => 'animation'],
    ];

    $field_deltas = $form_state->get('field_deltas');
    if (is_null($field_deltas)) {
      $field_deltas = NULL;
      $form_state->set('field_deltas', $field_deltas);
    }
    if (!is_null($field_deltas)) {
      for ($delta = 0; $delta < $field_deltas; $delta++) {
        $section_identity = [
          '#title' => $this->t('Add section class/Id'),
          '#description' => $this->t('Add class with dot(.) prefix and Id with hash(#) prefix.'),
          '#type' => 'textfield',
          '#size' => 20,
        ];
        $section_event = [
          '#title' => $this->t('Select event'),
          '#type' => 'select',
          '#options' => animate_on_event(),
          '#attributes' => ['class' => ['select_event']],
        ];
        $section_animation = [
          '#title' => $this->t('Select animation'),
          '#type' => 'select',
          '#options' => animate_any_options(),
          '#attributes' => ['class' => ['select_animate']],
        ];
        $animation = [
          '#markup' => 'ANIMATE ANY',
          '#prefix' => '<h1 id="animate" class="" style="font-size: 30px;">',
          '#suffix' => '</h1>',
        ];

        $remove = [
          '#type' => 'submit',
          '#value' => $this->t('Remove'),
          '#submit' => ['::animate_any_custom_add_more_remove_one'],
          '#ajax' => [
            'callback' => '::animate_any_custom_remove_callback',
            'wrapper' => 'item-fieldset-wrapper',
          ],
          '#name' => 'remove_name_' . $delta,
        ];

        $form['animate_fieldset'][$delta] = [
          'section_identity' => &$section_identity,
          'section_event' => &$section_event,
          'section_animation' => &$section_animation,
          'animation' => &$animation,
          'remove' => &$remove,
        ];
        $form['animate_fieldset']['#rows'][$delta] = [
          ['data' => &$section_identity],
          ['data' => &$section_event],
          ['data' => &$section_animation],
          ['data' => &$animation],
          ['data' => &$remove],
        ];
        unset($section_identity);
        unset($section_event);
        unset($section_animation);
        unset($animation);
        unset($remove);
      }
    }

    $form['instruction'] = [
      '#markup' => '<strong>Click on <i>Add item</i> button to add animation section.</strong>',
      '#prefix' => '<div class="form-item">',
      '#suffix' => '</div>',
    ];
    // Add more button with ajax callback.
    $form['add'] = [
      '#type' => 'submit',
      '#value' => $this->t('Add Item'),
      '#submit' => ['::animate_any_custom_add_more_add_one'],
      '#ajax' => [
        'callback' => '::animate_any_custom_add_more_callback',
        'wrapper' => 'item-fieldset-wrapper',
      ],
    ];
    $form['submit'] = [
      '#type' => 'submit',
      '#value' => $this->t('Save Settings'),
    ];
    return $form;
  }

  /**
   * Validate for Animate Any Settings Form.
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {
    $op = (string)$form_state->getValue('op');
    if ($op == $this->t('Save Settings')) {
      $parent = $form_state->getValue('parent_class');
      if (empty($parent)) {
        $form_state->setRebuild();
        $form_state->setErrorByName("parent_class", $this->t("Please select parent class"));
      }
      foreach ($form_state->getValue('animate_fieldset') as $key => $value) {
        if (empty($value['section_identity'])) {
          $form_state->setRebuild();
          $form_state->setErrorByName("animate_fieldset][{$key}][section_identity", $this->t("Please select section identity for row @key", ['@key' => $key + 1]));
        }
        if ($value['section_event'] == 'none') {
          $form_state->setRebuild();
          $form_state->setErrorByName("animate_fieldset][{$key}][section_event", $this->t("Please select section event for row @key", ['@key' => $key + 1]));
        }
        if ($value['section_animation'] == 'none') {
          $form_state->setRebuild();
          $form_state->setErrorByName("animate_fieldset][{$key}][section_animation", $this->t("Please select section animation for row @key", ['@key' => $key + 1]));
        }
      }
    }
  }

  /**
   * Submit for Animate Any Settings Form.
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $op = (string)$form_state->getValue('op');
    if ($op == $this->t('Save Settings')) {
      $parent = $form_state->getValue('parent_class');
      $identifiers = $form_state->getValue('animate_fieldset');
      // Fetch existing parent name to merge the identifiers.
      $fetch = $this->database->select("animate_any_settings", "a");
      $fetch->fields('a', ['identifier']);
      $fetch->condition('a.parent', $parent);
      $fetch_results = $fetch->execute()->fetchAssoc();
      if ($fetch_results) {
        $existing_identifiers = json_decode($fetch_results['identifier'], true);
        $all_identifiers = array_merge($identifiers, $existing_identifiers);
        $all_identifiers = json_encode($all_identifiers);
      }
      else {
        $all_identifiers = json_encode($identifiers);
      }
      // Merge the identifiers which have common parent.
      $this->database->merge('animate_any_settings')
        ->key('parent', $parent)
        ->fields([
          'identifier' => $all_identifiers,
        ])->execute();
      // Set values to configuration.
      $this->setConfigValue($parent, $all_identifiers);
      $this->messenger()->addMessage($this->t('Animation added for @parent.', ['@parent' => $parent]));
    }
  }

  /**
   * Implements Add more Callback.
   */
  public function animate_any_custom_add_more_callback(array $form, FormStateInterface $form_state) {
    return $form['animate_fieldset'];
  }

  /**
   * Implements Remove Animate Callback.
   */
  public function animate_any_custom_remove_callback(array $form, FormStateInterface $form_state) {
    return $form['animate_fieldset'];
  }

  /**
   * Submit handler for the "add-one-more" button.
   */
  public function animate_any_custom_add_more_add_one(array $form, FormStateInterface $form_state) {
    $max = $form_state->get('field_deltas') + 1;
    $form_state->set('field_deltas', $max);
    $form_state->setRebuild();
  }

  /**
   * Submit handler for the "remove" button.
   */
  public function animate_any_custom_add_more_remove_one(array $form, FormStateInterface $form_state) {
    $field_deltas = $form_state->get('field_deltas');
    if ($field_deltas > 0) {
      $remove_one = $field_deltas - 1;
      $form_state->set('field_deltas', $remove_one);
    }
    $form_state->setRebuild();
  }

  /**
   * Set animation values in configuration.
   * @param $parent
   * @param $identifiers
   */
  public function setConfigValue($parent, $identifiers) {
    $parent_key = str_replace('.', '::', $parent);
    $animate_data = ['parent' => $parent, 'identifier' => $identifiers];
    \Drupal::service('config.factory')->getEditable('animate_any.settings')
      ->set($parent_key, $animate_data)->save();
  }

}
