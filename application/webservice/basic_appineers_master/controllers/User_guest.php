<?php
defined('BASEPATH') || exit('No direct script access allowed');

/**
 * Description of User Login Email Controller
 *
 * @category webservice
 *
 * @package basic_appineers_master
 *
 * @subpackage controllers
 *
 * @module User Login Email
 *
 * @class User_guest.php
 *
 * @path application\webservice\basic_appineers_master\controllers\User_guest.php
 *
 * @version 4.4
 *
 * @author CIT Dev Team
 *
 * @since 12.02.2020
 */

class User_guest extends Cit_Controller
{
    public $settings_params;
    public $output_params;
    public $single_keys;
    public $multiple_keys;
    public $block_result;

    /**
     * __construct method is used to set controller preferences while controller object initialization.
     */
    public function __construct()
    {
        parent::__construct();
        $this->settings_params = array();
        $this->output_params = array();
        $this->single_keys = array(
            "user_guest_details",
            "update_device_details",
        );
        $this->multiple_keys = array(
            "prepare_where",
            "generate_auth_token",
        );
        $this->block_result = array();

        $this->load->library('wsresponse');
        $this->load->model('users_guest_model');
        $this->load->model("basic_appineers_master/users_guest_model");
    }

    /**
     * rules_user_guest method is used to validate api input params.
     * @created shri | 13.09.2019
     * @modified shri | 12.02.2020
     * @param array $request_arr request_arr array is used for api input.
     * @return array $valid_res returns output response of API.
     */
    public function rules_user_guest($request_arr = array())
    {
        $valid_arr = array(
            "device_type" => array(
                array(
                    "rule" => "required",
                    "value" => true,
                    "message" => "device_type_required",
                )
            ),
            "device_model" => array(
                array(
                    "rule" => "required",
                    "value" => true,
                    "message" => "device_model_required",
                )
            ),
            "device_os" => array(
                array(
                    "rule" => "required",
                    "value" => true,
                    "message" => "device_os_required",
                )
            )
        );
        $valid_res = $this->wsresponse->validateInputParams($valid_arr, $request_arr, "user_guest");

        return $valid_res;
    }

    /**
     * start_user_guest method is used to initiate api execution flow.
     * @created shri | 13.09.2019
     * @modified shri | 12.02.2020
     * @param array $request_arr request_arr array is used for api input.
     * @param bool $inner_api inner_api flag is used to idetify whether it is inner api request or general request.
     * @return array $output_response returns output response of API.
     */
    public function start_user_guest($request_arr = array(), $inner_api = false)
    {
        try {
            $validation_res = $this->rules_user_guest($request_arr);
            if ($validation_res["success"] == "-5") {
                if ($inner_api === true) {
                    return $validation_res;
                } else {
                    $this->wsresponse->sendValidationResponse($validation_res);
                }
            }
            $output_response = array();
            $input_params = $validation_res['input_params'];
            $output_array = $func_array = array();

            // $input_params = $this->prepare_where($input_params);

            // $condition_res = $this->check_status($input_params);

            $input_params = $this->create_guest_user($input_params);
           
            $condition_res = $this->is_user_created($input_params);
            
            if ($condition_res["success"]) {
                $input_params = $this->generate_auth_token($input_params);
       
                $input_params = $this->user_guest_details($input_params);

                $condition_res = $this->check_user_exists($input_params);
                
                if ($condition_res["success"]) {
                    $condition_res = $this->check_login_status($input_params);

                    if ($condition_res["success"]) {
                        $input_params = $this->update_device_details($input_params);
                        
                        $condition_res = $this->is_logged_in($input_params);

                        $input_params = $this->get_user_login_details($input_params);

                        if ($condition_res["success"]) {
                            $output_response = $this->users_finish_success_3($input_params);
                            return $output_response;
                        } else {
                            $output_response = $this->users_finish_success_4($input_params);
                            return $output_response;
                        }
                    } else {
                        $condition_res = $this->is_archieved($input_params);
                        if ($condition_res["success"]) {
                            $output_response = $this->users_finish_success_5($input_params);
                            return $output_response;
                        } else {
                            $condition_res = $this->is_email_confirmed($input_params);
                            if ($condition_res["success"]) {
                                $output_response = $this->users_finish_success_2($input_params);
                                return $output_response;
                            } else {
                                $output_response = $this->users_finish_success_1($input_params);
                                return $output_response;
                            }
                        }
                    }
                } else {
                    $output_response = $this->users_finish_success($input_params);
                    return $output_response;
                }
            } else {
                $output_response = $this->finish_success($input_params);
                return $output_response;
            }
        } catch (Exception $e) {
            $message = $e->getMessage();
        }
        return $output_response;
    }

