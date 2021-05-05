<?php

class MainModal extends StatusCodes{
	protected $conn;
	public $user;
	public $set_db;
	public $sql_date_format;
	function __construct(){
		parent::__construct();
		$this->set_db = '';
		$this->new_conn();
		if(isset($_SESSION['UserID'])){
			$this->user = $_SESSION['UserID'];
		}
		$this->sql_date_format = date("Y-m-d");
		date_default_timezone_set('Asia/Qatar');
	}

	public function replace_empty_value($value){
		if(strlen(trim($value)) == 0){
			return '';
		}
		else{
			return $value;
		}
	}

	public function new_conn($db_main_set = ''){
		include('dbconfig.php');
		if($db_main_set != ''){
			$db_main = $db_main_set;
		}
		else{
			$db_main = $db_main;
		}
		$this->servername = $db_array[$db_main]['servername'];
		$this->user_name = $db_array[$db_main]['user_name'];
		$this->password = $db_array[$db_main]['password'];
		if($this->set_db == ''){
			$this->database = $db_array[$db_main]['database'];
		}
		else{
			$this->database = $this->set_db;
		}
		
		$this->conn = new mysqli($this->servername,$this->user_name,$this->password,$this->database);
		$this->conn->set_charset("utf8");
	}

	public function fetch_one($sql,$db=''){
		if($db != ''){
			$this->new_conn($db);
		}
		else{
			$this->new_conn('');
		}
		$return_array = array();
		$result = $this->conn->query($sql);
		if($result == TRUE){
			if($result->num_rows > 0){
				$row = $result->fetch_assoc();
				return $row;
			}
			return $return_array;
		}
		else{
			return $this->query_error($sql);
		}
		
	}

	public function fetch_all($sql,$db=''){
		if($db != ''){
			$this->new_conn($db);
		}
		else{
			$this->new_conn('');
		}
		$return_array = array();
		$result = $this->conn->query($sql);
		if($result == TRUE){
			if($result->num_rows > 0){
				while($row = $result->fetch_assoc()) {
					$return_array[] = $row;
				}
			}
			return $return_array;
		}
		else{
			return $this->query_error($sql);
		}
		
	}

	public function fetch_all_by_key($sql,$col,$db=''){
		if($db != ''){
			$this->new_conn($db);
		}
		else{
			$this->new_conn('');
		}
		$return_array = array();
		$result = $this->conn->query($sql);
		if($result == TRUE){
			if($result->num_rows > 0){
				$i = 0;
				while($row = $result->fetch_assoc()) {
					$explode = explode(",", $col);
					$explode_0 = explode("__", $explode[0]);
					if(count($explode_0) > 1){
						if($explode_0[1]  == 'month'){
							$row0 = date("Y-m",strtotime($row[$explode_0[0]]));
						}
						else if($explode_0[1]  == 'year'){
							$row0 = date("Y",strtotime($row[$explode_0[0]]));
						}
						else if($explode_0[1]  == 'day'){
							$row0 = date("d",strtotime($row[$explode_0[0]]));
						}
						
					}
					else{
						if($explode[0] == 'null'){
							$row0 = $i;
						}
						else{
							$row0 = $row[$explode[0]];
						}
						
					}
					if(count($explode) > 1 && (count($explode) > 2 || count($explode) == 2)){
						$explode_1 = explode("__", $explode[1]);
						if(count($explode_1) > 1){
							if($explode_1[1]  == 'month'){

								$row1 = date("Y-m",strtotime($row[$explode_1[0]]));
							}
							else if($explode_1[1]  == 'year'){
								$row1 = date("Y",strtotime($row[$explode_1[0]]));
							}
							else if($explode_1[1]  == 'day'){
								$row1 = date("d",strtotime($row[$explode_1[0]]));
							}
						}
						else{
							if($explode[1] == 'null'){
								$row1 = $i;
							}
							else{
								$row1 = $row[$explode[1]];
							}
						}
					}
					if(count($explode) > 2 && (count($explode) == 3 || count($explode) > 2)){
						$explode_2 = explode("__", $explode[2]);
						if(count($explode_2) > 1){
							if($explode_2[1]  == 'month'){
								$row2 = date("Y-m",strtotime($row[$explode_2[0]]));
							}
							else if($explode_2[1]  == 'year'){
								$row2 = date("Y",strtotime($row[$explode_2[0]]));
							}
							else if($explode_2[1]  == 'day'){
								$row2 = date("d",strtotime($row[$explode_2[0]]));
							}
						}
						else{
							if($explode[2] == 'null'){
								$row2 = $i;
							}
							else{
								$row2 = $row[$explode[2]];
							}
						}
					}

					if(count($explode) > 3 && (count($explode) == 4 || count($explode) > 3)){
						$explode_3 = explode("__", $explode[3]);
						if(count($explode_3) > 1){
							if($explode_3[1]  == 'month'){
								$row3 = date("Y-m",strtotime($row[$explode_3[0]]));
							}
							else if($explode_3[1]  == 'year'){
								$row3 = date("Y",strtotime($row[$explode_3[0]]));
							}
							else if($explode_3[1]  == 'day'){
								$row3 = date("d",strtotime($row[$explode_3[0]]));
							}
						}
						else{
							if($explode[3] == 'null'){
								$row3 = $i;
							}
							else{
								$row3 = $row[$explode[3]];
							}
						}
					}
					//
					if(count($explode) == 2){
						$return_array[$row0][$row1] = $row;
					}
					else if(count($explode) == 3){
							$return_array[$row0][$row1][$row2] = $row;
					}
					else if(count($explode) == 4){
							$return_array[$row0][$row1][$row2][$row3] = $row;
					}
					else{
						$return_array[$row0] = $row;
					}
					$i++;
					
				}
			}
			return $return_array;
		}
		else{
			return $this->query_error($sql);
		}
		
	}

