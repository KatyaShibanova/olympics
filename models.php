<?php
    class Guest{
        public $id;
        public $name;
        public $surname;
        public $secondName;
        public $transfer;
        public $linkId;
        public $alcohole;
        public $food;
        public $approved;

        public $link;
        public $children;
    }

    class Link{
        public $id;
        public $url;
        public $header;
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