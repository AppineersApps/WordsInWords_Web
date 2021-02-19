<?php
defined('BASEPATH') || exit('No direct script access allowed');

/**
 * Description of Player_activity Model
 *
 * @category webservice
 *
 * @packagep player_activity
 *
 * @subpackage models
 *
 * @module Player_activity
 *
 * @class Player_activity_model.php
 *
 * @path application\webservice\player_activity\models\Player_activity_model.php
 *
 * @version 4.4
 *
 * @author CIT Dev Team
 *
 * @since 06.09.2019
 */

class Player_activity_model extends CI_Model
{
    public $default_lang = 'EN';

    /**
     * __construct method is used to set model preferences while model object initialization.
     */
    public function __construct()
    {
        parent::__construct();
        $this->load->helper('listing');
        $this->default_lang = $this->general->getLangRequestValue();
        $this->load->model('purchase_coin/player_coin_model');
        $this->load->model('game_master/game_level_master_model');
    }

    /**
     * get_player_activity method is used to execute database queries for States List API.
     * @created priyanka chillakuru | 18.09.2019
     * @modified priyanka chillakuru | 18.09.2019
     * @param string $STATES_LIST_COUNTRY_ID STATES_LIST_COUNTRY_ID is used to process query block.
     * @param string $STATES_LIST_COUNTRY_CODE STATES_LIST_COUNTRY_CODE is used to process query block.
     * @return array $return_arr returns response of query block.
     */
    public function get_player_activity($params_arr = array(), $where_arr = array())
    {
        try {
            $result_arr = array();
            $this->db->from("player_activity");
            $this->db->select("*");
            $this->db->where("iUserId=", $params_arr["user_id"]);

            if (isset($where_arr["level_id"]) && $where_arr["level_id"] != "") {
                $this->db->where("iLevelId=", $where_arr["level_id"]);
            }

            $result_obj = $this->db->get();
            $result_arr = is_object($result_obj) ? $result_obj->result_array() : array();
            
            if (!is_array($result_arr) || count($result_arr) == 0) {
                throw new Exception('No records found.');
            }
            $success = 1;
        } catch (Exception $e) {
            $success = 0;
            $message = $e->getMessage();
        }
        
        $this->db->_reset_all();
        $return_arr["success"] = $success;
        $return_arr["message"] = $message;
        $return_arr["data"] = $result_arr;
        return $return_arr;
    }

    /**
     * get_sync_level_round method is used to execute database queries for States List API.
     * @created priyanka chillakuru | 18.09.2019
     * @modified priyanka chillakuru | 18.09.2019
     * @param string $STATES_LIST_COUNTRY_ID STATES_LIST_COUNTRY_ID is used to process query block.
     * @param string $STATES_LIST_COUNTRY_CODE STATES_LIST_COUNTRY_CODE is used to process query block.
     * @return array $return_arr returns response of query block.
     */
    public function get_sync_level_round($params_arr = array())
    {
        try {
            $result_arr = array();
            $this->db->from("player_activity");
            $this->db->select("*");
            $this->db->where("iUserId=", $params_arr["user_id"]);
            $result_obj = $this->db->get();
            $result_arr = is_object($result_obj) ? $result_obj->result_array() : array();
            
            if (!is_array($result_arr) || count($result_arr) == 0) {
                throw new Exception('No records found.');
            }
            $success = 1;
        } catch (Exception $e) {
            $success = 0;
            $message = $e->getMessage();
        }
        
        $this->db->_reset_all();
        //echo $this->db->last_query();
        $return_arr["success"] = $success;
        $return_arr["message"] = $message;
        $return_arr["data"] = $result_arr;

        return $return_arr;
    }