	public function fetch_by_col_one($sql,$link_name,$db=''){
		if($db != ''){
			$this->new_conn($db);
		}
		else{
			$this->new_conn('');
		}
		$return_array = array();
		$result = $this->conn->query($sql);

		if($result == TRUE){
			if($result->num_rows > 0){
				$row = $result->fetch_assoc();
				if($row[$link_name]){
					return $row[$link_name];
				}
				return '';
			}
		}
		else{
			return $this->query_error($sql);
		}
		
	}

	public function fetch_all_by_col($sql,$link_name,$by_col=''){
		$this->new_conn('');
		$return_array = array();
		$result = $this->conn->query($sql);
		if($result == TRUE){
			if($result->num_rows > 0){
				while($row = $result->fetch_assoc()) {
					if($by_col == ''){
						$return_array[] = $row[$link_name];
					}
					else{
						if($link_name == ''){
							$return_array[$row[$by_col]][] = $row;
						}
						else{
							$explode_0 = explode("__", $by_col);

							if(count($explode_0) > 1){
								$row0 = $row[$explode_0[0]];
								if($explode_0[1] == 'single'){
									$return_array[$row0] = $row[$link_name];
								}
								else{
									$return_array[$row0][] = $row[$link_name];
								}
								
							}
							else{
								$row0 = $row[$explode_0[0]];
								$return_array[$row0][] = $row[$link_name];
							}
							
						}
						
					}
					
				}
			}
		}
		else{
			return $this->query_error($sql);
		}
		
		return $return_array;
	}

	public function query_error($sql=''){
		$return_array = array();
		$return_array['status'] = 'warning';
		$return_array['message'] = '<strong> WARNING! </strong>Please contact IT support';
		$return_array['sql'] = $sql;
		$return_array['error_message'] = $this->conn->error;
		return $return_array;
	}

	public function exec($sql){
		$this->new_conn('');
		$return_array = array();
		if ($this->conn->query($sql) == TRUE) {
			$return_array['status'] = 'success';
			if(strpos(strtolower($sql), "insert") !== false && substr(trim(strtolower($sql)), 0, 6) == "insert"){
				$last_insert_id = $this->conn->insert_id;
				$return_array['last_insert_id'] = $last_insert_id;
				$return_array['sql_func'] = 'insert';
				$return_array['message'] = '<strong> SUCCESS! </strong> Succesfully added new data';
			}
			else if(strrpos(strtolower($sql), "delete") !== false && substr(trim(strtolower($sql)), 0, 6) == "delete"){
				$return_array['message'] = '<strong> SUCCESS! </strong> Succesfully deleted data';
				$return_array['sql_func'] = 'delete';

			}
			else{
				$return_array['message'] = '<strong> SUCCESS! </strong> Succesfully updated data';
				$return_array['sql_func'] = 'update';
			}
			$return_array['sql'] = $sql;
			return $return_array;
			
		}
		else{
			return $this->query_error($sql);
		}
		
	}

	public function exec_not_exist($sql,$vars,$where,$table_name){
		$this->new_conn('');
		$sql_exist = "SELECT $vars FROM $table_name WHERE $where";
		$return_array = array();
		$result_exist = $this->conn->query($sql_exist);
		if($result_exist == TRUE){
			if(strpos(strtolower($sql), "insert") !== false && substr(trim(strtolower($sql)), 0, 6) == "insert"){
				if($result_exist->num_rows < 1){
					$return_array = $this->exec($sql);
				}
				else{
					$return_array['status'] = 'info';
					$return_array['exist'] = 'data_exist';
					$return_array['message'] = '<strong>INFO! </strong> Data already exist in the table';
				}
			}
			else if(strrpos(strtolower($sql), "delete") !== false && substr(trim(strtolower($sql)), 0, 6) == "delete"){
				if($result_exist->num_rows > 0){
					$return_array = $this->exec($sql);
				}
				else{
					$return_array['status'] = 'info';
					$return_array['message'] = '<strong>INFO!</strong> Data not exist in the table';
				}
			}
			else{
				if($result_exist->num_rows < 1){
					$return_array = $this->exec($sql);
				}
				else{
					$return_array['status'] = 'info';
					$return_array['message'] = '<strong>INFO!</strong> Data exist in the table';
				}
			}
			return $return_array;
		}
		else{
			return $this->query_error($sql_exist);
		}
		
		return $return_array;
	}

	public function get_count($sql){
		$this->new_conn('');
		$result = $this->conn->query($sql);
		if($result != TRUE){
			return $this->query_error($sql);
		}
		
		return $result->num_rows;
	}

	public function is_conn(){
		if($this->conn->connect_error){
			return $conn->connect_error . '<br>';
		}
		else{
			return 'success';
		}
	}

	public function test_modal(){

	}
	public function exec_message($sql,$message){
		$return_array = array();
		if ($this->conn->query($sql) === TRUE) {
		    $return_array['message'] = 'SUCCESS! ' . $message;
			$return_array['status'] = 'success';
			return $return_array;
		}
		else{
			$return_array['message'] = $this->status_message_internal_error;
			$return_array['status_code'] = $this->status_insert_fails;
			$return_array['status'] = 'warning';
			return $return_array;
		}
	}

	public function crypted_pass($password){
		if($this->encyType == 'sha1'){
			$password = sha1($password);
		}
		return $password;
	}
	
	public function create_middlewaretoken($bytes = 32){
		$token = bin2hex(openssl_random_pseudo_bytes($bytes));
		return $token;
	}
}