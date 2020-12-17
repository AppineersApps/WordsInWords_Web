<?php
defined('BASEPATH') || exit('No direct script access allowed');

/**
 * Description of Daily_notification Controller
 *
 * @category notification
 *
 * @package master
 *
 * @subpackage controllers
 *
 * @module Daily_notification
 *
 * @class Daily_notification.php
 *
 * @path application\notifications\user\controllers\Daily_notification.php
 *
 * @version 4.4
 *
 * @author CIT Dev Team
 *
 * @since 30.07.2019
 */

class Daily_notification extends Cit_Controller
{
    public $settings_params;
    public $output_params;
    public $single_keys;
    public $block_result;

    /**
     * __construct method is used to set controller preferences while controller object initialization.
     */
    public function __construct()
    {
        parent::__construct();
        $this->settings_params = array();
        $this->output_params = array();
        $this->multiple_keys = array(
            "custom_function",
            "get_daily_event"
        );
        $this->block_result = array();
       
        $this->load->library('notifyresponse');
        $this->load->model('notification_model');
    }

    /**
     * start_notification method is used to initiate api execution flow.
     * @created shri | 08.08.2020
     * @modified shri | 08.08.2020
     * @param array $request_arr request_arr array is used for api input.
     * @return array $output_response returns output response of API.
     */
    public function start_daily_notification($request_arr = array())
    {
        try {
            $output_response = array();
            $input_params = $request_arr;
            $output_array = array();

            $input_params = $this->getDailyEvent($input_params);
            if (false == empty($input_params['get_daily_event'])) {
                $input_params = $this->prepare_reminder_notification($input_params);
                if (is_array($input_params['get_daily_event']) && count($input_params['get_daily_event']) > 0) {
                                $i = 0;
                                foreach ($input_params['get_daily_event'] as $data_key2 => $row2) {
                                    $this->post_notification($row2);
                                    $this->push_notification($row2);
                                }
                            }

                        $output_response = $this->finish_success($input_params);
                        return $output_response;
            } else {
                $output_response = $this->finish_success_1($input_params);
                return $output_response;
            }

        } catch(Exception $e) {
            $message = $e->getMessage();
        }
        
        return $output_response;
    }

    /**
     * getDailyEvent method is used to process query block.
     * @created Devangi Nirmal | 12.09.2019
     * @modified Devangi Nirmal | 12.09.2019
     * @param array $input_params input_params array to process loop flow.
     * @return array $input_params returns modfied input_params array.
     */
    public function getDailyEvent($input_params = array())
    {

        $this->block_result = array();
        try {
            $this->block_result = $this->notification_model->getDailyEvent();
            if (!$this->block_result["success"]) {
                throw new Exception("No records found.");
            }
        } catch(Exception $e) {
            $success = 0;
            $this->block_result["data"] = array();
        }
        $input_params["get_daily_event"] = $this->block_result["data"];

        return $input_params;
    }

    /**
     * post_notification method is used to process query block.
     * @created CIT Dev Team
     * @modified ---
     * @param array $input_params input_params array to process loop flow.
     * @return array $input_params returns modfied input_params array.
     */
    public function post_notification($input_params = array())
    {
        $this->block_result = array();
        try {
            $params_arr = array();
            if (isset($input_params["iUserId"])) {
                $params_arr["user_id"] = $input_params["iUserId"];
            }
            if (isset($input_params["iEventId"])) {
                $params_arr["event_id"] = $input_params["iEventId"];
            }
            if (isset($input_params["iContactId"])) {
                $params_arr["contact_id"] = $input_params["iContactId"];
            }
            $params_arr["_enotificationtype"] = "daily";
            $params_arr["notification_message"] = $input_params["vEventTitle"];
            $params_arr["_dtaddedat"] = "NOW()";
            $params_arr["_dtupdatedat"] = "NOW()";
            $this->block_result = $this->notification_model->post_notification($params_arr);
        } catch(Exception $e) {
            $success = 0;
            $this->block_result["data"] = array();
        }
        $input_params["post_notification"] = $this->block_result["data"];
        return $input_params;
    }

