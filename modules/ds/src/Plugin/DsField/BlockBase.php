<?php

namespace Drupal\ds\Plugin\DsField;

use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\Core\Plugin\Context\ContextHandlerInterface;
use Drupal\Core\Plugin\Context\ContextRepositoryInterface;
use Drupal\Core\Plugin\ContextAwarePluginInterface;
use Drupal\Core\Render\Element;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\Core\Block\BlockManagerInterface;

/**
 * The base plugin to create DS block fields.
 */
abstract class BlockBase extends DsFieldBase implements ContainerFactoryPluginInterface {

  /**
   * The block.
   *
   * @var \Drupal\Core\Block\BlockPluginInterface
   */
  protected $block;

  /**
   * The BlockManager service.
   *
   * @var \Drupal\Core\Block\BlockManagerInterface
   */
  protected $blockManager;

  /**
   * The context handler.
   *
   * @var \Drupal\Core\Plugin\Context\ContextHandlerInterface
   */
  protected $contextHandler;

  /**
   * The context repository interface.
   *
   * @var \Drupal\Core\Plugin\Context\ContextRepositoryInterface
   */
  protected $contextRepository;

  /**
   * Constructs a Display Suite field plugin.
   */
  public function __construct($configuration, $plugin_id, $plugin_definition, ContextHandlerInterface $contextHandler, ContextRepositoryInterface $contextRepository, BlockManagerInterface $block_manager) {
    $this->blockManager = $block_manager;
    $this->contextHandler = $contextHandler;
    $this->contextRepository = $contextRepository;

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
      $container->get('context.handler'),
      $container->get('context.repository'),
      $container->get('plugin.manager.block')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function build() {
    // Get block.
    $block = $this->getBlock();

    // Apply block config.
    $block_config = $this->blockConfig();
    $block->setConfiguration($block_config);

    $add_wrappers = isset($this->getFieldConfiguration()['properties']['add_block_wrappers']) ? $this->getFieldConfiguration()['properties']['add_block_wrappers'] : FALSE;

    if ($block->access(\Drupal::currentUser())) {
      // Inject context values.
      if ($block instanceof ContextAwarePluginInterface) {
        $contexts = $this->contextRepository->getRuntimeContexts(array_values($block->getContextMapping()));
        $this->contextHandler->applyContextMapping($block, $contexts);
      }
      $block_build = $block->build();

      // If the user has chosen to add the block wrappers, theme as a block.
      if ($add_wrappers) {
        // @see \Drupal\block\BlockViewBuilder::buildPreRenderableBlock
        // @see template_preprocess_block()
        $render_element = [
          '#theme' => 'block',
          '#attributes' => [],
          '#configuration' => $block->getConfiguration(),
          '#plugin_id' => $block->getPluginId(),
          '#base_plugin_id' => $block->getBaseId(),
          '#derivative_plugin_id' => $block->getDerivativeId(),
          'content' => $block_build,
        ];
      }
      else {
        // Otherwise just use the block build.
        $render_element = $block_build;
      }


      // Merge cache contexts, tags and max-age.
      if ($contexts = $block->getCacheContexts()) {
        $render_element['#cache']['contexts'] = [];
        if (isset($block_build['#cache']) && isset($block_build['contexts']) && is_array($block_build['#cache']['contexts'])) {
          $render_element['#cache']['contexts'] = $block_build['#cache']['contexts'];
        }

        $render_element['#cache']['contexts'] = array_unique(array_merge($render_element['#cache']['contexts'], $contexts));
      }

      if ($tags = $block->getCacheTags()) {
        $render_element['#cache']['tags'] = [];
        if (isset($block_build['#cache']) && isset($block_build['tags']) && is_array($block_build['#cache']['tags'])) {
          $render_element['#cache']['tags'] = $block_build['#cache']['tags'];
        }

        $render_element['#cache']['tags'] = array_unique(array_merge($render_element['#cache']['tags'], $tags));
      }

      // Add the block base config cache tag.
      $render_element['#cache']['tags'][] = 'config:ds.block_base';

      $max_age = $block->getCacheMaxAge();
      if (is_numeric($max_age) && !isset($render_element['#cache']['max-age'])) {
        $render_element['#cache']['max-age'] = $max_age;
      }

      // Return an empty array if there is nothing to render.
      return Element::isEmpty($render_element) ? [] : $render_element;
    }

    return [];
  }

  /**
   * Returns the plugin ID of the block.
   */
  protected function blockPluginId() {
    return '';
  }

  /**
   * Returns the config of the block.
   */
  protected function blockConfig() {
    return [];
  }

  /**
   * Return the block entity.
   */
  protected function getBlock() {
    if (!$this->block) {
      // Create an instance of the block.
      /* @var $block BlockPluginInterface */
      $block_id = $this->blockPluginId();
      $block = $this->blockManager->createInstance($block_id);

      $this->block = $block;
    }

    return $this->block;
  }

}
