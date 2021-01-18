<?php
defined('BASEPATH') || exit('No direct script access allowed');

/**
 * Description of Player activity sync Controller
 *
 * @category webservice
 *
 * @package user
 *
 * @subpackage controllers
 *
 * @module Sync_level_round
 *
 * @class Sync_level_round.php
 *
 * @path application\webservice\player_activity\controllers\Sync_level_round.php
 *
 * @version 4.4
 *
 * @author CIT Dev Team
 *
 * @since 06.09.2019
 */
class Sync_level_round extends Cit_Controller
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
            "get_sync_level_round"
        );
        
        $this->block_result = array();

        $this->load->library('wsresponse');
        $this->load->model('player_activity_model');
        $this->load->model('game_master/game_level_master_model');
        $this->load->model('purchase_coin/player_coin_model');
    }

    /**
     * start_sync_level_round_add method is used to initiate api execution flow.
     * @created  | 28.01.2016
     * @modified ---
     * @param array $request_arr request_arr array is used for api input.
     * @param bool $inner_api inner_api flag is used to idetify whether it is inner api request or general request.
     * @return array $output_response returns output response of API.
     */
    public function start_sync_level_round($request_arr = array(), $inner_api = false)
    {
        $method = $_SERVER['REQUEST_METHOD'];
        $output_response = array();

        switch ($method) {
            case 'GET':
                $output_response =  $this->getSyncLevelRound($request_arr);

                return  $output_response;
                break;
        }
    }

    /**
     * getSyncLevelRound method is used to initiate api execution flow.
     * @created kavita sawant | 08.01.2020
     * @modified kavita sawant | 08.01.2020
     * @param array $request_arr request_arr array is used for api input.
     * @param bool $inner_api inner_api flag is used to idetify whether it is inner api request or general request.
     * @return array $output_response returns output response of API.
     */
    public function getSyncLevelRound($request_arr = array(), $inner_api = false)
    {
        try {
            $output_response = array();
            $output_array = $func_array = array();
            $input_params = $this->get_sync_level_round($request_arr);
            
            $key = "get_sync_level_round";
            $condition_res = $this->condition($input_params, $key);
            
            if ($condition_res["success"]) {
                $output_response = $this->get_sync_level_round_list_finish_success($input_params);

                return $output_response;
            } else {
                $output_response = $this->get_sync_level_round_finish_success_1($input_params);

                return $output_response;
            }
        } catch (Exception $e) {
            $message = $e->getMessage();
        }
        return $output_response;
    }

    /**
     * get_sync_level_round method is used to process query block.
     * @created priyanka chillakuru | 18.09.2019
     * @modified priyanka chillakuru | 18.09.2019
     * @param array $input_params input_params array to process loop flow.
     * @return array $input_params returns modfied input_params array.
     */
    public function get_sync_level_round($input_params = array())
    {
        $this->block_result = array();
        $this->block_result0 = array();
        try {
            $this->block_result = $this->player_activity_model->get_sync_level_round($input_params);
            $this->block_result0 = $this->game_level_master_model->get_game_level_master($input_params);

            $gameLevelInfo = [];
            $wordCoinInfo = [];
            $newKey = 0;
            $newUpKey=0;
            if (empty($this->block_result['data'])) {
                foreach ($this->block_result0['data'] as $configKey => $configValue) {
                    $gameLevelInfo[$newKey]['level_name'] = $configValue["vLevelName"];
                    $gameLevelInfo[$newKey]['level_id'] = $configValue["iGameLevelId"];
                    $gameLevelInfo[$newKey]['round_id'] = 0;
                    $gameLevelInfo[$newKey]['gain_coin'] = 0;
                    $gameLevelInfo[$newKey]['loss_coin'] = 0;
                    $gameLevelInfo[$newKey]['unlock_status'] = (bool)0;
                    ++$newKey;
                }
            } else {
                foreach ($this->block_result0['data'] as $configKey => $configValue) {
                    foreach ($this->block_result['data'] as $activityKey => $activityValue) {
                        if ($configValue['iGameLevelId'] == $activityValue['iLevelId']) {
                            $gameLevelInfo[$newKey]['level_id'] = $activityValue["iLevelId"];
                            $gameLevelInfo[$newKey]['level_name'] = $configValue["vLevelName"];
                            $gameLevelInfo[$newKey]['round_id'] = $activityValue["iRoundId"];
                            $gameLevelInfo[$newKey]['gain_coin'] = $activityValue["iCreditCoin"];
                            $gameLevelInfo[$newKey]['loss_coin'] = $activityValue["iDebitCoin"];
                            $gameLevelInfo[$newKey]['unlock_status'] = $activityValue["iUnlockStatus"];
                            ++$newKey;
                        } else {
                            $gameLevelInfo[$newKey]['level_name'] = $configValue["vLevelName"];
                            $gameLevelInfo[$newKey]['level_id'] = $configValue["iGameLevelId"];
                            $gameLevelInfo[$newKey]['round_id'] = 0;
                            $gameLevelInfo[$newKey]['gain_coin'] = 0;
                            $gameLevelInfo[$newKey]['loss_coin'] = 0;
                            $gameLevelInfo[$newKey]['unlock_status'] = (bool)0;
                            ++$newKey;
                        }
                    }
                }
            }
           
            $this->block_result = $this->player_coin_model->get_player_coins($input_params);
            if ($this->block_result["success"]) {
                foreach ($this->block_result['data'] as $configKey => $configValue) {
                    $coinInfo[$newUpKey]['total_coin'] = $configValue["iTotalCoin"];
                    ++$newUpKey;
                }
            } else {
                $coinInfo[$newUpKey]['total_coin'] = 0;
            }

            $this->block_result['data']=null;

            $this->block_result['data'][0]['sync_level_rounds'] = $gameLevelInfo;
            $this->block_result['data'][0]['sync_coins'] = $coinInfo;
        } catch (Exception $e) {
            $success = 0;
            $this->block_result["data"] = array();
        }
        
        $input_params["get_sync_level_round"] = $this->block_result["data"];
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
     * get_sync_level_round_list_finish_success method is used to process finish flow.
     * @created priyanka chillakuru | 18.09.2019
     * @modified priyanka chillakuru | 18.09.2019
     * @param array $input_params input_params array to process loop flow.
     * @return array $responce_arr returns responce array of api.
     */
    public function get_sync_level_round_list_finish_success($input_params = array())
    {
        $setting_fields = array(
            "success" => "1",
            "message" => "get_sync_level_round_finish_success",
        );
        $output_fields = array(
            "sync_level_rounds",
            "sync_coins"
        );
        $output_keys = array(
            'get_sync_level_round',
        );
        $ouput_aliases = array(
            "sync_level_rounds" => "sync_level_rounds",
            "sync_coins" => "sync_coins"
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
     * get_sync_level_round_finish_success_1 method is used to process finish flow.
     * @created priyanka chillakuru | 18.09.2019
     * @modified priyanka chillakuru | 18.09.2019
     * @param array $input_params input_params array to process loop flow.
     * @return array $responce_arr returns responce array of api.
     */
    public function get_sync_level_round_finish_success_1($input_params = array())
    {
        $setting_fields = array(
            "success" => "0",
            "message" => "get_sync_level_round_finish_success_1",
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
}
