<?php

/**
 * @file
 * Contains \Drupal\twilio\Form\TwilioAdminTestForm.
 */

namespace Drupal\twilio\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Render\Element;

class TwilioAdminTestForm extends FormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'twilio_admin_test_form';
  }

  public function buildForm(array $form, \Drupal\Core\Form\FormStateInterface $form_state) {
    $form['country'] = [
      '#type' => 'select',
      '#title' => t('Country code'),
      '#options' => twilio_country_codes(),
    ];
    $form['number'] = [
      '#type' => 'textfield',
      '#required' => TRUE,
      '#title' => t('Phone Number'),
      '#description' => t('The number to send your message to. Include all numbers except the country code'),
    ];
    $form['message'] = [
      '#type' => 'textarea',
      '#required' => TRUE,
      '#title' => t('Message'),
      '#description' => t("The body of your SMS message."),
    ];
    $form['media'] = [
      '#type' => 'textfield',
      '#required' => FALSE,
      '#title' => t('Media URL (MMS)'),
      '#description' => t('A publicly accessible URL to a media file such as a png, jpg, gif, etc. (e.g. http://something.com/photo.jpg)'),
    ];
    $form['submit'] = [
      '#type' => 'submit',
      '#value' => t('Send SMS'),
    ];
    return $form;
  }

  public function validateForm(array &$form, \Drupal\Core\Form\FormStateInterface $form_state) {
    $value = $form_state->getValue(['number']);
    if (!is_numeric($value)) {
      $form_state->setErrorByName('number', t('You must enter a phone number'));
    }
    if ($form_state->getValue(['media']) && !valid_url($form_state->getValue([
      'media'
      ]), TRUE)) {
      $form_state->setErrorByName('media', t('Media must be a valid URL'));
    }
  }

  public function submitForm(array &$form, \Drupal\Core\Form\FormStateInterface $form_state) {
    $sent = twilio_send($form_state->getValue(['number']), $form_state->getValue(['message']), $form_state->getValue(['country']), $form_state->getValue(['media']) ? $form_state->getValue(['media']) : NULL);
    if ($sent) {
      drupal_set_message(t('Your message has been sent'));
    }
    else {
      drupal_set_message(t('Unable to send your message.'), 'error');
    }
  }

}
