<?php

namespace App\Libraries;

/**
 * Custom Table Filter
 * Generate DB Query
 *
 * @author     Arpan Tanna <arpan.ec24@gmail.com>
 *
 */
class TFC {

	private $tableName = '';
	private $arrfields = array();
	private $numericfields = array();
	private $datefields = array();
	private $isJson = 0;
	private $jsonparams = array();
	private $sType = '';

	function __construct(){}

	public function init($table, $arrfields, $numericfields, $datefields)
	{
		$this->tableName = $table;
		$this->arrfields = $arrfields;
		$this->numericfields = $numericfields;
		$this->datefields = $datefields;
	}

	public function custom($stype)
    {
        $this->sType = $stype;
    }

	public function jsonparamsInit($isJson, $jsonparams)
	{
		$this->isJson = $isJson;
		$this->jsonparams = $jsonparams;
	}

	/*
	 * Generate WHERE conditions
	 *
	 */
	public function init_where($filters=array(), $where='')
	{
		$arr_operator = array('&&' => 'AND', '||' => 'OR');
		foreach($filters as $ffield=>$val)
		{
			if (substr($ffield, 0, 4) === 'srf-' && $val !== '')
			{
				$field_key = substr($ffield, 4);
				if(array_key_exists($field_key, $this->arrfields))
				{
				    if($this->isJson == 1 && array_key_exists($field_key, $this->jsonparams)) {
						$pfield = $this->jsonparams[$field_key][1];
						$cfield = $this->jsonparams[$field_key][0];
						$field = 'SUBSTRING_INDEX(SUBSTRING_INDEX(substring('.$pfield.', LOCATE(\'"'.$cfield.'":"\', '.$pfield.'), LENGTH('.$pfield.')),\'"'.$cfield.'":"\',-1),\'"\',1)';
					}
					/*elseif ($this->sType === 'booking' && $field_key === 'responses.confirmed') {

                    }*/
					else {
						$field = $this->arrfields[$field_key];
					}
					$conditions = preg_split('/(&&|\|\|)/', $val);
					preg_match_all('/(&&|\|\|)/', $val, $cond_operators_list);
					$cond_operators = $cond_operators_list[0];

					if(is_array($conditions) && count($conditions) > 0)
					{
						$temp_where = '';
						foreach($conditions as $k=>$condition)
						{
                            // Any customization add there

							if($k > 0) {
								$operator_name = $arr_operator[$cond_operators[$k-1]];
								$temp_where .= ' '.$operator_name.' ';
							}

							$get_where = $this->get_where($field, $condition, $field_key);
							// Add in where
							$temp_where .= $get_where;
						}

						if($temp_where !== '') {
							$where .= ($where !== '') ? ' AND ' : '';
							$where .= '('.$temp_where.')';
						}
					}
				}
			}
		}

		return $where;
	}

    /*
     * Generate ORDER BY
     *
     */
	public function init_order($orderby, $orderArr)
	{
	    foreach($orderArr as $orderSingle)
	    {
            $csortColumn = $orderSingle['column'];
            $csortType = $orderSingle['type'];
            if ($orderSingle['chk'] == 0 OR array_key_exists($csortColumn, $this->arrfields))
            {
                if($orderSingle['chk'] == 0) {
                    $fname = $orderSingle['field'];
                }
                elseif ($this->isJson == 1 && array_key_exists($csortColumn, $this->jsonparams)) {
                    $pfield = $this->jsonparams[$csortColumn][1];
                    $cfield = $this->jsonparams[$csortColumn][0];
                    $fname = 'SUBSTRING_INDEX(SUBSTRING_INDEX(substring(' . $pfield . ', LOCATE(\'"' . $cfield . '":"\', ' . $pfield . '), LENGTH(' . $pfield . ')),\'"' . $cfield . '":"\',-1),\'"\',1)';
                }
                else {
                    $fname = $this->arrfields[$csortColumn];
                }
                //$fname = $this->arrfields[$csortColumn];
                $csortType = ($csortType === 'desc') ? 'desc' : 'asc';

                $orderby .= ($orderby !== '') ? ", " : "";
                $orderby .= $this->getFCasting($fname, $csortColumn) . " {$csortType}";
            }
        }

		return $orderby;
	}

