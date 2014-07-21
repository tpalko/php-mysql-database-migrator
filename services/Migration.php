<?php

class Migration {

	private $dir;
	
	public function __construct($migrationFolder){

		$this->dir = $migrationFolder;		
	}

	public function run(){

		global $connection_string;
		global $last_migration;

		$migrations = array();

		try{
			
			$migrations = MigrationHistory::find('all', array('order' => 'migration_date desc'));

		}catch(Exception $e){
		
			//error_log($e);

			$create_migration_history = "CREATE TABLE `migration_history` (
			  `id` int(11) NOT NULL AUTO_INCREMENT,
			  `migration_timestamp` bigint(20) DEFAULT NULL,
			  `migration_file` varchar(100) DEFAULT NULL,
			  `migration_date` datetime DEFAULT NULL,
			  `datecreated` datetime NOT NULL,
			  PRIMARY KEY (`id`)
			) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=utf8";
	
			GetConnection()->query($create_migration_history);			
		}

		$last_migration_timestamp = strtotime($last_migration);

		//if(count($migrations) > 0){
		//	$last_migration_timestamp = $migrations[0]->migration_timestamp;
		//}

		$migrations_ran = array_map(function($m){ return $m->migration_file; } , $migrations);

		//$last_migration_date = new DateTime("@$last_migration_timestamp"); 

		$migrationFiles = array();	

		if (is_dir($this->dir)) {

		    if ($dh = opendir($this->dir)) {
				
			    while (false !== ($file = readdir($dh))) {

			        if ($file != "." && $file != ".." && filetype($this->dir . "/" . $file) == "file") {
			        	$migrationFiles[] = $file;
			        }
			    }

				sort($migrationFiles);
				closedir($dh);
			}
		}

    	foreach($migrationFiles as $file){

        	$file_parts = split('_', $file);
    		$datestamp = $file_parts[0];
        	$timestamp = strtotime($datestamp);

        	if($timestamp > $last_migration_timestamp && !in_array($file, $migrations_ran)){

        		error_log("executing " . $file . ": " . $timestamp);

        		try{

        			$sqlpipe = "/usr/bin/mysql " . $connection_string . " < \"" . $this->dir . "/" . $file . "\"  2>&1 1> /dev/null";
        			$return = shell_exec($sqlpipe);	

	        		if(!is_null($return)){
	        			throw new Exception("Migration failed: " . $return);
	        		}

	        		$migrationHistory = new MigrationHistory();
	        		$migrationHistory->migration_timestamp = $timestamp;
	        		$migrationHistory->migration_file = $file;
	        		$migrationHistory->migration_date = date("Y-m-d H:i:s", $timestamp);
	        		$migrationHistory->datecreated = GetNewDate();
	        		$migrationHistory->save();

	        		$last_migration_timestamp = $timestamp;

	        	}catch(Exception $e){

	        		error_log($e);
	        		break;
	        	}
        	}	        	
        }
	}
}