<?php


/**
 * Description of Word_coin Management Extended Controller
 *
 * @module Extended Word_coin Management
 *
 * @class Cit_Word_coin.php
 *
 * @path application\admin\basic_appineers_master\controllers\Cit_Word_coin.php
 *
 * @author CIT Dev Team
 *
 * @date 01.10.2019
 */

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}
 
class Cit_Word_coin extends Word_coin
{
    public function __construct()
    {
        parent::__construct();
    }
    public function checkUniqueWordCoin($value = '')
    {
        $return_arr='1';
        if (false == empty($value)) {
            $this->db->select('iWordCoinId');
            $this->db->from('word_coin_master');
            $this->db->where_in('iWordCoinId', $value);
            $arrWordCoinData=$this->db->get()->result_array();
            if (true == empty($arrWordCoinData)) {
                $return_arr = "0";
                return  $return_arr;
            }
        }
        return  $return_arr;
    }
    public function showStatusButton($id='', $arr=array())
    {
        $url = $this->general->getAdminEncodeURL('tools/word_coin/add').'|mode|'.$this->general->getAdminEncodeURL('Update').'|id|'.$this->general->getAdminEncodeURL($arr);
        return '<button type="button" data-id='.$arr.' class="btn btn-success operBut" data-url='.$url.' >Edit</button>';
    }
}
