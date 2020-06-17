<?php

namespace Drupal\simple_block\Plugin\Block;

use Drupal\Core\Cache\Cache;
use Drupal\filter\Entity\FilterFormat;
use Drupal\simple_block\Entity\SimpleBlock;
use Drupal\Core\Block\BlockBase;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Defines a generic custom block config type.
 *
 * @Block(
 *  id = "simple_block",
 *  admin_label = @Translation("Simple custom block"),
 *  category = @Translation("Simple"),
 *  deriver = "Drupal\simple_block\Plugin\Derivative\SimpleBlock"
 * )
 */
class SimpleBlockBlock extends BlockBase implements ContainerFactoryPluginInterface {

  /**
   * {@inheritdoc}
   */
  public function label() {
    return SimpleBlock::load($this->getDerivativeId())->label();
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition
    );
  }

  /**
   * {@inheritdoc}
   */
  public function build() {
    $build = [];
    /** @var \Drupal\simple_block\Entity\SimpleBlock $simple_block */
    if ($simple_block = SimpleBlock::load($this->getDerivativeId())) {
      $content = $simple_block->getContent();
      $build = [
        '#type' => 'processed_text',
        '#text' => $content['value'],
        '#format' => $content['format'],
        '#contextual_links' => [
          'simple_block' => [
            'route_parameters' => ['simple_block' => $simple_block->id()],
          ],
        ],

      ];
    }
    return $build;
  }

  /**
   * {@inheritdoc}
   */
  public function getCacheTags() {
    $cache_tags = parent::getCacheTags();
    /** @var \Drupal\simple_block\Entity\SimpleBlock $simple_block */
    if ($simple_block = SimpleBlock::load($this->getDerivativeId())) {
      $cache_tags = Cache::mergeTags($cache_tags, $simple_block->getCacheTags());
      if (!empty($format_id = $simple_block->getContent()['format'])) {
        if ($format = FilterFormat::load($format_id)) {
          $cache_tags = Cache::mergeTags($cache_tags, $format->getCacheTags());
        }
      }
    }
    return $cache_tags;
  }

}
