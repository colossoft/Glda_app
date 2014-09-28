<?php

require_once '../include/DbHandler.php';
require_once '../include/PassHash.php';
require '../libs/Slim/Slim.php';
require '../libs/PHPMailer/PHPMailerAutoload.php';

\Slim\Slim::registerAutoloader();

$app = new \Slim\Slim();
//$app->add(new \Slim\Middleware\ContentTypes());
//$app->response()->header('Content-Type', 'application/json;charset=utf-8');

// Global variables
$user_id = NULL;
$mail_error_info = NULL;

/**
 * Verifying required params or not
 */
function verifyRequiredParams($required_fields) {
    $error = false;
    $error_fields = "";
    $request_params = array();
    $request_params = $_REQUEST;
    
    // Handling PUT request params
    if($_SERVER['REQUEST_METHOD'] == 'PUT') {
        $app = \Slim\Slim::getInstance();
        parse_str($app->request()->getBody(), $request_params);
    }
    
    foreach ($required_fields as $field) {
        if(!isset($request_params[$field]) || empty($request_params[$field])) {
            $error = true;
            $error_fields .= $field . ', ';
        }
    }
    
    if($error) {
        // Required field(s) are missing or empty
        // echo error json and stop the app
        $response = array();
        $app = \Slim\Slim::getInstance();
        $response["error"] = true;
        $response["message"] = 'Required field(s) ' . substr($error_fields, 0, -2) . ' is missing or empty';
        echoResponse(400, $response);
        $app->stop();
    }
}

/**
 * Validating e-mail address
 */
function validateEmail($email) {
    $app = \Slim\Slim::getInstance();
    if(!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $response["error"] = true;
        $response["message"] = 'Érvénytelen e-mail cím!';
        echoResponse(400, $response);
        $app->stop();
    }
}

/**
 * Echoing json response to client
 * @param Int $status_code Http response code
 * @param String $response Json response
 */
function echoResponse($status_code, $response) {
    $app = \Slim\Slim::getInstance();
    
    // Http response code
    $app->status($status_code);
    
    // Setting response content type to json
    $app->contentType('application/json;charset=utf-8');
    
    echo json_encode($response);
}

/**
 * PHPMailer function
 */
function sendMailToUser($first_name, $last_name, $email_address, $password) {
	global $mail_error_info;

	$mail = new PHPMailer;

	$mail->CharSet = "UTF-8";
	
	$mail->From = 'akos@atyin.url.ph';
	$mail->FromName = 'GildaMAX';
	$mail->addAddress($email_address);
    
	$mail->isHTML(true);

	$mail->Subject = 'GildaMAX regisztráció';
	$mail->Body    = 'Kedves ' . $last_name . ' ' . $first_name . '!<br /><br />Sikeresen regisztráltál a <b>GildaMAX</b> applikáció használatához a következő adatokkal:<br />' . 
						'E-mail: ' . $email_address . '<br />Jelszó: ' . $password . '<br /><br />Üdvözlettel: <b>GildaMAX</b>';
	$mail->AltBody = 'Kedves ' . $last_name . ' ' . $first_name . '! Sikeresen regisztráltál a GildaMAX applikáció használatához a következő adatokkal: ' . 
						'E-mail: ' . $email_address . ', Jelszó: ' . $password . '    Üdvözlettel: GildaMAX';

	if(!$mail->send()) {
		$mail_error_info = $mail->ErrorInfo;
		return false;
	} else {
		return true;
	}
}

/**
 * User registration
 * url: /register
 * method: POST
 * params: name, email, password
 */
