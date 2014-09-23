<?php

/**
 * Class to handle all db operations
 * This class will have CRUD methods for database tables
 * 
 * @author Átyin Ákos
 */

class DbHandler {
    
    private $conn;
    
    public function __construct() {
        require_once dirname(__FILE__) . '/DbConnect.php';
        
        // Opening db connection
        $db = new DbConnect();
        $this->conn = $db->connect();
    }
    
    /* --------------------- 'gilda_user' table method  --------------------- */
    
    /**
     * Creating new user
     * 
     * @param String $name User full name
     * @param String $email User login email id
     * @param String $password User login password
     */
    public function createUser($first_name, $last_name, $email, $password) {
        require_once dirname(__FILE__) . '/PassHash.php';
        $response = array();
        
        // First check if user already existed in db
        if(!$this->isUserExists($email)) {
            // Generating password hash
            $password_hash = PassHash::hash($password);
            
            // Generating API Key
            $api_key = $this->generateApiKey();
            
            // insert query
            $queryString = 
                "INSERT INTO gilda_user(first_name, last_name, email, password_hash, api_key, status) 
                             VALUES(?, ?, ?, ?, ?, 1)";
            $stmt = $this->conn->prepare($queryString);
            $stmt->bind_param("sssss", $first_name, $last_name, $email, $password_hash, $api_key);
            
            $result = $stmt->execute();
            
            $stmt->close();
            
            // Check for successful insertion
            if($result) {
                return array("status" => USER_CREATED_SUCCESSFULLY, "api_key" => $api_key);
            }
            else {
                return array("status" => USER_CREATE_FAILED, "api_key" => null);
            }
        }
        else {
            return array("status" => USER_ALREADY_EXISTED, "api_key" => null);
        }
        
        return $response;
    }
    
    /**
     * Checking user login
     * @param String $email User login email id
     * @param String $password User login password
     * @return boolean User login status success/fail
     */
    public function checkLogin($email, $password) {
        // fetching user by email
        $queryString = "SELECT password_hash FROM gilda_user WHERE email = ?";
        $stmt = $this->conn->prepare($queryString);
        
        $stmt->bind_param("s", $email);
        
        $stmt->execute();
        
        $stmt->bind_result($password_hash);
        
        $stmt->store_result();
        
        if($stmt->num_rows > 0) {
            // Found user with e-mail
            // Now verify the password
            
            $stmt->fetch();
            
            $stmt->close();
            
            if(PassHash::check_password($password_hash, $password)) {
                // User password is correct
                return TRUE;
            }
            else {
                // User password is incorrect
                return FALSE;
            }
        }
        else {
            $stmt->close();
            
            // User not existed with the email
            return FALSE;
        }
    }
    
    /**
     * Checking for duplicate user by e-mail address
     * @param String $email E-mail to check in db
     * @return boolean
     */
    private function isUserExists($email) {
        $queryString = "SELECT id FROM gilda_user WHERE email = ?";
        $stmt = $this->conn->prepare($queryString);
        
        $stmt->bind_param("s", $email);
        
        $stmt->execute();
        
        $stmt->store_result();
        
        $num_rows = $stmt->num_rows;
        
        $stmt->close();
        
        return $num_rows > 0;
    }
    
    /**
     * Fetching user by email
     * @param String $email User email id
     */
    public function getUserByEmail($email) {
        $queryString = "SELECT first_name, last_name, email, api_key, status
                            FROM gilda_user WHERE email = ?";
        $stmt = $this->conn->prepare($queryString);
        
        $stmt->bind_param("s", $email);
        
        if($stmt->execute()) {
            $stmt->bind_result($user['first_name'], 
                               $user['last_name'], 
                               $user['email'], 
                               $user['api_key'], 
                               $user['status']);
            
            $stmt->fetch();
            
            $stmt->close();
            
            return $user;
        }
        else {
            return NULL;
        }
    }
    
    /**
     * Fetching user api key
     * @param String $user_id user id primary key in user table
     */
    public function getApiKeyById($user_id) {
        $queryString = "SELECT api_key FROM gilda_user WHERE id = ?";
        $stmt = $this->conn->prepare($queryString);
        
        $stmt->bind_param("i", $user_id);
        
        if($stmt->execute()) {
            
            $stmt->bind_result($api_key);
            
            $stmt->close();
            
            return $api_key;
        }
        else {
            return NULL;
        }
    }
    
    /**
     * Fetching user id by api key
     * @param String $api_key user api key
     */
    public function getUserId($api_key) {
        $queryString = "SELECT id FROM gilda_user WHERE api_key = ?";
        $stmt = $this->conn->prepare($queryString);
        
        $stmt->bind_param("s", $api_key);
        
        $stmt->execute();
        $stmt->bind_result($id);
        
        $stmt->fetch();

        $stmt->close();

        
        return $id;
    }
    
    /**
     * Validating user api key
     * If the api key is there in db, it is a valid key
     * @param String $api_key user api key
     * @return boolean
     */
    public function isValidApiKey($api_key) {
        $queryString = "SELECT id FROM gilda_user WHERE api_key = ?";
        $stmt = $this->conn->prepare($queryString);
        
        $stmt->bind_param("s", $api_key);
        
        $stmt->execute();
        
        $stmt->store_result();
        
        $num_rows = $stmt->num_rows;
        
        $stmt->close();
        
        return $num_rows > 0;
    }
    
    /**
     * Generating random Unique MD5 String for user Api key
     */
    private function generateApiKey() {
        return md5(uniqid(rand(), TRUE));
    }
    
    
    /* ----------------------- 'gilda_locations' table method  ----------------------- */
    
