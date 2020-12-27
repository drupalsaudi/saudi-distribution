<?php

namespace Drupal\ds\Plugin\DsField;

use Drupal\Core\Utility\Token;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\Core\Language\LanguageManager;

/**
 * The base plugin to create DS code fields.
 */
abstract class TokenBase extends DsFieldBase {

  /**
   * The Token service.
   *
   * @var \Drupal\Core\Utility\Token
   */
  protected $token;

  /**
   * The LanguageManager service.
   *
   * @var \Drupal\Core\Language\LanguageManager
   */
  protected $languageManager;

  /**
   * Constructs a Display Suite field plugin.
   */
  public function __construct($configuration, $plugin_id, $plugin_definition, Token $token_service, LanguageManager $language_manager) {
    $this->token = $token_service;
    $this->languageManager = $language_manager;

    parent::__construct($configuration, $plugin_id, $plugin_definition);
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {

    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->get('token'),
      $container->get('language_manager')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function build() {
    $content = $this->content();
    $format = $this->format();
    // Get the current code for current language.
    $langcode = $this->languageManager->getCurrentLanguage()->getId();
    $value = $this->token->replace($content, [$this->getEntityTypeId() => $this->entity()], ['langcode' => $langcode, 'clear' => TRUE]);

    // Empty string in token fields treated as empty field.
    if ($value === '') {
      return [];
    }

    return [
      '#type' => 'processed_text',
      '#text' => $value,
      '#format' => $format,
      '#filter_types_to_skip' => [],
      '#langcode' => $langcode,
    ];
  }

  /**
   * Returns the format of the code field.
   */
  protected function format() {
    return 'plain_text';
  }

  /**
   * Returns the value of the code field.
   */
  protected function content() {
    return '';
  }

}
