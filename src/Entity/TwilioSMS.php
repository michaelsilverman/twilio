<?php
/**
 * Created by PhpStorm.
 * User: michaelsilverman
 * Date: 4/24/18
 * Time: 12:20 PM
 */

namespace Drupal\twilio\Entity;

class TwilioSMS
{
    private $to_country;
    private $to_state;
    private $sms_message_sid;
    private $num_media;
    private $to_city;
    private $from_zip;
    private $sms_sid;
    private $from_state;
    private $sms_status;
    private $from_city;
    private $message;
    private $from_country;
    private $to;
    private $to_zip;
    private $num_segments;
    private $message_sid;
    private $account_sid;
    protected $from;
    private $api_version;
    private $MediaUrl0;

    function __construct($message='')
    {
        if (!empty($message)) {
            $this->message_sid = $message['MessageSid'];
            $this->sms_sid = $message['SmsSid'];
            $this->account_sid = $message['AccountSid'];
            //  $this->messaging_service_sid = $message['MessagingServiceSid'];
            $this->from = $message['From'];
            $this->to = $message['To'];
            $this->message = $message['Body'];
            $this->num_media = $message['NumMedia'];
            // if ($this->num_media > 0) {
            for ($i = 0; $i < $this->num_media; $i++) {
                $this->media_url[$i] = $message['MediaUrl'.$i];
                $this->media_content_type[$i] = $message['MediaContentType'.$i];
            }
            //  }
            //  $this->media_content_type_0 = $message['MediaContentType0'];
            //  $this->media_url_0 = $message['MediaUrl0'];
            $this->from_city = $message['FromCity'];
            $this->from_state = $message['FromState'];
            $this->from_zip = $message['FromZip'];
            $this->from_country = $message['FromCountry'];
            $this->to_city = $message['ToCity'];
            $this->to_state = $message['ToState'];
            $this->to_zip = $message['ToZip'];
            $this->to_country = $message['ToCountry'];
            $this->sms_message_sid = $message['SmsMessageSid'];
            $this->num_segments = $message['NumSegments'];
            $this->api_version = $message['ApiVersion'];
        }
    }

    /**
     * Get the text
     *
     * @return string
     */
    public function getFrom() {
        return $this->from;
    }

    /**
     * Get the text
     *
     * @return string
     */
    public function getMessage() {
        return $this->message;
    }

    /**
     * Get the text
     *
     * @return string
     */
    public function getTo() {
        return $this->to;
    }

    /**
     * Get the text
     *
     * @return string
     */
    public function getSID() {
        return $this->account_sid;
    }

    /**
     * Get the text
     *
     * @return string
     */
    public function getCountry() {
        return $this->from_country;
    }

    /**
     * Get the text
     *
     * @return string
     */
    public function getState() {
        return $this->from_state;
    }

    /**
     * Get the text
     *
     * @return string
     */
    public function getCity() {
        return $this->from_city;
    }

    /**
     * Get the text
     *
     * @return string
     */
    public function getZip() {
        return $this->from_zip;
    }
}