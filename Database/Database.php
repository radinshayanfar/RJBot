<?php


class Database
{
    private $dbConnection;

    /**
     * Database constructor.
     * @param $host string DB host
     * @param $user string DB user
     * @param $password string DB password
     * @param $name string DB name
     * @throws Exception If failed to connect to database
     */
    public function __construct($host, $user, $password, $name)
    {
        $this->dbConnection = mysqli_connect($host, $user, $password, $name);
        if (!$this->dbConnection)
            throw new Exception('Unable to connect to database');
    }

    public function updateUserMessageCount($update)
    {
        $chat_id = $update['message']['chat']['id'];
        $user_FName = $update['message']['chat']['first_name'];
        $user_FName = mysqli_real_escape_string($this->dbConnection, $user_FName);
        $query = "SELECT * FROM subs WHERE chat_id = {$chat_id} LIMIT 1";
        $r = @mysqli_query($this->dbConnection, $query);
        $num = mysqli_num_rows($r);
        if ($num > 0) {
            $row = mysqli_fetch_array($r, MYSQLI_ASSOC);
            $row['count']++;
            $query = "UPDATE subs SET count = ${row['count']}, last_time = CURRENT_TIMESTAMP WHERE chat_id = {$chat_id} LIMIT 1";
            $r = @mysqli_query($this->dbConnection, $query);
        } else {
            $query = "INSERT INTO subs (chat_id, name) VALUES ({$chat_id}, '{$user_FName}')";
            $r = @mysqli_query($this->dbConnection, $query);
        }
    }

    public function autoIncrementStart()
    {
        $query = 'SELECT AUTO_INCREMENT FROM information_schema.TABLES WHERE TABLE_SCHEMA = "rjbot" AND TABLE_NAME = "tracks"';
        $r = @mysqli_query($this->dbConnection, $query);
        $row = mysqli_fetch_array($r);
        return $row['AUTO_INCREMENT'];
    }

    public function getTrackLinkByID($id): string
    {
        $query = "SELECT link FROM tracks WHERE id = {$id} LIMIT 1";
        $r = @mysqli_query($this->dbConnection, $query);
        $row = mysqli_fetch_array($r, MYSQLI_ASSOC);
        return $row['link'];
    }

    public function addTracksLink($links)
    {
        foreach ($links as $track => $link)
        {
            $query = "INSERT INTO tracks (link) VALUES ('{$link}')";
            $r = @mysqli_query($this->dbConnection, $query);
        }
    }

}