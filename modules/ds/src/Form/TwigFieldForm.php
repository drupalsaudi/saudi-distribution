<?php

namespace Drupal\ds\Form;

use Drupal\Core\Form\FormStateInterface;

/**
 * Configures token fields.
 */
class TwigFieldForm extends FieldFormBase {

  /**
   * The type of the dynamic ds field.
   */
  const TYPE = 'twig';

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'ds_field_twig_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state, $field_key = '') {
    $form = parent::buildForm($form, $form_state, $field_key);
    $field = $this->field;

    $form['content'] = [
      '#type' => 'textarea',
      '#title' => $this->t('Template'),
      '#rows' => 5,
      '#default_value' => isset($field['properties']['content']) ? $field['properties']['content'] : '',
      '#required' => TRUE,
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function getProperties(FormStateInterface $form_state) {
    return [
      'content' => $form_state->getValue('content'),
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function getType() {
    return TwigFieldForm::TYPE;
  }

  /**
   * {@inheritdoc}
   */
  public function getTypeLabel() {
    return 'Twig field';
  }

}
