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
        $sql = "SELECT id, spam_word from $this->tableName";
        $result = mysqli_query($this->link, $sql);
        if($result != null) {
            $rows = mysqli_fetch_all($result, MYSQLI_ASSOC);
            if($rows != false) {
                foreach($rows as $row) {
                    $words[$row['id']] = $row['spam_word'];
                }
                return $words;
            }
        }
        return $words;
    }

    public function addToSpam($word)
    {
        if(!$this->isWordSpam($word)) {
            $word = $this->quote($word);
            $sql = "INSERT INTO $this->tableName (id, spam_word) VALUES (NULL, $word)";
            mysqli_query($this->link, $sql);
        }
    }

    public function removeFromSpam($id)
    {
        $sql = "DELETE FROM $this->tableName WHERE id = $id";
        mysqli_query($this->link, $sql);
    }

    private function quote($word)
    {
        if(!is_numeric($word)) {
            return "'" . mysqli_real_escape_string($this->link, $word) . "'";
        }
        return $word;
    }

    public function isWordSpam($word)
    {
        $sql = "SELECT * from $this->tableName WHERE spam_word = $word";
        $result = mysqli_query($this->link, $sql);
        if($result != false && $result != null) {
            return mysqli_num_rows($result) > 0;
        }
        return false;
    }

    function __destruct()
    {
        $this->link->close();
    }

    function focusSpamWord($text) {
        $spamWords = $this->getSpamWords();
    // $replacedSpamWord = "";
        foreach($spamWords as $spamWord) {
            // if(!strstr($replacedSpamWord, $spamWord)) {
                // $modifiedComment = preg_replace('/(' . $spamWord . ')/', '<strong><u>$1</u></strong>', $comment->comment);
                // if($modifiedComment != $comment->comment) {
                    // $replacedSpamWord = $spamWord;
                    // $comment->comment = $modifiedComment;
                // }
            // }
            $text = preg_replace('/(' . $spamWord . ')/i', '<strong><u>$1</u></strong>', $text);
        }
        return $text;
    }

    public function hasSpamWord($text) {
        $spamWords = $this->getSpamWords();
        foreach($spamWords as $spamWord) {
            if(strstr(strtolower($text), strtolower($spamWord))) {
                return true;
            }
        }
        return false;
    }
}