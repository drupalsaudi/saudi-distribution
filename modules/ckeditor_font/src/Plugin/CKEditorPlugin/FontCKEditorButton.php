<?php

namespace Drupal\ckeditor_font\Plugin\CKEditorPlugin;

use Drupal\ckeditor\CKEditorPluginBase;
use Drupal\ckeditor\CKEditorPluginConfigurableInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\editor\Entity\Editor;

/**
 * Defines the "font" plugin.
 *
 * NOTE: The plugin ID ('id' key) corresponds to the CKEditor plugin name.
 * It is the first argument of the CKEDITOR.plugins.add() function in the
 * plugin.js file.
 *
 * @CKEditorPlugin(
 *   id = "font",
 *   label = @Translation("Font settings")
 * )
 */
class FontCKEditorButton extends CKEditorPluginBase implements CKEditorPluginConfigurableInterface {

  /**
   * {@inheritdoc}
   *
   * NOTE: The keys of the returned array corresponds to the CKEditor button
   * names. They are the first argument of the editor.ui.addButton() or
   * editor.ui.addRichCombo() functions in the plugin.js file.
   */
  public function getButtons() {
    // Make sure that the path to the image matches the file structure of
    // the CKEditor plugin you are implementing.
    $modulePath = drupal_get_path('module', 'ckeditor_font');
    return array(
      'Font' => array(
        'label' => $this->t('Font Families'),
        'image' => $modulePath . '/icons/font.png',
      ),
      'FontSize' => array(
        'label' => $this->t('Font Size'),
        'image' => $modulePath . '/icons/fontsize.png',
      ),
    );
  }

  /**
   * Get library path.
   */
  public function getLibraryPath() {
    // See https://www.drupal.org/project/ckeditor_font/issues/3046772 for
    // considerations on $plugin_path moving forward, from ambiguity in 'font'.
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
      if (file_exists(DRUPAL_ROOT . '/' . $dir . 'font/plugin.js')) {
        return $dir . 'font';
      }
    }

    return FALSE;
  }

  /**
   * {@inheritdoc}
   */
  public function getFile() {
    $plugin_path = $this->getLibraryPath() . '/plugin.js';
    if (file_exists($plugin_path)) {
      return $plugin_path;
    }
    return FALSE;
  }

  /**
   * {@inheritdoc}
   */
  function isInternal() {
    return FALSE;
  }

  /**
   * {@inheritdoc}
   */
  function getDependencies(Editor $editor) {
    return array();
  }

  /**
   * {@inheritdoc}
   */
  function getLibraries(Editor $editor) {
    return array();
  }

  /**
   * {@inheritdoc}
   */
  public function getConfig(Editor $editor) {
    $config = array();
    $settings = $editor->getSettings();
    if (!isset($settings['plugins']['font']['font_names']) && !isset($settings['plugins']['font']['font_sizes'])) {
      return $config;
    }
    if (isset($settings['plugins']['font']['font_sizes'])) {
      $font_sizes = $settings['plugins']['font']['font_sizes'];
      $sizes = $this->generateFontStyleSetting($font_sizes, 'size');
      if (!empty($sizes)) {
        $config['fontSize_sizes'] = implode('; ', $sizes);
      }
    }
    if (isset($settings['plugins']['font']['font_names'])) {
      $font_names = $settings['plugins']['font']['font_names'];
      $fonts = $this->generateFontStyleSetting($font_names, 'font');
      if (!empty($fonts)) {
        $config['font_names'] = implode('; ', $fonts);
      }
    }
    return $config;
  }

  /**
   * {@inheritdoc}
   */
  public function settingsForm(array $form, FormStateInterface $form_state, Editor $editor) {
    // Defaults.
    $config = array('font_names' => '', 'font_sizes' => '');
    $settings = $editor->getSettings();
    if (isset($settings['plugins']['font'])) {
      $config = $settings['plugins']['font'];
    }

    $form['font_names'] = array(
      '#title' => $this->t('Font families'),
      '#type' => 'textarea',
      '#default_value' => $config['font_names'],
      '#description' => $this->t('Enter fonts on new lines. Fonts must be added with the following syntax:<br><code>Primary font, fallback1, fallback2|Font Label</code>'),
      '#element_validate' => array(
        array($this, 'validateFontValue'),
      ),
    );

    $form['font_sizes'] = array(
      '#title' => $this->t('Font sizes'),
      '#type' => 'textarea',
      '#default_value' => $config['font_sizes'],
      '#description' => $this->t('Enter font sizes on new lines. Sizes must be added with the following syntax:<br><code>123px|Size label</code><br><code>123em|Size label</code><br><code>123%|Size label</code>'),
      '#element_validate' => array(
        array($this, 'validateFontSizeValue'),
      ),
    );

    return $form;
  }

  /**
   * #element_validate handler for the "font" element in settingsForm().
   * @param array $element
   * @param FormStateInterface $form_state
   */
  public function validateFontValue(array $element, FormStateInterface $form_state) {
    if ($this->generateFontStyleSetting($element['#value'], 'font') === FALSE) {
      $form_state->setError($element, t('The provided list of fonts is syntactically incorrect.'));
    }
  }

  /**
   * #element_validate handler for the "font" element in settingsForm().
   * @param array $element
   * @param FormStateInterface $form_state
   */
  public function validateFontSizeValue(array $element, FormStateInterface $form_state) {
    if ($this->generateFontStyleSetting($element['#value'], 'size') === FALSE) {
      $form_state->setError($element, t('The provided list of font sizes is syntactically incorrect.'));
    }
  }

  /**
   * Builds the "font_names" configuration part of the CKEditor JS settings.
   *
   * @see getConfig()
   *
   * @param string $fonts
   *   The "font_names" setting.
   * @return array|FALSE
   *   An array containing the "font_names" configuration, or FALSE when the
   *   syntax is invalid.
   */
  protected function generateFontStyleSetting($fonts, $type) {
    $font_style = array();

    // Early-return when empty.
    $fonts = trim($fonts);
    if (empty($fonts)) {
      return $font_style;
    }

    $fonts = str_replace(array("\r\n", "\r"), "\n", $fonts);
    foreach (explode("\n", $fonts) as $font) {
      $font = trim($font);

      // Ignore empty lines in between non-empty lines.
      if (empty($font)) {
        continue;
      }

      switch ($type) {
        case 'font':
          // Match for patterns:
          // font1, font2, font3|font label
          // font1|font label
          $pattern = '@^\s*[a-zA-Z0-9\,\-\s]+\s*\|\s*.+\s*$@';
          break;
        case 'size':
          // Match for patterns:
          // 123px/pt/em/rem/%|Label
          $pattern = '@^\s*\d+(\.?\d+)?(px|em|%|pt|rem)\|.*$@';
          break;
      }

      if (!preg_match($pattern, $font)) {
        return FALSE;
      }

      list($families, $label) = explode('|', $font);

      // Build string for CKEditor.font_names.
      $font_name = $label ? $label . '/' : '';
      $font_name .= $families;

      $font_style[] = $font_name;
    }
    return $font_style;
  }
}
