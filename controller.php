<?php
//прием запросов с клиента
header("Access-Control-Allow-Origin: *"); 
header("Access-Control-Allow-Methods: GET, PUT, POST, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Origin, Content-Type, X-Auth-Token , Authorization");

include_once 'repository.php';
include_once './utils/token.php';

$repository = new OlympicsRepository();
$token = new Token();

// if(isset($_GET['token'])){

// }else {
//     echo json_encode(array("message" => "Ключ запроса не найден"));
//     return;
// }

if(isset($_GET['key'])){
    switch($_GET['key']){
        case 'get-tasks':
            echo json_encode($repository->GetTasks($_GET));
            return;
        case 'get-score':
            if($decodeToken = checkToken($token,true)){
                echo json_encode($repository->GetScore($_GET['studentID']));
            }
            return;
        case 'save-answer':
            if($decodeToken = checkToken($token,true)){
                $data = json_decode(file_get_contents("php://input"));
                echo json_encode($repository->SaveAnswer($decodeToken->id, $data));
            }
            return;
        case 'log-in':
            $data = json_decode(file_get_contents("php://input"));
            echo json_encode($repository->LogIn($data));
            return;
        case 'create-petition':
            if($decodeToken = checkToken($token,true)){
            $data = json_decode(file_get_contents("php://input"));
            echo json_encode($repository->CreatePetition($decodeToken->id, $data));
            }
            return;
        case 'set-score':
            if($decodeToken = checkToken($token)){
                $data = json_decode(file_get_contents("php://input"));
                echo json_encode($repository->SetScore($data));
            }
            return;
        case 'set-petition':
            if($decodeToken = checkToken($token)){
                $data = json_decode(file_get_contents("php://input"));
                echo json_encode($repository->SetPetition($data));
            }
            return;
        default: 
            if(!$isAdmin){
                echo json_encode(array("message" => "Ключ запроса не найден"));
                return;
            }
    }



} else {
    http_response_code(500);
    echo json_encode(array("message" => "Отсутствует ключ запроса."));
}

function checkToken($token, $checkStudent = false) {
    try{
        if(!isset($_GET['token'])){
            return false;
        }
        $data = $token->decode($_GET['token']);
        if($checkStudent && (!isset($data->isStudent) || !$data->isStudent)){
            return false;
        }
        return $data;
        
    } catch(Exception $e) {
        return false;
    }
}

?>