$app->post('/register', function() use($app) {
    global $mail_error_info;
    
    // Check for required params
    verifyRequiredParams(array('first_name', 'last_name', 'email', 'password'));
    
    $response = array();
    
    // reading post parameters
    $first_name = $app->request->post('first_name');
    $last_name = $app->request->post('last_name');
    $email = $app->request->post('email');
    $password = $app->request->post('password');
    
    // validating e-mail address
    validateEmail($email);
    
    $db = new DbHandler();
    $res = $db->createUser($first_name, $last_name, $email, $password);
    
    if($res["status"] == USER_CREATED_SUCCESSFULLY) {
        if(sendMailToUser($first_name, $last_name, $email, $password)) {
            $response["mail_error"] = false;
        }
        else {
            $response["mail_error"] = true;
            $response["mail_error_message"] = $mail_error_info;
        }
	
        $response["error"] = false;
        $response["api_key"] = $res["api_key"];
        $response["message"] = "Sikeres regisztráció!";
        echoResponse(201, $response);
    }
    else if($res["status"] == USER_CREATE_FAILED) {
        $response["error"] = true;
        $response["message"] = "Hiba történt a regisztráció során. Kérjük próbáld újra!";
        echoResponse(500, $response);
    }
    else if($res["status"] = USER_ALREADY_EXISTED) {
        $response["error"] = true;
        $response["message"] = "Ezzel az e-mail címmel már regisztráltak!";
        echoResponse(501, $response);
    }
});

/**
 * User login
 * url: /login
 * method: POST
 * params: email, password
 */
$app->post('/login', function() use($app) {
    // Check for required params
    verifyRequiredParams(array('email', 'password'));
    
    // reading post params
    $email = $app->request()->post('email');
    $password = $app->request()->post('password');
    $response = array();
    
    $db = new DbHandler();
    // Check for correct email and password
    if($db->checkLogin($email, $password)) {
        // get the user by email
        $user = $db->getUserByEmail($email);
        
        if($user != NULL) {
            $response['error'] = false;
            $response['first_name'] = $user['first_name'];
            $response['last_name'] = $user['last_name'];
            $response['email'] = $user['email'];
            $response['api_key'] = $user['api_key'];
            $response['status'] = $user['status'];
            
            echoResponse(200, $response);
        } else {
            $response['error'] = true;
            $response['message'] = 'An error occured. Please try again!';
            echoResponse(409, $response);
        }
    } else {
        $response['error'] = true;
        $response['message'] = 'Login failed. Incorrect credentials!';
        echoResponse(409, $response);
    }
});

/**
 * Adding Middle Layer to authenticate every request
 * Checking if the request has valid api key in the 'Authorization' header
 */
function authenticate(\Slim\Route $route) {
    // Getting request headers
    $headers = apache_request_headers();
    $response = array();
    $app = \Slim\Slim::getInstance();
 
    // Verifying Authorization Header
    if (isset($headers['Authorization'])) {
        $db = new DbHandler();
 
        // get the api key
        $api_key = $headers['Authorization'];
        // validating api key
        if (!$db->isValidApiKey($api_key)) {
            // api key is not present in gilda_user table
            $response["error"] = true;
            $response["message"] = "Access Denied. Invalid Api key";
            echoResponse(401, $response);
            $app->stop();
        } else {
            global $user_id;
            // get user primary key id
            $user = $db->getUserId($api_key);
            if ($user != NULL)
                $user_id = $user;
        }
    } else {
        // api key is missing in header
        $response["error"] = true;
        $response["message"] = "Api key is misssing";
        echoResponse(400, $response);
        $app->stop();
    }
}

/**
 * Listing all locations
 * method GET
 * url /locations
 */
$app->get('/locations', 'authenticate', function() {
    $response = array();
    $db = new DbHandler();

    // fetching all locations
    $result = $db->getAllLocations();
    
    if($result != NULL) {
        $response["error"] = false;
        $response["locations"] = $result;
        echoResponse(200, $response);
    }else {
        $response["error"] = true;
        $response["message"] = "The requested resource doesn't exists";
        echoResponse(404, $response);
    }
});

/**
 * Listing rooms by location id
 * method GET
 * url /rooms/:id
 */
