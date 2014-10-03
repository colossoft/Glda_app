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

    /*
    *Modify user's passord
    */
    public function PasswordModify($partnerId, $oldPassword, $newPassword, $confirmNewPassword) {
        require_once dirname(__FILE__) . '/PassHash.php';
        $response = array();

        //Check newPassword and confirmNewPassword
        if (strcmp($newPassword, $confirmNewPassword) != 0) {
            return null;
        }

        //Check the user
        $userIsGood = $this->CheckUser($partnerId, $oldPassword);
        if (!$userIsGood) {
            return null;
        }

        $password_hash = PassHash::hash($newPassword);

        $queryString = "Update gilda_user Set password_hash = ? Where id = ?";
        $stmt = $this->conn->prepare($queryString);
        
        $stmt->bind_param("si", $password_hash, $partnerId);
        
        if($stmt->execute()) {
            return true;
        }
        else {
            return NULL;
        }
    }

    /*
    *Modify the forgotten password of the user
    */
    public function forgottenPasswordModify($email, $newPassword) {
        require_once dirname(__FILE__) . '/PassHash.php';
        $response = array();

        $password_hash = PassHash::hash($newPassword);

        $queryString = "Update gilda_user Set password_hash = ? Where email = ?";
        $stmt = $this->conn->prepare($queryString);
        
        $stmt->bind_param("ss", $password_hash, $email);
        
        if($stmt->execute()) {
            return true;
        }
        else {
           return NULL;
        }
    }
    
    /**
     * Checking user login
     * @param String $email User login email id
     * @param String $password User login password
     * @return boolean User login status success/fail
     */
    public function checkLogin($email, $password) {
        // fetching user by email
        $queryString = "SELECT password_hash FROM gilda_user WHERE email = ? AND status <> 0";
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
            try{
                $stmt->close();
            }
            catch(Exception $exc) {
                error_log($exc->getMessage());
                return false;
            }
            
            // User not existed with the email
            return FALSE;
        }
    }

    /**
     * Checking user login
     * @param String $email User login email id
     * @param String $password User login password
     * @return boolean User login status success/fail
     */
    public function CheckUser($user_id, $password) {
        // fetching user by email
        $queryString = "SELECT password_hash FROM gilda_user WHERE id=?";
        $stmt = $this->conn->prepare($queryString);
        $stmt->bind_param("i", $user_id);
        
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
            try{
                $stmt->close();
            }
            catch(Exception $exc) {
                error_log($exc->getMessage());
                return false;
            }
            
            // User not existed with the email
            return FALSE;
        }
    }
    
    /**
     * Checking for duplicate user by e-mail address
     * @param String $email E-mail to check in db
     * @return boolean
     */
    public function isUserExists($email) {
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

    public function GetUserDetailsById($user_id) {
        $queryString = "SELECT first_name, last_name, email 
                            FROM gilda_user WHERE id = ?";
        $stmt = $this->conn->prepare($queryString);
        
        $stmt->bind_param("i", $user_id);
        
        if($stmt->execute()) {
            $stmt->bind_result($firstName, $lastName, $email);
            
            $stmt->fetch();

            $datas = array("firstName" => $firstName, "lastName" => $lastName, "email" => $email);
            
            $stmt->close();
            
            return $datas;
        }
        else {
            return NULL;
        }
    }

    /**
     * Return name of user by user_id
     * @param String $email User email id
     */
    public function GetUserNameById($user_id) {
        $queryString = "SELECT CONCAT(first_name, ' ', last_name)
                            FROM gilda_user WHERE id = ?";
        $stmt = $this->conn->prepare($queryString);
        
        $stmt->bind_param("i", $user_id);
        
        if($stmt->execute()) {
            $stmt->bind_result($name);
            
            $stmt->fetch();
            
            $stmt->close();
            
            return $name;
        }
        else {
            return NULL;
        }
    }

    /*
    *Fetching all partners
    */
    public function GetPartners() {
        $queryString = "SELECT id, first_name, last_name, email, status, created_at
                            FROM gilda_user WHERE status = 1 or status = 0 ORDER BY last_name, first_name";
        $stmt = $this->conn->prepare($queryString);

        if($stmt->execute()) {
            $stmt->bind_result($id, $first_name, $last_name, $email, $status, $created_at);

            $result = array();

            while($stmt->fetch()) {
                $tmp = array("id" => $id, 
                             "first_name" => $first_name, 
                             "last_name" => $last_name,
                             "email" => $email,
                             "status" => $status, 
                             "created_at" => $created_at);
            
                array_push($result, $tmp);
            }
            
            $stmt->close();
            
            return $result;
        }
        else {
            return NULL;
        }
    }

    /*
    * Ban a partner
    */
    public function DenyPartner($partnerId) {
        $queryString = "Update gilda_user Set status = 0 Where id = ?";
        $stmt = $this->conn->prepare($queryString);
        
        $stmt->bind_param("i", $partnerId);
        
        if($stmt->execute()) {
            return true;
        }
        else {
            return NULL;
        }
    }

    /*
    * Disengage a partner
    */
    public function DisengagePartner($partnerId) {
        $queryString = "Update gilda_user Set status = 1 Where id = ?";
        $stmt = $this->conn->prepare($queryString);
        
        $stmt->bind_param("i", $partnerId);
        
        if($stmt->execute()) {
            return true;
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
        $queryString = "SELECT id FROM gilda_user WHERE api_key = ? and status <> 0";
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

    /**
    * Check existing room by id
    */
    public function CheckRoomId($roomId) {
        $queryString = "SELECT * FROM gilda_rooms WHERE id=?";
        $stmt = $this->conn->prepare($queryString);
        $stmt->bind_param("i", $roomId);
        
        $stmt->execute();

        $stmt->store_result();
        
        $num_rows = $stmt->num_rows;
        
        $stmt->close();
        
        return $num_rows > 0;
    }

    public function CreateRoom($name, $locationId) {
        $queryString = 
                "INSERT INTO gilda_rooms(location_id, name) 
                             VALUES(?, ?)";

        $stmt = $this->conn->prepare($queryString);
        $stmt->bind_param("is", $locationId, $name);
        $result = $stmt->execute();
        $stmt->close();

        if ($result) {
            return $result;
        } else {
            return NULL;
        }
    }

    public function DeleteRoom($roomId) {
        if($this->CheckRoom($roomId) > 0) {
            return false;
        } else {
            $queryString = "DELETE FROM gilda_rooms WHERE id=?";
            $stmt = $this->conn->prepare($queryString);
            $stmt->bind_param("i", $roomId);

            $stmt->execute();

            $num_affected_rows = $stmt->affected_rows;
            
            $stmt->close();

            if($num_affected_rows > 0)
                return true;
            else 
                return false;
        }
    }

    public function CheckRoom($roomId) {
        $queryString = "SELECT COUNT(*) FROM gilda_events WHERE room_id=?";
        $stmt = $this->conn->prepare($queryString);
        $stmt->bind_param("i", $roomId);
        
        $stmt->execute();
        
        $stmt->bind_result($count);

        $stmt->fetch();
        
        $stmt->close();

        return $count;
    }
    
    /* ----------------------- 'gilda_events' table method  ----------------------- */
    
    /**
     * Fetching events by room_id
     * @param int $room_id id of the room
     * @param int $user_id id of the user
     */
    public function getEventsByRoomId($room_id, $user_id) {
        $queryString = "SELECT ev.id, ev.date, ev.start_time, ev.end_time, 
                                CONCAT(tr.last_name, ' ', tr.first_name) AS trainer, tri.name AS training, ev.spots,
					    ev.spots - (SELECT COUNT(*) FROM gilda_reservations WHERE event_id=ev.id) AS free_spots, 
                        CASE
                            WHEN (SELECT COUNT(*) FROM gilda_reservations WHERE event_id=ev.id AND user_id=?) > 0
                            THEN 1
                            ELSE 0
                        END AS is_reserved 
                        FROM gilda_events AS ev 
                        LEFT JOIN gilda_trainer AS tr ON ev.trainer = tr.id
                        LEFT JOIN gilda_training AS tri ON ev.training = tri.id
                        WHERE room_id=? AND date>=NOW() AND date<=(NOW() + INTERVAL 2 WEEK) ORDER BY date, start_time";
        $stmt = $this->conn->prepare($queryString);
        $stmt->bind_param("ii", $user_id, $room_id);
        
        $stmt->execute();
        
        $events = array();
        
        $stmt->bind_result($id, $date, $start_time, $end_time, $trainer, $training, $spots, $free_spots, $is_reserved);
        
        while($stmt->fetch()) {
            $tmp = array("id" => $id, 
                         "date" => $date, 
                         "start_time" => $start_time, 
                         "end_time" => $end_time, 
                         "trainer" => $trainer, 
                         "training" => $training,
                         "spots" => $spots,
                         "free_spots" => $free_spots, 
                         "is_reserved" => $is_reserved);
            
            array_push($events, $tmp);
        }
        
        $stmt->close();
        
        return $events;
    }

    /**
     * Fetching events by room_id and day
     * @param int $room_id id of the room
     * @param int $user_id id of the user
     */
    public function getEventsByRoomIdAndDay($user_id, $room_id, $day) {
        $queryString = "SELECT ev.id, ev.date, ev.start_time, ev.end_time,
                        CONCAT(tr.last_name, ' ', tr.first_name) AS trainer, tri.name AS training, ev.spots,
                        ev.spots - (SELECT COUNT(*) FROM gilda_reservations WHERE event_id=ev.id) AS free_spots, 
                        CASE
                            WHEN (SELECT COUNT(*) FROM gilda_reservations WHERE event_id=ev.id AND user_id=?) > 0
                            THEN 1
                            ELSE 0
                        END AS is_reserved 
                        FROM gilda_events AS ev 
                        LEFT JOIN gilda_trainer AS tr ON ev.trainer = tr.id
                        LEFT JOIN gilda_training AS tri ON ev.training = tri.id
                        WHERE room_id=? AND date=? ORDER BY date, start_time";
        $stmt = $this->conn->prepare($queryString);
        $stmt->bind_param("iis", $user_id, $room_id, $day);
        
        $stmt->execute();
        
        $events = array();
        
        $stmt->bind_result($id, $date, $start_time, $end_time, $trainer, $training, $spots, $free_spots, $is_reserved);
        
        while($stmt->fetch()) {
            $tmp = array("id" => $id, 
                         "date" => $date, 
                         "startTime" => $start_time, 
                         "endTime" => $end_time, 
                         "trainerName" => $trainer, 
                         "trainingName" => $training, 
                         "spots" => $spots, 
                         "reservedSpots" => $spots - $free_spots, 
                         "freeSpots" => $free_spots, 
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

    /**
     * Fetching information of event
     * @param int $event_id id of the event
     */
    public function GetInformationOfEventByEventId($event_id) {
        $queryString = "SELECT tri.name AS Training, CONCAT(tr.last_name, ' ', tr.first_name) AS Trainer, ev.date, 
                        ev.start_time, ev.end_time 
                        From gilda_events AS ev 
                        Inner Join gilda_trainer AS tr ON tr.Id = ev.Trainer 
                        Inner Join gilda_training AS tri ON tri.Id = ev.Training 
                        Where ev.Id = ?";
        $stmt = $this->conn->prepare($queryString);
        $stmt->bind_param("i", $event_id);
        
        $stmt->execute();

        $event = array();
        
        $stmt->bind_result($event['training'], $event['trainer'], $event['date'], $event['start_time'], $event['end_time']);
        
        $stmt->fetch();
        
        $stmt->close();
        
        return $event;
    }

    /**
    * Create a new event
    */
    public function CreateEvent($roomId, $date, $startTime, $endTime, $trainerId, $trainingId, $spots) {
        $errorList = '';
        $haveError = false;

        //Check the roomId
        if (!$this->CheckRoomId($roomId)) {
            $errorList .= '\nRosszul adta meg a termet.';
            $haveError = true;
        }

        //Check the trainerId
        if (!$this->CheckTrainerId($trainerId)) {
            $errorList .='\nRosszul adta meg az edzőt.';
            $haveError = true;
        }

        //Check the training
        if (!$this->CheckTrainingId($trainingId)) {
            $errorList .= '\nRosszul adta meg az edzés típusát.';
            $haveError = true;
        }
        if ($haveError) {
            error_log($errorList);
           return array('errorList' => $errorList);
        }
        
        // insert query
        $queryString = 
            "INSERT INTO gilda_events(room_id, date, start_time, end_time, trainer, training, spots) 
                         VALUES(?, ?, ?, ?, ?, ?, ?)";
        $stmt = $this->conn->prepare($queryString);
        $stmt->bind_param("isssiii", $roomId, $date, $startTime, $endTime, $trainerId, $trainingId, $spots);
        
        $result = $stmt->execute();
        
        $stmt->close();
        
        // Check for successful insertion
        if($result) {
            return array('errorList' => false);;
        }
        else {
             $errorList .= '\nVáratlan hiba történt.';
             error_log($errorList);
            return array('errorList' => $errorList);
        }
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
                $this->AddLog($event_id, $user_id, true);
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
            $this->AddLog($event_id, $user_id, false);
			return array('status' => true, 'free_spots' => ($free_spots+1));
		}
		else {
			return array('status' => false, 'free_spots' => $free_spots);
		}
    }

     /**
    * Fetching all reservation of evet by eventId
    */
    public function GetReservationsOfEventByEventId($eventId) {
        $queryString = "SELECT ev.id, ev.date, ev.start_time, ev.end_time, 
                        CONCAT(tr.last_name, ' ', tr.first_name) AS trainer, tri.name AS training, ev.spots,
                        ev.spots - (SELECT COUNT(*) FROM gilda_reservations WHERE event_id=ev.id) AS free_spots   
                        FROM gilda_events AS ev 
                        LEFT JOIN gilda_trainer AS tr ON ev.trainer = tr.id
                        LEFT JOIN gilda_training AS tri ON ev.training = tri.id  
                        WHERE ev.id=?";
        $stmt = $this->conn->prepare($queryString);
        $stmt->bind_param("i", $eventId);
        
        $stmt->execute();

        $event = array();
        
        $stmt->bind_result($event['id'], $event['date'], $event['startTime'], $event['endTime'], $event['trainerName'], $event['trainingName'], $event['spots'], $event['freeSpots']);
        
        $stmt->fetch();

        $stmt->close();

        $result = array();
        
        $result["event"] = $event;

        $result["reservations"] = $this->GetReservationsOfEvent($eventId);
        
        return $result;
    }

    /**
    * Fetching the reservations of event by eventId and put the $events array
    */
    public function GetReservationsOfEvent($eventId) {
        $queryString = "SELECT res.time AS date, us.first_name AS firstName, us.last_name AS lastName, us.email
                        FROM gilda_reservations AS res 
                        LEFT JOIN gilda_events AS ev ON res.event_id = ev.id
                        LEFT JOIN gilda_user AS us ON res.user_id = us.id
                        WHERE ev.id = ?";
        $stmt = $this->conn->prepare($queryString);
        $stmt->bind_param("i", $eventId);
        
        $stmt->execute();
        
        $stmt->bind_result($date, $firstName, $lastName, $email);
        
        $reservations = array();

        while($stmt->fetch()) {
            $tmp = array("date" => $date,
                         "firstName" => $firstName,
                         "lastName" => $lastName,
                         "email" => $email);
            
            array_push($reservations, $tmp);
        }

        $stmt->close();

        return $reservations;
    }

    public function GetReservationsOfUser($user_id) {
        $queryString = "SELECT ev.id, res.time, ev.date, ev.start_time, ev.end_time, 
                                CONCAT(tr.last_name, ' ', tr.first_name) AS trainer, tri.name AS training, ev.spots,
                        ev.spots - (SELECT COUNT(*) FROM gilda_reservations WHERE event_id=ev.id) AS free_spots 
                        FROM gilda_reservations AS res 
                        LEFT JOIN gilda_events AS ev ON ev.id = res.event_id 
                        LEFT JOIN gilda_trainer AS tr ON ev.trainer = tr.id
                        LEFT JOIN gilda_training AS tri ON ev.training = tri.id
                        WHERE res.user_id=? ORDER BY res.time DESC";
        $stmt = $this->conn->prepare($queryString);
        $stmt->bind_param("i", $user_id);
        
        $stmt->execute();
        
        $reservations = array();
        
        $stmt->bind_result($id, $time, $date, $start_time, $end_time, $trainer, $training, $spots, $free_spots);
        
        while($stmt->fetch()) {
            $tmp = array("id" => $id, 
                         "reservationTime" => $time, 
                         "date" => $date, 
                         "startTime" => $start_time, 
                         "endTime" => $end_time, 
                         "trainerName" => $trainer, 
                         "trainingName" => $training,
                         "spots" => $spots, 
                         "reservedSpots" => $spots - $free_spots, 
                         "freeSpots" => $free_spots);
            
            array_push($reservations, $tmp);
        }
        
        $stmt->close();
        
        return $reservations;
    }

    /* ----------------------- 'gilda_trainer' table method  ----------------------- */

     /**
    * Check existing trainer by id
    */
    public function CheckTrainerId($trainerId) {
        $queryString = "SELECT * FROM gilda_trainer WHERE id=?";
        $stmt = $this->conn->prepare($queryString);
        $stmt->bind_param("i", $trainerId);
        
        $stmt->execute();
        
        $stmt->store_result();
        
        $num_rows = $stmt->num_rows;
        
        $stmt->close();
        
        return $num_rows > 0;
    }

    /**
    * Fetching all trainers
    */
    public function GetAllTrainers() {
        $queryString = "SELECT * FROM gilda_trainer ORDER BY last_name, first_name";
        $stmt = $this->conn->prepare($queryString);
        
        $stmt->execute();
        
        $trainers = array();
        
        $stmt->bind_result($id, $first_name, $email, $last_name);
        while($stmt->fetch()) {
            $tmp = array("id" => $id, 
                         "firstName" => $first_name,
                         "lastName" => $last_name,
                         "email" => $email);
            
            array_push($trainers, $tmp);
        }
        
        $stmt->close();
        
        return $trainers;
    }

    public function CreateTrainer($first_name, $last_name, $email) {
        //Check training params
        if ($first_name == NULL || $first_name == '' || $last_name == NULL || $last_name == '' 
            || $email == NULL || $email == '') {
            return NULL;
        }

        $queryString = 
                "INSERT INTO gilda_trainer(first_name, last_name, email) 
                             VALUES(?, ?, ?)";

        $stmt = $this->conn->prepare($queryString);
        $stmt->bind_param("sss", $first_name, $last_name, $email);
        $result = $stmt->execute();
        $stmt->close();

        if ($result) {
            return $result;
        } else {
            return NULL;
        }   
    }

    public function DeleteTrainer($trainerId) {
        if($this->CheckTrainer($trainerId) > 0) {
            return false;
        } else {
            $queryString = "DELETE FROM gilda_trainer WHERE id=?";
            $stmt = $this->conn->prepare($queryString);
            $stmt->bind_param("i", $trainerId);

            $stmt->execute();

            $num_affected_rows = $stmt->affected_rows;
            
            $stmt->close();

            if($num_affected_rows > 0)
                return true;
            else 
                return false;
        }
    }

    public function CheckTrainer($trainerId) {
        $queryString = "SELECT COUNT(*) FROM gilda_events WHERE trainer=?";
        $stmt = $this->conn->prepare($queryString);
        $stmt->bind_param("i", $trainerId);
        
        $stmt->execute();
        
        $stmt->bind_result($count);

        $stmt->fetch();
        
        $stmt->close();
        
        return $count;
    }

    /* ----------------------- 'gilda_training' table method  ----------------------- */

     /**
    * Check existing training by id
    */
    public function CheckTrainingId($trainingId) {
        $queryString = "SELECT * FROM gilda_training WHERE id=?";
        $stmt = $this->conn->prepare($queryString);
        $stmt->bind_param("i", $trainingId);
        
        $stmt->execute();
        
        $stmt->store_result();
        
        $num_rows = $stmt->num_rows;
        
        $stmt->close();
        
        return $num_rows > 0;
    }

    /**
    * Fetching all trainings
    */
    public function GetAllTrainings() {
        $queryString = "SELECT * FROM gilda_training ORDER BY name";
        $stmt = $this->conn->prepare($queryString);
        
        $stmt->execute();
        
        $trainings = array();
        
        $stmt->bind_result($id, $name);
        
        while($stmt->fetch()) {
            $tmp = array("id" => $id, 
                         "name" => $name);
            
            array_push($trainings, $tmp);
        }
        
        $stmt->close();
        
        return $trainings;
    }

    public function CreateTraining($name) {
        //Check training params
        if ($name == NULL || $name == '') {
            return NULL;
        }

        $queryString = 
                "INSERT INTO gilda_training(name) 
                             VALUES(?)";

        $stmt = $this->conn->prepare($queryString);
        $stmt->bind_param("s", $name);
        $result = $stmt->execute();
        $stmt->close();

        if ($result) {
            return $result;
        } else {
            return NULL;
        }
    }

    public function DeleteTraining($trainingId) {
        if($this->CheckTraining($trainingId) > 0) {
            return false;
        } else {
            $queryString = "DELETE FROM gilda_training WHERE id=?";
            $stmt = $this->conn->prepare($queryString);
            $stmt->bind_param("i", $trainingId);

            $stmt->execute();

            $num_affected_rows = $stmt->affected_rows;
            
            $stmt->close();

            if($num_affected_rows > 0)
                return true;
            else 
                return false;
        }
    }

    public function CheckTraining($trainingId) {
        $queryString = "SELECT COUNT(*) FROM gilda_events WHERE training=?";
        $stmt = $this->conn->prepare($queryString);
        $stmt->bind_param("i", $trainingId);
        
        $stmt->execute();
        
        $stmt->bind_result($count);

        $stmt->fetch();
        
        $stmt->close();
        
        return $count;
    }

    /* ----------------------- 'gilda_news' table method  ----------------------- */

    public function GetAllLanguages() {
        $queryString = "SELECT * FROM gilda_language";
        $stmt = $this->conn->prepare($queryString);

        $stmt->execute();
        
        $languages = array();
        
        $stmt->bind_result($id, $name);
        
        while($stmt->fetch()) {
            $tmp = array("id" => $id, 
                         "name" => $name);
            
            array_push($languages, $tmp);
        }
        
        $stmt->close();
        
        return $languages;
    }

    public function GetAllNews($languageId) {
        $queryString = "SELECT * FROM gilda_news WHERE languageId=? ORDER BY created_date DESC";
        $stmt = $this->conn->prepare($queryString);
        $stmt->bind_param("i", $languageId);

        $stmt->execute();
        
        $news = array();
        
        $stmt->bind_result($id, $newsId, $title, $newsText, $createdDate, $languageId);
        
        while($stmt->fetch()) {
            $tmp = array("id" => $id, 
                         "newsId" => $newsId, 
                         "title" => $title, 
                         "newsText" => $newsText, 
                         "createdDate" => $createdDate,  
                         "languageId" => $languageId);
            
            array_push($news, $tmp);
        }
        
        $stmt->close();
        
        return $news;
    }

    public function CreateNews($created_date, $news) {
        $errorText = '';

        //Check news params
        if (!$this->CheckNewsParameter($news)) {
            $errorText= 'Nem töltött ki minden nyelvet megfelelően!';
            return $errorText;
        }

        $latestNewsId = $this->GetLatestNewsId();
        $newNewsId = $latestNewsId + 1;

        $queryString = 
                "INSERT INTO gilda_news(newsId, title, newsText, created_date, languageId) 
                             VALUES(?, ?, ?, ?, ?)";

        $stmt = $this->conn->prepare($queryString);

        foreach ($news as $key => $value) {
            $stmt->bind_param("isssi", $newNewsId, $value['Title'], $value['Text'], $created_date, $value['LanguageId']);
            
            $result = $stmt->execute();
            
            // Check for successful insertion
            if(!$result) {
                $errorText = 'Váratlan hiba történt. Kérjük próbálja újra!';
                break;
            }
        }

        $stmt->close();

        return $errorText;
    }

    public function CheckNewsParameter($news) {
        foreach ($news as $key => $value) {
            if ($value['Title'] == NULL || $value['Title'] == '' || $value['Text'] == NULL || $value['Text'] == '') {
                return false;
            }
        }

        return true;
    }

    public function GetLatestNewsId() {
        $queryString = "SELECT newsId From gilda_news Order By newsId Desc Limit 1 ";
        $stmt = $this->conn->prepare($queryString);
        
        $stmt->execute();
        
        $stmt->bind_result($newsId);

        $stmt->store_result();
        
        $num_rows = $stmt->num_rows;

        if ($num_rows == 0) {
           return 0;
        }
        
        $stmt->fetch();
        
        $stmt->close();
        
        return $newsId;
    }

    public function DeleteNews($newsId) {
        $queryString = "DELETE FROM gilda_news WHERE newsId=?";
        $stmt = $this->conn->prepare($queryString);
        $stmt->bind_param("i", $newsId);

        $stmt->execute();

        $num_affected_rows = $stmt->affected_rows;
        
        $stmt->close();

        if($num_affected_rows > 0)
            return true;
        else 
            return false;
    }

    /* ----------------------- 'gilda_devaluation' table method  ----------------------- */

    public function GetAllDevaluation($languageId) {
        $queryString = "SELECT * FROM gilda_devaluation WHERE languageId=? ORDER BY start_date DESC";
        $stmt = $this->conn->prepare($queryString);
        $stmt->bind_param("i", $languageId);

        $stmt->execute();
        
        $devaluations = array();
        
        $stmt->bind_result($id, $title, $text, $startDate, $endDate, $languageId, $devaluationId);
        
        while($stmt->fetch()) {
            $tmp = array("id" => $id, 
                         "title" => $title, 
                         "text" => $text, 
                         "startDate" => $startDate, 
                         "endDate" => $endDate,  
                         "languageId" => $languageId, 
                         "devaluationId" => $devaluationId);
            
            array_push($devaluations, $tmp);
        }
        
        $stmt->close();
        
        return $devaluations;
    }

    public function CreateDevaluation($start_date, $end_date, $devaluation) {
        $errorText = '';

        //Check news params
        if (!$this->CheckDevaluationParameter($devaluation)) {
            $errorText= 'Nem töltött ki minden nyelvet megfelelően!';
            return $errorText;
        }

        $latestDevaluationId = $this->GetLatestDevaluationId();
        $newDevaluationId = $latestDevaluationId + 1;

        $queryString = 
                "INSERT INTO gilda_devaluation(devaluationId, title, text, start_date, end_date, languageId) 
                             VALUES(?, ?, ?, ?, ?, ?)";

        $stmt = $this->conn->prepare($queryString);

        foreach ($devaluation as $key => $value) {
            $stmt->bind_param("issssi", $newDevaluationId, $value['Title'], $value['Text'], $start_date, $end_date, $value['LanguageId']);
            
            $result = $stmt->execute();
            
            // Check for successful insertion
            if(!$result) {
                $errorText = 'Váratlan hiba történt. Kérjük próbálja újra!';
                $errorText = $devaluation[0].Title;
                break;
            }
        }

        $stmt->close();

        return $errorText;
    }

    public function CheckDevaluationParameter($news) {
        foreach ($news as $key => $value) {
            if ($value['Title'] == NULL || $value['Title'] == '' || $value['Text'] == NULL || $value['Text'] == '') {
                return false;
            }
        }

        return true;
    }

    public function GetLatestDevaluationId() {
        $queryString = "SELECT devaluationId From gilda_devaluation Order By devaluationId Desc Limit 1 ";
        $stmt = $this->conn->prepare($queryString);
        
        $stmt->execute();
        
        $stmt->bind_result($devaluationId);

        $stmt->store_result();
        
        $num_rows = $stmt->num_rows;

        if ($num_rows == 0) {
           return 0;
        }
        
        $stmt->fetch();
        
        $stmt->close();
        
        return $devaluationId;
    }

    public function DeleteDevaluation($devaluationId) {
        $queryString = "DELETE FROM gilda_devaluation WHERE devaluationId=?";
        $stmt = $this->conn->prepare($queryString);
        $stmt->bind_param("i", $devaluationId);

        $stmt->execute();

        $num_affected_rows = $stmt->affected_rows;
        
        $stmt->close();

        if($num_affected_rows > 0)
            return true;
        else 
            return false;
    }

     /* ----------------------- 'gilda_log' table method  ----------------------- */

     public function AddLog($event_id, $user_id, $isCreated) {
        $name = $this->GetUserNameById($user_id);
        $event = $this->GetInformationOfEventByEventId($event_id);
        $created_date = date("Y-m-d H:i:s");
        $operation = '';

        if ($isCreated) {
            $operation .= 'Feliratkozott a(z) ' . $event['training'] . ' eseményre, amit ' . $event['trainer'] . ' tart ' . $event['date'] . ' '
             . $event['start_time'] . ' -tól ' . $event['end_time'] . ' -ig';
            //var_dump($operation);

        } else {
            $operation .= 'Leiratkozott a(z) ' . $event['training'] . ' eseményről, amit ' . $event['trainer'] . ' tart ' . $event['date'] . ' '
             . $event['start_time'] . ' -tól ' . $event['end_time'] . ' -ig';
            //var_dump($operation);
        }

        $queryString = 
            "INSERT INTO gilda_log(name, created_date, operation, user_id) 
                         VALUES(?, ?, ?, ?)";

        $stmt = $this->conn->prepare($queryString);
        $stmt->bind_param('sssi', $name, $created_date, $operation, $user_id);
        $stmt->execute();
        $stmt->close();
     }

     public function GetLogByPartnerId($partnerId) {
        $queryString = "SELECT * FROM gilda_log Where user_id = ?";
        $stmt = $this->conn->prepare($queryString);
        $stmt->bind_param("i", $partnerId);
        
        $stmt->execute();
        
        $result = array();
        $logs = array();
        
        $stmt->bind_result($id, $name, $created_date, $operation, $user_id);
        
        while($stmt->fetch()) {
            $tmp = array("id" => $id, 
                         "name" => $name,
                         "created_date" => $created_date,
                         "operation" => $operation,
                         "user_id" => $user_id);
            
            array_push($logs, $tmp);
        }
        
        $stmt->close();

        $result["logs"] = $logs;
        $result["userDetails"] = $this->GetUserDetailsById($partnerId);
        
        return $result;
     }
}

?>