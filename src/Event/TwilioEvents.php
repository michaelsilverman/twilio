<?php
/**
 * Created by PhpStorm.
 * User: michaelsilverman
 * Date: 4/23/18
 * Time: 4:22 PM
 */

namespace Drupal\twilio\Event;


final class TwilioEvents
{
    const SEND_TEXT_EVENT = 'twilio.send_text_event';
    const RECEIVE_TEXT_EVENT = 'twilio.receive_text_event';
    const SEND_VOICE_EVENT = 'twilio.send_voice_event';
    const RECEIVE_VOICE_EVENT = 'twilio.receive_voice_event';
}