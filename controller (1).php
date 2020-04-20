<?php
//прием запросов с клиента
header("Access-Control-Allow-Origin: *"); 
header("Access-Control-Allow-Methods: GET, PUT, POST, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Origin, Content-Type, X-Auth-Token , Authorization");

include_once 'repository.php';
include_once './utils/token.php';

if(!isset($_GET['token'])){
    http_response_code(401);
    echo json_encode(array("message" => "В доступе отказано"));
    return;
}
$token = new Token();
$guestId = null;
$isAdmin = false;
try{
    $data = $token->decode($_GET['token']);
    
    if(!isset($data->isAdmin)){
        if(isset($data->guestId)){
            $guestId = $data->guestId;
        } else {
            return;
        }
    } else {
        $isAdmin = true; 
    }
    
} catch(Exception $e) {
    // http_response_code(401);
    return json_encode(array("message" => "В доступе отказано", "error" => $e->getMessage()));
}

$repository = new WeddingRepository();

if(isset($_GET['key'])){
    switch($_GET['key']){
        case 'get-access':
            echo json_encode($isAdmin);
            return;
        case 'get-guest-info':
            // http_response_code(200);
            echo json_encode($repository->GetGuestInfo($guestId));
            return;
        case 'get-guests':
            // http_response_code(200);
            echo json_encode($repository->GetGuests($guestId));
            return;
        case 'approve-comming':
            // http_response_code(200);
            $data = json_decode(file_get_contents("php://input"));
            echo json_encode($repository->ApproveComming($guestId, $data->approved));
            return;
        case 'save-answer':
            // http_response_code(200);
            $data = json_decode(file_get_contents("php://input"));
            echo json_encode($repository->SaveAnswer($guestId, $data));
            return;
        default: 
            if(!$isAdmin){
                // http_response_code(500);
                echo json_encode(array("message" => "Ключ запроса не найден"));
                return;
            }
    }
    
    if($isAdmin){
        switch($_GET['key']){
            
            // admin
            case 'get-guest-info-by-id':
                // http_response_code(200);
                echo json_encode($repository->GetGuestInfo($_GET['guestId']));
                return;
            case 'create-guest':
                // http_response_code(200);
                $data = json_decode(file_get_contents("php://input"));
                echo json_encode($repository->CreateGuest($data));
                return;
            case 'update-guest':
                // http_response_code(200);
                $data = json_decode(file_get_contents("php://input"));
                echo json_encode($repository->UpdateGuest($data));
                return;
            case 'generate-link':
                // http_response_code(200);
                $data = json_decode(file_get_contents("php://input"));
                echo json_encode($repository->GenerateLink($data));
                return;
            case 'add-to-link':
                // http_response_code(200);
                $data = json_decode(file_get_contents("php://input"));
                echo json_encode($repository->AddToLink($data));
                return;
            case 'remove-from-link':
                // http_response_code(200);
                $data = json_decode(file_get_contents("php://input"));
                echo json_encode($repository->RemoveFromLink($data));
                return;
            case 'get-statistics':
                // http_response_code(200);
                echo json_encode($repository->GetStatistics());
                return;
            case 'get-questioning-results':
                // http_response_code(200);
                echo json_encode($repository->GetQuestioningResults());
                return;
            default: 
                http_response_code(500);
                echo json_encode(array("message" => "Ключ запроса не найден"));
                return;
            
        }
    }

    http_response_code(401);
    echo json_encode(array("message" => "В доступе отказано"));
    return;


} else {
    http_response_code(500);
    echo json_encode(array("message" => "Отсутствует ключ запроса."));
}
?>