<?php

include_once(__DIR__ . '/class.YapoAction.php');

class YapoSave extends YapoAction {
	var $Where;
	var $Mode;

	function __construct(& $Core, & $Where) {
		parent::__construct($Core);
		$this->Where = & $Where;
	}
	
	function GenerateSql($params) {
		parent::GenerateSql($params);
		if (is_array($params))
			extract($params);
		
		if (!$this->Core->HasActiveRecord() && !$all && !$this->Core->__mismatched_set_equals) {
			// insert new record
			$this->Mode = "insert";
			return $this->insert();
		} else if (!$this->Core->HasActiveRecord() || $all) {
			// update set
			$this->Mode = "update_set";
			return $this->update_set();
		} else if ($this->Core->HasActiveRecord() || $this->Core->PrimaryKeyIsSet()) {
			// update current record
			$this->Mode = "update";
			return $this->update();
		} else {
			// not sure how we got here ...
		}
	}
	
	private function insert() {
		$sql = "insert into {$this->Core->__table} ";
		$insert_fields = array();
		foreach ($this->Core->__definition as $field => $definition) {
			if (strtoupper($definition['Null']) == 'NO') {
				$insert_fields[$this->Core->GetQualifiedName($field)] = "";
			}
		}
		foreach ($this->Core->__field_actions as $field => $comparator) {
			if (isset($comparator[Yapo::SET])) {
				$insert_fields[$this->Core->GetQualifiedName($field)] = $comparator[Yapo::SET];
			}
		}
		$sql .= "(" . implode(", ", array_keys($insert_fields)) . ")";
		$fields = array();
		foreach ($insert_fields as $field => $value)
			$fields["insert_" . str_replace('.','_',$field)] = $value;
		$sql .= " values (" . implode(", ", array_map(function($n) { return ":$n"; }, array_keys($fields))) . ")";
		return array($sql, $fields);
	}
	
	private function update_set() {
		list($sql, $update_fields) = $this->update_base();
		list($wsql, $where_fields) = $this->Where->GenerateSql(array());
		$sql .= $wsql;
		$update_fields = array_merge($update_fields, $where_fields);
		
		return array($sql, $update_fields);
	}
	
	private function update() {
		list($sql, $update_fields) = $this->update_base();
		$primary_key = $this->Core->GetPrimaryKeyField();
		$this->Core->Comparator($primary_key, Yapo::EQUALS, $this->Core->$primary_key);
		list($wsql, $where_fields) = $this->Where->GenerateSql(array('primary' => true));
		$sql .= $wsql;
		$update_fields = array_merge($update_fields, $where_fields);
		
		return array($sql, $update_fields);
	}
	
	private function update_base() {
		$sql = "update {$this->Core->__table} set ";
		$fields = array();
		$update_fields = array();
		foreach ($this->Core->__field_actions as $field => $comparator) {
			if (isset($comparator[Yapo::SET])) {
				$fields[] = $this->Core->GetQualifiedName($field) . " = :update_" . $this->Core->GetQualifiedName($field, '_'); 
				$update_fields["update_" . $this->Core->GetQualifiedName($field, '_')] = $comparator[Yapo::SET];
			}
		}
		$sql .= implode(', ', $fields) . ' ';
		return array($sql, $update_fields);
	}

}

?>