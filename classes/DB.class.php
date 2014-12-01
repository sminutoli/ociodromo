<?php
	
	class DB extends PDO {
		
		private static $db;
		
		public static function getDB(){
			if( !self::$db ) self::$db = new DB();
			return self::$db;
		}
		
		private function DB(){
			/*
			if( Main::isLocal() ){
				$db = 'txtpack';
				$user = 'root';
				$pass = '';
				$host = "host=localhost";
			} else {
				
				$db = 'txt_armatubolsa';
				$user = 'sandezmarina';
				$pass = 'marinatxt';
				$host = "host=192.168.0.195";
				
			}
			try {
				parent::__construct("mysql:$host;dbname=$db", $user, $pass);
			} catch ( PDOException $e ){
				echo( $e->getMessage() );
			}
			
			$this->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_WARNING);
			$this->query("SET NAMES UTF8");
			*/
		}
				
		public function saveWord($word){
			$words = $this->getWords();
			if( $this->findWord( $word ) ){
				return false;
			} else {
				file_put_contents( "dummy.db", $word."\r\n", FILE_APPEND);
				return true;
			}
		}
		public function findWord($word){
			$words = $this->getWords();
			$check = false;
			foreach( $words as $w ){
				if( $word == $w["tag"] ){
					$check = true;
					break;
				}
			}
			return $check;
		}
		public function getWords(){
			$result = array();
			
			if( file_exists("dummy.db") ){
			
				$words = file_get_contents( "dummy.db" );
				$words = trim( $words, "\r\n" );
				$exp = explode( "\r\n", $words);
				
				foreach( $exp as $tag ){
					$result[] = array( "tag"=>$tag );
				}
			}
			return $result;
		}
		
	}
	
?>