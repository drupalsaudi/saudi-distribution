<?php

/**
 * @file
 * Enables modules and site configuration for a drupalsaudi site installation.
 */

use Drupal\contact\Entity\ContactForm;
use Drupal\Core\Form\FormStateInterface;

/**
 * Implements hook_form_FORM_ID_alter() for install_configure_form().
 *
 * Allows the profile to alter the site configuration form.
 */
function drupalsaudi_form_install_configure_form_alter(&$form, FormStateInterface $form_state) {

  // Date/time settings
  $form['regional_settings']['site_default_country']['#default_value'] = 'SA';
  $form['regional_settings']['date_default_timezone']['#default_value'] = 'Asia/Riyadh';
  
  $form['#submit'][] = 'drupalsaudi_form_install_configure_submit';
}


/**
 * Submission handler to sync the contact.form.feedback recipient.
 */
function drupalsaudi_form_install_configure_submit($form, FormStateInterface $form_state) {
  $site_mail = $form_state->getValue('site_mail');
  ContactForm::load('feedback')->setRecipients([$site_mail])->trustData()->save();
}
