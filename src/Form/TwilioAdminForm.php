<?php

/**
 * @file
 * Contains \Drupal\twilio\Form\TwilioAdminForm.
 */

namespace Drupal\twilio\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Render\Element;

class TwilioAdminForm extends ConfigFormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'twilio_admin_form';
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $config = $this->config('twilio.settings');

    foreach (Element::children($form) as $variable) {
      $config->set($variable, $form_state->getValue($form[$variable]['#parents']));
    }
    $config->save();

    if (method_exists($this, '_submitForm')) {
      $this->_submitForm($form, $form_state);
    }

    parent::submitForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames() {
    return ['twilio.settings'];
  }

  public function buildForm(array $form, \Drupal\Core\Form\FormStateInterface $form_state) {

    // Detect the Twilio library and provide feedback to the user if not present.
    if (\Drupal::moduleHandler()->moduleExists('libraries') && function_exists('libraries_detect')) {
      $library = libraries_detect(TWILIO_LIBRARY);
      if (!$library['installed']) {
        $twilio_readme_link = \Drupal::l(t('README.txt'), \Drupal\Core\Url::fromUri('http://drupalcode.org/project/twilio.git/blob/refs/heads/7.x-1.x:/README.txt'));
        $twilio_error_text = t('The Twilo library was not detected or installed correctly. Please review the installation instructions provided in the !link file', [
          '!link' => $twilio_readme_link
          ]);
        drupal_set_message($twilio_error_text, 'error');
      }
    }

    $form['account'] = [
      '#type' => 'textfield',
      '#required' => TRUE,
      '#title' => t('Twilio Account SID'),
      '#default_value' => \Drupal::config('twilio.settings')->get('account'),
      '#description' => t('Enter your Twilio account id'),
    ];
    $form['token'] = [
      '#type' => 'textfield',
      '#required' => TRUE,
      '#title' => t('Twilio Auth Token'),
      '#default_value' => \Drupal::config('twilio.settings')->get('token'),
      '#description' => t('Enter your Twilio token id'),
    ];
    $form['number'] = [
      '#type' => 'textfield',
      '#required' => TRUE,
      '#title' => t('Twilio Phone Number'),
      '#default_value' => \Drupal::config('twilio.settings')->get('number'),
      '#description' => t('Enter your Twilio phone number'),
    ];
    $form['long_sms'] = [
      '#type' => 'radios',
      '#title' => t('Long SMS handling'),
      '#description' => t('How would you like to handle SMS messages longer than 160 characters.'),
      '#options' => [
        t('Send multiple messages'),
        t('Truncate message to 160 characters'),
      ],
      '#default_value' => \Drupal::config('twilio.settings')->get('long_sms'),
    ];
    $form['registration_form'] = [
      '#type' => 'radios',
      '#title' => t('Show mobile fields during user registration'),
      '#description' => t('Specify if the site should collect mobile information during registration.'),
      '#options' => [
        t('Disabled'),
        t('Optional'),
        t('Required'),
      ],
      '#default_value' => \Drupal::config('twilio.settings')->get('registration_form'),
    ];

    $form['twilio_country_codes_container'] = [
      '#type' => 'fieldset',
      '#title' => t('Country codes'),
      '#description' => t('Select the country codes you would like available, If none are selected all will be available.'),
      '#collapsible' => TRUE,
      '#collapsed' => TRUE,
    ];
    $form['twilio_country_codes_container']['country_codes'] = [
      '#type' => 'checkboxes',
      '#options' => twilio_country_codes(TRUE),
      '#default_value' => \Drupal::config('twilio.settings')->get('country_codes'),
    ];

    // Expose the callback URLs to the user for convenience.
    $form['twilio_callback_container'] = [
      '#type' => 'fieldset',
      '#title' => t('Module callbacks'),
      '#description' => t('Enter these callback addresses into your Twilio phone number configuration on the Twilio dashboard to allow your site to respond to incoming voice calls and SMS messages.'),
    ];

    // Initialize URL variables.
    $voice_callback = $GLOBALS['base_url'] . '/twilio/voice';
    $sms_callback = $GLOBALS['base_url'] . '/twilio/sms';

    $form['twilio_callback_container']['voice_callback'] = [
      '#type' => 'item',
      '#title' => t('Voice request URL'),
      '#markup' => '<p>' . $voice_callback . '</p>',
    ];

    $form['twilio_callback_container']['sms_callback'] = [
      '#type' => 'item',
      '#title' => t('SMS request URL'),
      '#markup' => '<p>' . $sms_callback . '</p>',
    ];

    return parent::buildForm($form, $form_state);
  }

}
