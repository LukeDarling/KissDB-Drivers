<?php
// Driver written by Luke Darling
// All rights reserved.

class KissDB {

    public static function sendRequest(string $type, string $path, $content = "") {
        $options = array(
            "http" => array(
              "method" => $type,
              "header" => "Content-type: text/plain",
              "content" => $content
            )
          );
          $response = file_get_contents($path, false, stream_context_create($options));
          if($response != false) {
            $response = json_decode($response, true);
            if($response["success"]) {
                return $response["result"];
            } else {
                throw new Exception($response["result"]);
                return null;
            }
        } else {
            throw new Exception("Could not connect to KissDB server.");
            return null;
        }
    }

}

class KissDBServer {

    public $port;

    function __construct(int $port) {
        $this->port = $port;
    }

    // Get specified database
    function database($db) {
        return new KissDBDatabase($this->port, $db);
    }

    // Get specified table from specified database
    function table(string $db, string $table) {
        return new KissDBTable($this->port, $db, $table);
    }

    // List databases
    function index() {
        return KissDB::sendRequest("GET", "127.0.0.1:" . strval($this->port) . "/");
    }

}

class KissDBDatabase {

    public $port;
    public $database;

    function __construct(int $port, string $db) {
        $this->port = $port;
        $this->database = $db;
    }

    // Get parent server
    function parent() {
        return new KissDBServer($this->port);
    }

    // Get specified table
    function table(string $table) {
        return new KissDBTable($this->port, $this->database, $table);
    }
    
    // List tables
    function index() {
        return KissDB::sendRequest("GET", "127.0.0.1:" . strval($this->port) . "/" . urlencode($this->database) . "/");
    }

}

class KissDBTable {

    public $port;
    public $database;
    public $table;

    function __construct(int $port, string $db, string $table) {
        $this->port = $port;
        $this->database = $db;
        $this->table = $table;
    }

    // Get parent database
    function parent() {
        return new KissDBDatabase($this->port, $this->database);
    }

    // Get box contents
    function getBox(string $box) {
        return KissDB::sendRequest("GET", "127.0.0.1:" . strval($this->port) . "/" . urlencode($this->database) . "/" . urlencode($this->table) . "/" . urlencode($box));
    }

    // Set box contents
    function setBox(string $box, string $content) {
        return KissDB::sendRequest("GET", "127.0.0.1:" . strval($this->port) . "/" . urlencode($this->database) . "/" . urlencode($this->table) . "/" . urlencode($box));
    }

    // Remove box from table
    function removeBox(string $box) {

    }

    // List boxes
    function index() {
        return KissDB::sendRequest("GET", "127.0.0.1:" . strval($this->port) . "/" . urlencode($this->database) . "/" . urlencode($this->table) . "/");
    }

}