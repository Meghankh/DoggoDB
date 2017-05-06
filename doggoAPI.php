<?php
date_default_timezone_set("America/New_York");
include 'database.php';

$method = $_SERVER['REQUEST_METHOD'];
 
if (strcmp($method, 'POST') !== 0) {
    /**********************************************************************
     * this is a get
     *    - check to make sure it is okay for GET
     */
    $input = filter_input_array(INPUT_GET);
}
else {
    /**********************************************************************
     * this is a post
     */
    $input = filter_input_array(INPUT_POST);
}

try {
    $inputJSON = file_get_contents('php://input');
    $input= json_decode( $inputJSON, TRUE ); //convert JSON into array
    if (!isset($input['action'])) {
        throw new Exception('Invalid api call - no action');
    } // require action in body
    else {
        if ($input['action'] == 'check_login') {
            if (!isset($input['username'])) {
                throw new Exception('Invalid api call - no username');
            }
	        if (!isset($_COOKIE[$input['username']])) {
                throw new Exception('Invalid api call - no cookie');
            }
            $result['status'] = true;
            $result['username'] = $input['username'];
            $t = json_encode($result);
            echo $t;
            return $t;
		}
        if ($input['action'] == 'login') {
            if (!isset($input['username'])) {
                throw new Exception('Invalid api call - no username');
            }
            else {
                $username = $input['username'];
                $password = $input['password'];
                $result['status'] = false;
                $pdo = Database::connect();
                $sql =("SELECT * FROM users WHERE username = '$username' and password = '$password'");
                foreach ($pdo->query($sql) as $user_row) {
                    $result = array();
                    $result['status'] = true;
                    $result['userID'] = $user_row['userID'];
                    $result['firstName'] = $user_row['firstName'];
                    $result['lastName'] = $user_row['lastName'];
                    $ownerID = $user_row['userID'];
                    $dogs = array();
                    $sqla =("SELECT * FROM dogs WHERE ownerID = $ownerID");
                    foreach ($pdo->query($sqla) as $dog_row) {
                        $dogs['dogName'] = $dog_row['dogName'];
                        $dogs['weight'] = $dog_row['weight'];
                        $dogs['age'] = $dog_row['age'];
                        $dogs['dogID'] = $dog_row['dogID'];
                        $dogs['gender'] = $dog_row['gender'];
                        $result['dogs'] = $dogs;
                    }
                }
        		$cookie_name = $username;
	        	$cookie_value = "logged-in";
        		setcookie($cookie_name, $cookie_value, time() + (86400 * 30), "/"); // 86400 = 1 day
                $t = json_encode($result);
                Database::disconnect();
                echo $t;
                return $t;
            }
        } // end login
        else if ($input['action'] == 'steps') {
            $result['sucess'] = false;
            $previoud_id = 0;
            $pdo=Database::connect();
            foreach ($pdo->query("select max(id) as prev_id from step_progress;") as $row) {
                $previous_id = (int)$row['prev_id'];
            }
            Database::disconnect();
            $id = $previous_id + 1;
            $user_id = $input['user_id'];
            $date = $input['date'];
            $epoch = $input['epoch'];
            $steps_total = $input['steps_total'];
            $steps_delta = $input['steps_delta'];
            $pdo=Database::connect();
            $pdo->beginTransaction();                     
            $sql = "INSERT INTO step_progress (id, user_id, date, epoch, steps_total, steps_delta)
            values(?, ?, ?, ?, ?, ?);";
            $q = $pdo->prepare($sql);
            $q->execute(array($id, $user_id, $date, $epoch, $steps_total, $steps_total));
                       
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $last_id = $pdo->lastInsertId();
            
            $pdo->commit();
            Database::disconnect();
            $result['sucess'] = true;
            
            $t = json_encode($result);
            echo $t;
            return $t;
        }
        else if ($input['action'] == 'findUser')
        {   
            $result['success'] = false;
            $user_id = $input['user_id'];
            $pdo=Database::connect();
            foreach ($pdo->query("select lastName, firstName from users where userID = '$user_id'") as $row) {
                $user_last = $row['lastName'];
                $user_first = $row['firstName'];
            }
            Database::disconnect();
            $result['success'] = true;
            $result['lastName'] = $user_last;
            $result['firstName'] = $user_first;
            $t = json_encode($result);
            echo $t;
            return $t;
        }
        else if ($input['action'] == 'findDogs')
        {
            $result['success'] = false;
            $user_id = $input['user_id'];
            $pdo=Database::connect();
            foreach ($pdo->query("select dogName, age, weight, gender from dogs where ownerID = '$user_id'") as $row) {
                $dogName = $row['dogName'];
                $age = $row['age'];
                $weight = $row['weight'];
                $gender = $row['gender'];
                $result['dogs'][$dogName]['dogName'] = $dogName;
                $result['dogs'][$dogName]['age'] = $age;
                $result['dogs'][$dogName]['weight'] = $weight;
                $result['dogs'][$dogName]['gender'] = $gender;
            }
            Database::disconnect();
            $result['success'] = true;
            $t = json_encode($result);
            echo $t;
            return $t;
        }
        else if ($input['action'] == 'createUser') {
            if (!isset($input['username'])) {
                throw new Exception('Invalid api call - no username');
            }
            else if (!isset($input['password'])) {
                throw new Exception('Invalid api call - no password');
            }
            else if (!isset($input['firstName'])) {
                throw new Exception('Invalid api call - no first name');
            }
            else if (!isset($input['lastName'])) {
                throw new Exception('Invalid api call - no last name');
            }
            else {
                $result['status'] = false;
                $u = $input['username'];
                $p = $input['password'];
                $fn = $input['firstName'];
                $ln = $input['lastName'];
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
        } // end createUser
        else if ($input['action'] == 'createDog') {
            if (!isset($input['userID'])) {
                throw new Exception('Invalid api call - no user ID');
            }
            else if (!isset($input['dogName'])) {
                throw new Exception('Invalid api call - no dog name');
            }
            else if (!isset($input['weight'])) {
                throw new Exception('Invalid api call - no weight');
            }
            else if (!isset($input['age'])) {
                throw new Exception('Invalid api call - no age');
            }
            else if (!isset($input['gender'])) {
                throw new Exception('Invalid api call - no gender');
            }
            else {
                $result['status'] = false;
                $o = $input['userID'];
                $dn = $input['dogName'];
                $w = $input['weight'];
                $a = $input['age'];
                $g = $input['gender'];
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
        } // End createDog
        else if ($input['action'] == 'deleteDog') {
            if (!isset($input['dogID'])) {
                throw new Exception('Invalid api call - no user ID');
            }
            else {
                $result['status'] = false;
                $id = $input['dogID'];

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
        } // End deleteDog
        else if ($input['action'] == 'updateDog') {
            if (!isset($input['userID'])) {
                throw new Exception('Invalid api call - no user ID');
            }
            else if (!isset($input['dogName'])) {
                throw new Exception('Invalid api call - no dog name');
            }
            else if (!isset($input['weight'])) {
                throw new Exception('Invalid api call - no weight');
            }
            else if (!isset($input['age'])) {
                throw new Exception('Invalid api call - no age');
            }
            else if (!isset($input['gender'])) {
                throw new Exception('Invalid api call - no gender');
            }
            else if (!isset($input['dogID'])) {
                throw new Exception('Invalid api call - no dogID');
            }
            else {
                $result['status'] = false;
                $o = $input['userID'];
                $dn = $input['dogName'];
                $w = $input['weight'];
                $a = $input['age'];
                $g = $input['gender'];
                $did = $input['dogID'];
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
        } // End updateDog
    }
}
catch (Exception $e) {
    // catch any exceptions and report the problem
    $result = array();
    $result['status'] = false;
    $result['errormsg'] = $e->getMessage();
    
    $t = json_encode($result);
    echo $t;
    return $t;
}

?>