    /**
     * get_user_login_details method is used to process query block.
     * @created priyanka chillakuru | 13.09.2019
     * @modified priyanka chillakuru | 23.12.2019
     * @param array $input_params input_params array to process loop flow.
     * @return array $input_params returns modfied input_params array.
     */
    public function get_user_login_details($input_params = array())
    {
        $this->block_result = array();
        try
        {
            $auth_token = isset($input_params["auth_token"]) ? $input_params["auth_token"] : "";
            $where_clause = isset($input_params["u_user_id"]) ? $input_params["u_user_id"] : "";
            $this->block_result = $this->users_guest_model->get_user_login_details( $auth_token, $where_clause);
            if (!$this->block_result["success"])
            {
                throw new Exception("No records found.");
            }
            $result_arr = $this->block_result["data"];
            if (is_array($result_arr) && count($result_arr) > 0)
            {
                $i = 0;
                foreach ($result_arr as $data_key => $data_arr)
                {

                    $data = $data_arr["u_profile_image"];
                    $image_arr = array();
                    $image_arr["image_name"] = $data;
                    $image_arr["ext"] = implode(",", $this->config->item("IMAGE_EXTENSION_ARR"));
                    $image_arr["color"] = "FFFFFF";
                    $image_arr["no_img"] = FALSE;
                    $dest_path = "user_profile";
                    /*$image_arr["path"] = $this->general->getImageNestedFolders($dest_path);
                    $data = $this->general->get_image($image_arr);*/
                    $image_arr["path"] ="monkey_sphere/user_profile";
                    $data = $this->general->get_image_aws($image_arr);
                    
                    $result_arr[$data_key]["u_profile_image"] = $data;

                    $i++;
                }
                $this->block_result["data"] = $result_arr;
            }
        }
        catch(Exception $e)
        {
            $success = 0;
            $this->block_result["data"] = array();
        }
        $input_params["get_user_login_details"] = $this->block_result["data"];
        $input_params = $this->wsresponse->assignSingleRecord($input_params, $this->block_result["data"]);

        return $input_params;
    }

    /**
     * create_guest_user method is used to process query block.
     * @created shri | 23.12.2020
     * @modified shri | 23.12.2019
     * @param array $input_params input_params array to process loop flow.
     * @return array $input_params returns modfied input_params array.
     */
    public function create_guest_user($input_params = array())
    {
        $this->block_result = array();
        try {
            $params_arr = array();
            $params_arr["guest_user_id"] = uniqid();
            $params_arr["guest_user"] = "Yes";
            $params_arr["status"] = "Active";
            $params_arr["_dtaddedat"] = "NOW()";
            if (isset($input_params["device_type"])) {
                $params_arr["device_type"] = $input_params["device_type"];
            }
            if (isset($input_params["device_model"])) {
                $params_arr["device_model"] = $input_params["device_model"];
            }
            if (isset($input_params["device_os"])) {
                $params_arr["device_os"] = $input_params["device_os"];
            }
            if (isset($input_params["device_token"])) {
                $params_arr["device_token"] = $input_params["device_token"];
            }
            $this->block_result = $this->users_guest_model->create_guest_user($params_arr);
            if (!$this->block_result["success"]) {
                throw new Exception("Insertion failed.");
            }
            $data_arr = $this->block_result["array"];
        } catch (Exception $e) {
            $success = 0;
            $this->block_result["data"] = array();
        }
        $input_params["create_guest_user"] = $this->block_result["data"];
        $input_params = $this->wsresponse->assignSingleRecord($input_params, $this->block_result["data"]);

        return $input_params;
    }

    /**
     * is_user_created method is used to process conditions.
     * @created shri | 23.12.2020
     * @modified shri | 18.09.2019
     * @param array $input_params input_params array to process condition flow.
     * @return array $block_result returns result of condition block as array.
     */
    public function is_user_created($input_params = array())
    {
        $this->block_result = array();
        try {
            $cc_lo_0 = $input_params["insert_id"];
            $cc_ro_0 = 0;

            $cc_fr_0 = ($cc_lo_0 > $cc_ro_0) ? true : false;
            if (!$cc_fr_0) {
                throw new Exception("Some conditions does not match.");
            }
            $success = 1;
            $message = "Conditions matched.";
        } catch (Exception $e) {
            $success = 0;
            $message = $e->getMessage();
        }
        $this->block_result["success"] = $success;
        $this->block_result["message"] = $message;
        return $this->block_result;
    }