    /**
     * Fetching all locations
     */
    public function getAllLocations() {
        $queryString = "SELECT * FROM gilda_locations";
        $result = $this->conn->query($queryString);
        
        $locations = array();
        
        while($row = $result->fetch_assoc()) {
            $tmp = array("id" => $row["id"], 
                         "name" => $row["name"], 
                         "address" => $row["address"], 
                         "latitude" => $row["latitude"], 
                         "longitude" => $row["longitude"]);
            
            array_push($locations, $tmp);
        }
        
        return $locations;
    }
    
    /* ----------------------- 'gilda_rooms' table method  ----------------------- */
    
    /**
     * Fetching rooms by location id
     * @param int $location_id id of the location
     */
    public function getRoomsByLocationId($location_id) {
        $queryString = "SELECT r.id, r.name FROM gilda_rooms r
                        INNER JOIN gilda_locations l ON r.location_id=l.id
                        WHERE r.location_id=?";
        $stmt = $this->conn->prepare($queryString);
        $stmt->bind_param("i", $location_id);
        
        $stmt->execute();
        
        $rooms = array();
        
        $stmt->bind_result($id, $name);
        
        while($stmt->fetch()) {
            $tmp = array("id" => $id, 
                         "name" => $name);
            
            array_push($rooms, $tmp);
        }
        
        $stmt->close();
        
        return $rooms;
    }
    
    /* ----------------------- 'gilda_events' table method  ----------------------- */
    
    /**
     * Fetching events by room_id
     * @param int $room_id id of the room
     * @param int $user_id id of the user
     */
    public function getEventsByRoomId($room_id, $user_id) {
        $queryString = "SELECT ev.id, ev.date, ev.start_time, ev.end_time,
                               tr.name AS trainer, 
                               tri.name AS training, 
					ev.spots - (SELECT COUNT(*) FROM gilda_reservations WHERE event_id=ev.id) AS free_spots, 
                                        CASE
                                            WHEN (SELECT COUNT(*) FROM gilda_reservations WHERE event_id=ev.id AND user_id=?) > 0
                                            THEN 1
                                            ELSE 0
                                        END AS is_reserved 
                        FROM gilda_events AS ev 
                        LEFT JOIN gilda_trainer AS tr ON ev.trainer = tr.id
                        LEFT JOIN gilda_training AS tri ON ev.training = tri.id
                        WHERE room_id=? AND date>NOW() ORDER BY date, start_time";
        $stmt = $this->conn->prepare($queryString);
        $stmt->bind_param("ii", $user_id, $room_id);
        
        $stmt->execute();
        
        $events = array();
        
        $stmt->bind_result($id, $date, $start_time, $end_time, $trainer, $training, $free_spots, $is_reserved);
        
        while($stmt->fetch()) {
            $tmp = array("id" => $id, 
                         "date" => $date, 
                         "start_time" => $start_time, 
                         "end_time" => $end_time, 
                         "trainer" => $trainer, 
                         "training" => $training,
                         "free_spots" => $free_spots, 
                         "is_reserved" => $is_reserved);
            
            array_push($events, $tmp);
        }
        
        $stmt->close();
        
        return $events;
    }
    
    /**
     * Fetching free spots of event
     * @param int $event_id id of the event
     */
    public function getFreeSpotsByEventId($event_id) {
        $queryString = "SELECT ev.spots - (SELECT COUNT(*) FROM gilda_reservations WHERE event_id=?) AS free_spots FROM gilda_events AS ev WHERE ev.id=?";
        $stmt = $this->conn->prepare($queryString);
        $stmt->bind_param("ii", $event_id, $event_id);
        
        $stmt->execute();
        
        $stmt->bind_result($free_spots);
        
        $stmt->fetch();
        
        $stmt->close();
        
        return $free_spots;
    }
    
    /* ----------------------- 'gilda_reservations' table method  ----------------------- */
    
    /**
     * Create reservation
     * @param int $event_id id of the event
     * @param int $user_id id of the user
     */
    public function createReservation($event_id, $user_id) {
		$free_spots = $this->getFreeSpotsByEventId($event_id);
		
        if($free_spots != 0) {
            $queryString = "INSERT INTO gilda_reservations(user_id, event_id) VALUES (?, ?)";
            $stmt = $this->conn->prepare($queryString);
            $stmt->bind_param("ii", $user_id, $event_id);

            $result = $stmt->execute();

            $stmt->close();

            if($result) {
				return array('status' => RESERVATION_CREATED_SUCCESSFULLY, 'free_spots' => ($free_spots-1));
            }
            else {
				return array('status' => RESERVATION_CREATE_FAILED, 'free_spots' => $free_spots);
            }
        }
        else {
            return array('status' => NO_FREE_SPOTS, 'free_spots' => $free_spots);
        }
    }
    
    /**
     * Delete reservation
     * @param int $event_id id of the event
     * @param int $user_id id of the user
     */
    public function deleteReservation($event_id, $user_id) {
		$free_spots = $this->getFreeSpotsByEventId($event_id);
	
        $queryString = "DELETE FROM gilda_reservations WHERE user_id=? AND event_id=?";
        $stmt = $this->conn->prepare($queryString);
        $stmt->bind_param("ii", $user_id, $event_id);

        $stmt->execute();

        $num_affected_rows = $stmt->affected_rows;
        
        $stmt->close();
        
        if($num_affected_rows > 0) {
			return array('status' => true, 'free_spots' => ($free_spots+1));
		}
		else {
			return array('status' => false, 'free_spots' => $free_spots);
		}
    }
    
}

?>