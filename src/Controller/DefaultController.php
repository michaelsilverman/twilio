<?php /**
 * @file
 * Contains \Drupal\twilio\Controller\DefaultController.
 */

namespace Drupal\twilio\Controller;

use Drupal\Core\Controller\ControllerBase;

/**
 * Default controller for the twilio module.
 */
class DefaultController extends ControllerBase {

  public function twilio_receive_message() {
    if (!empty($_REQUEST['From']) && !empty($_REQUEST['Body']) && !empty($_REQUEST['ToCountry']) && twilio_command('validate')) {
      $codes = twilio_country_codes();
      $message_code = twilio_get_country_short_codes($_REQUEST['ToCountry']);
      if (empty($codes[$message_code])) {
        \Drupal::logger('Twilio')->notice('A message was blocked from the country @country, due to your currrent country code settings.', [
          '@country' => $_REQUEST['ToCountry']
          ]);
        return;
      }
      $number = \Drupal\Component\Utility\SafeMarkup::checkPlain(str_replace('+' . $message_code, '', $_REQUEST['From']));
      $number_twilio = !empty($_REQUEST['To']) ? \Drupal\Component\Utility\SafeMarkup::checkPlain(str_replace('+', '', $_REQUEST['To'])) : '';
      $message = \Drupal\Component\Utility\SafeMarkup::checkPlain(htmlspecialchars_decode($_REQUEST['Body'], ENT_QUOTES));
      // @todo: Support more than one media entry.
      $media = !empty($_REQUEST['MediaUrl0']) ? $_REQUEST['MediaUrl0'] : '';
      $options = [];
      // Add the receiver to the options array.
      if (!empty($_REQUEST['To'])) {
        $options['receiver'] = \Drupal\Component\Utility\SafeMarkup::checkPlain($_REQUEST['To']);
      }
      \Drupal::logger('Twilio')->notice('An SMS message was sent from @number containing the message "@message"', [
        '@number' => $number,
        '@message' => $message,
      ]);
      twilio_sms_incoming($number, $number_twilio, $message, $media, $options);
    }
  }

  public function twilio_receive_voice() {
    if (!empty($_REQUEST['From']) && twilio_command('validate', [
      'type' => 'voice'
      ])) {
      $number = \Drupal\Component\Utility\SafeMarkup::checkPlain(str_replace('+1', '', $_REQUEST['From']));
      $number_twilio = !empty($_REQUEST['To']) ? \Drupal\Component\Utility\SafeMarkup::checkPlain(str_replace('+', '', $_REQUEST['To'])) : '';
      $options = [];
      if (!empty($_REQUEST['To'])) {
        $options['receiver'] = \Drupal\Component\Utility\SafeMarkup::checkPlain($_REQUEST['To']);
      }
      \Drupal::logger('Twilio')->notice('A voice call from @number was received.', [
        '@number' => $number
        ]);
      twilio_voice_incoming($number, $number_twilio, $options);
    }
  }

}