    /**
     * prepare_where method is used to process custom function.
     * @created shri | 13.09.2019
     * @modified shri | 13.09.2019
     * @param array $input_params input_params array to process loop flow.
     * @return array $input_params returns modfied input_params array.
     */
    public function prepare_where($input_params = array())
    {
        if (!method_exists($this, "helperPrepareWhere")) {
            $result_arr["data"] = array();
        } else {
            $result_arr["data"] = $this->helperPrepareWhere($input_params);
        }
        $format_arr = $result_arr;

        $format_arr = $this->wsresponse->assignFunctionResponse($format_arr);
        $input_params["prepare_where"] = $format_arr;

        $input_params = $this->wsresponse->assignSingleRecord($input_params, $format_arr);
        return $input_params;
    }

    /**
     * check_status method is used to process conditions.
     * @created shri | 13.09.2019
     * @modified shri | 13.09.2019
     * @param array $input_params input_params array to process condition flow.
     * @return array $block_result returns result of condition block as array.
     */
    public function check_status($input_params = array())
    {
        $this->block_result = array();
        try {
            $cc_lo_0 = $input_params["status"];
            $cc_ro_0 = 1;

            $cc_fr_0 = ($cc_lo_0 == $cc_ro_0) ? true : false;
            if (!$cc_fr_0) {
                throw new Exception("Some conditions does not match.");
            }
            $success = 1;
            $message = "Conditions matched.";
        } catch (Exception $e) {
            $success = 0;
            $message = $e->getMessage();
        }
        $this->block_result["success"] = $success;
        $this->block_result["message"] = $message;
        return $this->block_result;
    }