	protected function get_where($field, $val, $field_key)
	{
		$where = '';
		$val = trim($val);
		$val_pure = preg_replace_callback("~([a-z]+)\(\)~",
			function ($m){
				 return trim($m[1]);
		}, $val);

		$chkop2 = substr($val, 0, 2);
		$chkop1 = substr($val, 0, 1);
		$where .= $this->getFCasting($field, $field_key);

		if($chkop2 === ">=") {
			$valpure = $this->valpure(substr($val, 2), $field, $field_key);
			$wcond = $this->wcond($field, '>=', $valpure);
			$where .= $wcond;
		}
		elseif($chkop2 === "<=") {
			$valpure = $this->valpure(substr($val, 2), $field, $field_key);
			$wcond = $this->wcond($field, '<=', $valpure);
			$where .= $wcond;
		}
		elseif($chkop1 === ">") {
			$valpure = $this->valpure(substr($val, 1), $field, $field_key);
			$wcond = $this->wcond($field, '>', $valpure);
			$where .= $wcond;
		}
		elseif($chkop1 === "<") {
			$valpure = $this->valpure(substr($val, 1), $field, $field_key);
			$wcond = $this->wcond($field, '<', $valpure);
			$where .= $wcond;
		}
		elseif($chkop1 === "=") {
			$valpure = $this->valpure(substr($val, 1), $field, $field_key);
			$wcond = $this->wcond($field, '=', $valpure);
			$where .= $wcond;
		}
		elseif($chkop1 === "{") {
			$valpure = $this->valpure(substr($val, 1), $field, $field_key);
			$wcond = " LIKE '{$valpure}%'";
			$where .= $wcond;
		}
		elseif($chkop1 === "}") {
			$valpure = $this->valpure(substr($val, 1), $field, $field_key);
			$wcond = " LIKE '%{$valpure}'";
			$where .= $wcond;
		}
		elseif($chkop1 === "*") {
			$valpure = $this->valpure(substr($val, 1), $field, $field_key);
			$wcond = " LIKE '%{$valpure}%'";
			$where .= $wcond;
		}
		elseif($chkop1 === "!") {
			$valpure = $this->valpure(substr($val, 1), $field, $field_key);
			$wcond = " NOT LIKE '%{$valpure}%'";
			$where .= $wcond;
		}
		elseif($val === "[empty]") {
			$wcond = " = ''";
			$where .= $wcond;
		}
		elseif($val === "[nonempty]") {
			$wcond = " != ''";
			$where .= $wcond;
		}
		elseif(substr($val, 0, 4) === 'rgx:') {
			$valpure = $this->valpure(substr($val, 4), $field, $field_key);
			$wcond = " REGEXP '{$valpure}'";
			$where .= $wcond;
		}
		else {
			$valpure = $this->valpure($val, $field);
			$wcond = " LIKE '%{$valpure}%'";
			$where .= $wcond;
		}

		return $where;
	}

	protected function valpure($val, $field='', $field_key='', $type='')
	{
		if(in_array($type, array('date'))) {
			$val = date('Y-m-d', strtotime($val));
		}
		else if(array_key_exists($field_key, $this->datefields)) {
			$val = date('Y-m-d', strtotime($val));
		}

		return $val;
	}

	protected function wcond($field, $operator, $valpure)
	{
		if(in_array($field, $this->numericfields)) {
			$wcond = " {$operator} {$valpure}";
		}
		else {
			$wcond = " {$operator} '{$valpure}'";
		}

		return $wcond;
	}

	protected function getFCasting($fname, $key)
	{
		if(array_key_exists($key, $this->numericfields)) {
			$return = " CAST({$fname} as DECIMAL(20,2))";
		}
		else if(array_key_exists($key, $this->datefields)) {
			$return = " CAST({$fname} as DATE)";
		}
		else {
			$return = " {$fname}";
		}

		return $return;
	}
}

?>
