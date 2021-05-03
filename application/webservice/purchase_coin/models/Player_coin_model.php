<?php
defined('BASEPATH') || exit('No direct script access allowed');

/**
 * Description of Player_activity Model
 *
 * @category webservice
 *
 * @packagep player_coins
 *
 * @subpackage models
 *
 * @module Player_activity
 *
 * @class player_coin_model.php
 *
 * @path application\webservice\player_coins\models\player_coin_model.php
 *
 * @version 4.4
 *
 * @author CIT Dev Team
 *
 * @since 06.09.2019
 */

class Player_coin_model extends CI_Model
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
    }

    /**
     * get_player_coins method is used to execute database queries for States List API.
     * @created priyanka chillakuru | 18.09.2019
     * @modified priyanka chillakuru | 18.09.2019
     * @param string $STATES_LIST_COUNTRY_ID STATES_LIST_COUNTRY_ID is used to process query block.
     * @param string $STATES_LIST_COUNTRY_CODE STATES_LIST_COUNTRY_CODE is used to process query block.
     * @return array $return_arr returns response of query block.
     */
    public function get_player_coins($params_arr = array())
    {
        try {
            $result_arr = array();
            $this->db->from("player_coins");
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
        $return_arr["success"] = $success;
        $return_arr["message"] = $message;
        $return_arr["data"] = $result_arr;

        return $return_arr;
    }

     /**
     * get_player_coins method is used to execute database queries for States List API.
     * @created priyanka chillakuru | 18.09.2019
     * @modified priyanka chillakuru | 18.09.2019
     * @param string $STATES_LIST_COUNTRY_ID STATES_LIST_COUNTRY_ID is used to process query block.
     * @param string $STATES_LIST_COUNTRY_CODE STATES_LIST_COUNTRY_CODE is used to process query block.
     * @return array $return_arr returns response of query block.
     */
    public function check_player_sufficent_coins($params_arr = array())
    {
        try {
            $result_arr = array();
            $this->db->from("player_coins");
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
        $return_arr["success"] = $success;
        $return_arr["message"] = $message;
        $return_arr["data"] = $result_arr;

        return $return_arr;
    }

    /**
    * insert_player_coins_data method is used to execute database queries for player_coins Add API.
    * @created  | 28.01.2016
    * @modified ---
    * @param array $params_arr params_arr array to process query block.
    * @return array $return_arr returns response of query block.
    */
    public function insert_player_coins_data($params_arr = array())
    {
        try {
            $result_arr = array();
            if (!is_array($params_arr) || count($params_arr) == 0) {
                throw new Exception("Insert data not found.");
            }
            if (isset($params_arr["user_id"])) {
                $this->db->set("iUserId", $params_arr["user_id"]);
            }
            if (isset($params_arr["total_coin"])) {
                $this->db->set("iTotalCoin", $params_arr["total_coin"]);
            }

            $this->db->insert("player_coins");
            $insert_id = $this->db->insert_id();
            if (!$insert_id) {
                throw new Exception("Failure in insertion.");
            }
            $result_param = "insert_id";
            $result_arr[0][$result_param] = $insert_id;
            $success = 1;
        }
        catch(Exception $e)
        {
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
     * update_player_coins_data method is used to execute database queries for player_coins Update API.
     * @created  | 29.01.2016
     * @modified ---
     * @param array $params_arr params_arr array to process query block.
     * @param array $where_arr where_arr are used to process where condition(s).
     * @return array $return_arr returns response of query block.
     */
    public function update_player_coins_data($params_arr = array(), $where_arr = array())
    {
        try {
            $result_arr = array();
            $this->db->start_cache();
            if (isset($where_arr["player_coin_id"]) && $where_arr["player_coin_id"] != "") {
                $this->db->where("iPlayerCoinId=", $where_arr["player_coin_id"]);
            }
            if (isset($params_arr["user_id"])) {
                $this->db->where("iUserId", $params_arr["user_id"]);
            }
            
            $this->db->stop_cache();
            if (isset($params_arr["total_coin"])) {
                $this->db->set("iTotalCoin", $params_arr["total_coin"]);
            }
            $res = $this->db->update("player_coins");

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
}
