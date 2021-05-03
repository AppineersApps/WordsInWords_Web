<?php
defined('BASEPATH') || exit('No direct script access allowed');

/**
 * Description of Notification Add Controller
 *
 * @category webservice
 *
 * @package user
 *
 * @subpackage controllers
 *
 * @module Player_activity Add
 *
 * @class Player_activity.php
 *
 * @path application\webservice\player_activity\controllers\Player_activity.php
 *
 * @version 4.4
 *
 * @author CIT Dev Team
 *
 * @since 06.09.2019
 */
class Player_activity extends Cit_Controller
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
            "get_player_activity"
        );
        
        $this->block_result = array();

        $this->load->library('wsresponse');
        $this->load->model('player_activity_model');
    }

    /**
     * rule player activity method is used to validate api input params.
     * @created  | 28.01.2016
     * @modified ---
     * @param array $request_arr request_arr array is used for api input.
     * @return array $valid_res returns output response of API.
     */
    public function rules_player_activity($request_arr = array())
    {
        $valid_arr = array(
            "level_id" => array(
                array(
                    "rule" => "required",
                    "value" => true,
                    "message" => "level_id_required",
                )
            ),
            "round_no" => array(
                array(
                    "rule" => "required",
                    "value" => true,
                    "message" => "round_no_required",
                ),
            )
        );
        $this->wsresponse->setResponseStatus(422);
        $valid_res = $this->wsresponse->validateInputParams($valid_arr, $request_arr, "calllog_add");
        
        return $valid_res;
    }

    /**
     * start_player_activity_add method is used to initiate api execution flow.
     * @created  | 28.01.2016
     * @modified ---
     * @param array $request_arr request_arr array is used for api input.
     * @param bool $inner_api inner_api flag is used to idetify whether it is inner api request or general request.
     * @return array $output_response returns output response of API.
     */
    public function start_player_activity($request_arr = array(), $inner_api = false)
    {
        $method = $_SERVER['REQUEST_METHOD'];
        $output_response = array();

        switch ($method) {
            case 'GET':
                $output_response =  $this->getPlayerActivity($request_arr);

                return  $output_response;
                break;
            case 'POST':
                $output_response =  $this->addPlayerActivity($request_arr);

                return  $output_response;
                break;
            case 'DELETE':
                $output_response = $this->deletePlayerActivity($request_arr);

                return  $output_response;
                break;
        }
    }

    /**
     * getPlayerActivity method is used to initiate api execution flow.
     * @created kavita sawant | 08.01.2020
     * @modified kavita sawant | 08.01.2020
     * @param array $request_arr request_arr array is used for api input.
     * @param bool $inner_api inner_api flag is used to idetify whether it is inner api request or general request.
     * @return array $output_response returns output response of API.
     */
    public function getPlayerActivity($request_arr = array(), $inner_api = false)
    {
        try {
            $output_response = array();
            $output_array = $func_array = array();
            $input_params = $this->get_player_activity($request_arr);
            
            $key = "get_player_activity";
            $condition_res = $this->condition($input_params, $key);
            
            if ($condition_res["success"]) {
                $output_response = $this->get_player_activity_list_finish_success($input_params);

                return $output_response;
            } else {
                $output_response = $this->get_player_activity_finish_success_1($input_params);

                return $output_response;
            }
        } catch (Exception $e) {
            $message = $e->getMessage();
        }
        return $output_response;
    }

    public function addPlayerActivity($input_params)
    {
        try {
            $validation_res = $this->rules_player_activity($input_params);
            if ($validation_res["success"] == "-5") {
                if ($inner_api === true) {
                    return $validation_res;
                } else {
                    $this->wsresponse->sendValidationResponse($validation_res);
                }
            }
            
            $input_params = $validation_res['input_params'];
            $output_response = array();
           
            //$input_params = $validation_res['input_params'];
            // $output_array = $func_array = array();

            // $input_params = $this->set_helper($input_params);

            // $condition_res = $this->is_posted($input_params);
            $input_params['round_no'] = $input_params['round_no'] + 1;

            $input_params = $this->insert_player_activity_data($input_params);

            $key = "insert_player_activity_data";
            $condition_res = $this->condition($input_params, $key);

            if ($condition_res["success"]) {
                $output_response = $this->finish_player_activity_add_success($input_params);
                return $output_response;
            } else {
                $output_response = $this->finish_player_activity_add_failure($input_params);
                return $output_response;
            }
        } catch (Exception $e) {
            $message = $e->getMessage();
        }
        return $output_response;
    }

    /**
     * insert_player_activity_data method is used to process query block.
     * @created  | 28.01.2016
     * @modified ---
     * @param array $input_params input_params array to process loop flow.
     * @return array $input_params returns modfied input_params array.
     */
    public function insert_player_activity_data($input_params = array())
    {
        $this->block_result = array();
        try {
            $params_arr = array();

            $this->block_result = $this->player_activity_model->insert_or_update_player_activity_data($input_params);
            if (!$this->block_result["success"]) {
                throw new Exception("Insertion failed.");
            }
            $data_arr = $this->block_result["array"];

        } catch (Exception $e) {
            $success = 0;
            $this->block_result["data"] = array();
        }
        $input_params["insert_player_activity_data"] = $this->block_result["data"];
        $input_params = $this->wsresponse->assignSingleRecord($input_params, $this->block_result["data"]);

        return $input_params;
    }

    /**
     * get_player_activity method is used to process query block.
     * @created priyanka chillakuru | 18.09.2019
     * @modified priyanka chillakuru | 18.09.2019
     * @param array $input_params input_params array to process loop flow.
     * @return array $input_params returns modfied input_params array.
     */
    public function get_player_activity($input_params = array())
    {
        $this->block_result = array();
        try {
            $this->block_result = $this->player_activity_model->get_player_activity($input_params);
            if (!$this->block_result["success"]) {
                throw new Exception("No records found.");
            }
        } catch (Exception $e) {
            $success = 0;
            $this->block_result["data"] = array();
        }
        
        $input_params["get_player_activity"] = $this->block_result["data"];
        $input_params = $this->wsresponse->assignSingleRecord($input_params, $this->block_result["data"]);
       
        return $input_params;
    }

    /**
     * deletePlayerActivity method is used to initiate api execution flow.
     * @created kavita sawant | 08.01.2020
     * @modified kavita sawant | 08.01.2020
     * @param array $request_arr request_arr array is used for api input.
     * @param bool $inner_api inner_api flag is used to idetify whether it is inner api request or general request.
     * @return array $output_response returns output response of API.
     */
    public function deletePlayerActivity($request_arr = array(), $inner_api = false)
    {
        try {
            $output_response = array();
            $input_params = $validation_res['input_params'];
            $output_array = $func_array = array();
            
            $input_params = $this->delete_player_activity($request_arr);
              
            $key = "delete_player_activity";
            $condition_res = $this->condition($input_params, $key);
            
            if ($condition_res["success"]) {
                $output_response = $this->delete_player_activity_finish_success($input_params);
                return $output_response;
            } else {
                $output_response = $this->delete_player_activity_finish_success_1($input_params);
                return $output_response;
            }
        } catch (Exception $e) {
            $message = $e->getMessage();
        }
        return $output_response;
    }

    public function delete_player_activity($input_params = array())
    {
        $this->block_result = array();
        try {
            $arrResult = array();
           
            $this->block_result = $this->player_activity_model->delete_player_activity($input_params);

            if (!$this->block_result["success"]) {
                throw new Exception("No records found.");
            }
            $result_arr = $this->block_result["data"];
           
            $this->block_result["data"] = $result_arr;
        } catch (Exception $e) {
            $success = 0;
            $this->block_result["data"] = array();
        }
        $input_params["delete_player_activity"] = $this->block_result["data"];
        
        $input_params = $this->wsresponse->assignSingleRecord($input_params, $this->block_result["data"]);
        return $input_params;
    }

    /**
     * condition method is used to process conditions.
     * @created priyanka chillakuru | 18.09.2019
     * @modified priyanka chillakuru | 18.09.2019
     * @param array $input_params input_params array to process condition flow.
     * @return array $block_result returns result of condition block as array.
     */
    public function condition($input_params = array(), $key)
    {
        $this->block_result = array();
        try {
            $cc_lo_0 = (empty($input_params[$key]) ? 0 : 1);
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
     * finish_player_activity_add_success method is used to process finish flow.
     * @created  | 28.01.2016
     * @modified ---
     * @param array $input_params input_params array to process loop flow.
     * @return array $responce_arr returns responce array of api.
     */
    public function finish_player_activity_add_success($input_params = array())
    {
        $setting_fields = array(
            "success" => "1",
            "message" => "finish_player_activity_add_success",
        );
        $output_fields = array(
            'iActivityId',
        );
        $output_keys = array(
            'insert_player_activity_data',
        );

        $ouput_aliases = array(
            "iActivityId" => "activity_id",
        );

        $output_array["settings"] = $setting_fields;
        $output_array["settings"]["fields"] = $output_fields;
        $output_array["data"] = $input_params;

        $func_array["function"]["name"] = "player_activity_add";
        $func_array["function"]["output_keys"] = $output_keys;
        $func_array["function"]["output_alias"] = $ouput_aliases;
        $func_array["function"]["multiple_keys"] = $this->multiple_keys;

        $this->wsresponse->setResponseStatus(201);

        $responce_arr = $this->wsresponse->outputResponse($output_array, $func_array);

        return $responce_arr;
    }

    /**
     * finish_player_activity_add_failure method is used to process finish flow.
     * @created  | 28.01.2016
     * @modified ---
     * @param array $input_params input_params array to process loop flow.
     * @return array $responce_arr returns responce array of api.
     */
    public function finish_player_activity_add_failure($input_params = array())
    {
        $setting_fields = array(
            "success" => "0",
            "message" => "finish_player_activity_add_failure",
        );
        $output_fields = array();

        $output_array["settings"] = $setting_fields;
        $output_array["settings"]["fields"] = $output_fields;
        $output_array["data"] = $input_params;

        $func_array["function"]["name"] = "player_activity_add";
        $func_array["function"]["single_keys"] = $this->single_keys;

        $this->wsresponse->setResponseStatus(0);

        $responce_arr = $this->wsresponse->outputResponse($output_array, $func_array);

        return $responce_arr;
    }

    /**
     * get_player_activity_list_finish_success method is used to process finish flow.
     * @created priyanka chillakuru | 18.09.2019
     * @modified priyanka chillakuru | 18.09.2019
     * @param array $input_params input_params array to process loop flow.
     * @return array $responce_arr returns responce array of api.
     */
    public function get_player_activity_list_finish_success($input_params = array())
    {
        $setting_fields = array(
            "success" => "1",
            "message" => "get_player_activity_finish_success",
        );
        $output_fields = array(
            "iActivityId",
            "iLevelId",
            "iRoundId",
            "iCreditCoin",
            "iDebitCoin",
            "iUnlockStatus",
            "dtAddedAt",
            "dtUpdatedAt"
        );
        $output_keys = array(
            'get_player_activity',
        );
        $ouput_aliases = array(
            "iActivityId" => "activity_id",
            "iLevelId" => "level_id",
            "iRoundId" => "round_no",
            "iCreditCoin "=> "credit_coin",
            "iDebitCoin" => "debit_coin",
            "iUnlockStatus" => "unlock_status",
            "dtAddedAt" => "added_at",
            "dtUpdatedAt" => "updated_at",
        );

        $output_array["settings"] = $setting_fields;
        $output_array["settings"]["fields"] = $output_fields;
        $output_array["data"] = $input_params;

        $func_array["function"]["name"] = "player_activity_list";
        $func_array["function"]["output_keys"] = $output_keys;
        $func_array["function"]["output_alias"] = $ouput_aliases;
        $func_array["function"]["single_keys"] = $this->single_keys;
        $func_array["function"]["multiple_keys"] = $this->multiple_keys;
         
        $this->wsresponse->setResponseStatus(200);

        $responce_arr = $this->wsresponse->outputResponse($output_array, $func_array);
        
        return $responce_arr;
    }

    /**
     * get_player_activity_finish_success_1 method is used to process finish flow.
     * @created priyanka chillakuru | 18.09.2019
     * @modified priyanka chillakuru | 18.09.2019
     * @param array $input_params input_params array to process loop flow.
     * @return array $responce_arr returns responce array of api.
     */
    public function get_player_activity_finish_success_1($input_params = array())
    {
        $setting_fields = array(
            "success" => "0",
            "message" => "get_player_activity_finish_success_1",
        );
        $output_fields = array();

        $output_array["settings"] = $setting_fields;
        $output_array["settings"]["fields"] = $output_fields;
        $output_array["data"] = "";

        $func_array["function"]["name"] = "player_activity_list";
        $func_array["function"]["multiple_keys"] = $this->multiple_keys;

        $this->wsresponse->setResponseStatus(200);

        $responce_arr = $this->wsresponse->outputResponse($output_array, $func_array);

        return $responce_arr;
    }

    /**
    * delete_player_activity_finish_success method is used to process finish flow.
    * @created CIT Dev Team
    * @modified priyanka chillakuru | 16.09.2019
    * @param array $input_params input_params array to process loop flow.
    * @return array $responce_arr returns responce array of api.
    */
    public function delete_player_activity_finish_success($input_params = array())
    {
        $setting_fields = array(
            "success" => "1",
            "message" => "delete_player_activity_finish_success",
        );
        $output_fields = array();

        $output_array["settings"] = $setting_fields;
        $output_array["settings"]["fields"] = $output_fields;
        $output_array["data"] = $input_params;

        $func_array["function"]["name"] = "delete_player_activity";
        $func_array["function"]["single_keys"] = $this->single_keys;

        $this->wsresponse->setResponseStatus(200);

        $responce_arr = $this->wsresponse->outputResponse($output_array, $func_array);

        return $responce_arr;
    }


    /**
     * delete_player_activity_finish_success_1 method is used to process finish flow.
     * @created CIT Dev Team
     * @modified priyanka chillakuru | 16.09.2019
     * @param array $input_params input_params array to process loop flow.
     * @return array $responce_arr returns responce array of api.
     */
    public function delete_player_activity_finish_success_1($input_params = array())
    {
        $setting_fields = array(
            "success" => "0",
            "message" => "delete_player_activity_finish_success_1",
        );
        $output_fields = array();

        $output_array["settings"] = $setting_fields;
        $output_array["settings"]["fields"] = $output_fields;
        $output_array["data"] = $input_params;

        $func_array["function"]["name"] = "delete_player_activity";
        $func_array["function"]["single_keys"] = $this->single_keys;

        $this->wsresponse->setResponseStatus(200);

        $responce_arr = $this->wsresponse->outputResponse($output_array, $func_array);

        return $responce_arr;
    }
}
