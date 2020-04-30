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

    class Child{
        public $id;
        public $name;
        public $age;
        public $guestId;
    }

    class Neighbour{
        public $id;
        public $neighbourId;
        public $guestId;
    }
?>