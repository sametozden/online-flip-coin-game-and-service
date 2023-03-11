<?php
session_start();
header("Access-Control-Allow-Origin: *");
header("Access-Control-Expose-Headers: Content-Length, X-JSON");
header("Access-Control-Allow-Methods: GET, POST, PATCH, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: *");

require_once "include/Database.php";
require_once "include/FlipCoin.php";
$database = new Database("localhost", "db_username", "db_password", "db_name");
$flipCoin = new FlipCoin($database);
$type = explode("/", filter_var($_GET['route'], FILTER_SANITIZE_URL));
$first = array_shift($type);

switch ($first) {

    case "createRoom": print $flipCoin->createRoom($type[0], $type[1], $type[2]);
        break;

    case "joinRoom": print $flipCoin->joinRoom($type[0], $type[1], $type[2], $type[3]);
        break;

    case "leaveRoom": print $flipCoin->leaveRoom($type[0]);
        break;

    case "listRooms": print $flipCoin->listRooms($type[0], $type[1], $type[2]);
        break;

    case "myRooms": print $flipCoin->myRooms($type[0]);
        break;

    case "searchOpponent": print $flipCoin->searchOpponent($type[0]);
        break;

    case "getRoomInfo": print $flipCoin->getRoomInfo($type[0]);
        break;

    case "closeRoom": print $flipCoin->closeRoom($type[0]);
        break;

    case "startGame": print $flipCoin->startGame($type[0]);
        break;

    case "checkUser": print $flipCoin->checkUser($type[0]);
        break;

    case "userEvents": print $flipCoin->userEvents($type[0]);
        break;

    case "deposit": print $flipCoin->depositManualAmount($type[0], $type[1]);
        break;

    case "withdraw": print $flipCoin->withdrawManualAmount($type[0], $type[1]);
        break;

    default: print  "";
        break;
}
?>