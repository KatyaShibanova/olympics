<?php
    //обработка запросов
    include_once './utils/token.php';
    include_once './utils/database.php';
    include_once 'models.php';
    class OlympicsRepository{
        private $database;
        private $token;

        public function __construct()
        {
            $this->database = new DataBase();
            $this->token = new Token();
        }

        public function GetTasks(){
            $query = $this->database->db->$query("SELECT * FROM tasks");
            $query->setFetchMode(PDO::FETCH_CLASS, 'Task');
                
            return $query->fetchAll();            
        }

        public function GetScore($studentID){
            if($studentID == null){
                return array("message" => "ID пользователя не введен", "method" => "GetScore", "requestData" => $studentID)
            }
            $query = $this->database->db->prepare("SELECT score FROM checks WHERE studentID = ?");
            $query->execute(array($studentID))
            $query->setFetchMode(PDO::FETCH_CLASS, 'Score');
            
            return $query->fetch();
            
        }

        public function SaveAnswer($userID, $answer){
            if($userID == null){
                return array("message" => "Id гостя не может быть пустым", "method" => "SaveAnswer", "requestData" => $userID);
            }
            $answer['userID'] = $userID;
            $insert = $this->database->getInsertQuery((array)$answer, 'checks');
            $query = $this->database->prepare($insert[0]);
            if($insert[1][0]!=null){
                $query->execute($insert[1]);
            }

            return $this->database->db->lastInsertId();
        }

        public function LogIn($user){
            if($user == null, $user->email == null, $user->password == null){
                return array("message" => "Укажите данные", "method" => "SignIn", "requestData" => $user);
            }
            $query = $this->database->db->prepare("SELECT * FROM users WHERE email = ? and password = ?");
            $query->execute(array($user->email,$user->password));
            $user = $query->fetch();
            if($user == null) {
                return array("message" => "Пользователь не найден", "method" => "SignIn", "requestData" => $user);
            }
            $user->answers=$this->GetAnswers($user->id);
            $user->petitions=$this->GetPetitions($user->id);
        }

        private function GetAnswers($userID){
            $query = $this->database->db->prepare("SELECT c.id, studentID, stage, answer, t.task, score FROM checks c JOIN tasks t on c.taskID = t.id WHERE c.studentID = ?");
            $query->execute(array($userID));
            $query->setFetchMode(PDO::FETCH_CLASS, 'Answer');
              
            return $query->fetchAll();            
        }

        private function GetPetitions($userID){
            $query = $this->database->db->prepare("SELECT studentID, petition, decision FROM petitions WHERE studentID = ?");
            $query->execute(array($userID));
            $query->setFetchMode(PDO::FETCH_CLASS, 'Petition');
              
            return $query->fetchAll();            
        }

        public function CreatePetition($userID, $petition){
            if($userID == null){
                return array("message" => "Id гостя не может быть пустым", "method" => "CreatePetition", "requestData" => $userID);
            }
            if($petition == null){
                return array("message" => "Апелляция не может быть пустой", "method" => "CreatePetition", "requestData" => $petition);
            }
            $petition['userID'] = $userID;
            $insert = $this->database->getInsertQuery((array)$petition, 'petitions');
            $query = $this->database->prepare($insert[0]);
            if($insert[1][0]!=null){
                $query->execute($insert[1]);
            }

            return $this->database->db->lastInsertId();
        }

        public function SetScore($userID, $score){
            if($userID == null){
                return array("message" => "Id гостя не может быть пустым", "method" => "SetScore", "requestData" => $userID);
            }
            if($score == null){
                return array("message" => "Баллы не могут быть пустыми", "method" => "SetScore", "requestData" => $score);
            }
            $score['userID'] = $userID;
            $insert = $this->database->getInsertQuery((array)$score, 'checks');
            $query = $this->database->prepare($insert[0]);
            if($insert[1][0]!=null){
                $query->execute($insert[1]);
            }

            return $this->database->db->lastInsertId();
        }

        // public function GenerateLink($link){
        //     if($link == null || !isset($link->guestId) || $link->guestId == null){
        //         //http_response_code(500);
        //         return array("message" => "Укажите id гостя", "method" => "GenerateLink", "requestData" => $link);
        //     }

        //     if($link == null || !isset($link->header) || $link->header == null){
        //         //http_response_code(500);
        //         return array("message" => "Укажите заголовок приглашения для ссылки", "method" => "GenerateLink", "requestData" => $link);
        //     }

        //     if($this->LinkExists($link->guestId)){
        //         //http_response_code(403);
        //         return array("message" => "У гостя уже есть ссылка", "method" => "GenerateLink", "requestData" => $link);
        //     }

        //     $token = $this->token->encode(array('guestId' => $link->guestId));
        //     $url = $this->baseUrl.'guest/'.$token;
        //     $link->url = $url;
        //     $guestId = $link->guestId;
        //     unset( $link->guestId );
            
        //     $insert = $this->database->genInsertQuery((array)$link, 'link');
        //     $query = $this->database->db->prepare($insert[0]);
        //     if($insert[1][0]!=null){
        //         $query->execute($insert[1]);
        //     }

        //     $linkId = $this->database->db->lastInsertId();

        //     $this->UpdateGuest((object) array('id' => $guestId, 'linkId' => $linkId));

        //     return $this->GetGuestLink($linkId);
            
        // }

        // public function UpdateGuest($guest){
        //     if($guest == null || !isset($guest->id)){
        //         //http_response_code(500);
        //         return array("message" => "Укажите id гостя", "method" => "UpdateGuest", "requestData" => $guest);
        //     }

        //     $guestId = $guest->id;
        //     unset($guest->id);
        //     $a = $this->database->genUpdateQuery(array_keys((array)$guest), array_values((array)$guest), "guest", $guestId);
        //     $query = $this->database->db->prepare($a[0]);
        //     $query->execute($a[1]);
        //     return array('message' => 'Гость обновлен');
        // }

        // public function AddToLink($guest){
        //     if($guest == null || !isset($guest->guestId) || !isset($guest->linkId)){
        //         //http_response_code(500);
        //         return array("message" => "Укажите id гостя и ссылки", "method" => "AddToLink", "requestData" => $guest);
        //     }
        //     $this->UpdateGuest((object) array('id' => $guest->guestId, 'linkId' => $guest->linkId));
        //     return array("message" => "Гость добавлен в ссылку");
            
        // }

        // public function RemoveFromLink($guest){
        //     if($guest == null || !isset($guest->guestId) || !isset($guest->linkId)){
        //         //http_response_code(500);
        //         return array("message" => "Укажите id гостя и ссылки", "method" => "RemoveFromLink", "requestData" => $guest);
        //     }
        //     $this->UpdateGuest((object) array('id' => $guest->guestId, 'linkId' => null));
        //     $this->CheckEmptyLink($guest->linkId);
        //     return array("message" => "Гость убран из ссылки");
        // }
        
        // public function GetStatistics(){
        //     return array("message" => "Статистика");
            
        // }

        // public function GetQuestioningResults(){
        //     return array("message" => "Ответы");
            
        // }

        // private function GetGuestLink($linkId = null)
        // {
        //     if($linkId != null){
        //         $query = $this->database->db->prepare("SELECT * FROM link WHERE id = ?");
        //         $query->execute(array($linkId));
        //         $query->setFetchMode(PDO::FETCH_CLASS, 'Link');
        //         return $query->fetch();
        //     }
        //     return null;
        // }

        // private function GetGuestChildren($guestId = null)
        // {
        //     if($guestId != null){
        //         $query = $this->database->db->prepare("SELECT * FROM child WHERE guestId = ?");
        //         $query->execute(array($guestId));
        //         $query->setFetchMode(PDO::FETCH_CLASS, 'Child');
        //         return $query->fetchAll();
        //     }
        //     return array();
        // } 

        // private function GetGuestNeighbours($guestId = null)
        // {
        //     if($guestId != null){
        //         $query = $this->database->db->prepare("SELECT n.id, neighbourId, guestId, g.name as neighbourName FROM neighbour n JOIN guest g ON n.neighbourId=g.id WHERE n.guestId = ?");
        //         $query->execute(array($guestId));
        //         $query->setFetchMode(PDO::FETCH_CLASS, 'Neighbour');
        //         return $query->fetchAll();
        //     }
        //     return array();
        // } 

        // private function AddGuestChild($child)
        // {
        //     $insert = $this->database->genInsertQuery((array)$child, 'child');
        //     $query = $this->database->db->prepare($insert[0]);
        //     if($insert[1][0]!=null){
        //         $query->execute($insert[1]);
        //     }
        // }

        // private function AddGuestNeighbour($neighbour)
        // {
        //     $insert = $this->database->genInsertQuery((array)$neighbour, 'neighbour');
        //     $query = $this->database->db->prepare($insert[0]);
        //     if($insert[1][0]!=null){
        //         $query->execute($insert[1]);
        //     }
        // }

        // private function LinkExists($guestId){
        //     $query = "SELECT linkId FROM guest WHERE id = ?";
 
        //     // подготовка запроса 
        //     $stmt = $this->database->db->prepare( $query );
        //     // выполняем запрос 
        //     $stmt->execute(array($guestId));

        //     $guest = $stmt->fetch();
        //     return isset($guest['linkId']) && $guest['linkId'] != null;
        // }

        // private function CheckEmptyLink($linkId){
        //     $query = "SELECT * FROM guest WHERE linkId = ?";
 
        //     // подготовка запроса 
        //     $stmt = $this->database->db->prepare( $query );
        //     // выполняем запрос 
        //     $stmt->execute(array($linkId));

        //     $count = $stmt->rowCount();
        //     if($count == 0){
        //         $query = "DELETE FROM link WHERE id = ?";
 
        //         // подготовка запроса 
        //         $stmt = $this->database->db->prepare( $query );
        //         // выполняем запрос 
        //         $stmt->execute(array($linkId));
        //     }
        // }

        // private function RemoveGuestChildren($guestId){
        //     $query = "DELETE FROM child WHERE guestId = ?";
        //     $stmt = $this->database->db->prepare( $query );
        //     $stmt->execute(array($guestId));
        // }
        
        // private function RemoveGuestNeighbours($guestId){
        //     $query = "DELETE FROM neighbour WHERE guestId = ?";
        //     $stmt = $this->database->db->prepare( $query );
        //     $stmt->execute(array($guestId));
        // }

    }
?>