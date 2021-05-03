<?php

namespace Drupal\simple_block;

use Drupal\Core\Config\Entity\ConfigEntityStorage;
use Drupal\Core\Entity\EntityForm;
use Drupal\Core\Form\FormStateInterface;
use Drupal\simple_block\Entity\SimpleBlock;

/**
 * Base form for simple block edit forms.
 */
class SimpleBlockEditForm extends EntityForm {

  /**
   * {@inheritdoc}
   */
  public function form(array $form, FormStateInterface $form_state) {
    $simple_block = $this->getEntity();

    $form['title'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Title'),
      '#maxlength' => ConfigEntityStorage::MAX_ID_LENGTH,
      '#default_value' => $simple_block->label(),
      '#required' => TRUE,
    ];
    $form['id'] = [
      '#type' => 'machine_name',
      '#default_value' => $simple_block->id(),
      '#maxlength' => ConfigEntityStorage::MAX_ID_LENGTH,
      '#machine_name' => [
        'source' => ['title'],
        'exists' => SimpleBlock::class . '::load',
        'label' => t('Internal name'),
      ],
      '#disabled' => !$simple_block->isNew(),
      '#title' => $this->t('Internal name'),
      '#description' => $this->t('A unique internal name. Can only contain lowercase letters, numbers, and underscores.'),
      '#required' => TRUE,
    ];
    $form['content'] = [
      '#type' => 'text_format',
      '#format' => $simple_block->getContent()['format'],
      '#title' => $this->t('Content'),
      '#default_value' => $simple_block->getContent()['value'],
      '#required' => TRUE,
    ];

    return parent::form($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function save(array $form, FormStateInterface $form_state) {
    $status = parent::save($form, $form_state);
    $messenger = $this->messenger();
    $arguments = ['%id' => $this->getEntity()->id()];
    if ($status === SAVED_NEW) {
      $messenger->addStatus($this->t('Block %id has been added.', $arguments));
    }
    elseif ($status === SAVED_UPDATED) {
      $messenger->addStatus($this->t('Block %id has been updated.', $arguments));
    }
  }

}
