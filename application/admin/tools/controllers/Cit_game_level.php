<?php


/**
 * Description of Game_level Management Extended Controller
 *
 * @module Extended Game_level Management
 *
 * @class Cit_Game_level.php
 *
 * @path application\admin\basic_appineers_master\controllers\Cit_Game_level.php
 *
 * @author CIT Dev Team
 *
 * @date 01.10.2019
 */

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}
 
class Cit_Game_level extends Game_level
{
    public function __construct()
    {
        parent::__construct();
    }
    public function checkUniqueGameLevel($value = '')
    {
        $return_arr='1';
        if (false == empty($value)) {
            $this->db->select('iGameLevelId');
            $this->db->from('game_level_master');
            $this->db->where_in('iGameLevelId', $value);
            $arrGameLevelData=$this->db->get()->result_array();
            if (true == empty($arrGameLevelData)) {
                $return_arr = "0";
                return  $return_arr;
            }
        }
        return  $return_arr;
    }
    public function showStatusButton($id='', $arr=array())
    {
        $url = $this->general->getAdminEncodeURL('tools/game_level/add').'|mode|'.$this->general->getAdminEncodeURL('Update').'|id|'.$this->general->getAdminEncodeURL($arr);
        return '<button type="button" data-id='.$arr.' class="btn btn-success operBut" data-url='.$url.' >Edit</button>';
    }
}
