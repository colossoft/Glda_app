<?php

require_once '../include/DbHandler.php';
require_once '../include/PassHash.php';
require_once '../include/LangHuStrings.php';
require_once '../include/LangEnStrings.php';
require_once '../include/LangDeStrings.php';
require '../libs/Slim/Slim.php';
require '../libs/PHPMailer/PHPMailerAutoload.php';

\Slim\Slim::registerAutoloader();

$app = new \Slim\Slim();
//$app->add(new \Slim\Middleware\ContentTypes());
//$app->response()->header('Content-Type', 'application/json;charset=utf-8');

// Global variables
$user_id = NULL;
$mail_error_info = NULL;
$responseStrings = $responseLangHu;

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
    global $responseStrings;

    $app = \Slim\Slim::getInstance();
    if(!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $response["error"] = true;
        $response["message"] = $responseStrings["registrationInvalidEmail"];
        echoResponse(400, $response);
        $app->stop();
    }
}

/**
 * Validating password
 */
function validatePassword($password) {
    global $responseStrings;

    $app = \Slim\Slim::getInstance();

    $errorList = "";

    if(strlen($password) < 8) {
        $errorList .= $responseStrings["registrationEmailValidMessageCharNum"] . "\n";
    }
    if(!preg_match("#[0-9]+#", $password)) {
        $errorList .= $responseStrings["registrationEmailValidMessageNumber"] . "\n";
    }
    if(!preg_match("#[A-Z]+#", $password)) {
        $errorList .= $responseStrings["registrationEmailValidMessageBigLetter"] . "\n";
    }
    if(!preg_match("#[a-z]+#", $password)) {
        $errorList .= $responseStrings["registrationEmailValidMessageSmallLetter"];
    }

    if(strlen($errorList) != 0) {
        $response["error"] = true;
        $response["message"] = $errorList;
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
 * PHPMailer function for new password
 */
function SendMailToUserForNewPassword($first_name, $last_name, $email_address, $newPassword) {
    global $mail_error_info;

    $mail = new PHPMailer;

    $mail->CharSet = "UTF-8";
    
    $mail->From = 'akos@atyin.url.ph';
    $mail->FromName = 'GildaMAX';
    $mail->addAddress($email_address);
    
    $mail->isHTML(true);

    $mail->Subject = 'GildaMAX új jelszó értesítő';
    $mail->Body    = 'Kedves ' . $last_name . ' ' . $first_name . '!<br /><br />A <b>GildaMAX</b> elkészítette új jelszavát:<br />' . 
                        'Jelszó: ' . '<b>' . $newPassword . '</b><br />' . 'Üdvözlettel: GildaMAX';

    if(!$mail->send()) {
        $mail_error_info = $mail->ErrorInfo;
        return false;
    } else {
        return true;
    }
}

function sendMailToPartnerForReservation($eventDetails, $userDetails) {
    global $mail_error_info;

    $mail = new PHPMailer;

    $mail->CharSet = "UTF-8";
    
    $mail->From = 'akos@atyin.url.ph';
    $mail->FromName = 'GildaMAX';
    $mail->addAddress($userDetails["email"]);
    
    $mail->isHTML(true);

    $mail->Subject = 'GildaMAX foglalás';
    $mail->Body    = 'Kedves ' . $userDetails["lastName"] . ' ' . $userDetails["firstName"] . '!<br /><br />A <b>GildaMAX</b> rögzítette a foglalását a következő eseményre:<br />' . 
                        'Terem: ' . $eventDetails["locationName"] . ' - ' . $eventDetails["locationAddress"] . '<br />' . 
                        'Szoba: ' . $eventDetails["roomName"] .  '<br />' . 
                        'Dátum: ' . $eventDetails["eventDate"] .  '<br />' .
                        'Óra kezdete: ' . $eventDetails["eventStartTime"] . '<br />' . 
                        'Óra vége: ' . $eventDetails["eventEndTime"] . '<br />' . 
                        'Edző neve: ' . $eventDetails["trainerName"] . '<br />' . 
                        'Edzés típusa: ' . $eventDetails["trainingName"] . '<br /><br />'
                        . 'Üdvözlettel: GildaMAX';

    if(!$mail->send()) {
        $mail_error_info = $mail->ErrorInfo;
        return false;
    } else {
        return true;
    }
}

function sendMailToPartnerForDeleteReservation($eventDetails, $userDetails) {
    global $mail_error_info;

    $mail = new PHPMailer;

    $mail->CharSet = "UTF-8";
    
    $mail->From = 'akos@atyin.url.ph';
    $mail->FromName = 'GildaMAX';
    $mail->addAddress($userDetails["email"]);
    
    $mail->isHTML(true);

    $mail->Subject = 'GildaMAX foglalás törlése';
    $mail->Body    = 'Kedves ' . $userDetails["lastName"] . ' ' . $userDetails["firstName"] . '!<br /><br />A <b>GildaMAX</b> törölte a foglalását a következő eseményről:<br />' . 
                        'Terem: ' . $eventDetails["locationName"] . ' - ' . $eventDetails["locationAddress"] . '<br />' . 
                        'Szoba: ' . $eventDetails["roomName"] .  '<br />' . 
                        'Dátum: ' . $eventDetails["eventDate"] .  '<br />' .
                        'Óra kezdete: ' . $eventDetails["eventStartTime"] . '<br />' . 
                        'Óra vége: ' . $eventDetails["eventEndTime"] . '<br />' . 
                        'Edző neve: ' . $eventDetails["trainerName"] . '<br />' . 
                        'Edzés típusa: ' . $eventDetails["trainingName"] . '<br /><br />'
                        . 'Üdvözlettel: GildaMAX';

    if(!$mail->send()) {
        $mail_error_info = $mail->ErrorInfo;
        return false;
    } else {
        return true;
    }
}

function sendMailToUserForPartnerReservation($eventDetails, $userDetails, $partnerDetails) {
    global $mail_error_info;

    $mail = new PHPMailer;

    $mail->CharSet = "UTF-8";
    
    $mail->From = 'akos@atyin.url.ph';
    $mail->FromName = 'GildaMAX';
    $mail->addAddress($userDetails["email"]);
    
    $mail->isHTML(true);

    $mail->Subject = 'GildaMAX foglalás';
    $mail->Body    = 'Kedves ' . $userDetails["lastName"] . ' ' . $userDetails["firstName"] . '!<br /><br />A <b>GildaMAX</b> rögzítette a foglalását a következő eseményre:<br />' . 
                        'Terem: ' . $eventDetails["locationName"] . ' - ' . $eventDetails["locationAddress"] . '<br />' . 
                        'Szoba: ' . $eventDetails["roomName"] .  '<br />' . 
                        'Dátum: ' . $eventDetails["eventDate"] .  '<br />' .
                        'Óra kezdete: ' . $eventDetails["eventStartTime"] . '<br />' . 
                        'Óra vége: ' . $eventDetails["eventEndTime"] . '<br />' . 
                        'Edző neve: ' . $eventDetails["trainerName"] . '<br />' . 
                        'Edzés típusa: ' . $eventDetails["trainingName"] . '<br /><br />' . 
                        'a következő regisztrált partner számára: ' . '<br />' . 
                        'Partner neve: ' . $partnerDetails["lastName"] . ' ' . $partnerDetails["firstName"] . '<br />' . 
                        'Partner e-mail címe: ' . $partnerDetails["email"] . '<br /><br />'
                        . 'Üdvözlettel: GildaMAX';

    if(!$mail->send()) {
        $mail_error_info = $mail->ErrorInfo;
        return false;
    } else {
        return true;
    }
}

function sendMailToUserForDeletePartnerReservation($eventDetails, $userDetails, $partnerDetails) {
    global $mail_error_info;

    $mail = new PHPMailer;

    $mail->CharSet = "UTF-8";
    
    $mail->From = 'akos@atyin.url.ph';
    $mail->FromName = 'GildaMAX';
    $mail->addAddress($userDetails["email"]);
    
    $mail->isHTML(true);

    $mail->Subject = 'GildaMAX foglalás törlése';
    $mail->Body    = 'Kedves ' . $userDetails["lastName"] . ' ' . $userDetails["firstName"] . '!<br /><br />A <b>GildaMAX</b> törölte a foglalását a következő eseményről:<br />' . 
                        'Terem: ' . $eventDetails["locationName"] . ' - ' . $eventDetails["locationAddress"] . '<br />' . 
                        'Szoba: ' . $eventDetails["roomName"] .  '<br />' . 
                        'Dátum: ' . $eventDetails["eventDate"] .  '<br />' .
                        'Óra kezdete: ' . $eventDetails["eventStartTime"] . '<br />' . 
                        'Óra vége: ' . $eventDetails["eventEndTime"] . '<br />' . 
                        'Edző neve: ' . $eventDetails["trainerName"] . '<br />' . 
                        'Edzés típusa: ' . $eventDetails["trainingName"] . '<br /><br />' . 
                        'a következő regisztrált partner számára: ' . '<br />' . 
                        'Partner neve: ' . $partnerDetails["lastName"] . ' ' . $partnerDetails["firstName"] . '<br />' . 
                        'Partner e-mail címe: ' . $partnerDetails["email"] . '<br /><br />'
                        . 'Üdvözlettel: GildaMAX';

    if(!$mail->send()) {
        $mail_error_info = $mail->ErrorInfo;
        return false;
    } else {
        return true;
    }
}

function sendMailToUserForCustomerReservation($eventDetails, $userDetails, $customerDetails) {
    global $mail_error_info;

    $mail = new PHPMailer;

    $mail->CharSet = "UTF-8";
    
    $mail->From = 'akos@atyin.url.ph';
    $mail->FromName = 'GildaMAX';
    $mail->addAddress($userDetails["email"]);
    
    $mail->isHTML(true);

    $mail->Subject = 'GildaMAX foglalás';
    $mail->Body    = 'Kedves ' . $userDetails["lastName"] . ' ' . $userDetails["firstName"] . '!<br /><br />A <b>GildaMAX</b> rögzítette a foglalását a következő eseményre:<br />' . 
                        'Terem: ' . $eventDetails["locationName"] . ' - ' . $eventDetails["locationAddress"] . '<br />' . 
                        'Szoba: ' . $eventDetails["roomName"] .  '<br />' . 
                        'Dátum: ' . $eventDetails["eventDate"] .  '<br />' .
                        'Óra kezdete: ' . $eventDetails["eventStartTime"] . '<br />' . 
                        'Óra vége: ' . $eventDetails["eventEndTime"] . '<br />' . 
                        'Edző neve: ' . $eventDetails["trainerName"] . '<br />' . 
                        'Edzés típusa: ' . $eventDetails["trainingName"] . '<br /><br />' . 
                        'a következő nem regisztrált ügyfél számára: ' . '<br />' . 
                        'Ügyfél neve: ' . $customerDetails["name"] . '<br />' . 
                        'Ügyfél adatai: ' . $customerDetails["details"] . '<br /><br />'
                        . 'Üdvözlettel: GildaMAX';

    if(!$mail->send()) {
        $mail_error_info = $mail->ErrorInfo;
        return false;
    } else {
        return true;
    }
}

function sendMailToUserForDeleteCustomerReservation($eventDetails, $userDetails, $customerDetails) {
    global $mail_error_info;

    $mail = new PHPMailer;

    $mail->CharSet = "UTF-8";
    
    $mail->From = 'akos@atyin.url.ph';
    $mail->FromName = 'GildaMAX';
    $mail->addAddress($userDetails["email"]);
    
    $mail->isHTML(true);

    $mail->Subject = 'GildaMAX foglalás törlése';
    $mail->Body    = 'Kedves ' . $userDetails["lastName"] . ' ' . $userDetails["firstName"] . '!<br /><br />A <b>GildaMAX</b> törölte a foglalását a következő eseményről:<br />' . 
                        'Terem: ' . $eventDetails["locationName"] . ' - ' . $eventDetails["locationAddress"] . '<br />' . 
                        'Szoba: ' . $eventDetails["roomName"] .  '<br />' . 
                        'Dátum: ' . $eventDetails["eventDate"] .  '<br />' .
                        'Óra kezdete: ' . $eventDetails["eventStartTime"] . '<br />' . 
                        'Óra vége: ' . $eventDetails["eventEndTime"] . '<br />' . 
                        'Edző neve: ' . $eventDetails["trainerName"] . '<br />' . 
                        'Edzés típusa: ' . $eventDetails["trainingName"] . '<br /><br />' . 
                        'a következő nem regisztrált ügyfél számára: ' . '<br />' . 
                        'Ügyfél neve: ' . $customerDetails["name"] . '<br />' . 
                        'Ügyfél adatai: ' . $customerDetails["details"] . '<br /><br />'
                        . 'Üdvözlettel: GildaMAX';

    if(!$mail->send()) {
        $mail_error_info = $mail->ErrorInfo;
        return false;
    } else {
        return true;
    }
}

/*
*Radom password generator
*/
function randomPassword() {
    $alphabet = "abcdefghijklmnopqrstuwxyzABCDEFGHIJKLMNOPQRSTUWXYZ0123456789";
    $pass = array(); //remember to declare $pass as an array
    $alphaLength = strlen($alphabet) - 1; //put the length -1 in cache
    for ($i = 0; $i < 8; $i++) {
        $n = rand(0, $alphaLength);
        $pass[] = $alphabet[$n];
    }

    return implode($pass); //turn the array into a string
}

/**
 * User registration
 * url: /register
 * method: POST
 * params: name, email, password
 */
$app->post('/register', 'language', function() use($app) {
    global $mail_error_info;
    global $responseStrings;
    
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

    // validating password
    validatePassword($password);
    
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
        $response["message"] = $responseStrings["registrationSuccessfully"];
        echoResponse(201, $response);
    }
    else if($res["status"] == USER_CREATE_FAILED) {
        $response["error"] = true;
        $response["message"] = $responseStrings["registrationFailed"];
        echoResponse(500, $response);
    }
    else if($res["status"] == USER_ALREADY_EXISTED) {
        $response["error"] = true;
        $response["message"] = $responseStrings["registrationEmailExists"];
        echoResponse(501, $response);
    }
});

/**
 * User login
 * url: /login
 * method: POST
 * params: email, password
 */
$app->post('/login', 'language',  function() use($app) {
    global $responseStrings;

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
            $response['message'] = $responseStrings["ErrorUnexpected"];
            echoResponse(409, $response);
        }
    } else {
        $response['error'] = true;
        $response['message'] = $responseStrings["loginErrorWrongCredentials"];
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
            $response["message"] = "Hozzáférés megtagadva! A felhasználót nem lehet azonosítani!";
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
        $response["message"] = "Api key is missing!";
        echoResponse(400, $response);
        $app->stop();
    }
}

