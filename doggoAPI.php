<?php
date_default_timezone_set("America/New_York");
include 'database.php';

    $mthd = $_SERVER['REQUEST_METHOD'];
     
    if (strcmp($mthd, 'POST') !== 0){
        /**********************************************************************
         * this is a get
         *    - check to make sure it is okay for GET
         */
        $sArgs = filter_input_array(INPUT_GET);
    } else {
        /**********************************************************************
         * this is a post
         */
        $sArgs = filter_input_array(INPUT_POST);
    }


try {
	if (!isset($sArgs['action'])) {
		throw new Exception('Invalid api call - no action');
	}
	else
	{
		if ($sArgs['action'] == 'login')
		{
				if (!isset($sArgs['username'])) {
					throw new Exception('Invalid api call - no username');
				}
				else
				{
					$u = $sArgs['username'];
					$p = $sArgs['password'];
					$result['status'] = false;
					$pdo = Database::connect();
							$sql =("SELECT * FROM users WHERE username = '$u' and password = '$p'");
					foreach ($pdo->query($sql) as $row) {
					$result = array();
					$result['status'] = true;
					$result['userID'] = $row['userID'];
					$result['firstName'] = $row['firstName'];
					$result['lastName'] = $row['lastName'];
					$ownerID = $row['userID'];
						$dogs = array();
						$sqla =("SELECT * FROM dogs WHERE ownerID = $ownerID");
						foreach ($pdo->query($sqla) as $rowa) {
							$dogs['dogName'] = $rowa['dogName'];
							$dogs['weight'] = $rowa['weight'];
							$dogs['age'] = $rowa['age'];
							$dogs['dogID'] = $rowa['dogID'];
							$dogs['gender'] = $rowa['gender'];
							$result['dogs'] = $dogs;
						}
					}
					$t = json_encode($result);
					Database::disconnect();
					echo $t;
					return $t;
				}
		}
		
		else if ($sArgs['action'] == 'createUser'){
					if (!isset($sArgs['username'])) {
				throw new Exception('Invalid api call - no username');
				}
				else if (!isset($sArgs['password'])) {
				throw new Exception('Invalid api call - no password');
			}
				else if (!isset($sArgs['firstName'])) {
				throw new Exception('Invalid api call - no first name');
			}
				else if (!isset($sArgs['lastName'])) {
				throw new Exception('Invalid api call - no last name');
			}
			else{
			$result['status'] = false;
					$u = $sArgs['username'];
					$p = $sArgs['password'];
					$fn = $sArgs['firstName'];
					$ln = $sArgs['lastName'];
			$pdo=Database::connect();
			$pdo->beginTransaction();                     
            $sql = "INSERT INTO users (username, password, firstName, lastName)
            values(?, ?, ?, ?);";
            $q = $pdo->prepare($sql);
            $q->execute(array($u, $p, $fn, $ln));
                       
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $last_id = $pdo->lastInsertId();
            
            $pdo->commit();
            Database::disconnect();
            $result['status'] = true;
            $result['userID'] = $last_id;
            
            $t = json_encode($result);
            echo $t;
            return $t;
					
			}
					
		}
		
				else if ($sArgs['action'] == 'createDog'){
					if (!isset($sArgs['userID'])) {
				throw new Exception('Invalid api call - no user ID');
				}
				else if (!isset($sArgs['dogName'])) {
				throw new Exception('Invalid api call - no dog name');
			}
				else if (!isset($sArgs['weight'])) {
				throw new Exception('Invalid api call - no weight');
			}
				else if (!isset($sArgs['age'])) {
				throw new Exception('Invalid api call - no age');
			}
			else if (!isset($sArgs['gender'])) {
				throw new Exception('Invalid api call - no gender');
			}
			else{
			$result['status'] = false;
					$o = $sArgs['userID'];
					$dn = $sArgs['dogName'];
					$w = $sArgs['weight'];
					$a = $sArgs['age'];
					$g = $sArgs['gender'];
			$pdo=Database::connect();
			$pdo->beginTransaction();                     
            $sql = "INSERT INTO dogs (dogName, weight, age, gender, ownerID)
            values(?, ?, ?, ?, ?);";
            $q = $pdo->prepare($sql);
            $q->execute(array($dn, $w, $a, $g, $o));
                       
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $last_id = $pdo->lastInsertId();
            
            $pdo->commit();
            Database::disconnect();
            $result['status'] = true;
            $result['dogID'] = $last_id;
            
            $t = json_encode($result);
            echo $t;
            return $t;
					
			}
					
		}
		
			else if ($sArgs['action'] == 'deleteDog'){
					if (!isset($sArgs['dogID'])) {
				throw new Exception('Invalid api call - no user ID');
				}
			
			else{
			$result['status'] = false;
					$id = $sArgs['dogID'];

			$pdo=Database::connect();                   
            $sql = "DELETE FROM `dogs` WHERE dogID = $id";
            $q->execute($sql);                      
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            Database::disconnect();
            $result['status'] = true;
            $result['message'] = 'Dog Deleted';
            
            $t = json_encode($result);
            echo $t;
            return $t;
					
			}
					
		}
		
		else if ($sArgs['action'] == 'updateDog'){
					if (!isset($sArgs['userID'])) {
				throw new Exception('Invalid api call - no user ID');
				}
				else if (!isset($sArgs['dogName'])) {
				throw new Exception('Invalid api call - no dog name');
			}
				else if (!isset($sArgs['weight'])) {
				throw new Exception('Invalid api call - no weight');
			}
				else if (!isset($sArgs['age'])) {
				throw new Exception('Invalid api call - no age');
			}
			else if (!isset($sArgs['gender'])) {
				throw new Exception('Invalid api call - no gender');
			}
			else if (!isset($sArgs['dogID'])) {
				throw new Exception('Invalid api call - no dogID');
			}
			else{
			$result['status'] = false;
					$o = $sArgs['userID'];
					$dn = $sArgs['dogName'];
					$w = $sArgs['weight'];
					$a = $sArgs['age'];
					$g = $sArgs['gender'];
					$did = $sArgs['dogID'];
			$pdo=Database::connect();
			$sql = "UPDATE `dogs` SET `age`= ?,`weight`=?,`gender`=?,`dogName`=? WHERE dogID = $did;";
            $q = $pdo->prepare($sql);
            $q->execute(array($a, $w, $g, $dn));      
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            Database::disconnect();
            $result['status'] = true;
            $result['message'] = $dn.' has been updated';
            $t = json_encode($result);
            echo $t;
            return $t;
					
			}
					
		}
	}
}

catch (Exception $e) {
    //catch any exceptions and report the problem
    $result = array();
    $result['status'] = false;
    $result['errormsg'] = $e->getMessage();
    
    $t = json_encode($result);
    echo $t;
    return $t;
}
?>