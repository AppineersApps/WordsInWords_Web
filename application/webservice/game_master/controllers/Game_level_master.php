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
 * @module Game_level_master Add
 *
 * @class Game_level_master.php
 *
 * @path application\webservice\game_level_master\controllers\Game_level_master.php
 *
 * @version 4.4
 *
 * @author CIT Dev Team
 *
 * @since 06.09.2019
 */
class Game_level_master extends Cit_Controller
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
            "get_game_level_master"
        );
        
        $this->block_result = array();

        $this->load->library('wsresponse');
        $this->load->model('game_level_master_model');
    }

    /**
     * start_game_level_master_add method is used to initiate api execution flow.
     * @created  | 28.01.2016
     * @modified ---
     * @param array $request_arr request_arr array is used for api input.
     * @param bool $inner_api inner_api flag is used to idetify whether it is inner api request or general request.
     * @return array $output_response returns output response of API.
     */
    public function start_game_level_master($request_arr = array(), $inner_api = FALSE)
    {
        $method = $_SERVER['REQUEST_METHOD'];
        $output_response = array();

        switch ($method) {
            case 'GET':
                $output_response =  $this->getGame_level_masters($request_arr);

                return  $output_response;
                break;
            case 'DELETE':
                $output_response = $this->deleteGame_level_masters($request_arr);

                return  $output_response;
                break;
        }
    }

    /**
     * get_game_level_master method is used to initiate api execution flow.
     * @created kavita sawant | 08.01.2020
     * @modified kavita sawant | 08.01.2020
     * @param array $request_arr request_arr array is used for api input.
     * @param bool $inner_api inner_api flag is used to idetify whether it is inner api request or general request.
     * @return array $output_response returns output response of API.
     */
    public function getGame_level_masters($request_arr = array(), $inner_api = FALSE)
    {
        try {
            $output_response = array();
            $output_array = $func_array = array();
            $input_params = $this->get_game_level_master($request_arr);
            
            $key = "get_game_level_master";
            $condition_res = $this->condition($input_params, $key);
            
            if ($condition_res["success"]) {
                $output_response = $this->get_game_level_master_list_finish_success($input_params);

                return $output_response;
            } else {
                $output_response = $this->get_game_level_master_finish_success_1($input_params);

                return $output_response;
            }
        } catch(Exception $e) {
            $message = $e->getMessage();
        }
        return $output_response;
    }

    /**
     * get_game_level_master method is used to process query block.
     * @created priyanka chillakuru | 18.09.2019
     * @modified priyanka chillakuru | 18.09.2019
     * @param array $input_params input_params array to process loop flow.
     * @return array $input_params returns modfied input_params array.
     */
    public function get_game_level_master($input_params = array())
    {
        $this->block_result = array();
        try {
            $this->block_result = $this->game_level_master_model->get_game_level_master($input_params);
            if (!$this->block_result["success"]) {
                throw new Exception("No records found.");
            }

        } catch(Exception $e) {
            $success = 0;
            $this->block_result["data"] = array();
        }
        
        $input_params["get_game_level_master"] = $this->block_result["data"];
        $input_params = $this->wsresponse->assignSingleRecord($input_params, $this->block_result["data"]);
       
        return $input_params;
    }

    /**
     * deleteNotifications method is used to initiate api execution flow.
     * @created kavita sawant | 08.01.2020
     * @modified kavita sawant | 08.01.2020
     * @param array $request_arr request_arr array is used for api input.
     * @param bool $inner_api inner_api flag is used to idetify whether it is inner api request or general request.
     * @return array $output_response returns output response of API.
     */
    public function deleteNotifications($request_arr = array(), $inner_api = FALSE)
    {
        try {
            $output_response = array();
            $input_params = $validation_res['input_params'];
            $output_array = $func_array = array();
            
            $input_params = $this->delete_game_level_master($request_arr);
              
            $key = "delete_game_level_master";
            $condition_res = $this->condition($input_params, $key);
            
            if ($condition_res["success"]) {
                $output_response = $this->delete_game_level_master_finish_success($input_params);
                return $output_response;
            } else {
                $output_response = $this->delete_game_level_master_finish_success_1($input_params);
                return $output_response;
            }
        } catch (Exception $e) {
            $message = $e->getMessage();
        }
        return $output_response;
    }

    public function delete_game_level_master($input_params = array())
    {
        $this->block_result = array();
        try {
            $arrResult = array();
           
            $this->block_result = $this->game_level_master_model->delete_game_level_master($input_params);

            if (!$this->block_result["success"]) {
                throw new Exception("No records found.");
            }
            $result_arr = $this->block_result["data"];
           
          $this->block_result["data"] = $result_arr;
        } catch (Exception $e) {
            $success = 0;
            $this->block_result["data"] = array();
        }
        $input_params["delete_game_level_master"] = $this->block_result["data"];
        
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

            $cc_fr_0 = ($cc_lo_0 == $cc_ro_0) ? TRUE : FALSE;
            if (!$cc_fr_0) {
                throw new Exception("Some conditions does not match.");
            }
            $success = 1;
            $message = "Conditions matched.";
        } catch(Exception $e) {
            $success = 0;
            $message = $e->getMessage();
        }
        $this->block_result["success"] = $success;
        $this->block_result["message"] = $message;
        return $this->block_result;
    }

    /**
     * get_game_level_master_list_finish_success method is used to process finish flow.
     * @created priyanka chillakuru | 18.09.2019
     * @modified priyanka chillakuru | 18.09.2019
     * @param array $input_params input_params array to process loop flow.
     * @return array $responce_arr returns responce array of api.
     */
    public function get_game_level_master_list_finish_success($input_params = array())
    {
        $setting_fields = array(
            "success" => "1",
            "message" => "get_game_level_master_finish_success",
        );
        $output_fields = array(
            "iGameLevelId",
            "vLevelName",
            "tDescription",
            "iMaxWordLength",
            "iMaxRound",
            "iRoundToUnlock"
        );
        $output_keys = array(
            'get_game_level_master',
        );
        $ouput_aliases = array(
            "iGameLevelId" => "game_level_id",
            "vLevelName" => "level_name",
            "tDescription" => "description",
            "iMaxWordLength" => "max_word_length",
            "iMaxRound" => "max_round",
            "iRoundToUnlock" => "round_to_unlock",
        );

        $output_array["settings"] = $setting_fields;
        $output_array["settings"]["fields"] = $output_fields;
        $output_array["data"] = $input_params;

        $func_array["function"]["name"] = "game_level_master_list";
        $func_array["function"]["output_keys"] = $output_keys;
        $func_array["function"]["output_alias"] = $ouput_aliases;
        $func_array["function"]["single_keys"] = $this->single_keys;
        $func_array["function"]["multiple_keys"] = $this->multiple_keys;
         
        $this->wsresponse->setResponseStatus(200);

        $responce_arr = $this->wsresponse->outputResponse($output_array, $func_array);
        
        return $responce_arr;
    }

    /**
     * get_game_level_master_finish_success_1 method is used to process finish flow.
     * @created priyanka chillakuru | 18.09.2019
     * @modified priyanka chillakuru | 18.09.2019
     * @param array $input_params input_params array to process loop flow.
     * @return array $responce_arr returns responce array of api.
     */
    public function get_game_level_master_finish_success_1($input_params = array())
    {
        $setting_fields = array(
            "success" => "0",
            "message" => "get_game_level_master_finish_success_1",
        );
        $output_fields = array();

        $output_array["settings"] = $setting_fields;
        $output_array["settings"]["fields"] = $output_fields;
        $output_array["data"] = "";

        $func_array["function"]["name"] = "game_level_master_list";
        $func_array["function"]["multiple_keys"] = $this->multiple_keys;

        $this->wsresponse->setResponseStatus(200);

        $responce_arr = $this->wsresponse->outputResponse($output_array, $func_array);

        return $responce_arr;
    }

     /**
     * delete_game_level_master_finish_success method is used to process finish flow.
     * @created CIT Dev Team
     * @modified priyanka chillakuru | 16.09.2019
     * @param array $input_params input_params array to process loop flow.
     * @return array $responce_arr returns responce array of api.
     */
    public function delete_game_level_master_finish_success($input_params = array())
    {
        $setting_fields = array(
            "success" => "1",
            "message" => "delete_game_level_master_finish_success",
        );
        $output_fields = array();

        $output_array["settings"] = $setting_fields;
        $output_array["settings"]["fields"] = $output_fields;
        $output_array["data"] = $input_params;

        $func_array["function"]["name"] = "delete_game_level_master";
        $func_array["function"]["single_keys"] = $this->single_keys;

        $this->wsresponse->setResponseStatus(200);

        $responce_arr = $this->wsresponse->outputResponse($output_array, $func_array);

        return $responce_arr;
    }


    /**
     * delete_game_level_master_finish_success_1 method is used to process finish flow.
     * @created CIT Dev Team
     * @modified priyanka chillakuru | 16.09.2019
     * @param array $input_params input_params array to process loop flow.
     * @return array $responce_arr returns responce array of api.
     */
    public function delete_game_level_master_finish_success_1($input_params = array())
    {
        $setting_fields = array(
            "success" => "0",
            "message" => "delete_game_level_master_finish_success_1",
        );
        $output_fields = array();

        $output_array["settings"] = $setting_fields;
        $output_array["settings"]["fields"] = $output_fields;
        $output_array["data"] = $input_params;

        $func_array["function"]["name"] = "delete_game_level_master";
        $func_array["function"]["single_keys"] = $this->single_keys;

        $this->wsresponse->setResponseStatus(200);

        $responce_arr = $this->wsresponse->outputResponse($output_array, $func_array);

        return $responce_arr;
    }
}
