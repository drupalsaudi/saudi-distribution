<?php

namespace Drupal\flexslider\Form;

use Drupal\Core\Entity\EntityForm;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\flexslider\FlexsliderDefaults;

/**
 * Class FlexsliderForm.
 *
 * @package Drupal\flexslider\Form
 */
class FlexsliderForm extends EntityForm {

  /**
   * {@inheritdoc}
   */
  public function form(array $form, FormStateInterface $form_state) {
    $form = parent::form($form, $form_state);
    $flexslider = $this->entity;
    $options = $flexslider->getOptions();
    $default_options = FlexsliderDefaults::defaultOptions();

    $form['label'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Label'),
      '#maxlength' => 255,
      '#default_value' => $flexslider->label(),
      '#description' => $this->t('A human-readable title for this option set.'),
      '#required' => TRUE,
    ];

    $form['id'] = [
      '#type' => 'machine_name',
      '#default_value' => $flexslider->id(),
      '#machine_name' => [
        'exists' => '\Drupal\flexslider\Entity\Flexslider::load',
      ],
      '#disabled' => !$flexslider->isNew(),
    ];

    // Options Vertical Tab Group table.
    $form['tabs'] = [
      '#type' => 'vertical_tabs',
    ];

    // General Slideshow and Animiation Settings.
    $form['animation_slideshow'] = [
      '#type' => 'details',
      '#title' => $this->t('General Slideshow and Animation Settings'),
      '#group' => 'tabs',
      '#open' => TRUE,
    ];

    $form['animation_slideshow']['animation'] = [
      '#type' => 'select',
      '#title' => $this->t('Animation'),
      '#description' => $this->t('Select your animation type'),
      '#options' => [
        'fade'   => $this->t('Fade'),
        'slide'  => $this->t('Slide'),
      ],
      '#default_value' => isset($options['animation']) ? $options['animation'] : $default_options['animation'],
      // @todo add states to enable/disable the direction
    ];

    $form['animation_slideshow']['animationSpeed'] = [
      '#type' => 'number',
      '#title' => $this->t('Animation Speed'),
      '#description' => $this->t('Set the speed of animations, in milliseconds'),
    // Only positive numbers.
      '#min' => 0,
    // Only integers.
      '#step' => 1,
      '#default_value' => isset($options['animationSpeed']) ? $options['animationSpeed'] : $default_options['animationSpeed'],
    ];

    $form['animation_slideshow']['direction'] = [
      '#type' => 'select',
      '#title' => $this->t('Slide Direction'),
      '#description' => $this->t('Select the sliding direction, "horizontal" or "vertical"'),
      '#options' => [
        'horizontal'   => $this->t('Horizontal'),
        'vertical'  => $this->t('Vertical'),
      ],
      '#default_value' => isset($options['direction']) ? $options['direction'] : $default_options['direction'],
    ];

    $form['animation_slideshow']['slideshow'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Slideshow'),
      '#description' => $this->t('Animate the slides automatically'),
      '#default_value' => isset($options['slideshow']) ? $options['slideshow'] : $default_options['slideshow'],
    ];

    // Build in support for easing plugin.
    $easing_options = ['swing' => $this->t('Swing'), 'linear' => $this->t('Linear')];
    if ($this->moduleHandler->moduleExists('jqeasing')) {
      $easing_options = array_merge($easing_options, _flexslider_jqeasing_options());
    }

    $form['animation_slideshow']['easing'] = [
      '#type' => 'select',
      '#title' => $this->t('Easing'),
      '#multiple' => FALSE,
      '#description' => $this->t('The description appears usually below the item.'),
      '#options' => $easing_options,
      '#default_value' => isset($options['easing']) ? $options['easing'] : $default_options['easing'],
    ];

    $form['animation_slideshow']['smoothHeight'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Smooth Height'),
      '#description' => $this->t('Animate the height of the slider smoothly for slides of varying height.'),
      '#default_value' => isset($options['smoothHeight']) ? $options['smoothHeight'] : $default_options['smoothHeight'],
    ];

    $form['animation_slideshow']['reverse'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Reverse'),
      '#description' => $this->t('Animate the slides in reverse'),
      '#default_value' => isset($options['reverse']) ? $options['reverse'] : $default_options['reverse'],
    ];

    $form['animation_slideshow']['slideshowSpeed'] = [
      '#type' => 'number',
      '#title' => $this->t('Slideshow speed'),
      '#description' => $this->t('Set the speed of the slideshow cycling, in milliseconds'),
    // Only positive numbers.
      '#min' => 0,
    // Only integers.
      '#step' => 1,
      '#default_value' => isset($options['slideshowSpeed']) ? $options['slideshowSpeed'] : $default_options['slideshowSpeed'],
    ];

    $form['animation_slideshow']['animationLoop'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Loop Slideshow'),
      '#description' => $this->t('Loop the slideshow once it reaches the last slide.'),
      '#default_value' => isset($options['animationLoop']) ? $options['animationLoop'] : $default_options['animationLoop'],
    ];

    $form['animation_slideshow']['randomize'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Randomize Slide Order'),
      '#description' => $this->t('Randomize the order the slides play back.'),
      '#default_value' => isset($options['randomize']) ? $options['randomize'] : $default_options['randomize'],
    ];
    $form['animation_slideshow']['startAt'] = [
      '#type' => 'number',
      '#title' => $this->t('Starting Slide'),
      '#description' => $this->t('The slide that the slider should start on. Ex: For the first slide enter "0", for the second enter "1", etc. If you enter a value which is greater than the number of slides, the slider will default to the first slide.'),
    // Only positive numbers.
      '#min' => 0,
    // Only integers.
      '#step' => 1,
      '#default_value' => isset($options['startAt']) ? $options['startAt'] : $default_options['startAt'],
      // @todo add states to disable if randomize is set
    ];

    $form['animation_slideshow']['itemWidth'] = [
      '#type' => 'number',
    // Only positive numbers.
      '#min' => 0,
    // Only integers.
      '#step' => 1,
      '#title' => $this->t('Item Width'),
      '#description' => $this->t('Box-model width of individual carousel items, including horizontal borders and padding.'),
      '#default_value' => isset($options['itemWidth']) ? $options['itemWidth'] : $default_options['itemWidth'],
    ];
    $form['animation_slideshow']['itemMargin'] = [
      '#type' => 'number',
    // Only positive numbers.
      '#min' => 0,
    // Only integers.
      '#step' => 1,
      '#title' => $this->t('Item Margin'),
      '#description' => $this->t('Margin between carousel items. (NB: the margin must be set in your CSS styles. This property merely informs FlexSlider of the margin.)'),
      '#default_value' => isset($options['itemMargin']) ? $options['itemMargin'] : $default_options['itemMargin'],
    ];
    $form['animation_slideshow']['minItems'] = [
      '#type' => 'number',
    // Only positive numbers.
      '#min' => 0,
    // Only integers.
      '#step' => 1,
      '#title' => $this->t('Minimum Items'),
      '#description' => $this->t('Minimum number of carousel items that should be visible.'),
      '#default_value' => isset($options['minItems']) ? $options['minItems'] : $default_options['minItems'],
    ];
    $form['animation_slideshow']['maxItems'] = [
      '#type' => 'number',
    // Only positive numbers.
      '#min' => 0,
    // Only integers.
      '#step' => 1,
      '#title' => $this->t('Max Items'),
      '#description' => $this->t('Maximum number of carousel items that should be visible.'),
      '#default_value' => isset($options['maxItems']) ? $options['maxItems'] : $default_options['maxItems'],
    ];
    $form['animation_slideshow']['move'] = [
      '#type' => 'number',
    // Only positive numbers.
      '#min' => 0,
    // Only integers.
      '#step' => 1,
      '#title' => $this->t('Move'),
      '#description' => $this->t('Number of carousel items that should move on animation. If 0, slider will move all visible items.'),
      '#default_value' => isset($options['move']) ? $options['move'] : $default_options['move'],
    ];

    // Navigation and Control Settings.
    $form['nav_controls'] = [
      '#type' => 'details',
      '#title' => $this->t('Navigation and Control Settings'),
      '#group' => 'tabs',
    ];
    $form['nav_controls']['directionNav'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Next/Previous Controls'),
      '#description' => $this->t('Add controls for previous/next navigation'),
      '#default_value' => isset($options['directionNav']) ? $options['directionNav'] : $default_options['directionNav'],
    ];
    $form['nav_controls']['controlNav'] = [
      '#type' => 'select',
      '#title' => $this->t('Paging Controls'),
      '#description' => $this->t('Add controls to jump to individual slides. (Note: set to "On" if using Manual Controls)'),
      '#default_value' => isset($options['controlNav']) ? $options['controlNav'] : $default_options['controlNav'],
      '#options' => [
        0 => $this->t('Off'),
        1 => $this->t('On'),
        'thumbnails' => $this->t('Thumbnails'),
      ],
    ];
    $form['nav_controls']['thumbCaptions'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Thumbnail Captions'),
      '#description' => $this->t('<em>Requires FlexSlider Library 2.2+</em>. After selecting this captions will be added to thumbnails and removed from the main slide.'),
      '#default_value' => isset($options['thumbCaptions']) ? $options['thumbCaptions'] : $default_options['thumbCaptions'],
      '#states' => [
        'visible' => [
          ':input[name="controlNav"]' => ['value' => 'thumbnails'],
        ],
      ],
      '#element_validate' => ['::validateThumbnailOptions'],
    ];
    $form['nav_controls']['thumbCaptionsBoth'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Display both thumbnail captions and normal captions'),
      '#description' => $this->t('<em>Requires FlexSlider Library 2.2+</em>. Display captions in the thumbnail as well as in the slider.'),
      '#default_value' => isset($options['thumbCaptionsBoth']) ? $options['thumbCaptionsBoth'] : $default_options['thumbCaptionsBoth'],
      '#states' => [
        'visible' => [
          ':input[name="controlNav"]' => ['value' => 'thumbnails'],
        ],
      ],
      '#element_validate' => ['::validateThumbnailOptions'],
    ];
    $form['nav_controls']['keyboard'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Keyboard Navigation'),
      '#description' => $this->t('Allow slider navigating via keyboard left/right keys'),
      '#default_value' => isset($options['keyboard']) ? $options['keyboard'] : $default_options['keyboard'],
    ];
    $form['nav_controls']['multipleKeyboard'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Multiple Keyboard'),
      '#description' => $this->t('Allow keyboard navigation to affect multiple sliders.'),
      '#default_value' => isset($options['multipleKeyboard']) ? $options['multipleKeyboard'] : $default_options['multipleKeyboard'],
    ];
    $form['nav_controls']['mousewheel'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Mousewheel Navigation'),
      '#description' => $this->t('Allow slider navigating via mousewheel'),
      '#default_value' => isset($options['mousewheel']) ? $options['mousewheel'] : $default_options['mousewheel'],
      // @todo add check for jquery mousewheel library
    ];
    $form['nav_controls']['touch'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Touch'),
      '#description' => $this->t('Allow touch swipe navigation.'),
      '#default_value' => isset($options['touch']) ? $options['touch'] : $default_options['touch'],
    ];
    $form['nav_controls']['prevText'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Previous Link Text'),
      '#description' => $this->t('Set the text for the "previous" control item. <em>Text translation can be controlled using the <a href="http://drupal.org/project/stringoverrides">String Overrides module</a>.</em>'),
      '#default_value' => isset($options['prevText']) ? $options['prevText'] : $default_options['prevText'],
    ];
    $form['nav_controls']['nextText'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Next Link Text'),
      '#description' => $this->t('Set the text for the "next" control item. <em>Text translation can be controlled using the <a href="http://drupal.org/project/stringoverrides">String Overrides module</a>.</em>'),
      '#default_value' => isset($options['nextText']) ? $options['nextText'] : $default_options['nextText'],
    ];

    // Advanced Options.
    $form['advanced'] = [
      '#type' => 'details',
      '#title' => $this->t('Advanced Options'),
      '#group' => 'tabs',
    ];
    $form['advanced']['namespace'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Namespace'),
      '#description' => $this->t('Prefix string attached to the classes of all elements generated by the plugin.'),
      '#size' => 40,
      '#maxlength' => 255,
      '#element_validate' => ['::validateNamespace'],
      '#default_value' => isset($options['namespace']) ? $options['namespace'] : $default_options['namespace'],
    ];
    $form['advanced']['selector'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Selector'),
      '#description' => $this->t('Must match a simple pattern. "{container} > {slide}".'),
      '#size' => 40,
      '#maxlength' => 255,
      '#element_validate' => ['::validateSelector'],
      '#default_value' => isset($options['selector']) ? $options['selector'] : $default_options['selector'],
    ];
    $form['advanced']['sync'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Sync'),
      '#description' => $this->t('Mirror the actions performed on this slider with another slider.'),
      '#size' => 40,
      '#maxlength' => 255,
      '#default_value' => isset($options['sync']) ? $options['sync'] : $default_options['sync'],
    ];
    $form['advanced']['asNavFor'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Use as navigation'),
      '#description' => $this->t('Turn the slider into a thumbnail navigation for another slider.'),
      '#size' => 40,
      '#maxlength' => 255,
      '#default_value' => isset($options['asNavFor']) ? $options['asNavFor'] : $default_options['asNavFor'],
    ];
    $form['advanced']['initDelay'] = [
      '#type' => 'number',
      '#title' => $this->t('Initialize Delay'),
      '#description' => $this->t('Set an initialization delay, in milliseconds.'),
    // Only positive numbers.
      '#min' => 0,
    // Only integers.
      '#step' => 1,
      '#default_value' => isset($options['initDelay']) ? $options['initDelay'] : $default_options['initDelay'],
    ];
    $form['advanced']['useCSS'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Use CSS'),
      '#description' => $this->t('Slider will use CSS3 transitions, if available.'),
      '#default_value' => isset($options['useCSS']) ? $options['useCSS'] : $default_options['useCSS'],
    ];
    $form['advanced']['video'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Video'),
      '#description' => $this->t('Will prevent use of CSS3 3D Transforms, avoiding graphical glitches.'),
      '#default_value' => isset($options['video']) ? $options['video'] : $default_options['video'],
    ];
    $form['advanced']['pausePlay'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Add Pause/Play Indicator'),
      '#description' => $this->t('Have FlexSlider add an element indicating the current state of the slideshow (i.e. "pause" or "play").'),
      '#default_value' => isset($options['pausePlay']) ? $options['pausePlay'] : $default_options['pausePlay'],
      // @todo add states value for pause/play text
    ];
    $form['advanced']['pauseText'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Pause State Text'),
      '#description' => $this->t('Set the text for the "pause" state indicator. <em>Text translation can be controlled using the <a href="http://drupal.org/project/stringoverrides">String Overrides module</a>.</em>'),
      '#default_value' => isset($options['pauseText']) ? $options['pauseText'] : $default_options['pauseText'],
    ];
    $form['advanced']['playText'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Play State Text'),
      '#description' => $this->t('Set the text for the "play" state indicator. <em>Text translation can be controlled using the <a href="http://drupal.org/project/stringoverrides">String Overrides module</a>.</em>'),
      '#default_value' => isset($options['playText']) ? $options['playText'] : $default_options['playText'],
    ];
    $form['advanced']['pauseOnAction'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Pause On Controls'),
      '#description' => $this->t('Pause the slideshow when interacting with control elements.'),
      '#default_value' => isset($options['pauseOnAction']) ? $options['pauseOnAction'] : $default_options['pauseOnAction'],
    ];
    $form['advanced']['pauseOnHover'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Pause On Hover'),
      '#description' => $this->t('Pause the slideshow when hovering over slider, then resume when no longer hovering.'),
      '#default_value' => isset($options['pauseOnHover']) ? $options['pauseOnHover'] : $default_options['pauseOnHover'],
    ];
    $form['advanced']['controlsContainer'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Controls container (Advanced)'),
      '#description' => $this->t('Declare which container the navigation elements should be appended too. Default container is the flexSlider element. Example use would be ".flexslider-container", "#container", etc. If the given element is not found, the default action will be taken.'),
      '#default_value' => isset($options['controlsContainer']) ? $options['controlsContainer'] : $default_options['controlsContainer'],
    ];
    $form['advanced']['manualControls'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Manual controls (Advanced)'),
      '#description' => $this->t('Declare custom control navigation. Example would be ".flex-control-nav li" or "#tabs-nav li img", etc. The number of elements in your controlNav should match the number of slides/tabs.'),
      '#default_value' => isset($options['manualControls']) ? $options['manualControls'] : $default_options['manualControls'],
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function save(array $form, FormStateInterface $form_state) {
    /** @var \Drupal\flexslider\Entity\Flexslider $flexslider */
    $flexslider = $this->entity;

    $status = parent::save($form, $form_state);

    switch ($status) {
      case SAVED_NEW:
        $this->messenger()->addStatus($this->t('Created the %label FlexSlider optionset.', [
          '%label' => $flexslider->label(),
        ]));
        break;

      default:
        $this->messenger()->addStatus($this->t('Saved the %label FlexSlider optionset.', [
          '%label' => $flexslider->label(),
        ]));
    }
    $form_state->setRedirectUrl($flexslider->toUrl('collection'));
  }

  /**
   * {@inheritdoc}
   */
  protected function copyFormValuesToEntity(EntityInterface $entity, array $form, FormStateInterface $form_state) {
    $options = [];
    $values = $form_state->getValues();
    foreach ($values as $key => $value) {
      if (in_array($key, ['id', 'label'])) {
        $entity->set($key, $value);
      }
      else {
        $options[$key] = $value;
      }
    }
    $entity->set('options', $options);
  }

  /**
   * {@inheritdoc}
   */
  protected function actions(array $form, FormStateInterface $form_state) {
    $actions = parent::actions($form, $form_state);
    // Prevent access to delete button when editing default configuration.
    if ($this->entity->id() == 'default' && isset($actions['delete'])) {
      $actions['delete']['#access'] = FALSE;
    }
    return $actions;
  }

  /**
   * Validation functions.
   */
  public function validateNamespace(array &$element, FormStateInterface $form_state) {
    // @todo
    // @see form_error()
    return TRUE;
  }

  /**
   * Validation functions.
   */
  public function validateSelector(array &$element, FormStateInterface $form_state) {
    // @todo
    // @see form_error()
    return TRUE;
  }

  /**
   * Validate thumbnail option values.
   *
   * Empties the value of the thumbnail caption option when the paging control
   * is not set to thumbnails.
   *
   * @param array $element
   *   The element to validate.
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   *   The form state.
   */
  public function validateThumbnailOptions(array &$element, FormStateInterface $form_state) {
    if ($form_state->getValue('controlNav') !== 'thumbnails' && $element['#value']) {
      $form_state->setValueForElement($element, '');
    }
  }

}