    /**
     * generate_auth_token method is used to process custom function.
     * @created shri | 13.09.2019
     * @modified shri | 13.09.2019
     * @param array $input_params input_params array to process loop flow.
     * @return array $input_params returns modfied input_params array.
     */
    public function generate_auth_token($input_params = array())
    {
        if (!method_exists($this->general, "generateAuthToken")) {
            $result_arr["data"] = array();
        } else {
            $result_arr["data"] = $this->general->generateAuthToken($input_params);
        }
        $format_arr = $result_arr;

        $format_arr = $this->wsresponse->assignFunctionResponse($format_arr);
        $input_params["
        "] = $format_arr;

        $input_params = $this->wsresponse->assignSingleRecord($input_params, $format_arr);
        return $input_params;
    }

    /**
     * user_guest_details method is used to process query block.
     * @created shri | 13.09.2019
     * @modified shri | 23.12.2019
     * @param array $input_params input_params array to process loop flow.
     * @return array $input_params returns modfied input_params array.
     */
    public function user_guest_details($input_params = array())
    {
        $this->block_result = array();
        try {
            $auth_token = isset($input_params["auth_token"]) ? $input_params["auth_token"] : "";
            $where_clause = isset($input_params["insert_id"]) ? $input_params["insert_id"] : "";
            $this->block_result = $this->users_guest_model->user_guest_details($where_clause, $auth_token);
            if (!$this->block_result["success"]) {
                throw new Exception("No records found.");
            }
            $result_arr = $this->block_result["data"];
            if (is_array($result_arr) && count($result_arr) > 0) {
                $i = 0;
                foreach ($result_arr as $data_key => $data_arr) {
                    $data = $data_arr["u_profile_image"];
                    $image_arr = array();
                    $image_arr["image_name"] = $data;
                    $image_arr["ext"] = implode(",", $this->config->item("IMAGE_EXTENSION_ARR"));
                    $image_arr["color"] = "FFFFFF";
                    $image_arr["no_img"] = false;
                    $dest_path = "user_profile";
                    /*$image_arr["path"] = $this->general->getImageNestedFolders($dest_path);
                    $data = $this->general->get_image($image_arr);*/
                    $image_arr["path"] ="monkey_sphere/user_profile";
                    $data = $this->general->get_image_aws($image_arr);
                    
                    $result_arr[$data_key]["u_profile_image"] = $data;

                    $i++;
                }
                $this->block_result["data"] = $result_arr;
            }
        } catch (Exception $e) {
            $success = 0;
            $this->block_result["data"] = array();
        }
        $input_params["user_guest_details"] = $this->block_result["data"];
        $input_params = $this->wsresponse->assignSingleRecord($input_params, $this->block_result["data"]);

        return $input_params;
    }

    /**
     * check_user_exists method is used to process conditions.
     * @created shri | 13.09.2019
     * @modified shri | 13.09.2019
     * @param array $input_params input_params array to process condition flow.
     * @return array $block_result returns result of condition block as array.
     */
    public function check_user_exists($input_params = array())
    {
        $this->block_result = array();
        try {
            $cc_lo_0 = (empty($input_params["user_guest_details"]) ? 0 : 1);
            $cc_ro_0 = 1;

            $cc_fr_0 = ($cc_lo_0 == $cc_ro_0) ? true : false;
            if (!$cc_fr_0) {
                throw new Exception("Some conditions does not match.");
            }
            $success = 1;
            $message = "Conditions matched.";
        } catch (Exception $e) {
            $success = 0;
            $message = $e->getMessage();
        }
        $this->block_result["success"] = $success;
        $this->block_result["message"] = $message;
        return $this->block_result;
    }

    /**
     * check_login_status method is used to process conditions.
     * @created shri | 13.09.2019
     * @modified shri | 13.09.2019
     * @param array $input_params input_params array to process condition flow.
     * @return array $block_result returns result of condition block as array.
     */
    public function check_login_status($input_params = array())
    {
        $this->block_result = array();
        try {
            $cc_lo_0 = $input_params["u_status"];
            $cc_ro_0 = "Active";

            $cc_fr_0 = ($cc_lo_0 == $cc_ro_0) ? true : false;
            if (!$cc_fr_0) {
                throw new Exception("Some conditions does not match.");
            }
            $success = 1;
            $message = "Conditions matched.";
        } catch (Exception $e) {
            $success = 0;
            $message = $e->getMessage();
        }
        $this->block_result["success"] = $success;
        $this->block_result["message"] = $message;
        return $this->block_result;
    }

    /**
     * update_device_details method is used to process query block.
     * @created shri | 13.09.2019
     * @modified shri | 13.09.2019
     * @param array $input_params input_params array to process loop flow.
     * @return array $input_params returns modfied input_params array.
     */
    public function update_device_details($input_params = array())
    {
        $this->block_result = array();
        try {
            $params_arr = $where_arr = array();
            if (isset($input_params["u_user_id"])) {
                $where_arr["u_user_id"] = $input_params["u_user_id"];
            }
            if (isset($input_params["device_type"])) {
                $params_arr["device_type"] = $input_params["device_type"];
            }
            if (isset($input_params["device_model"])) {
                $params_arr["device_model"] = $input_params["device_model"];
            }
            if (isset($input_params["device_os"])) {
                $params_arr["device_os"] = $input_params["device_os"];
            }
            if (isset($input_params["device_token"])) {
                $params_arr["device_token"] = $input_params["device_token"];
            }
            $params_arr["_vaccesstoken"] = "'".$input_params["auth_token"]."'";
            $this->block_result = $this->users_guest_model->update_device_details($params_arr, $where_arr);
        } catch (Exception $e) {
            $success = 0;
            $this->block_result["data"] = array();
        }
        $input_params["update_device_details"] = $this->block_result["data"];
        $input_params = $this->wsresponse->assignSingleRecord($input_params, $this->block_result["data"]);

        return $input_params;
    }

    /**
     * is_logged_in method is used to process conditions.
     * @created shri | 13.09.2019
     * @modified shri | 13.09.2019
     * @param array $input_params input_params array to process condition flow.
     * @return array $block_result returns result of condition block as array.
     */
    public function is_logged_in($input_params = array())
    {
        $this->block_result = array();
        try {
            $cc_lo_0 = (empty($input_params["update_device_details"]) ? 0 : 1);
            $cc_ro_0 = 1;

            $cc_fr_0 = ($cc_lo_0 == $cc_ro_0) ? true : false;
            if (!$cc_fr_0) {
                throw new Exception("Some conditions does not match.");
            }
            $success = 1;
            $message = "Conditions matched.";
        } catch (Exception $e) {
            $success = 0;
            $message = $e->getMessage();
        }
        $this->block_result["success"] = $success;
        $this->block_result["message"] = $message;
        return $this->block_result;
    }

    /**
     * users_finish_success_3 method is used to process finish flow.
     * @created shri | 13.09.2019
     * @modified shri | 23.12.2019
     * @param array $input_params input_params array to process loop flow.
     * @return array $responce_arr returns responce array of api.
     */
    public function users_finish_success_3($input_params = array())
    {
        $setting_fields = array(
            "success" => "1",
            "message" => "users_finish_success_3",
        );
        $output_fields = array(
            'u_user_id',
            'auth_token1',
            'u_guest_user_id',
            'u_guest_user'
        );
        $output_keys = array(
            'get_user_login_details',
        );
        $ouput_aliases = array(
            "get_user_login_details" => "get_user_login_details",
            "u_user_id" => "user_id",
            "auth_token1" => "access_token",
            "u_guest_user_id" => "guest_user_id",
            "u_guest_user" => "guest_user",
        );

        $output_array["settings"] = $setting_fields;
        $output_array["settings"]["fields"] = $output_fields;
        $output_array["data"] = $input_params;

        $func_array["function"]["name"] = "user_guest";
        $func_array["function"]["output_keys"] = $output_keys;
        $func_array["function"]["output_alias"] = $ouput_aliases;
        $func_array["function"]["single_keys"] = $this->single_keys;
        $func_array["function"]["multiple_keys"] = $this->multiple_keys;

        $this->wsresponse->setResponseStatus(200);

        $responce_arr = $this->wsresponse->outputResponse($output_array, $func_array);

        return $responce_arr;
    }

    /**
     * users_finish_success_4 method is used to process finish flow.
     * @created shri | 13.09.2019
     * @modified shri | 13.09.2019
     * @param array $input_params input_params array to process loop flow.
     * @return array $responce_arr returns responce array of api.
     */
    public function users_finish_success_4($input_params = array())
    {
        $setting_fields = array(
            "success" => "0",
            "message" => "users_finish_success_4",
        );
        $output_fields = array();

        $output_array["settings"] = $setting_fields;
        $output_array["settings"]["fields"] = $output_fields;
        $output_array["data"] = $input_params;

        $func_array["function"]["name"] = "user_guest";
        $func_array["function"]["single_keys"] = $this->single_keys;
        $func_array["function"]["multiple_keys"] = $this->multiple_keys;

        $this->wsresponse->setResponseStatus(200);

        $responce_arr = $this->wsresponse->outputResponse($output_array, $func_array);

        return $responce_arr;
    }

    /**
     * is_archieved method is used to process conditions.
     * @created shri | 01.10.2019
     * @modified shri | 01.10.2019
     * @param array $input_params input_params array to process condition flow.
     * @return array $block_result returns result of condition block as array.
     */
    public function is_archieved($input_params = array())
    {
        $this->block_result = array();
        try {
            $cc_lo_0 = $input_params["u_status"];
            $cc_ro_0 = "Archived";

            $cc_fr_0 = ($cc_lo_0 == $cc_ro_0) ? true : false;
            if (!$cc_fr_0) {
                throw new Exception("Some conditions does not match.");
            }
            $success = 1;
            $message = "Conditions matched.";
        } catch (Exception $e) {
            $success = 0;
            $message = $e->getMessage();
        }
        $this->block_result["success"] = $success;
        $this->block_result["message"] = $message;
        return $this->block_result;
    }

    /**
     * users_finish_success_5 method is used to process finish flow.
     * @created shri | 01.10.2019
     * @modified shri | 12.02.2020
     * @param array $input_params input_params array to process loop flow.
     * @return array $responce_arr returns responce array of api.
     */
    public function users_finish_success_5($input_params = array())
    {
        $setting_fields = array(
            "success" => "0",
            "message" => "users_finish_success_5",
        );
        $output_fields = array();

        $output_array["settings"] = $setting_fields;
        $output_array["settings"]["fields"] = $output_fields;
        $output_array["data"] = $input_params;

        $func_array["function"]["name"] = "user_guest";
        $func_array["function"]["single_keys"] = $this->single_keys;
        $func_array["function"]["multiple_keys"] = $this->multiple_keys;

        $this->wsresponse->setResponseStatus(200);

        $responce_arr = $this->wsresponse->outputResponse($output_array, $func_array);

        return $responce_arr;
    }

    /**
     * is_email_confirmed method is used to process conditions.
     * @created shri | 13.09.2019
     * @modified shri | 13.09.2019
     * @param array $input_params input_params array to process condition flow.
     * @return array $block_result returns result of condition block as array.
     */
    public function is_email_confirmed($input_params = array())
    {
        $this->block_result = array();
        try {
            $cc_lo_0 = $input_params["u_email_verified"];
            $cc_ro_0 = "Yes";

            $cc_fr_0 = ($cc_lo_0 == $cc_ro_0) ? true : false;
            if (!$cc_fr_0) {
                throw new Exception("Some conditions does not match.");
            }
            $success = 1;
            $message = "Conditions matched.";
        } catch (Exception $e) {
            $success = 0;
            $message = $e->getMessage();
        }
        $this->block_result["success"] = $success;
        $this->block_result["message"] = $message;
        return $this->block_result;
    }

    /**
     * users_finish_success_2 method is used to process finish flow.
     * @created shri | 13.09.2019
     * @modified shri | 01.10.2019
     * @param array $input_params input_params array to process loop flow.
     * @return array $responce_arr returns responce array of api.
     */
    public function users_finish_success_2($input_params = array())
    {
        $setting_fields = array(
            "success" => "0",
            "message" => "users_finish_success_2",
        );
        $output_fields = array();

        $output_array["settings"] = $setting_fields;
        $output_array["settings"]["fields"] = $output_fields;
        $output_array["data"] = $input_params;

        $func_array["function"]["name"] = "user_guest";
        $func_array["function"]["single_keys"] = $this->single_keys;
        $func_array["function"]["multiple_keys"] = $this->multiple_keys;

        $this->wsresponse->setResponseStatus(200);

        $responce_arr = $this->wsresponse->outputResponse($output_array, $func_array);

        return $responce_arr;
    }

    /**
     * users_finish_success_1 method is used to process finish flow.
     * @created shri | 13.09.2019
     * @modified shri | 13.09.2019
     * @param array $input_params input_params array to process loop flow.
     * @return array $responce_arr returns responce array of api.
     */
    public function users_finish_success_1($input_params = array())
    {
        $setting_fields = array(
            "success" => "3",
            "message" => "users_finish_success_1",
        );
        $output_fields = array();

        $output_array["settings"] = $setting_fields;
        $output_array["settings"]["fields"] = $output_fields;
        $output_array["data"] = $input_params;

        $func_array["function"]["name"] = "user_guest";
        $func_array["function"]["single_keys"] = $this->single_keys;
        $func_array["function"]["multiple_keys"] = $this->multiple_keys;

        $this->wsresponse->setResponseStatus(200);

        $responce_arr = $this->wsresponse->outputResponse($output_array, $func_array);

        return $responce_arr;
    }

    /**
     * users_finish_success method is used to process finish flow.
     * @created shri | 13.09.2019
     * @modified shri | 13.09.2019
     * @param array $input_params input_params array to process loop flow.
     * @return array $responce_arr returns responce array of api.
     */
    public function users_finish_success($input_params = array())
    {
        $setting_fields = array(
            "success" => "0",
            "message" => "users_finish_success",
        );
        $output_fields = array();

        $output_array["settings"] = $setting_fields;
        $output_array["settings"]["fields"] = $output_fields;
        $output_array["data"] = $input_params;

        $func_array["function"]["name"] = "user_guest";
        $func_array["function"]["single_keys"] = $this->single_keys;
        $func_array["function"]["multiple_keys"] = $this->multiple_keys;

        $this->wsresponse->setResponseStatus(200);

        $responce_arr = $this->wsresponse->outputResponse($output_array, $func_array);

        return $responce_arr;
    }

    /**
     * finish_success method is used to process finish flow.
     * @created shri | 13.09.2019
     * @modified shri | 13.09.2019
     * @param array $input_params input_params array to process loop flow.
     * @return array $responce_arr returns responce array of api.
     */
    public function finish_success($input_params = array())
    {
        $setting_fields = array(
            "success" => "0",
            "message" => "finish_success",
        );
        $output_fields = array();

        $output_array["settings"] = $setting_fields;
        $output_array["settings"]["fields"] = $output_fields;
        $output_array["data"] = $input_params;

        $func_array["function"]["name"] = "user_guest";
        $func_array["function"]["single_keys"] = $this->single_keys;
        $func_array["function"]["multiple_keys"] = $this->multiple_keys;

        $this->wsresponse->setResponseStatus(200);

        $responce_arr = $this->wsresponse->outputResponse($output_array, $func_array);

        return $responce_arr;
    }
}