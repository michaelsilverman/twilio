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
class TwilioAdminCheckNUmberForm extends FormBase {

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
    return 'twilio_admin_check_number_form';
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

    $form['submit'] = [
      '#type' => 'submit',
      '#value' => $this->t('Check Number'),
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
    $country = $form_state->getValue(['country']);
    $phone_info = $this->command->checkNumber($country.$form_state->getValue(['number']), ['type' => 'carrier']);
 //   drupal_set_message($form_state->getValue(['number']).$form_state->getValue(['country']));
    drupal_set_message($this->t('Carrier type: '.$phone_info->carrier["type"].' Carrier name:'.$phone_info->carrier["name"]));
  }

}