    /**
    * insert_player_activity_data method is used to execute database queries for Coin Add API.
    * @created  | 28.01.2016
    * @modified ---
    * @param array $params_arr params_arr array to process query block.
    * @return array $return_arr returns response of query block.
    */
    public function insert_or_update_player_activity_data($params_arr = array())
    {
        try {
            $this->db->trans_begin();
            $result_arr = array();
            $where_arr = array();
            $where_arr["level_id"] = $params_arr['level_id'];
            $playerActivityData = $this->get_player_activity($params_arr, $where_arr);
            $gameLevelConfig = $this->game_level_master_model->get_game_level_master_by_level($params_arr);
            $gameLevelConfig['data'][0]['iRoundToUnlock'];
            
            $params_arr['unlock_status']= 0;
            if ($gameLevelConfig['data'][0]['iRoundToUnlock'] == $params_arr['round_no']) {
                $params_arr['unlock_status']= 1;
            }
            if ($playerActivityData['success'] == 0) {
                $player_activity_data = $this->insert_player_activity_data($params_arr);
                $where_arr['activity_id'] = $player_activity_data['data'][0]["insert_id"];
            } else {
                $where_arr['activity_id'] = $playerActivityData['data'][0]["iActivityId"];
                $player_activity_data = $this->update_player_activity_data($params_arr, $where_arr);
            }

            if ($player_activity_data['success']) {
                $gain_coin = 0;

                if (isset($params_arr["gain_coins"])) {
                    $gain_coin = $params_arr["gain_coins"];
                }
                $result = Null;
                $result = $this->player_coin_model->get_player_coins($params_arr);

                if ($result['success'] == 0) {
                    $params_arr['total_coin'] = $gain_coin;
                    $player_data = $this->player_coin_model->insert_player_coins_data($params_arr);
                } else {
                    $params_arr['total_coin'] = $gain_coin + $result['data'][0]["iTotalCoin"];
                    $params_arr['player_coin_id'] = $result['data'][0]["iPlayerCoinId"];
                    $player_data = $this->player_coin_model->update_player_coins_data($params_arr);
                }
            }

            if ($player_data['success'] == 0) {
                $this->db->trans_rollback();
                throw new Exception("Failure in insertion.");
            }
            
            $this->db->from("player_activity");
            $this->db->select("*");
            if (isset($where_arr['activity_id']) && $where_arr['activity_id'] != "") {
                $this->db->where("iActivityId =", $where_arr['activity_id']);
            }
            $result_obj = $this->db->get();
            $result_arr = is_object($result_obj) ? $result_obj->result_array() : array();
            $success = 1;
        } catch (Exception $e) {
            $this->db->trans_rollback();
            $success = 0;
            $message = $e->getMessage();
        }

        $this->db->_reset_all();
        //echo $this->db->last_query();
        $this->db->trans_commit();
        $return_arr["success"] = $success;
        $return_arr["message"] = $message;
        $return_arr["data"] = $result_arr;
        return $return_arr;
    }

    
    /**
    * insert_player_activity_data method is used to execute database queries for Coin Add API.
    * @created  | 28.01.2016
    * @modified ---
    * @param array $params_arr params_arr array to process query block.
    * @return array $return_arr returns response of query block.
    */
    public function insert_player_activity_data($params_arr = array())
    {
        try {
            $result_arr = array();
            if (!is_array($params_arr) || count($params_arr) == 0) {
                throw new Exception("Insert data not found.");
            }
            if (isset($params_arr["user_id"])) {
                $this->db->set("iUserId", $params_arr["user_id"]);
            }
            if (isset($params_arr["level_id"])) {
                $this->db->set("iLevelId", $params_arr["level_id"]);
            }
            if (isset($params_arr["round_no"])) {
                $this->db->set("iRoundId", $params_arr["round_no"]);
            }
            if (isset($params_arr["gain_coins"])) {
                $this->db->set("iCreditCoin", $params_arr["gain_coins"]);
            }
            if (isset($params_arr["unlock_status"])) {
                $this->db->set("iUnlockStatus", $params_arr["unlock_status"]);
            }

            $this->db->insert("player_activity");
            $insert_id = $this->db->insert_id();

            if (!$insert_id) {
                throw new Exception("Failure in insertion.");
            }
            $result_param = "insert_id";
            $result_arr[0][$result_param] = $insert_id;
            $success = 1;
        } catch (Exception $e) {
            $success = 0;
            $message = $e->getMessage();
        }

        $this->db->_reset_all();
        //echo $this->db->last_query();
        $return_arr["success"] = $success;
        $return_arr["message"] = $message;
        $return_arr["data"] = $result_arr;

        return $return_arr;
    }

    /**
     * update_player_activity_data method is used to execute database queries for player_activity Update API.
     * @created  | 29.01.2016
     * @modified ---
     * @param array $params_arr params_arr array to process query block.
     * @param array $where_arr where_arr are used to process where condition(s).
     * @return array $return_arr returns response of query block.
     */
    public function update_player_activity_data($params_arr = array(), $where_arr = array())
    {
        try {
            $result_arr = array();
            $this->db->start_cache();
            if (isset($where_arr["activity_id"]) && $where_arr["activity_id"] != "") {
                $this->db->where("iActivityId=", $where_arr["activity_id"]);
            }
            $this->db->stop_cache();
            if (isset($params_arr["user_id"])) {
                $this->db->set("iUserId", $params_arr["user_id"]);
            }
            if (isset($params_arr["level_id"])) {
                $this->db->set("iLevelId", $params_arr["level_id"]);
            }
            if (isset($params_arr["round_no"])) {
                $this->db->set("iRoundId", $params_arr["round_no"]);
            }
            if (isset($params_arr["gain_coins"])) {
                $this->db->set("iCreditCoin", $params_arr["gain_coins"]);
            }
            if (isset($params_arr["unlock_status"])) {
                $this->db->set("iUnlockStatus", $params_arr["unlock_status"]);
            }
            
            $res = $this->db->update("player_activity");

            $affected_rows = $this->db->affected_rows();
            if (!$res || $affected_rows == -1) {
                throw new Exception("Failure in updation.");
            }
            $result_param = "affected_rows";
            $result_arr[0][$result_param] = $affected_rows;
            $success = 1;
        } catch (Exception $e) {
            $success = 0;
            $message = $e->getMessage();
        }
        $this->db->flush_cache();
        $this->db->_reset_all();
        $return_arr["success"] = $success;
        $return_arr["message"] = $message;
        $return_arr["data"] = $result_arr;
        return $return_arr;
    }
    