$app->get('/rooms/:id', 'authenticate', function($location_id) {
    $response = array();
    $db = new DbHandler();

    // fetch rooms
    $result = $db->getRoomsByLocationId($location_id);

    if($result != NULL) {
        $response["error"] = false;
        $response["rooms"] = $result;
        echoResponse(200, $response);

    } else {
        $response["error"] = true;
        $response["message"] = "The requested resource doesn't exists";
        echoResponse(404, $response);
    }
});

/**
 * Listing events by room_id
 * method GET
 * url /events/:room_id
 */
$app->get('/events/:room_id', 'authenticate', function($room_id) {
    global $user_id;
    
    $response = array();
    $db = new DbHandler();

    // fetch events
    $result = $db->getEventsByRoomId($room_id, $user_id);

    if(count($result) == 0 || $result != NULL) {
        $response["error"] = false;
        $response["events"] = $result;
        echoResponse(200, $response);

    } else {
        $response["error"] = true;
        $response["message"] = "The requested resource doesn't exists";
        echoResponse(404, $response);
    }
});

/**
 * Listing events by room_id and day
 * method GET
 * url /events/:room_id
 */
$app->get('/events/:room_id/:day', 'authenticate', function($room_id, $day) {
    global $user_id;
    $response = array();
    $db = new DbHandler();

    // fetch events
    $result = $db->getEventsByRoomIdAndDay($room_id, $user_id, $day);

    if(count($result) == 0 || $result != NULL) {
        $response["error"] = false;
        $response["events"] = $result;
        echoResponse(200, $response);

    } else {
        $response["error"] = true;
        $response["message"] = "Az események lekérése sikertelen!";
        echoResponse(404, $response);
    }
});

/**
 * Listing event and reservations of the event by event_id
 * method GET
 * url /events/:room_id
 */
$app->get('/event/:eventId', 'authenticate', function($eventId) {
    global $user_id;
    
    $response = array();
    $db = new DbHandler();

    // fetch events
    $result = $db->GetReservationOfEventByEventId($eventId, $user_id);

    if(count($result) == 0 || $result != NULL) {
        $response["error"] = false;
        $response["events"] = $result;
        echoResponse(200, $response);

    } else {
        $response["error"] = true;
        $response["message"] = "The requested resource doesn't exists";
        echoResponse(404, $response);
    }
});

/*
*Create event
*/
$app->post('/event/', 'authenticate', function() use($app) {
    global $user_id;

    // Check for required params
    verifyRequiredParams(array('roomId', 'date', 'startTime', 'endTime', 'trainerId', 'trainingId', 'spots'));
    
    // reading post params
    $roomId = $app->request()->post('roomId');
    $date = $app->request()->post('date');
    $startTime = $app->request()->post('startTime');
    $endTime = $app->request()->post('endTime');
    $trainerId = $app->request()->post('trainerId');
    $trainingId = $app->request()->post('trainingId');
    $spots = $app->request()->post('spots');
    
    $response = array();
    $db = new DbHandler();

    // fetch events
    $result = $db->CreateEvent($roomId, $date, $startTime, $endTime, $trainerId, $trainingId, $spots);

    if(!$result['errorList']) {
        $response["error"] = false;
        $response["message"] = "Sikeres eseménylétrehozás!";
        echoResponse(201, $response);
    }
    else {
        $response["error"] = true;
        $response["message"] = $result['errorList'];
        echoResponse(409, $response);
    }
});

/**
 * Make reservation
 * url: /reservation
 * method: POST
 * params: $event_id
 */
