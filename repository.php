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
            $query = $this->database->db->query("SELECT * FROM tasks");
            $query->setFetchMode(PDO::FETCH_CLASS, 'Task');
                
            return $query->fetchAll();            
        }

        public function GetPrepods(){
            $query = $this->database->db->query("SELECT * FROM users WHERE isStudent = 0");
            $query->setFetchMode(PDO::FETCH_CLASS, 'User');
                
            return $query->fetchAll();            
        }


        public function GetScore($studentID){
            if($studentID == null){
                return array("message" => "ID пользователя не введен", "method" => "GetScore", "requestData" => $studentID);
            }
            $query = $this->database->db->prepare("SELECT score FROM checks WHERE studentID = ?");
            $query->execute(array($studentID));
            $query->setFetchMode(PDO::FETCH_CLASS, 'Score');
            
            return $query->fetch();
            
        }

        public function SaveAnswer($studentID, $answer){
            if($studentID == null){
                return array("message" => "Id гостя не может быть пустым", "method" => "SaveAnswer", "requestData" => $studentID);
            }
            
            if(!isset($answer->id)){
                $answer->studentID = $studentID;
                $prepods=$this->GetPrepods();
                $index=rand (0, count($prepods)-1);
                $answer->professorID = $prepods[$index]->id;
            }
            $insert = $this->database->genInsertQuery((array)$answer, 'checks');
            if(isset($answer->id)){
                $id = $answer->id;
                unset($answer->id);
                $insert = $this->database->genUpdateQuery(array_keys((array)$answer), array_values((array)$answer), "checks", $id);
            }
            
            
            $query = $this->database->db->prepare($insert[0]);
            if($insert[1][0]!=null){
                $query->execute($insert[1]);
            }

            // return $this->database->db->lastInsertId();
            return true;
        }

        public function CreatePetition($studentID, $petition){
            if($studentID == null){
                return array("message" => "Id гостя не может быть пустым", "method" => "CreatePetition", "requestData" => $studentID);
            }
            if($petition == null){
                return array("message" => "Апелляция не может быть пустой", "method" => "CreatePetition", "requestData" => $petition);
            }
            
            if(!isset($petition->id)){
                $petition->studentID = $studentID;
                $prepods=$this->GetPrepods();
                $index=rand (0, count($prepods)-1);
                $petition->professorID = $prepods[$index]->id;
            }
            $insert = $this->database->genInsertQuery((array)$petition, 'petitions');
            if(isset($petition->id)){
                $id = $petition->id;
                unset($petition->id);
                $insert = $this->database->genUpdateQuery(array_keys((array)$petition), array_values((array)$petition), "petitions", $id);
            }
            $query = $this->database->db->prepare($insert[0]);
            if($insert[1][0]!=null){
                $query->execute($insert[1]);
            }

            // return $this->database->db->lastInsertId();
            return true;
        }


        public function LogIn($user){
            if($user == null || $user->email == null || $user->password == null){
                return array("message" => "Укажите данные", "method" => "LogIn", "requestData" => $user);
            }
            $query = $this->database->db->prepare("SELECT * FROM users WHERE email = ? and password = ?");
            $query->execute(array($user->email,$user->password));
            $query->setFetchMode(PDO::FETCH_CLASS, 'User');
            $user = $query->fetch();
            if($user == null) {
                return false;
            }
            $user->answers=$this->GetAnswers($user->id);
            return array("token"=>$this->token->encode(array("id"=>$user->id, "isStudent"=>$user->isStudent)),"user"=>$user);
        }

        private function GetAnswers($userID){
            $query = $this->database->db->prepare("SELECT c.id, studentID, answer, t.task, t.type, score FROM checks c JOIN tasks t on c.taskID = t.id WHERE c.studentID = ?");
            $query->execute(array($userID));
            $query->setFetchMode(PDO::FETCH_CLASS, 'Check');
              
            return $query->fetchAll();            
        }

        public function GetWorks($userID){
            $query = $this->database->db->prepare("SELECT c.id, studentID, answer, t.task, t.type, score FROM checks c JOIN tasks t on c.taskID = t.id WHERE c.studentID = ?");
            $query->execute(array($userID));
            $query->setFetchMode(PDO::FETCH_CLASS, 'Check');
              
            return $query->fetchAll();            
        }

        public function GetWorkAnswers($workID){
            $query = $this->database->db->prepare("SELECT c.id, studentID, answer, t.task, t.type, score FROM checks c JOIN tasks t on c.taskID = t.id WHERE c.id = ?");
            $query->execute(array($workID));
            $query->setFetchMode(PDO::FETCH_CLASS, 'Check');
              
            return $query->fetchAll();            
        }

        public function GetPetitionDecision($petitionID){
            $query = $this->database->db->prepare("SELECT * FROM petitions WHERE id = ?");
            $query->execute(array($petitionID));
            $query->setFetchMode(PDO::FETCH_CLASS, 'Petition');
              
            return $query->fetchAll();            
        }

        public function GetPetitions($userID){
            $query = $this->database->db->prepare("SELECT * FROM petitions WHERE studentID = ?");
            $query->execute(array($userID));
            $query->setFetchMode(PDO::FETCH_CLASS, 'Petition');
              
            return $query->fetchAll();            
        }

        public function GetPrepodPetitions($userID){
            $query = $this->database->db->prepare("SELECT * FROM petitions WHERE professorID = ?");
            $query->execute(array($userID));
            $query->setFetchMode(PDO::FETCH_CLASS, 'Petition');
              
            return $query->fetchAll();            
        }

        public function GetPrepodWorks($userID){
            $query = $this->database->db->prepare("SELECT * FROM checks WHERE professorID = ?");
            $query->execute(array($userID));
            $query->setFetchMode(PDO::FETCH_CLASS, 'Check');
              
            return $query->fetchAll();            
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

        public function SetPetition($userID, $decision){
            if($userID == null){
                return array("message" => "Id гостя не может быть пустым", "method" => "SetPetition", "requestData" => $userID);
            }
            if($decision == null){
                return array("message" => "Решение по апелляции не могут быть пустыми", "method" => "SetPetition", "requestData" => $decision);
            }
            $decision['userID'] = $userID;
            $insert = $this->database->getInsertQuery((array)$decision, 'petitions');
            $query = $this->database->prepare($insert[0]);
            if($insert[1][0]!=null){
                $query->execute($insert[1]);
            }

            return $this->database->db->lastInsertId();
        }

    }
?>