<?php
class FieldValidator{
    
    static function requiredValidator($data,$field=null){
		return !empty($data) ? true : false;
    }
    
    static function alphanumericValidator($data,$field=null){
        $pattern = '/[^a-zA-Z0-9\s]/u';
        return $data == preg_replace($pattern, '', (string) $data);
    }
    
    static function numericValidator($data,$field=null){
        return is_numeric($data) ? true : false;
    }
    
    static function rangeValidator($data,$field=null){
        $values = array();
        if($field instanceof FormFields){
            foreach($field->fieldsValues as $val){
                $values[] = $val->form_field_value;
            }
        }else{
            $values = $field;
        }
        if(is_array($data)){
            return count($data) == count(array_intersect($data,$values));   
        }else{
            return in_array($data,$values)? true: false;
        }
    }
  
}