function language(\Slim\Route $route) {
    global $responseStrings;
    global $responseLangHu;
    global $responseLangDe;
    global $responseLangEn;

    $headers = apache_request_headers();
 
    // Verifying Authorization Header
    if (isset($headers['Accept-Language'])) {
        // get the language
        $lang = $headers['Accept-Language'];
        
        switch ($lang) {
            case 'hu':
                $responseStrings = $responseLangHu;
                break;
            
            case 'en':
                $responseStrings = $responseLangEn;
                break;

            case 'de':
                $responseStrings = $responseLangDe;
                break;
            default:
                $responseStrings = $responseLangHu;
                break;
        }
    } else {
        $responseStrings = $responseLangHu;
    }
}

/**
 * Listing all locations
 * method GET
 * url /locations
 */
$app->get('/locations', 'authenticate', 'language', function() {
    global $responseStrings;

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
        $response["message"] = $responseStrings["ErrorUnexpected"];
        echoResponse(404, $response);
    }
});

/**
 * Listing rooms by location id
 * method GET
 * url /rooms/:id
 */
$app->get('/rooms/:id', 'authenticate', 'language', function($location_id) {
    global $responseStrings;

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
        $response["message"] = $responseStrings["ErrorUnexpected"];
        echoResponse(404, $response);
    }
});

