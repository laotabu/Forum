<?php 
header("content-type:text/html;charset=utf-8"); 
	class Mysql{
		private $_dsn="localhost"; 
		private $_name="root";  
		private $_password="123";
		private $_database="forum";
		private $_serverLink;
		// 构造函数
		 function __construct() {
       		$this->_serverLink = mysql_connect($this->_dsn,$this->_name,$this->_password);
       		mysql_select_db($this->_database);
  		 }
  		 // 析构函数
 		 function __destruct() {
      		mysql_close($this->_serverLink);
   		}
		public function setDsn($dsn){
			$this->_dsn=$dsn;
		}
		public function setName($name){
			$this->_name=$name;
		}
		public function setPassword($password){
			$this->_password=$password;
		}
		public function setDatabase($database){
			$this->_database=$database;
		}

		/*
			@return :
				(1) when the sql is selecting the data, return the resultSet or false;
					if the data were found, return resultSet;
					if not found, return false;
				(2) when the sql is updating or deleting or inserting, return the affected rows or zero
					if the table of the database is changed, return rows;
					if not changed, return zero; 
		*/
		public function exec($sql){
		    $resultSet = mysql_query($sql);
			if(is_bool($resultSet)){
				// update, insert, delete
				$res = mysql_affected_rows();	
				return $res>0?$res:0;
			}else {
				// select
				$res = mysql_fetch_array($resultSet);
				mysql_free_result($resultSet);
				return $res>0?$res:false;
			}
		}
		
		public function queryAll($sql){//返回全部结果集
			$resultSet = mysql_query($sql);
			$res = array();
			while ($temp = mysql_fetch_array($resultSet)) {
				array_push($res, $temp);
			}
			mysql_free_result($resultSet);
			//print_r($res);
			return json_encode($res);	
		}


		/*public function queryOne($sql){//返回一条结果集
			$pdo=new PDO($this->_dsn,$this->_name,$this->_password);
			$stmt=$pdo->query($sql);
			return $stmt->fetch(PDO::FETCH_ASSOC);
		}
		public function queryAll($sql){//返回全部结果集
			$pdo=new PDO($this->_dsn,$this->_name,$this->_password);
			$stmt=$pdo->query($sql);
			return $stmt->fetchAll(PDO::FETCH_ASSOC);
		}
		public function exec($sql){//exec 返回受影响的行数
			$pdo=new PDO($this->_dsn,$this->_name,$this->_password);
			$stmt=$pdo->exec($sql);
			return $stmt;
		}*/
	}
	
?>