<?php

// CommentSecurityCheck.php

namespace MFissehaye\CommentSecurityCheck;

class CommentSecurityCheck {

    protected $host;
    protected $user;
    protected $password;
    protected $db;
    protected $link;

    protected $tableName = 'spam';

    public function __construct($config)
    {
        @list($host, $user, $password, $db) = $config;

        $this->host = $host;
        $this->user = $user;
        $this->password = $password;
        $this->db = $db;

        $this->connect();
        $this->createTable();
    }

    public function createTable()
    { // if not exists
        $sql = "CREATE TABLE $this->tableName(id INT(4) NOT NULL PRIMARY KEY AUTO_INCREMENT, spam_word VARCHAR(255) NOT NULL)";
        mysqli_query($this->link, $sql);
    }

    public function connect()
    {
        $this->link = mysqli_connect($this->host, $this->user, $this->password, $this->db);
    }

    public function getSpamWords()
    {
        $words = array();
        $sql = "SELECT spam_word from $this->tableName";
        $result = mysqli_query($this->link, $sql);
        if($result != null) {
            $rows = mysqli_fetch_all($result, MYSQLI_ASSOC);
            if($rows != false) {
                foreach($rows as $row) {
                    $words[] = $row['spam_word'];
                }
                return $words;
            }
        }
        return $words;
    }

    public function addToSpam($word)
    {
        if(!$this->isWordSpam($word)) {
            if(!is_numeric($word)) {
                $word = "'" . mysqli_real_escape_string($this->link, $word) . "'";
            }
            $sql = "INSERT INTO $this->tableName (spam_word) VALUES ($word)";
            mysqli_query($this->link, $sql);
        }
    }

    public function isWordSpam($word)
    {
        $sql = "SELECT spam_word from $this->tableName WHERE spam_word = $word";
        $result = mysqli_query($this->link, $sql);
        if($result !== null && mysqli_num_rows($result)) return true;
        return false;
    }

    function __destruct()
    {
        $this->link->close();
    }


}