$app->post('/rooms', 'authenticate', function() use($app){
    // Check for required params
    verifyRequiredParams(array('name', 'locationId'));
    
    // reading post params
    $name = $app->request()->post('name');
    $locationId = $app->request()->post('locationId');
    
    $response = array();
    $db = new DbHandler();

    // fetch events
    $result = $db->CreateRoom($name, $locationId);

    if(is_null($result)) {
        $response["error"] = true;
        $response["message"] = "Sajnos nem sikerült létrehozni a megadott termet!";
        echoResponse(500, $response);
    }
    else if($result == false) {
        $response["error"] = true;
        $response["message"] = "Ilyen nevű terem már létezik!";
        echoResponse(405, $response);
    }
    else if($result == true) {
        $response["error"] = false;
        $response["message"] = "Sikeres mentés!";
        echoResponse(201, $response);
    }
});

$app->delete('/rooms/:roomId', 'authenticate', function($roomId) use($app){
    
    $response = array();
    $db = new DbHandler();

    $result = $db->DeleteRoom($roomId);

    if($result) {
        $response["error"] = false;
        $response["message"] = "Sikeres törlés!";
        echoResponse(201, $response);
    }
    else {
        $response["error"] = true;
        $response["message"] = "A terem törlése nem lehetséges, mert eseményekhez van hozzárendelve!";
        echoResponse(405, $response);
    }
});

