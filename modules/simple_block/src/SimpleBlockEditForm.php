<?php

namespace Drupal\simple_block;

use Drupal\Core\DependencyInjection\ContainerInjectionInterface;
use Drupal\simple_block\Entity\SimpleBlock;
use Drupal\Core\Entity\EntityForm;
use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\Core\Form\ConfigFormBaseTrait;
use Drupal\Core\Form\FormStateInterface;

/**
 * Base form for simple block edit forms.
 */
class SimpleBlockEditForm extends EntityForm implements ContainerInjectionInterface {

  use ConfigFormBaseTrait;

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames() {
    return ['simple_block.simple_block.' . $this->entity->id()];
  }

  /**
   * {@inheritdoc}
   */
  public function form(array $form, FormStateInterface $form_state) {
    $form = parent::form($form, $form_state);

    /** @var \Drupal\simple_block\Entity\SimpleBlock $simple_block */
    $simple_block = $this->entity;

    $form['id'] = [
      '#type' => 'machine_name',
      '#default_value' => $simple_block->id(),
      '#maxlength' => EntityTypeInterface::BUNDLE_MAX_LENGTH,
      '#machine_name' => [
        'exists' => SimpleBlock::class . '::load',
      ],
      '#disabled' => !$simple_block->isNew(),
    ];
    $form['title'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Title'),
      '#maxlength' => 255,
      '#default_value' => $simple_block->label(),
      '#description' => $this->t("The block title."),
      '#required' => TRUE,
    ];
    $form['content'] = array(
      '#type' => 'text_format',
      '#format' => $simple_block->getContent()['format'],
      '#title' => $this->t('Block content'),
      '#default_value' => $simple_block->getContent()['value'],
      '#description' => $this->t("The block content."),
      '#required' => TRUE,
    );
    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function save(array $form, FormStateInterface $form_state) {
    $this->entity->save();
  }

}
