<?php

namespace Drupal\ds\Plugin\DsField;

use Drupal\Core\Entity\EntityTypeBundleInfoInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Defines a generic bundle field.
 *
 * @DsField(
 *   id = "bundle_field",
 *   deriver = "Drupal\ds\Plugin\Derivative\BundleField"
 * )
 */
class BundleField extends DsFieldBase {

  /**
   * The EntityDisplayRepository service.
   *
   * @var \Drupal\Core\Entity\EntityTypeBundleInfoInterface
   */
  protected $entityTypeBundleInfo;

  /**
   * Constructs a Display Suite field plugin.
   *
   * @param $configuration
   * @param $plugin_id
   * @param $plugin_definition
   * @param \Drupal\Core\Entity\EntityTypeBundleInfoInterface $entity_type_bundle_info
   */
  public function __construct($configuration, $plugin_id, $plugin_definition, EntityTypeBundleInfoInterface $entity_type_bundle_info) {
    $this->entityTypeBundleInfo = $entity_type_bundle_info;

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
      $container->get('entity_type.bundle.info')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function build() {
    $config = $this->getConfiguration();
    $entity = $this->entity();
    $bundles_info = $this->entityTypeBundleInfo->getBundleInfo($config['field']['entity_type']);
    $output = $bundles_info[$entity->bundle()]['label'];

    return [
      '#markup' => $output,
    ];
  }

}
