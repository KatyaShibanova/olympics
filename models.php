<?php
    class User{
        public $id;
        public $name;
        public $surname;
        public $middlename;
        public $isStudent;
        public $email;
        public $password;
    }

    class Task{
        public $id;
        public $type;
        public $task;
    }

    class Petition{
        public $id;
        public $studentID;
        public $professorID;
        public $petition;
        public $decision;
    }

    class Check{
        public $id;
        public $professorID;
        public $studentID;
        public $taskId;
        public $score;
        public $answer;
    }
?>