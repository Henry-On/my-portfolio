<?php 

class form_validation {

  public $form_errors = array();

	private function sanitize_field($field) {
		$field = trim($field);
		return $field;
	}

  private function set_error_key(string $default, array $params_array = []) {
    $key = isset($params_array) && array_key_exists("error", $params_array) ? $params_array['error'] : $default;
    return $key;  
  }

  public function set_error($key, $message) {
    $this->form_errors[$key] = $message;
  }

	public function validate_number($num, $params = []) {

    if(count($params) < 1 || !isset($params['required']) || ($params['required'] !== true)) return $num;
    
    $key = $this->set_error_key( "number", $params );

    if (empty($num)) {
      $this->form_errors[$key] = "This field is required";
    }
    else if (preg_match('/[^0-9]/', $num)) {
      $this->form_errors[$key] = "Invalid character set";
    }
    else if (array_key_exists('max_length', $params) && $params['max_length'] < strlen($num)) {
      $this->form_errors[$key] = "Maximum required length is {$params['max_length']} digits";
    }
    else if (array_key_exists('min_length', $params) && $params['min_length'] > strlen($num)) {
      $this->form_errors[$key] = "Minimum required length is {$params['min_length']} digits";
    }
    else if (array_key_exists('min_value', $params) && $params['min_value'] > $num) {
      $this->form_errors[$key] = "Minimum value is {$params['min_value']}";
    }
    else if (array_key_exists('max_value', $params) && $params['max_value'] < $num) {
      $this->form_errors[$key] = "Maximum value is {$params['min_value']}";
    } else {
      return $num;
    }
    
  }

  public function validate_email($email, $params=[]) {

    $email = $this->sanitize_field($email);

    if(!isset($params['required']) || $params['required'] !== true) return $email;

    $key = $this->set_error_key( "email", $params );

    if (empty($email)) {
        $this->form_errors[$key] = "This field is required";
    }
    else if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $this->form_errors[$key] = "Invalid email format"; 
    } else {
        return $email;
    }

      
  }

  public function validate_name($name, $params=[]) {

    $name = $this->sanitize_field($name);

    if(!isset($params['required']) || $params['required'] !== true) return $name;

    $key = $this->set_error_key( "name", $params );

    if (empty($name)) {
        $this->form_errors[$key] = "This field is required";
    }
    else if(strlen($name) < 1) {
        $this->form_errors[$key] = "Must be at least two characters";
    }
    else if (!preg_match('/[a-zA-Z]+/',$name)) {
        $this->form_errors[$key] = "Name cannot contain special characters"; 
    } else {
        return $name;
    }
  }

  public function validate_string($string, array $params = []) {

    $string = $this->sanitize_field($string);
    $key = isset($params['error']) ? $params['error'] : "string";

    if(!array_key_exists('required', $params) OR $params['required'] !== true) return;

    if (empty($string)) {
      $this->form_errors[$key] = "This field is required";
    }
    else if(isset($params['min_length']) && str_word_count($string) < $params['min_length']) {
      $this->form_errors[$key] = "Too short, must be at least {$params['min_length']} words";
    }
    else if(isset($params['max_length']) && str_word_count($string) > $params['max_length']) {
      $this->form_errors[$key] = "Too long, maximum words count allowed is {$params['max_length']}";
    }
    return $string;
  }
    
  public function validate_password($password, $params = []) {

    if(!array_key_exists('space', $params)) $params['space'] = true;
    if(!array_key_exists('minLength', $params)) $params['minLength'] = 1;
    if(!array_key_exists('mixedChars', $params)) $params['mixedChars'] = false;

    if($params['space'] !== true) $password = $this->sanitize_field($password);

    $key = isset($params['error']) ? $params['error'] : "password";

    if (empty($password) || $password == "") {

        $this->form_errors[$key] = "This field is required";

    } else if(strlen($password) < $params['minLength']) {

        $this->form_errors[$key] = "Must be at least ".$params['minLength'];

    }
    // else if ($params['mixedChars'] == true) {

    //   if(!preg_match('/[\w]+[\d]+/',$password)) {
    //     $this->form_errors[$key] = "Must contain mix of a number, Uppercase and Lowercase letters"; 
    //   }

    //     $this->form_errors[$key] = "Must contain mix of a number, Uppercase and Lowercase letters and a special character(s) e.g !@#$%)("; 
    // }
    else {
        return $password;
    }
  }

  function resetInputFields(array $array_set) {
  
    if(!is_array($array_set)) return false;

    foreach ($array_set as $key => $value) { 
        $array_set[$key] = "";
    }

    return true;

  }

  public function validate_date($date, $params = []) {

    if(!isset($params['required']) || $params['required'] !== true) return $date;

    $key = $this->set_error_key( "date", $params );

    if(empty($date)) {
      $this->form_errors[$key] = "This field is required";
    }
    else {
      return $date;
    }
  }
}

?>