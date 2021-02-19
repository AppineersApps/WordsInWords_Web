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
 * @module Buy_word Add
 *
 * @class Buy_word.php
 *
 * @path application\webservice\player_activity\controllers\Buy_word.php
 *
 * @version 4.4
 *
 * @author CIT Dev Team
 *
 * @since 06.09.2019
 */
class Buy_word extends Cit_Controller
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
    public function rules_buy_word($request_arr = array())
    {
        $valid_arr = array(
            "buy_word_coins" => array(
                array(
                    "rule" => "required",
                    "value" => true,
                    "message" => "buy_word_coins_required",
                )
            )
        );
        $this->wsresponse->setResponseStatus(422);
        $valid_res = $this->wsresponse->validateInputParams($valid_arr, $request_arr, "add_buy_word");
        
        return $valid_res;
    }

    /**
     * start_buy_word_add method is used to initiate api execution flow.
     * @created  | 28.01.2016
     * @modified ---
     * @param array $request_arr request_arr array is used for api input.
     * @param bool $inner_api inner_api flag is used to idetify whether it is inner api request or general request.
     * @return array $output_response returns output response of API.
     */
    public function start_buy_word($request_arr = array(), $inner_api = false)
    {
        $method = $_SERVER['REQUEST_METHOD'];
        $output_response = array();

        $output_response =  $this->addBuyWord($request_arr);

        return  $output_response;
    }

    public function addBuyWord($input_params)
    {
        try {
            $validation_res = $this->rules_buy_word($input_params);
            if ($validation_res["success"] == "-5") {
                if ($inner_api === true) {
                    return $validation_res;
                } else {
                    $this->wsresponse->sendValidationResponse($validation_res);
                }
            }
            $output_response = array();
            $input_params = $validation_res['input_params'];
            $input_params = $this->check_player_sufficent_coins($input_params);

            $key = "check_player_sufficent_coins";
            $condition_res = $this->condition($input_params, $key);
            ;
            if ($condition_res["success"]) {
                $input_params = $this->insert_buy_word_data($input_params);

                $key = "insert_buy_word_data";
                $condition_res = $this->condition($input_params, $key);

                if ($condition_res["success"]) {
                    $output_response = $this->finish_buy_word_add_success($input_params);
                    return $output_response;
                } else {
                    $output_response = $this->finish_buy_word_add_failure($input_params);
                    return $output_response;
                }
            } else {
                $output_response = $this->finish_check_player_sufficent_coins_failure($input_params);
                return $output_response;
            }
        } catch (Exception $e) {
            $message = $e->getMessage();
        }
        return $output_response;
    }

    /**
     * check_player_sufficent_coins method is used to process query block.
     * @created  | 29.01.2016
     * @modified ---
     * @param array $input_params input_params array to process loop flow.
     * @return array $input_params returns modfied input_params array.
     */
    public function check_player_sufficent_coins($input_params = array())
    {
        $this->block_result = array();
        try {
            $this->block_result = $this->player_coin_model->check_player_sufficent_coins($input_params);
            if (!$this->block_result["success"]) {
                throw new Exception("No records found.");
            }
            if (isset($input_params["buy_word_coins"])) {
                if ($this->block_result['data'][0]['iTotalCoin'] < $input_params["buy_word_coins"]) {
                    throw new Exception("You have insufficient coins, Please purchase more coins.");
                }
            }
        } catch (Exception $e) {
            $success = 0;
            $this->block_result["data"] = array();
        }
        
        $input_params["check_player_sufficent_coins"] = $this->block_result["data"];
        $input_params = $this->wsresponse->assignSingleRecord($input_params, $this->block_result["data"]);

        return $input_params;
    }

    /**
     * insert_buy_word_data method is used to process query block.
     * @created  | 28.01.2016
     * @modified ---
     * @param array $input_params input_params array to process loop flow.
     * @return array $input_params returns modfied input_params array.
     */
    public function insert_buy_word_data($input_params = array())
    {
        $this->block_result = array();
        try {
            $params_arr = array();

            $this->block_result = $this->player_activity_model->insert_buy_word_data($input_params);
            if (!$this->block_result["success"]) {
                throw new Exception("Insertion failed.");
            }
            $data_arr = $this->block_result["array"];
        } catch (Exception $e) {
            $success = 0;
            $this->block_result["data"] = array();
        }
        $input_params["insert_buy_word_data"] = $this->block_result["data"];
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
     * finish_buy_word_add_success method is used to process finish flow.
     * @created  | 28.01.2016
     * @modified ---
     * @param array $input_params input_params array to process loop flow.
     * @return array $responce_arr returns responce array of api.
     */
    public function finish_buy_word_add_success($input_params = array())
    {
        $setting_fields = array(
            "success" => "1",
            "message" => "finish_buy_word_add_success",
        );
        $output_fields = array(
            'iTotalCoin'
        );
        $output_keys = array(
            'insert_buy_word_data',
        );

        $ouput_aliases = array(
            "iTotalCoin" => "total_coins",
        );

        $output_array["settings"] = $setting_fields;
        $output_array["settings"]["fields"] = $output_fields;
        $output_array["data"] = $input_params;

        $func_array["function"]["name"] = "buy_word_add";
        $func_array["function"]["output_keys"] = $output_keys;
        $func_array["function"]["output_alias"] = $ouput_aliases;
        $func_array["function"]["multiple_keys"] = $this->multiple_keys;

        $this->wsresponse->setResponseStatus(201);

        $responce_arr = $this->wsresponse->outputResponse($output_array, $func_array);

        return $responce_arr;
    }

    /**
     * finish_buy_word_add_failure method is used to process finish flow.
     * @created  | 28.01.2016
     * @modified ---
     * @param array $input_params input_params array to process loop flow.
     * @return array $responce_arr returns responce array of api.
     */
    public function finish_buy_word_add_failure($input_params = array())
    {
        $setting_fields = array(
            "success" => "0",
            "message" => "finish_buy_word_add_failure",
        );
        $output_fields = array();

        $output_array["settings"] = $setting_fields;
        $output_array["settings"]["fields"] = $output_fields;
        $output_array["data"] = $input_params;

        $func_array["function"]["name"] = "buy_word_add";
        $func_array["function"]["single_keys"] = $this->single_keys;

        $this->wsresponse->setResponseStatus(0);

        $responce_arr = $this->wsresponse->outputResponse($output_array, $func_array);

        return $responce_arr;
    }

    /**
    * finish_check_player_sufficent_coins_failure method is used to process finish flow.
    * @created priyanka chillakuru | 12.09.2019
    * @modified priyanka chillakuru | 13.09.2019
    * @param array $input_params input_params array to process loop flow.
    * @return array $responce_arr returns responce array of api.
    */
    public function finish_check_player_sufficent_coins_failure($input_params = array())
    {
        $setting_fields = array(
            "success" => "0",
            "message" => "finish_check_player_sufficent_coins_failure",
        );
        $output_fields = array();

        $output_array["settings"] = $setting_fields;
        $output_array["settings"]["fields"] = $output_fields;
        $output_array["data"] = $input_params;

        $func_array["function"]["name"] = "check_player_sufficent";
        $func_array["function"]["single_keys"] = $this->single_keys;
        $func_array["function"]["multiple_keys"] = $this->multiple_keys;

        $this->wsresponse->setResponseStatus(200);

        $responce_arr = $this->wsresponse->outputResponse($output_array, $func_array);

        return $responce_arr;
    }
}
