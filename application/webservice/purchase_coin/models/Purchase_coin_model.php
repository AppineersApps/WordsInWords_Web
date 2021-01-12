<?php
defined('BASEPATH') || exit('No direct script access allowed');

/**
 * Description of Player_activity Model
 *
 * @category webservice
 *
 * @packagep purchase_coin
 *
 * @subpackage models
 *
 * @module Player_activity
 *
 * @class Purchase_coin_model.php
 *
 * @path application\webservice\purchase_coin\models\Purchase_coin_model.php
 *
 * @version 4.4
 *
 * @author CIT Dev Team
 *
 * @since 06.09.2019
 */

class Purchase_coin_model extends CI_Model
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
        $this->load->model('player_coin_model');
    }

    /**
     * get_purchase_coin method is used to execute database queries for States List API.
     * @created priyanka chillakuru | 18.09.2019
     * @modified priyanka chillakuru | 18.09.2019
     * @param string $STATES_LIST_COUNTRY_ID STATES_LIST_COUNTRY_ID is used to process query block.
     * @param string $STATES_LIST_COUNTRY_CODE STATES_LIST_COUNTRY_CODE is used to process query block.
     * @return array $return_arr returns response of query block.
     */
    public function get_purchase_coin($params_arr = array())
    {
        try {
            $result_arr = array();
            $this->db->from("purchase_coin");
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
     * insert_purchase_coin_data method is used to execute database queries for purchase_coin Add API.
     * @created  | 28.01.2016
     * @modified ---
     * @param array $params_arr params_arr array to process query block.
     * @return array $return_arr returns response of query block.
     */
    public function insert_purchase_coin_data($params_arr = array())
    {
        try {
            $this->db->trans_begin();
            $result_arr = array();
            if (!is_array($params_arr) || count($params_arr) == 0) {
                throw new Exception("Insert data not found.");
            }
            if (isset($params_arr["user_id"])) {
                $this->db->set("iUserId", $params_arr["user_id"]);
            }
            if (isset($params_arr["transaction_id"])) {
                $this->db->set("iTransactionId", $params_arr["transaction_id"]);
            }
            if (isset($params_arr["transaction_date"])) {
                $this->db->set("iTransactionDate", $params_arr["transaction_date"]);
            }
            if (isset($params_arr["purchased_coin"])) {
                $this->db->set("iPurchasedCoin", $params_arr["purchased_coin"]);
            }
            if (isset($params_arr["transaction_amount"])) {
                $this->db->set("dTransactionAmount", $params_arr["transaction_amount"]);
            }

            $this->db->insert("purchase_coins");
            $insert_id = $this->db->insert_id();
            if (isset($insert_id))
            {
                $result = $this->player_coin_model->get_player_coins($params_arr);

                if($result['success'] == 0) {
                    $params_arr['total_coin'] = $params_arr["purchased_coin"];
                    $player_data = $this->player_coin_model->insert_player_coins_data($params_arr);
                } else {

                    $params_arr['total_coin'] = $params_arr["purchased_coin"] + $result['data'][0]["iTotalCoin"];
                    $params_arr['player_coin_id'] = $result['data'][0]["iPlayerCoinId"];
                    $player_data = $this->player_coin_model->update_player_coins_data($params_arr);
                }
            }

            if (!$insert_id || $player_data['success'] == 0)
            {
                $this->db->trans_rollback();
                throw new Exception("Failure in insertion.");
            }
            
            $this->db->from("purchase_coins");
            $this->db->select("*");
            if (isset($insert_id) && $insert_id != "") {
                $this->db->where("iPurchaseCoinId =", $insert_id);
            }
            $result_obj = $this->db->get();
            $result_arr = is_object($result_obj) ? $result_obj->result_array() : array();
            $success = 1;

        } catch(Exception $e) {
            $this->db->trans_rollback();
            $success = 0;
            $message = $e->getMessage();
        }

        $this->db->_reset_all();
        $this->db->trans_commit();

        $return_arr["success"] = $success;
        $return_arr["message"] = $message;
        $return_arr["data"] = $result_arr;

        return $return_arr;
    }

    /**
     * update_purchase_coin_data method is used to execute database queries for purchase_coin Update API.
     * @created  | 29.01.2016
     * @modified ---
     * @param array $params_arr params_arr array to process query block.
     * @param array $where_arr where_arr are used to process where condition(s).
     * @return array $return_arr returns response of query block.
     */
    public function update_purchase_coin_data($params_arr = array(), $where_arr = array())
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
            if (isset($params_arr["round_id"])) {
                $this->db->set("iRoundId", $params_arr["round_id"]);
            }
            if (isset($params_arr["credit_coin"])) {
                $this->db->set("iCreditCoin", $params_arr["credit_coin"]);
            }
            if (isset($params_arr["debit_coin"])) {
                $this->db->set("iDebitCoin", $params_arr["debit_coin"]);
            }
            if (isset($params_arr["unlock_status"])) {
                $this->db->set("iUnlockStatus", $params_arr["unlock_status"]);
            }
            
            $res = $this->db->update("purchase_coin");

            $affected_rows = $this->db->affected_rows();
            if (!$res || $affected_rows == -1)
            {
                throw new Exception("Failure in updation.");
            }
            $result_param = "affected_rows";
            $result_arr[0][$result_param] = $affected_rows;
            $success = 1;
        } catch(Exception $e) {
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

    public function delete_purchase_coin($params_arr = array())
    {
        try {
            $result_arr = array();
            $this->db->start_cache();
            if (isset($params_arr["activity_id"])) {
                $this->db->where("iActivityId =", $params_arr["activity_id"]);
            }
            $this->db->stop_cache();

            $res = $this->db->delete("purchase_coin");

            $affected_rows = $this->db->affected_rows();
            if (!$res || $affected_rows == -1) {
                throw new Exception("Failure in deletion.");
            }
            $result_param = "affected_rows";
            $result_arr[0][$result_param] = $affected_rows;
            $success = 1;
        } catch(Exception $e) {
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
}