    /**
     * push_notification method is used to process mobile push notification.
     * @created Devangi Nirmal | 12.09.2019
     * @modified Devangi Nirmal | 12.09.2019
     * @param array $input_params input_params array to process loop flow.
     * @return array $input_params returns modfied input_params array.
     */
    public function push_notification($input_params = array())
    {

        $this->block_result = array();
        try {

            $device_id = $input_params["device_token"];
            $code = "USER";
            $sound = "";
            $badge = "";
            $silent = "";
            $title = "";
            $send_vars = array(
                array(
                    "key" => "user_type",
                    "value" => "driver",
                    "send" => "Yes",
                )
            );
            $push_msg =  $push_msg = $input_params["vEventTitle"];
            //$push_msg = $this->general->getReplacedInputParams($push_msg, $input_params);
            $send_mode = "default";

            $send_arr = array();
            $send_arr['device_id'] = $device_id;
            $send_arr['code'] = $code;
            $send_arr['sound'] = $sound;
            $send_arr['badge'] = intval($badge);
            $send_arr['silent'] = $silent;
            $send_arr['title'] = $title;
            $send_arr['message'] = $push_msg;
            $send_arr['variables'] = json_encode($send_vars);
            $send_arr['send_mode'] = $send_mode;
            
            $uni_id = $this->general->insertPushNotification($send_arr);
            if (!$uni_id) {
                throw new Exception('Failure in insertion of push notification batch entry.');
            }

            $success = 1;
            $message = "Push notification send succesfully.";
        } catch(Exception $e) {
            $success = 0;
            $message = $e->getMessage();
        }
        $this->block_result["success"] = $success;
        $this->block_result["message"] = $message;
        $input_params["push_notification"] = $this->block_result["success"];

        return $input_params;
    }

    /**
     * prepare_reminder_notification method is used to process custom function.
     * @created Devangi Nirmal | 12.09.2019
     * @modified Devangi Nirmal | 12.09.2019
     * @param array $input_params input_params array to process loop flow.
     * @return array $input_params returns modfied input_params array.
     */
    public function prepare_reminder_notification($input_params = array())
    {
        if (!method_exists($this, "reminder_notification")) {
            $result_arr["data"] = array();
        } else {
            $result_arr["data"] = $this->reminder_notification($input_params);
        }
        $input_params = $this->notifyresponse->assignSingleRecord($input_params, $result_arr["data"]);

        $input_params["prepare_reminder_notification"] = $this->notifyresponse->assignFunctionResponse($result_arr);

        return $input_params;
    }

    /**
     * push_notification_1 method is used to process mobile push notification.
     * @created Devangi Nirmal | 12.09.2019
     * @modified Devangi Nirmal | 12.09.2019
     * @param array $input_params input_params array to process loop flow.
     * @return array $input_params returns modfied input_params array.
     */
    public function push_notification_1($input_params = array())
    {

        $this->block_result = array();
        try
        {

            $device_id = $input_params["device_token2"];
            $code = "USER";
            $sound = "";
            $badge = "";
            $silent = "";
            $title = "";
            $send_vars = array(
                array(
                    "key" => "user_type",
                    "value" => "driver",
                    "send" => "Yes",
                )
            );
            $push_msg = "".$input_params["notification_message_remind"]."";
            $push_msg = $this->general->getReplacedInputParams($push_msg, $input_params);
            $send_mode = "default";

            $send_arr = array();
            $send_arr['device_id'] = $device_id;
            $send_arr['code'] = $code;
            $send_arr['sound'] = $sound;
            $send_arr['badge'] = intval($badge);
            $send_arr['silent'] = $silent;
            $send_arr['title'] = $title;
            $send_arr['message'] = $push_msg;
            $send_arr['variables'] = json_encode($send_vars);
            $send_arr['send_mode'] = $send_mode;
            $uni_id = $this->general->insertPushNotification($send_arr);
            if (!$uni_id)
            {
                throw new Exception('Failure in insertion of push notification batch entry.');
            }

            $success = 1;
            $message = "Push notification send succesfully.";
        }
        catch(Exception $e)
        {
            $success = 0;
            $message = $e->getMessage();
        }
        $this->block_result["success"] = $success;
        $this->block_result["message"] = $message;
        $input_params["push_notification_1"] = $this->block_result["success"];

        return $input_params;
    }

    public function finish_success($input_params = array())
    {

        $setting_fields = array(
            "success" => "1",
            "message" => "succesfully send notification",
        );
        $output_fields = array();

        $output_array["settings"] = $setting_fields;
        $output_array["settings"]["fields"] = $output_fields;
        $output_array["data"] = $input_params;

        $func_array["function"]["name"] = "get_daily_event";
        $func_array["function"]["single_keys"] = $this->single_keys;
        $func_array["function"]["multiple_keys"] = $this->multiple_keys;

        //$this->notifyresponse->setResponseStatus(200);

        $responce_arr = $this->notifyresponse->outputResponse($output_array, $func_array);

        return $responce_arr;
    }

    public function finish_success_1($input_params = array())
    {
        $setting_fields = array(
            "success" => "0",
            "message" => "No Data.",
        );
        $output_fields = array();
        $output_keys = array();
    

        $output_array["settings"] = $setting_fields;
        $output_array["settings"]["fields"] = $output_fields;
        $output_array["data"] = "";

        $func_array["function"]["name"] = "get_daily_event";
        $func_array["function"]["output_keys"] = $output_keys;
        $func_array["function"]["multiple_keys"] = $this->multiple_keys;

        $responce_arr = $this->notifyresponse->outputResponse($output_array, $func_array);

        return $responce_arr;
    }
}