    public function delete_player_activity($params_arr = array())
    {
        try {
            $result_arr = array();
            $this->db->start_cache();
            if (isset($params_arr["activity_id"])) {
                $this->db->where("iActivityId =", $params_arr["activity_id"]);
            }
            $this->db->stop_cache();

            $res = $this->db->delete("player_activity");

            $affected_rows = $this->db->affected_rows();
            if (!$res || $affected_rows == -1) {
                throw new Exception("Failure in deletion.");
            }
            $result_param = "affected_rows";
            $result_arr[0][$result_param] = $affected_rows;
            $success = 1;
        } catch (Exception $e) {
            $success = 0;
            $message = $e->getMessage();
        }
        $this->db->flush_cache();
        $this->db->_reset_all();
        //echo $this->db->last_query();
        $return_arr["success"] = $success;
        $return_arr["message"] = $message;
        $return_arr["data"] = $result_arr;
        return $return_arr;
    }

    /**
    * insert_hint_coin_data method is used to execute database queries for Coin Add API.
    * @created  | 28.01.2016
    * @modified ---
    * @param array $params_arr params_arr array to process query block.
    * @return array $return_arr returns response of query block.
    */
    public function insert_hint_coin_data($params_arr = array())
    {
        try {
            $this->db->trans_begin();
            $result_arr = array();
            $result = $this->player_coin_model->get_player_coins($params_arr);

            if ($result['success']) {
                $params_arr['total_coin'] = $result['data'][0]["iTotalCoin"]-(int)$params_arr['hint_coins'] ;
                $params_arr['player_coin_id'] = $result['data'][0]["iPlayerCoinId"];
                $player_data = $this->player_coin_model->update_player_coins_data($params_arr);
            }
            if ($player_data['success'] == 0) {
                $this->db->trans_rollback();
            }

            $result = $this->player_coin_model->get_player_coins($params_arr);
            if ($result['success']) {
                $result_arr = $result['data'][0];
            }
            $success = 1;
        } catch (Exception $e) {
            $this->db->trans_rollback();
            $success = 0;
            $message = $e->getMessage();
        }

        $this->db->_reset_all();
        //echo $this->db->last_query();
        $this->db->trans_commit();
        $return_arr["success"] = $success;
        $return_arr["message"] = $message;
        $return_arr["data"] = $result_arr;
        return $return_arr;
    }

    /**
    * insert_buy_word_data method is used to execute database queries for Coin Add API.
    * @created  | 28.01.2016
    * @modified ---
    * @param array $params_arr params_arr array to process query block.
    * @return array $return_arr returns response of query block.
    */
    public function insert_buy_word_data($params_arr = array())
    {
        try {
            $this->db->trans_begin();
            $result_arr = array();
            $result = $this->player_coin_model->get_player_coins($params_arr);

            if ($result['success']) {
                $params_arr['total_coin'] = $result['data'][0]["iTotalCoin"]-(int)$params_arr['buy_word_coins'] ;
                $params_arr['player_coin_id'] = $result['data'][0]["iPlayerCoinId"];
                $player_data = $this->player_coin_model->update_player_coins_data($params_arr);
            }
            if ($player_data['success'] == 0) {
                $this->db->trans_rollback();
            }

            $result = $this->player_coin_model->get_player_coins($params_arr);
            if ($result['success']) {
                $result_arr = $result['data'][0];
            }
            $success = 1;
        } catch (Exception $e) {
            $this->db->trans_rollback();
            $success = 0;
            $message = $e->getMessage();
        }

        $this->db->_reset_all();
        //echo $this->db->last_query();
        $this->db->trans_commit();
        $return_arr["success"] = $success;
        $return_arr["message"] = $message;
        $return_arr["data"] = $result_arr;
        return $return_arr;
    }
}
