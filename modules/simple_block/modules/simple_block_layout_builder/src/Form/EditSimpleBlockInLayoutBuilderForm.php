<?php

namespace Drupal\simple_block_layout_builder\Form;

use Drupal\Component\Utility\Html;
use Drupal\Component\Uuid\UuidInterface;
use Drupal\Core\Ajax\AjaxFormHelperTrait;
use Drupal\Core\Ajax\AjaxResponse;
use Drupal\Core\Form\FormStateInterface;
use Drupal\layout_builder\Controller\LayoutRebuildTrait;
use Drupal\layout_builder\LayoutBuilderHighlightTrait;
use Drupal\layout_builder\LayoutTempstoreRepositoryInterface;
use Drupal\layout_builder\SectionComponent;
use Drupal\layout_builder\SectionStorageInterface;
use Drupal\simple_block\SimpleBlockEditForm;
use Drupal\simple_block\SimpleBlockInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Provides a form to add/edit a simple block in Layout Builder.
 */
class EditSimpleBlockInLayoutBuilderForm extends SimpleBlockEditForm {

  use AjaxFormHelperTrait;
  use LayoutBuilderHighlightTrait;
  use LayoutRebuildTrait;

  /**
   * The layout tempstore repository.
   *
   * @var \Drupal\layout_builder\LayoutTempstoreRepositoryInterface
   */
  protected $layoutTempstoreRepository;

  /**
   * The UUID generator.
   *
   * @var \Drupal\Component\Uuid\UuidInterface
   */
  protected $uuidGenerator;

  /**
   * The field delta.
   *
   * @var int
   */
  protected $delta;

  /**
   * The current region.
   *
   * @var string
   */
  protected $region;

  /**
   * The UUID of the component.
   *
   * @var string
   */
  protected $uuid;

  /**
   * The section storage.
   *
   * @var \Drupal\layout_builder\SectionStorageInterface
   */
  protected $sectionStorage;

  /**
   * Constructs a new block form.
   *
   * @param \Drupal\layout_builder\LayoutTempstoreRepositoryInterface $layout_tempstore_repository
   *   The layout tempstore repository.
   * @param \Drupal\Component\Uuid\UuidInterface $uuid
   *   The UUID generator.
   */
  public function __construct(LayoutTempstoreRepositoryInterface $layout_tempstore_repository, UuidInterface $uuid) {
    $this->layoutTempstoreRepository = $layout_tempstore_repository;
    $this->uuidGenerator = $uuid;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container): self {
    return new static(
      $container->get('layout_builder.tempstore_repository'),
      $container->get('uuid')
    );
  }

  /**
   * Builds the form for the block.
   *
   * @param array $form
   *   An associative array containing the structure of the form.
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   *   The current state of the form.
   * @param \Drupal\layout_builder\SectionStorageInterface|null $section_storage
   *   The section storage being configured.
   * @param string|null $delta
   *   The delta of the section.
   * @param string|null $region
   *   The region of the block.
   * @param \Drupal\simple_block\SimpleBlockInterface|null $simple_block
   *   The plugin ID of the block to add.
   * @param string|null $uuid
   *   The component UUID.
   *
   * @return array
   *   The form array.
   */
  public function buildForm(array $form, FormStateInterface $form_state, SectionStorageInterface $section_storage = NULL, ?string $delta = NULL, ?string $region = NULL, ?SimpleBlockInterface $simple_block = NULL, ?string $uuid = NULL): array {
    $form = parent::buildForm($form, $form_state);

    // Only generate a new component once per form submission.
    if (!$component = $form_state->get('layout_builder__component')) {
      $id = $simple_block ? $simple_block->id() : NULL;
      $section = $section_storage->getSection($delta);
      if ($id) {
        $component = $section->getComponent($uuid);
      }
      else {
        $component = new SectionComponent($this->uuidGenerator->generate(), $region, ['id' => $id]);
        $section->appendComponent($component);
      }
      $form_state->set('layout_builder__component', $component);
    }
    unset($form['actions']['delete']);
    $form['#attributes']['data-layout-builder-target-highlight-id'] = $this->blockAddHighlightId($delta, $region);

    $this->sectionStorage = $section_storage;
    $this->delta = $delta;
    $this->uuid = $component->getUuid();

    if ($this->isAjax()) {
      $form['actions']['submit']['#ajax']['callback'] = '::ajaxSubmit';
      // @todo static::ajaxSubmit() requires data-drupal-selector to be the same
      //   between the various Ajax requests. A bug in
      //   \Drupal\Core\Form\FormBuilder prevents that from happening unless
      //   $form['#id'] is also the same. Normally, #id is set to a unique HTML
      //   ID via Html::getUniqueId(), but here we bypass that in order to work
      //   around the data-drupal-selector bug. This is okay so long as we
      //   assume that this form only ever occurs once on a page. Remove this
      //   workaround in https://www.drupal.org/node/2897377.
      $form['#id'] = Html::getId($form_state->getBuildInfo()['form_id']);
    }

    // Mark this as an administrative page for JavaScript ("Back to site" link).
    $form['#attached']['drupalSettings']['path']['currentPathIsAdmin'] = TRUE;

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state): void {
    parent::submitForm($form, $form_state);

    $section = $this->sectionStorage->getSection($this->delta);
    $section->getComponent($this->uuid)->setConfiguration([
      'id' => "simple_block:{$this->getEntity()->id()}",
    ]);
    $this->layoutTempstoreRepository->set($this->sectionStorage);
    $form_state->setRedirectUrl($this->sectionStorage->getLayoutBuilderUrl());
  }

  /**
   * {@inheritdoc}
   */
  protected function successfulAjaxSubmit(array $form, FormStateInterface $form_state): AjaxResponse {
    return $this->rebuildAndClose($this->sectionStorage);
  }

}