/**
 * Listing events by room_id
 * method GET
 * url /events/:room_id
 */
$app->get('/events/:room_id', 'authenticate', 'language', function($room_id) {
    global $user_id;
    global $responseStrings;

    $response = array();
    $db = new DbHandler();

    // fetch events
    $result = $db->getEventsByRoomId($room_id, $user_id);

    if(!is_null($result)) {
        $response["error"] = false;
        $response["events"] = $result;
        echoResponse(200, $response);

    } else {
        $response["error"] = true;
        $response["message"] = $responseStrings["ErrorUnexpected"];
        echoResponse(404, $response);
    }
});

/**
 * Listing events by room_id and day
 * method GET
 * url /events/:room_id
 */
$app->get('/events/:room_id/:startDay/:endDay', 'authenticate', function($room_id, $startDay, $endDay) {
    global $user_id;

    $response = array();
    $db = new DbHandler();

    // fetch events
    $result = $db->getEventsByRoomIdAndDay($user_id, $room_id, $startDay, $endDay);

    if(!is_null($result)) {
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
    
    $response = array();
    $db = new DbHandler();

    // fetch events
    $result = $db->GetReservationsOfEventByEventId($eventId);

    if(!is_null($result)) {
        $response["error"] = false;
        $response["eventDetails"] = $result;
        echoResponse(200, $response);

    } else {
        $response["error"] = true;
        $response["message"] = "The requested resource doesn't exists";
        echoResponse(404, $response);
    }
});

/**
 * Listing event and datas for reservation
 * method GET
 * url /event/book/:eventId
 */
$app->get('/event/book/:eventId', 'authenticate', function($eventId) {
    
    $response = array();
    $db = new DbHandler();

    // fetch datas
    $result = $db->GetDatasForEventReservation($eventId);

    if(!is_null($result)) {
        $response["error"] = false;
        $response["eventDetails"] = $result;
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
 * Get reservations
 * url: /reservation
 * method: GET
 */
$app->get('/reservation', 'authenticate', 'language', function() {
    global $user_id;
    global $responseStrings;

    $response = array();
    $db = new DbHandler();

    // fetch events
    $result = $db->GetReservationsOfUser($user_id);

    if(!is_null($result)) {
        $response["error"] = false;
        $response["reservations"] = $result;
        echoResponse(200, $response);

    } else {
        $response["error"] = true;
        $response["message"] = $responseStrings["ErrorUnexpected"];
        echoResponse(404, $response);
    }
});

/**
 * Make reservation
 * url: /reservation
 * method: POST
 * params: $event_id
 */
$app->post('/reservation/', 'authenticate', 'language', function() use($app) {
    global $user_id;
    global $responseStrings;
    
    // Check for required params
    verifyRequiredParams(array('event_id'));
    
    $response = array();
    
    // reading post parameters
    $event_id = $app->request->post('event_id');
    
    $db = new DbHandler();
    $res = $db->createReservation($event_id, $user_id);
    
    if($res['status'] == RESERVATION_CREATED_SUCCESSFULLY) {
        $eventDetails = $db->getEventDetailsById($event_id);
        $userDetails = $db->GetUserDetailsById($user_id);
        sendMailToPartnerForReservation($eventDetails, $userDetails);

        $response["error"] = false;
        $response["message"] = $responseStrings["makeReservationSuccessfully"];
		$response["free_spots"] = $res['free_spots'];
        echoResponse(201, $response);
    }
    else if($res['status'] == RESERVATION_CREATE_FAILED) {
        $response["error"] = true;
        $response["message"] = $responseStrings["makeReservationError"];
		$response["free_spots"] = $res['free_spots'];
        echoResponse(500, $response);
    }
    else if($res['status'] == RESERVATION_CREATE_DEADLINE_EXPIRED) {
        $response["error"] = true;
        $response["message"] = $responseStrings["makeReservationExpired"];
        $response["free_spots"] = $res['free_spots'];
        echoResponse(405, $response);
    }
    else if($res['status'] == NO_FREE_SPOTS) {
        $response["error"] = true;
        $response["message"] = $responseStrings["makeReservationNoFreeSpots"];
		$response["free_spots"] = $res['free_spots'];
        echoResponse(405, $response);
    }
});

/**
 * Booking for partner
 * url: /partnerbook
 * method: POST
 * params: $event_id, $partner_id, $comment
 */
$app->post('/partnerbook', 'authenticate', function() use($app) {
    global $user_id;
    
    // Check for required params
    verifyRequiredParams(array('event_id', 'partner_id'));
    
    $response = array();
    
    // reading post parameters
    $event_id = $app->request->post('event_id');
    $partner_id = $app->request->post('partner_id');
    $comment = $app->request->post('comment');
    
    $db = new DbHandler();
    $res = $db->createReservationForPartner($event_id, $user_id, $partner_id, $comment);
    
    if($res == RESERVATION_CREATED_SUCCESSFULLY) {
        $eventDetails = $db->getEventDetailsById($event_id);
        $partnerDetails = $db->GetUserDetailsById($partner_id);
        $userDetails = $db->GetUserDetailsById($user_id);

        sendMailToPartnerForReservation($eventDetails, $partnerDetails);
        sendMailToUserForPartnerReservation($eventDetails, $userDetails, $partnerDetails);

        $response["error"] = false;
        $response["message"] = "Sikeres foglalás!";
        echoResponse(201, $response);
    }
    else if($res == RESERVATION_CREATE_FAILED) {
        $response["error"] = true;
        $response["message"] = "Hiba történt a foglalás során. Kérjük próbáld újra!";
        echoResponse(500, $response);
    }
    else if($res == ALREADY_RESERVED) {
        $response["error"] = true;
        $response["message"] = "Erre az eseményre már van foglalva hely a partner számára!";
        echoResponse(405, $response);
    }
});

/**
 * Checking existing customer
 * url: /checkcustomer
 * method: GET
 * params: $customerName
 */
$app->get('/checkcustomer/:name', 'authenticate', function($name) {
    $response = array();
    
    $db = new DbHandler();
    $res = $db->checkExistingCustomer($name);
    
    if($res == 0) {
        $response["error"] = false;
        echoResponse(200, $response);
    }
    else if($res > 0) {
        $response["error"] = true;
        $response["message"] = 'Már van ' . $res . ' ilyen nevű ügyfél a rendszerben! Biztos benne, hogy folytatja a foglalást? (Az új felhasználó rögzítve lesz!)';
        echoResponse(200, $response);
    }
    else {
        $response["error"] = true;
        $response["message"] = 'Hiba történt a lekérdezés során!';
        echoResponse(500, $response);
    }
});

/**
 * Booking for new customer
 * url: /customerbook/new
 * method: POST
 * params: $eventId, $customerName, $customerDetails, $comment
 */
$app->post('/customerbook/new', 'authenticate', function() use($app) {
    global $user_id;
    
    // Check for required params
    verifyRequiredParams(array('eventId', 'customerName', 'customerDetails'));
    
    $response = array();
    
    // reading post parameters
    $eventId = $app->request->post('eventId');
    $customerName = $app->request->post('customerName');
    $customerDetails = $app->request->post('customerDetails');
    $comment = $app->request->post('comment');
    
    $db = new DbHandler();
    $res = $db->createReservationForNewCustomer($eventId, $user_id, $customerName, $customerDetails, $comment);
    
    if($res["status"] == RESERVATION_CREATED_SUCCESSFULLY) {
        $eventDetails = $db->getEventDetailsById($eventId);
        $customerDetails = $db->GetCustomerDetailsById($res["customerId"]);
        $userDetails = $db->GetUserDetailsById($user_id);

        sendMailToUserForCustomerReservation($eventDetails, $userDetails, $customerDetails);

        $response["error"] = false;
        $response["message"] = "Sikeres foglalás!";
        echoResponse(201, $response);
    }
    else if($res["status"] == RESERVATION_CREATE_FAILED) {
        $response["error"] = true;
        $response["message"] = "Hiba történt a foglalás során. Kérjük próbáld újra!";
        echoResponse(500, $response);
    }
});

/**
 * Booking for existing customer
 * url: /customerbook/existing
 * method: POST
 * params: $eventId, $customerId, $customerDetails, $comment
 */
$app->post('/customerbook/existing', 'authenticate', function() use($app) {
    global $user_id;
    
    // Check for required params
    verifyRequiredParams(array('eventId', 'customerId', 'customerDetails'));
    
    $response = array();
    
    // reading post parameters
    $eventId = $app->request->post('eventId');
    $customerId = $app->request->post('customerId');
    $customerDetails = $app->request->post('customerDetails');
    $comment = $app->request->post('comment');
    
    $db = new DbHandler();
    $res = $db->createReservationForCustomer($eventId, $user_id, $customerId, $customerDetails, $comment);
    
    if($res == RESERVATION_CREATED_SUCCESSFULLY) {
        $eventDetails = $db->getEventDetailsById($eventId);
        $customerDetails = $db->GetCustomerDetailsById($customerId);
        $userDetails = $db->GetUserDetailsById($user_id);

        sendMailToUserForCustomerReservation($eventDetails, $userDetails, $customerDetails);

        $response["error"] = false;
        $response["message"] = "Sikeres foglalás!";
        echoResponse(201, $response);
    }
    else if($res == RESERVATION_CREATE_FAILED) {
        $response["error"] = true;
        $response["message"] = "Hiba történt a foglalás során. Kérjük próbáld újra!";
        echoResponse(500, $response);
    }
    else if($res == ALREADY_RESERVED) {
        $response["error"] = true;
        $response["message"] = "Erre az eseményre már van foglalva hely az ügyfél számára!";
        echoResponse(405, $response);
    }
});

/**
 * Delete reservation
 * url: /reservation
 * method: DELETE
 * params: $event_id
 */
$app->delete('/reservation/:event_id', 'authenticate', 'language', function($event_id) use($app) {
    global $user_id;
    global $responseStrings;
    
    $response = array();
    $db = new DbHandler();

    // Delete reservation
    $result = $db->deleteReservation($event_id, $user_id);
    
    if($result['status'] == RESERVATION_DELETED_SUCCESSFULLY) {
        $eventDetails = $db->getEventDetailsById($event_id);
        $userDetails = $db->GetUserDetailsById($user_id);
        sendMailToPartnerForDeleteReservation($eventDetails, $userDetails);
        
        $response["error"] = false;
        $response["message"] = $responseStrings["deleteReservationSuccessfully"];
        $response["free_spots"] = $result['free_spots'];
        echoResponse(200, $response);
    }
    else if($result['status'] == RESERVATION_DELETE_FAILED) {
        $response["error"] = true;
        $response["message"] = $responseStrings["deleteReservationFailed"];
        $response["free_spots"] = $result['free_spots'];
        echoResponse(500, $response);
    }
    else if($result['status'] == RESERVATION_DELETE_DEADLINE_EXPIRED) {
        $response["error"] = true;
        $response["message"] = $responseStrings["deleteReservationExpired"];
        $response["free_spots"] = $result['free_spots'];
        echoResponse(405, $response);   
    }
});

/**
 * Delete reservation by user
 * url: /reservation/user
 * method: DELETE
 * params: $reservation_id, $event_id
 */
$app->delete('/reservation/user/:reservation_id/:event_id/:client_id/:client_type', 'authenticate', function($reservation_id, $event_id, $client_id, $client_type) use($app) {
    global $user_id;

    $response = array();
    $db = new DbHandler();

    // Delete reservation
    $result = $db->deleteReservationByUser($reservation_id, $event_id);
    
    if($result == RESERVATION_DELETED_SUCCESSFULLY) {
        $eventDetails = $db->getEventDetailsById($event_id);
        $userDetails = $db->GetUserDetailsById($user_id);

        if($client_type == 1) { // Ez partner
            $partnerDetails = $db->GetUserDetailsById($client_id);
            sendMailToPartnerForDeleteReservation($eventDetails, $partnerDetails);
            sendMailToUserForDeletePartnerReservation($eventDetails, $userDetails, $partnerDetails);
        }
        else if($client_type == 2) { // Ez ügyfél
            $customerDetails = $db->GetCustomerDetailsById($client_id);
            sendMailToUserForDeleteCustomerReservation($eventDetails, $userDetails, $customerDetails);
        }
        
        $response["error"] = false;
        $response["message"] = "Sikeresen törölted a foglalást!";
        echoResponse(200, $response);
    }
    else if($result == RESERVATION_DELETE_FAILED) {
        $response["error"] = true;
        $response["message"] = "A foglalás törlése sikertelen! Kérjük próbáld újra!";
        echoResponse(500, $response);
    }
    else if($result == RESERVATION_DELETE_DEADLINE_EXPIRED) {
        $response["error"] = true;
        $response["message"] = "A foglalás törlésének határideje lejárt!";
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

$app->post('/training', 'authenticate', function() use($app){
    // Check for required params
    verifyRequiredParams(array('name'));
    
    // reading post params
    $name = $app->request()->post('name');
    
    $response = array();
    $db = new DbHandler();

    // fetch events
    $result = $db->CreateTraining($name);

    if(is_null($result)) {
        $response["error"] = true;
        $response["message"] = "Sajnos nem sikerült létrehozni a megadott edzéstípust!";
        echoResponse(500, $response);
        
    }
    else if($result == false) {
        $response["error"] = true;
        $response["message"] = "Ilyen nevű edzés már létezik!";
        echoResponse(405, $response);
    }
    else if($result == true) {
        $response["error"] = false;
        $response["message"] = "Sikeres mentés!";
        echoResponse(201, $response);
    }
});

$app->delete('/training/:trainingId', 'authenticate', function($trainingId) use($app){
    
    $response = array();
    $db = new DbHandler();

    $result = $db->DeleteTraining($trainingId);

    if($result) {
        $response["error"] = false;
        $response["message"] = "Sikeres törlés!";
        echoResponse(201, $response);
    }
    else {
        $response["error"] = true;
        $response["message"] = "Az edzéstípus törlése nem lehetséges, mert eseményekhez van hozzárendelve!";
        echoResponse(405, $response);
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
        echoResponse(500, $response);
    }
});

$app->post('/trainer/', 'authenticate', function() use($app) {
    // Check for required params
    verifyRequiredParams(array('lastName', 'firstName', 'email'));
    
    // reading post params
    $last_name = $app->request()->post('lastName');
    $first_name = $app->request()->post('firstName');
    $email = $app->request()->post('email');
    
    $response = array();
    $db = new DbHandler();

    // fetch events
    $result = $db->CreateTrainer($first_name, $last_name, $email);

    if(is_null($result)) {
        $response["error"] = true;
        $response["message"] = "Sajnos nem sikerült létrehozni az edzőt!";
        echoResponse(500, $response);
    }
    else if($result == false) {
        $response["error"] = true;
        $response["message"] = "Ezzel az e-mail címmel már regisztráltak edzőt!";
        echoResponse(405, $response);
    }
    else if($result == true) {
        $response["error"] = false;
        $response["message"] = "Sikeres mentés!";
        echoResponse(201, $response);
    }
});

$app->delete('/trainer/:trainerId', 'authenticate', function($trainerId) use($app){
    
    $response = array();
    $db = new DbHandler();

    $result = $db->DeleteTrainer($trainerId);

    if($result) {
        $response["error"] = false;
        $response["message"] = "Sikeres törlés!";
        echoResponse(201, $response);
    }
    else {
        $response["error"] = true;
        $response["message"] = "Az edző törlése nem lehetséges, mert eseményekhez van hozzárendelve!";
        echoResponse(405, $response);
    }
});

/*
** Get All Languages
 */
$app->get('/languages', 'authenticate', function() {
    $response = array();
    $db = new DbHandler();

    // fetch rooms
    $result = $db->GetAllLanguages();

    if($result != NULL) {
        $response["error"] = false;
        $response["languages"] = $result;
        echoResponse(200, $response);

    } else {
        $response["error"] = true;
        $response["message"] = "The requested resource doesn't exists";
        echoResponse(500, $response);
    }
});

/*
** Get All News
 */
$app->get('/news/:languageId', 'authenticate', function($languageId) {
    $response = array();
    $db = new DbHandler();

    // fetch rooms
    $result = $db->GetAllNews($languageId);

    if(!is_null($result)) {
        $response["error"] = false;
        $response["news"] = $result;
        echoResponse(200, $response);

    } else {
        $response["error"] = true;
        $response["message"] = "The requested resource doesn't exists";
        echoResponse(500, $response);
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
** Delete News By newsId
 */
$app->delete('/news/:newsId', 'authenticate', function($newsId) use($app){
    
    $response = array();
    $db = new DbHandler();

    $result = $db->DeleteNews($newsId);

    if($result) {
        $response["error"] = false;
        $response["message"] = "Sikeres törlés!";
        echoResponse(201, $response);
    }
    else {
        $response["error"] = true;
        $response["message"] = "A törlés sikertelen!";
        echoResponse(500, $response);
    }
});

/*
** Get All devaluation
*/
$app->get('/devaluations/:languageId', 'authenticate', function($languageId) {
    $response = array();
    $db = new DbHandler();

    // fetch rooms
    $result = $db->GetAllDevaluation($languageId);

    if(!is_null($result)) {
        $response["error"] = false;
        $response["specialOffers"] = $result;
        echoResponse(200, $response);

    } else {
        $response["error"] = true;
        $response["message"] = "The requested resource doesn't exists";
        echoResponse(500, $response);
    }
});

/*
*Create devaluation
*/
$app->post('/createDevaluation/', 'authenticate', function() use($app) {
    global $user_id;

    // Check for required params
    verifyRequiredParams(array('startDate', 'endDate', 'offers'));

    // reading post params
    $start_date = $app->request()->post('startDate');
    $end_date = $app->request()->post('endDate');
    $devaluation = $app->request()->post('offers');
    
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

/*
** Delete Devaluation By devaluationId
 */
$app->delete('/devaluation/:devaluationId', 'authenticate', function($devaluationId) use($app){
    
    $response = array();
    $db = new DbHandler();

    $result = $db->DeleteDevaluation($devaluationId);

    if($result) {
        $response["error"] = false;
        $response["message"] = "Sikeres törlés!";
        echoResponse(201, $response);
    }
    else {
        $response["error"] = true;
        $response["message"] = "A törlés sikertelen!";
        echoResponse(500, $response);
    }
});

/*
*Fetching all partners
*/
$app->get('/partners', 'authenticate', function() {
    $response = array();
    $db = new DbHandler();

    // fetch partners
    $result = $db->GetPartners();

    if(!is_null($result)) {
        $response["error"] = false;
        $response["partners"] = $result;
        echoResponse(200, $response);
    } else {
        $response["error"] = true;
        $response["message"] = "The requested resource doesn't exists";
        echoResponse(500, $response);
    }
});

/*
*Fetching logs of partner
*/
$app->get('/log/:partnerId', 'authenticate', function($partnerId) {
    $response = array();
    $db = new DbHandler();

    // fetch rooms
    $result = $db->GetLogByPartnerId($partnerId);

    if($result != NULL) {
        $response["error"] = false;
        $response["result"] = $result;
        echoResponse(200, $response);

    } else {
        $response["error"] = true;
        $response["message"] = "The requested resource doesn't exists";
        echoResponse(500, $response);
    }
});

/*
*Ban a user
*/
$app->put('/ban/:partnerId', 'authenticate', function($partnerId) {
    global $user_id;

    $response = array();
    $db = new DbHandler();

    // fetch rooms
    $result = $db->DenyPartner($partnerId, $user_id);

    if($result != NULL) {
        $response["error"] = false;
        $response["message"] = "A felhasználó ki lett tiltva!";
        echoResponse(200, $response);

    } else {
        $response["error"] = true;
        $response["message"] = "The requested resource doesn't exists";
        echoResponse(500, $response);
    }
});


/*
*Disengage a user
*/
$app->put('/disengage/:partnerId', 'authenticate', function($partnerId) {
    global $user_id;

    $response = array();
    $db = new DbHandler();

    // fetch rooms
    $result = $db->DisengagePartner($partnerId, $user_id);

    if($result != NULL) {
        $response["error"] = false;
        $response["message"] = "A felhasználó tiltása feloldódott!";
        echoResponse(200, $response);

    } else {
        $response["error"] = true;
        $response["message"] = "The requested resource doesn't exists";
        echoResponse(500, $response);
    }
});

/*
*Modify user password
*/
$app->put('/passmodify', 'authenticate', 'language', function() use($app) {
    global $user_id;
    global $responseStrings;
    
    // Check for required params
    verifyRequiredParams(array('oldPassword', 'newPassword', 'confirmNewPassword'));

    // reading post params
    parse_str($app->request()->getBody(), $request_params);
    $oldPassword = $request_params['oldPassword'];
    $newPassword = $request_params['newPassword'];
    $confirmNewPassword = $request_params['confirmNewPassword'];
    
    $response = array();
    $db = new DbHandler();

    // fetch rooms
    $result = $db->PasswordModify($user_id, $oldPassword, $newPassword, $confirmNewPassword);

    if(!is_null($result)) {
        $response["error"] = false;
        $response["message"] = $responseStrings["changePasswordSuccess"];
        echoResponse(200, $response);
    } else {
        $response["error"] = true;
        $response["message"] = $responseStrings["changePasswordWrongCredentials"];
        echoResponse(409, $response);
    }
});

/*
*User forget her/his password
*/
$app->post('/getnewpassword', 'language', function() use($app) {
    global $responseStrings;
    
    // Check for required params
    verifyRequiredParams(array('email'));

    // reading post params
    $email = $app->request()->post('email');

    $response = array();
    $db = new DbHandler();

    $result = $db->isUserExists($email);

    if($result) {
        $result = $db->getUserByEmail($email);
        $first_name = $result['first_name'];
        $last_name = $result['last_name'];
        $newPassword = randomPassword();

        $result2 = $db->forgottenPasswordModify($email, $newPassword);

        if (!is_null($result2) && SendMailToUserForNewPassword($first_name, $last_name, $email, $newPassword)) {
            $response["error"] = true;
            $response["message"] = $responseStrings["forgotPasswordSuccess"];
            $response["newPassword"] = $newPassword;
            echoResponse(200, $response);
        } else {
            $response["error"] = true;
            $response["message"] = $responseStrings["ErrorUnexpected"];
            echoResponse(500, $response);
        }

    } else {
        $response["error"] = true;
        $response["message"] = $responseStrings["forgotPasswordWrongEmail"];
        echoResponse(409, $response);
    }
});



$app->get('/teszt', 'authenticate', function() {
    echo DateTimeZone::UTC;
});

$app->run();

?>