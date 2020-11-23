<?php

namespace Drupal\commerce_cart_reminder\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\filter\Entity\FilterFormat;

class CommerceCartReminderSettingsForm extends ConfigFormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'commerce_cart_reminder_settings_form';
  }

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames() {
    return ['commerce_cart_reminder.config'];
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $config = $this->config('commerce_cart_reminder.config');



    // Fieldset for custom module configuration.
    $form['modal'] = [
      '#type' => 'details',
      '#title' => $this->t('Modal settings'),
      '#open' => TRUE,
      '#tree' => TRUE,
    ];

    $form['modal']['modal_title'] = [
      '#title' => t('Modal title'),
      '#type' => 'textfield',
      '#required' => TRUE,
      '#default_value' => $config->get('modal_title') ?? $this->t('Cart Reminder'),
    ];

    $form['modal']['view_cart_text'] = [
      '#title' => t('Go to cart button text'),
      '#type' => 'textfield',
      '#required' => TRUE,
      '#default_value' => $config->get('view_cart_text') ?? $this->t('View your cart'),
    ];

    $form['modal']['close_modal_text'] = [
      '#title' => t('Close modal button text'),
      '#type' => 'textfield',
      '#required' => TRUE,
      '#default_value' => $config->get('close_modal_text') ?? $this->t('Close'),
    ];

    $productEntity = \Drupal::service('entity_display.repository');
    // get list of available view modes for commerce product entities.
    $viewModes = $productEntity->getViewModes('commerce_product_variation');
    $options = [
      'just_title' => $this->t('Just title'),
    ];
    foreach ($viewModes as $key => $mode) {
      $options[$key] = $mode['label'];
    }

    $form['modal']['product_variation_view_mode'] = [
      '#type' => 'select',
      '#title' => $this->t('Product Variation View Mode to display in the modal '),
      '#options' => $options,
      '#default_value' => $config->get('product_variation_view_mode') ?? 'just_title',
    ];


    $default_filter_format = filter_default_format();
    $config_format = $config->get('intro_text.format');
    if (!empty($config_format)) {
      $filter_format = FilterFormat::load($config_format);
      if (empty($filter_format) || !$filter_format->get('status')) {
        $config_format = $default_filter_format;
      }
    }


    $form['modal']['intro_text'] = [
      '#type' => 'text_format',
      '#title' => $this->t('Intro text shown in the modal'),
      '#default_value' => $config->get('intro_text.value') ?? '',
      '#format' => $config_format,
    ];

    $form['visibility'] = [
      '#type' => 'details',
      '#title' => $this->t('Modal visibility'),
      '#open' => TRUE,
      '#tree' => TRUE,
    ];

    $form['visibility']['first_popup'] = [
      '#type' => 'fieldset',
      '#title' => $this->t('First popup appear'),
      '#description' => $this->t('This time is relative to the cart creation date'),
    ];

    $form['visibility']['first_popup']['first_value'] = [
      '#title' => t('After'),
      '#type' => 'number',
      '#size' => 10,
      '#default_value' => $config->get('first_value') ?? 1,
    ];
    $form['visibility']['first_popup']['first_type'] = [
      '#type' => 'select',
      '#options' => [
        'none' => $this->t('Do not show popup'),
        'minute' => $this->t('Minutes'),
        'day' => $this->t('Days'),
      ],
      '#default_value' => $config->get('first_type') ?? 'day',
    ];


    $form['visibility']['second_popup'] = [
      '#type' => 'fieldset',
      '#title' => $this->t('Second popup appear'),
      '#description' => $this->t('This time is relative to the first popup appear'),
    ];
    $form['visibility']['second_popup']['second_value'] = [
      '#title' => t('After'),
      '#type' => 'number',
      '#size' => 10,
      '#default_value' => $config->get('second_value') ?? 2,
    ];
    $form['visibility']['second_popup']['second_type'] = [
      '#type' => 'select',
      '#options' => [
        'none' => $this->t('Do not show popup'),
        'minute' => $this->t('Minutes'),
        'day' => $this->t('Days'),
      ],
      '#default_value' => $config->get('second_type') ?? 'day',
    ];


    $form['visibility']['third_popup'] = [
      '#type' => 'fieldset',
      '#title' => $this->t('Third popup appear'),
      '#description' => $this->t('This time is relative to the second popup appear'),
    ];
    $form['visibility']['third_popup']['third_value'] = [
      '#title' => t('After'),
      '#type' => 'number',
      '#size' => 10,
      '#default_value' => $config->get('third_value') ?? 2,
    ];
    $form['visibility']['third_popup']['third_type'] = [
      '#type' => 'select',
      '#options' => [
        'none' => $this->t('Do not show popup'),
        'minute' => $this->t('Minutes'),
        'day' => $this->t('Days'),
      ],
      '#default_value' => $config->get('third_type') ?? 'day',
    ];

    $form['visibility']['limit_by_sku'] = [
      '#title' => t('Limit by sku'),
      '#description' => $this->t('Show popup only if it contains product with these skus. Limit skus with a ";". Leave these field empty to always show the popup'),
      '#type' => 'textfield',
      '#default_value' => $config->get('limit_by_sku') ?? '',
    ];

    return parent::buildForm($form, $form_state);
  }

   /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {

    $values = $form_state->getValues();
    if ($values['visibility']['first_popup']['first_type'] == 'none') {
      if ($values['visibility']['second_popup']['second_type'] != 'none') {
        $form_state->setErrorByName('visibility][second_popup][second_type',  $this->t('Second notification should be set to "Do not show popup"'));
      }
    }

    if ($values['visibility']['first_popup']['first_type'] == 'none') {
      if ($values['visibility']['third_popup']['third_type'] != 'none') {
        $form_state->setErrorByName('visibility][third_popup][third_type',  $this->t('Third notification should be set to "Do not show popup"'));
      }
    }

    if ($values['visibility']['second_popup']['second_type'] == 'none') {
      if ($values['visibility']['third_popup']['third_type'] != 'none') {
        $form_state->setErrorByName('visibility][third_popup][third_type',  $this->t('Third notification should be set to "Do not show popup"'));
      }
    }
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $values = $form_state->getValues();

    $first_modal_appear = $this->transformToTimestamp($values['visibility']['first_popup']['first_value'], $values['visibility']['first_popup']['first_type']);
    $second_modal_appear = $this->transformToTimestamp($values['visibility']['second_popup']['second_value'], $values['visibility']['second_popup']['second_type']);
    $third_modal_appear = $this->transformToTimestamp($values['visibility']['third_popup']['third_value'], $values['visibility']['third_popup']['third_type']);

    $this->config('commerce_cart_reminder.config')
      ->set('modal_title', $values['modal']['modal_title'])
      ->set('view_cart_text', $values['modal']['view_cart_text'])
      ->set('close_modal_text', $values['modal']['close_modal_text'])
      ->set('intro_text.value', $values['modal']['intro_text']['value'])
      ->set('intro_text.format', $values['modal']['intro_text']['format'])
      ->set('close_modal_text', $values['modal']['close_modal_text'])
      ->set('product_variation_view_mode', $values['modal']['product_variation_view_mode'])
      ->set('first_type',  $values['visibility']['first_popup']['first_type'])
      ->set('first_value', $values['visibility']['first_popup']['first_value'])
      ->set('first_modal_appear', $first_modal_appear)
      ->set('second_type', $values['visibility']['second_popup']['second_type'])
      ->set('second_value', $values['visibility']['second_popup']['second_value'])
      ->set('second_modal_appear', $second_modal_appear)
      ->set('third_type', $values['visibility']['third_popup']['third_type'])
      ->set('third_value', $values['visibility']['third_popup']['third_value'])
      ->set('third_modal_appear', $third_modal_appear)
      ->set('limit_by_sku', $values['visibility']['limit_by_sku'])
      ->save();

    parent::submitForm($form, $form_state);
  }

  public function transformToTimestamp($value, $type) {
    if ($type == 'none') {
      return 0;
    }
    if (!$value) {
      return 0;
    }
    if ($type == 'minute') {
      return $value * 60;
    }
    if ($type == 'day') {
      return $value * 60 * 24;
    }
  }
}
