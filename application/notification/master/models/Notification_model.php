<?php
defined('BASEPATH') || exit('No direct script access allowed');

/**
 * Description of NS Engine Values Model
 *
 * @category notification
 * 
 * @package nsengine
 *  
 * @subpackage models
 * 
 * @module NS Engine
 * 
 * @class Notification_model.php
 * 
 * @path application\front\nsengine\models\Notification_model.php
 *
 * @version 4.0
 * 
 * @author CIT Dev Team
 *
 * @since 01.08.2016
 */
class Notification_model extends CI_Model
{
    public $main_table;
    public $table_alias;

    /**
     * __construct method is used to set model preferences while model object initialization.
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * post_notification method is used to execute database queries for Send Message API.
     * @created CIT Dev Team
     * @modified ---
     * @param array $params_arr params_arr array to process query block.
     * @return array $return_arr returns response of query block.
     */
    public function post_notification($params_arr = array())
    {
        try {
            $result_arr = array();
            if (!is_array($params_arr) || count($params_arr) == 0) {
                throw new Exception("Insert data not found.");
            }
            if (isset($params_arr["user_id"])) {
                $this->db->set("iUserId", $params_arr["user_id"]);
            }
            if (isset($params_arr["event_id"])) {
                $this->db->set("iEventId", $params_arr["event_id"]);
            }
            if (isset($params_arr["contact_id"])) {
                $this->db->set("iContactId", $params_arr["contact_id"]);
            }
            if (isset($params_arr["notification_message"])) {
                $this->db->set("vNotificationMessage", $params_arr["notification_message"]);
            }
            $this->db->set("vNotificationType", $params_arr["_enotificationtype"]);
            $this->db->set($this->db->protect("dtAddedAt"), $params_arr["_dtaddedat"], FALSE);
            $this->db->set($this->db->protect("dtUpdatedAt"), $params_arr["_dtupdatedat"], FALSE);
            $this->db->insert("notifications");
            $insert_id = $this->db->insert_id();
            if (!$insert_id) {
                throw new Exception("Failure in insertion.");
            }
            $result_param = "insert_id";
            $result_arr[0][$result_param] = $insert_id;
            $success = 1;
        } catch(Exception $e) {
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
     * getDailyEvent method is used to execute database queries for States List API.
     * @created priyanka chillakuru | 18.09.2019
     * @modified priyanka chillakuru | 18.09.2019
     * @param string $STATES_LIST_COUNTRY_ID STATES_LIST_COUNTRY_ID is used to process query block.
     * @param string $STATES_LIST_COUNTRY_CODE STATES_LIST_COUNTRY_CODE is used to process query block.
     * @return array $return_arr returns response of query block.
     */
    public function getDailyEvent($params_arr = array())
    {
        try {
            $date = new DateTime("now");
            $curr_date = $date->format('Y-m-d ');
            $result_arr = array();
                                
            $this->db->from("events AS e");
            $this->db->join("contacts AS c", "c.iContactId = e.iContactId", "inner");
            $this->db->join('users as u', ' e.iUserId = u.iUserId');
            $this->db->select("u.iUserId,u.vEmail AS email,u.vFirstName AS first_name,u.vLastName AS last_name,u.vDeviceToken AS device_token,e.iEventId, e.iUserId, e.iContactId, e.vEventTitle, e.vEventNote,
            e.vEventType, e.dtEventDate, e.eRepetition, e.dtAddedAt,
            c.iContactRefId, c.vFirstName, c.vLastName,
            c.eContactType, c.vContactImage, c.vContactNumber_1, c.vContactNumber_2,
            c.vContactNumber_3, c.vContactNumber_4, c.vContactNumber_5");
            //$this->db->where("e.iUserId=", $params_arr["user_id"]);
            $this->db->where("e.eRepetition =", "daily");
            $result_obj = $this->db->get();
            $result_arr = is_object($result_obj) ? $result_obj->result_array() : array();
            
            if(!is_array($result_arr) || count($result_arr) == 0){
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
     * getWeeklyEvent method is used to execute database queries for States List API.
     * @created priyanka chillakuru | 18.09.2019
     * @modified priyanka chillakuru | 18.09.2019
     * @param string $STATES_LIST_COUNTRY_ID STATES_LIST_COUNTRY_ID is used to process query block.
     * @param string $STATES_LIST_COUNTRY_CODE STATES_LIST_COUNTRY_CODE is used to process query block.
     * @return array $return_arr returns response of query block.
     */
    public function getWeeklyEvent($params_arr = array())
    {
        try {
            $date = new DateTime("now");
            $curr_date = $date->format('Y-m-d ');
            $result_arr = array();
                                
            $this->db->from("events AS e");
            $this->db->join("contacts AS c", "c.iContactId = e.iContactId", "inner");
            $this->db->join('users as u', ' e.iUserId = u.iUserId');
            $this->db->select("u.iUserId,u.vEmail AS email,u.vFirstName AS first_name,u.vLastName AS last_name,u.vDeviceToken AS device_token,e.iEventId, e.iUserId, e.iContactId, e.vEventTitle, e.vEventNote,
            e.vEventType, e.dtEventDate, e.eRepetition, e.dtAddedAt,
            c.iContactRefId, c.vFirstName, c.vLastName,
            c.eContactType, c.vContactImage, c.vContactNumber_1, c.vContactNumber_2,
            c.vContactNumber_3, c.vContactNumber_4, c.vContactNumber_5");
            //$this->db->where("e.iUserId=", $params_arr["user_id"]);
            $this->db->where("e.eRepetition =", "weekly");
            $result_obj = $this->db->get();
            $result_arr = is_object($result_obj) ? $result_obj->result_array() : array();
            
            if(!is_array($result_arr) || count($result_arr) == 0){
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
     * getYearlyEvent method is used to execute database queries for States List API.
     * @created priyanka chillakuru | 18.09.2019
     * @modified priyanka chillakuru | 18.09.2019
     * @param string $STATES_LIST_COUNTRY_ID STATES_LIST_COUNTRY_ID is used to process query block.
     * @param string $STATES_LIST_COUNTRY_CODE STATES_LIST_COUNTRY_CODE is used to process query block.
     * @return array $return_arr returns response of query block.
     */
    public function getYearlyEvent($params_arr = array())
    {
        try {
            $date = new DateTime("now");
            $curr_date = $date->format('Y-m-d ');
            $result_arr = array();
                                
            $this->db->from("events AS e");
            $this->db->join("contacts AS c", "c.iContactId = e.iContactId", "inner");
            $this->db->join('users as u', ' e.iUserId = u.iUserId');
            $this->db->select("u.iUserId,u.vEmail AS email,u.vFirstName AS first_name,u.vLastName AS last_name,u.vDeviceToken AS device_token,e.iEventId, e.iUserId, e.iContactId, e.vEventTitle, e.vEventNote,
            e.vEventType, e.dtEventDate, e.eRepetition, e.dtAddedAt,
            c.iContactRefId, c.vFirstName, c.vLastName,
            c.eContactType, c.vContactImage, c.vContactNumber_1, c.vContactNumber_2,
            c.vContactNumber_3, c.vContactNumber_4, c.vContactNumber_5");
            //$this->db->where("e.iUserId=", $params_arr["user_id"]);
            $this->db->where("e.vEventType =", "event");
            $this->db->where("e.dtEventDate =", $curr_date);
            $result_obj = $this->db->get();
            $result_arr = is_object($result_obj) ? $result_obj->result_array() : array();
            
            if(!is_array($result_arr) || count($result_arr) == 0){
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
}
