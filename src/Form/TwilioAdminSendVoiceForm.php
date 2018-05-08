<?php

namespace Drupal\twilio\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Component\Utility\UrlHelper;
use Drupal\twilio\Controller\TwilioController;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\twilio\Services\Command;

/**
 * Form to send test SMS messages.
 */
class TwilioAdminSendVoiceForm extends FormBase {

  /**
   * Injected Twilio service Command class.
   *
   * @var Command
   */
  private $command;

  /**
   * {@inheritdoc}
   */
  public function __construct(Command $command) {
    $this->command = $command;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    $command = $container->get('twilio.command');
    return new static($command);
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'twilio_admin_send_voice_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $form['country'] = [
      '#type' => 'select',
      '#title' => $this->t('Country code'),
      '#options' => TwilioController::countryDialCodes(FALSE),
    ];
    $form['number'] = [
      '#type' => 'textfield',
      '#required' => TRUE,
      '#title' => $this->t('Phone Number'),
      '#description' => $this->t('The number to send your message to. Include all numbers except the country code'),
    ];
    $form['message'] = [
      '#type' => 'textarea',
      '#required' => TRUE,
      '#title' => $this->t('Message'),
      '#description' => $this->t("The body of your SMS message."),
    ];

    $form['submit'] = [
      '#type' => 'submit',
      '#value' => $this->t('Call phone'),
    ];
    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {
    $value = $form_state->getValue(['number']);
    if (!is_numeric($value)) {
      $form_state->setErrorByName('number', $this->t('You must enter a phone number'));
    }
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $message = [];
    $message['body'] = $form_state->getValue('message') ? $form_state->getValue('message') : '';
    // '+19192960477', $to_number='+16308999711', $message
    $this->command->voiceCall('+19192960477',$form_state->getValue(['number']), 'alumco');
    drupal_set_message($this->t('Attempted to call device. Check your receiving device.'));
  }

}