$app->post('/reservation', 'authenticate', function() use($app) {
    global $user_id;
    
    // Check for required params
    verifyRequiredParams(array('event_id'));
    
    $response = array();
    
    // reading post parameters
    $event_id = $app->request->post('event_id');
    
    $db = new DbHandler();
    $res = $db->createReservation($event_id, $user_id);
    
    if($res['status'] == RESERVATION_CREATED_SUCCESSFULLY) {
        $response["error"] = false;
        $response["message"] = "Sikeres foglalás!";
		$response["free_spots"] = $res['free_spots'];
        echoResponse(201, $response);
    }
    else if($res['status'] == RESERVATION_CREATE_FAILED) {
        $response["error"] = true;
        $response["message"] = "Hiba történt a foglalás során. Kérjük próbáld újra!";
		$response["free_spots"] = $res['free_spots'];
        echoResponse(500, $response);
    }
    else if($res['status'] = NO_FREE_SPOTS) {
        $response["error"] = true;
        $response["message"] = "Sajnos nincs több szabad hely erre az edzésre!";
		$response["free_spots"] = $res['free_spots'];
        echoResponse(400, $response);
    }
});

/**
 * Delete reservation
 * url: /reservation
 * method: DELETE
 * params: $event_id
 */
$app->delete('/reservation/:event_id', 'authenticate', function($event_id) use($app) {
    global $user_id;
    
    $response = array();
    $db = new DbHandler();

    // Delete reservation
    $result = $db->deleteReservation($event_id, $user_id);
    
    if($result['status']) {
        // Esetleg mail küldés?
        
        $response["error"] = false;
        $response["message"] = "Sikeresen törölted a foglalást!";
        $response["free_spots"] = $result['free_spots'];
        echoResponse(200, $response);
    }
    else {
        $response["error"] = true;
        $response["message"] = "A foglalás törlése sikertelen! Kérjük próbáld újra!";
        $response["free_spots"] = $result['free_spots'];
        echoResponse(405, $response);
    }
});

$app->get('/trainings', 'authenticate', function() {
    $response = array();
    $db = new DbHandler();

    // fetch rooms
    $result = $db->GetAllTrainings();

    if($result != NULL) {
        $response["error"] = false;
        $response["trainings"] = $result;
        echoResponse(200, $response);

    } else {
        $response["error"] = true;
        $response["message"] = "The requested resource doesn't exists";
        echoResponse(404, $response);
    }
});

$app->get('/trainers', 'authenticate', function() {
    $response = array();
    $db = new DbHandler();

    // fetch rooms
    $result = $db->GetAllTrainers();

    if($result != NULL) {
        $response["error"] = false;
        $response["trainers"] = $result;
        echoResponse(200, $response);

    } else {
        $response["error"] = true;
        $response["message"] = "The requested resource doesn't exists";
        echoResponse(404, $response);
    }
});

/*
*Create news
*/
$app->post('/createNews/', 'authenticate', function() use($app) {
    global $user_id;

    // Check for required params
    verifyRequiredParams(array('created_date', 'news'));

    // reading post params
    $created_date = $app->request()->post('created_date');
    $news = $app->request()->post('news');
    
    $response = array();
    $db = new DbHandler();

    // fetch events
    $result = $db->CreateNews($created_date, $news);

    if($result == '') {
        $response["error"] = false;
        $response["message"] = "Sikeres mentés!";
        echoResponse(201, $response);
    }
    else {
        $response["error"] = true;
        $response["message"] = $result;
        echoResponse(500, $response);
    }
});

/*
*Create news
*/
$app->post('/createDevaluation/', 'authenticate', function() use($app) {
    global $user_id;

    // Check for required params
    verifyRequiredParams(array('start_date', 'end_date', 'devaluation'));

    // reading post params
    $start_date = $app->request()->post('start_date');
    $end_date = $app->request()->post('end_date');
    $devaluation = $app->request()->post('devaluation');
    
    $response = array();
    $db = new DbHandler();

    // fetch events
    $result = $db->CreateDevaluation($start_date, $end_date, $devaluation);

    if($result == '') {
        $response["error"] = false;
        $response["message"] = "Sikeres mentés!";
        echoResponse(201, $response);
    }
    else {
        $response["error"] = true;
        $response["message"] = $result;
        echoResponse(500, $response);
    }
});

$app->run();

?>