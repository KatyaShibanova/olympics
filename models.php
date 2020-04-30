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
        public $studentId;
        public $professorId;
        public $petition;
        public $decision;
    }

    class Neighbour{
        public $id;
        public $neighbourId;
        public $guestId;
    }
?>