<?php
//прием запросов с клиента
header("Access-Control-Allow-Origin: *"); 
header("Access-Control-Allow-Methods: GET, PUT, POST, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Origin, Content-Type, X-Auth-Token , Authorization");

include_once 'repository.php';
include_once './utils/token.php'

$repository = new OlympicsRepository();

if(isset($_GET['token'])){

}else {
    echo json_encode(array("message" => "Ключ запроса не найден"));
    return;
}

if(isset($_GET['key'])){
    switch($_GET['key']){
        case 'get-tasks':
            echo json_encode($repository->GetTasks($_GET[]));
            return;
        case 'get-score':
            if($decodeToken = checkToken(true)){
                echo json_encode($repository->GetScore($_GET['studentID']));
            }
            return;
        case 'save-answer':
            if($decodeToken = checkToken(true)){
                $data = json_decode(file_get_contents("php://input"));
                echo json_encode($repository->SaveAnswer($decodeToken->userID, $data));
            }
            return;
        case 'log-in':
            $data = json_decode(file_get_contents("php://input"));
            echo json_encode($repository->SignIn($data));
            return;
        case 'create-appeal':
            if($decodeToken = checkToken(true)){
            $data = json_decode(file_get_contents("php://input"));
            echo json_encode($repository->SignIn($data));
            }
            return;
        case 'set-score':
            if($decodeToken = checkToken()){
                $data = json_decode(file_get_contents("php://input"));
                echo json_encode($repository->SignIn($data));
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

function checkToken($checkStudent){
    try{
        $data = $token->decode($_GET['token']);
        if($checkStudent && !$data->isStudent){
            json_encode(array("message" => "В доступе отказано");
            return false;
        } 
        return $data;
        
    } catch(Exception $e) {
        return json_encode(array("message" => "В доступе отказано", "error" => $e->getMessage()));
    }
}
?>