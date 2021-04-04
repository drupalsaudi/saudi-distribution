<?php

namespace Drupal\anchor_link\Plugin\CKEditorPlugin;

use Drupal\editor\Entity\Editor;
use Drupal\ckeditor\CKEditorPluginBase;

/**
 * Defines the "link" plugin.
 *
 * @CKEditorPlugin(
 *   id = "link",
 *   label = @Translation("CKEditor Web link"),
 *   module = "anchor_link"
 * )
 */
class AnchorLink extends CKEditorPluginBase {

  /**
   * {@inheritdoc}
   */
  public function getFile() {
    return $this->getLibraryPath() . '/plugin.js';
  }

  /**
   * {@inheritdoc}
   */
  public function getDependencies(Editor $editor) {
    return [
      'fakeobjects',
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function getLibraries(Editor $editor) {
    return [];
  }

  /**
   * {@inheritdoc}
   */
  public function isInternal() {
    return FALSE;
  }

  /**
   * {@inheritdoc}
   */
  public function getButtons() {
    $libraryUrl = $this->getLibraryUrl();

    return [
      'Link' => [
        'label' => $this->t('Link'),
        'image' => $libraryUrl . '/icons/link.png',
      ],
      'Unlink' => [
        'label' => $this->t('Unlink'),
        'image' => $libraryUrl . '/icons/unlink.png',
      ],
      'Anchor' => [
        'label' => $this->t('Anchor'),
        'image' => $libraryUrl . '/icons/anchor.png',
      ],
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function getConfig(Editor $editor) {
    return [];
  }

  /**
   * Get the CKEditor Link library path.
   */
  protected function getLibraryPath() {
    // Following the logic in Drupal 8.9.x and Drupal 9.x
    // ----------------------------------------------------------------------
    // Issue #3096648: Add support for third party libraries in site specific
    // and install profile specific libraries folders
    // https://www.drupal.org/project/drupal/issues/3096648
    //
    // https://git.drupalcode.org/project/drupal/commit/1edf15f
    // -----------------------------------------------------------------------
    // Search sites/<domain>/*.
    $directories[] = \Drupal::service('site.path') . "/libraries/";

    // Always search the root 'libraries' directory.
    $directories[] = 'libraries/';

    // Installation profiles can place libraries into a 'libraries' directory.
    if ($installProfile = \Drupal::installProfile()) {
      $profile_path = drupal_get_path('profile', $installProfile);
      $directories[] = "$profile_path/libraries/";
    }

    foreach ($directories as $dir) {
      if (file_exists(DRUPAL_ROOT . '/' . $dir . 'link/plugin.js')) {
        return $dir . 'link';
      }
    }

    return 'libraries/link';
  }

  /**
   * Get the CKEditor Link library URL.
   */
  protected function getLibraryUrl() {

    $originUrl = \Drupal::request()->getSchemeAndHttpHost() . \Drupal::request()->getBaseUrl();

    $librarayPath = DRUPAL_ROOT . '/libraries/link';
    $librarayUrl = $originUrl . '/libraries/link';

    // Is the library found in the root libraries path.
    $libraryFound = file_exists($librarayPath . '/plugin.js');

    // If library is not found, then look in the current profile libraries path.
    if (!$libraryFound) {
      if ($installProfile = \Drupal::installProfile()) {
        $profilePath = drupal_get_path('profile', $installProfile);
        $profilePath .= '/libraries/link';

        // Is the library found in the current profile libraries path.
        if (file_exists(DRUPAL_ROOT . '/' . $profilePath . '/plugin.js')) {
          $libraryFound = TRUE;
          $librarayUrl = $originUrl . '/' . $profilePath;
        }
      }
    }

    if ($libraryFound) {
      return $librarayUrl;
    }
    else {
      return $originUrl . '/libraries/link';
    }
  }

}
