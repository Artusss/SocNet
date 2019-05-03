<?php
class Form{
	private $action;
	private $method;
	private $includes;

	function __construct($action, $method='POST'){
		$this->action = $action;
		$this->method = $method;
		$this->includes = [];
	}

	public function input($name, $type='text', $properties=[]){
		$input_str = "<input type=\"{$type}\" name=\"{$name}\"";
		foreach ($properties as $k => $v) {
			$input_str .= " $k=\"{$v}\"";
		}
		$input_str .= ">";
		$this->includes[] = array('component' => $input_str, 'type' => $type);
	}

	public function textarea($name, $value, $assoc_properties=[], $alone_properties=[]){
		$textarea_str = "<textarea name=\"{$name}\"";
		foreach ($assoc_properties as $k => $v) {
			$textarea_str .= " $k=\"{$v}\"";
		}
		foreach ($alone_properties as $v) {
			$textarea_str .= " {$v}";
		}
		$textarea_str .= ">{$value}</textarea>";
		$this->includes[] = array('component' => $textarea_str, 'type' => 'textarea');
	}

	public function button($name, $value, $type='submit', $assoc_properties=[], $alone_properties=[]){
		$button_str = "<button name=\"{$name}\" type=\"{$type}\"";
		foreach ($assoc_properties as $k => $v) {
			$button_str .= " $k=\"{$v}\"";
		}
		foreach ($alone_properties as $v) {
			$button_str .= " {$v}";
		}
		$button_str .= ">{$value}</button>";
		$this->includes[] = array('component' => $button_str, 'type' => $type);
	}

	public function getData(){
		if($this->method === 'POST'){
			$data = $_POST;
		}else if($this->method === 'GET'){
			$data = $_GET;
		}else if($this->method === 'REQUEST'){
			$data = $_REQUEST;
		}
		return $data;
	}

	private function joinIncludes(){
		foreach ($this->includes as $v) {
			echo "<p>".$v['component']."</p>";
		}
	}

	public function run(){
		echo "<form action=".$this->action." method=".$this->method.">";
		$this->joinIncludes();
		echo "</form>";
	}
}
