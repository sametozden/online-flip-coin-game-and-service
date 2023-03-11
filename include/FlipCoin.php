<?php

class FlipCoin
{
    private Database $database;

    function __construct()
    {
        global $database;
        $this->database = $database;
    }

    private function setRoomStatus(string $roomId, string $status)
    {
        $values = array("status" => $status);
        $this->database->update("rooms", $values, " where roomid='$roomId' ");
    }

    private function getRoomStatus(string $roomId): string
    {
        $getRoomS = $this->database->select("rooms", "status", " where roomid='$roomId' ");
        $readRoomS = $this->database->read($getRoomS);
        return $readRoomS[1]['status'];
    }

    public function getRoomInfo(string $roomId): string
    {
        $getRoom = $this->database->select("rooms", "*", " where roomid='$roomId' ");

        if ($this->database->num_rowz($getRoom) == 1) {
            $readRoom = $this->database->read($getRoom);
            return json_encode(["result" => $readRoom[1]]);
        } else {
            return json_encode(["result" => "fail"]);
        }
    }

    public function createRoom(float $amount, string $choose, string $walletId): string
    {
        $check = $this->getUser(["walletid" => $walletId]);

        if ($check !== false) {
            $roomId = md5(time() . rand(0, 300));
            //$p1json = json_encode(["choose" => $choose]);

            $values = array(
                "status" => "open",
                "datex" => time(),
                "amount" => $amount,
                "roomid" => $roomId,
                "p1id" => $check['id'],
                "p1info" => $choose
            );
            $this->database->insert("rooms", $values);

            return json_encode(["result" => $roomId]);
        } else {
            return json_encode(["result" => "fail"]);
        }

    }

    public function leaveRoom(string $roomId): string
    {
        $room = json_decode($this->getRoomInfo($roomId), true);

        if ($room['result']['p2id'] == 0) {
            $this->database->delete("rooms", " where roomid='$roomId' ");
            return json_encode(["result" => "ok"]);
        } else {
            return json_encode(["result" => "fail"]);
        }

    }

    public function joinRoom(float $amount, string $choose, string $roomId, string $walletId): string
    {
        $room = json_decode($this->getRoomInfo($roomId), true);

        if ($room['result'] != "fail") {
            //$p1info = json_decode($room['result']['p1info'], true);
            $check = $this->getUser(["walletid" => $walletId]);

            if ($room['result']['amount'] == $amount && $room['result']['p1info'] != $choose && $this->getRoomStatus($roomId) == "open") {
                $this->setRoomStatus($roomId, "ready");
                //$p2json = json_encode(["choose" => $choose]);
                $values = array("p2info" => $choose, "p2id" => $check['id']);
                $this->database->update("rooms", $values, " where roomid='$roomId' ");

                return json_encode(["result" => "ready"]);
            } else {
                return json_encode(["result" => "fail"]);
            }
        } else {
            return json_encode(["result" => "fail"]);
        }


    }


    public function listRooms(string $status = "open", int $amount = 0, string $choose = "all"): string
    {

        // delete old rooms from 30 minutes, 1800 seconds
        $limit = time() - 1800;
        $this->database->delete("rooms", " where datex < $limit and ( status='open' or status='die' ) ");

        if (!in_array($status, ["all", "open", "ready", "finished", "current"]) || !in_array($choose, ["all", "heads", "tails"])) {
            return json_encode(["result" => "fail"]);
        }

        $sql = "";
        if ($status == "current") {
            $sql .= " and ( status='open' or status='ready' ) ";
        } elseif ($status != "all") {
            $sql .= " and status='$status' ";
        }

        if ($amount != 0) {
            $sql .= " and amount='$amount' ";
        }
        if ($choose != "all") {
            $sql .= " and p1info='$choose' ";
        }

        $getRooms = $this->database->select("rooms", "*", " where id is not null $sql order by id desc ");
        if ($this->database->num_rowz($getRooms) > 0) {
            $readRooms = $this->database->readall($getRooms);
            return json_encode(["result" => $readRooms[1]]);
        } else {
            return json_encode(["result" => "fail"]);
        }

    }


    public function myRooms($walletId): string
    {

        $walletId = htmlspecialchars($walletId, ENT_QUOTES);
        $check = $this->getUser(["walletid" => $walletId]);

        if ($check === false) {
            return json_encode(["result" => "fail"]);
        } else {
            $userId = $check['id'];
            $getRooms = $this->database->select("rooms", "*", " where (p1id='$userId' or p2id='$userId') and status<>'die' order by id desc ");
            if ($this->database->num_rowz($getRooms) > 0) {
                $readRooms = $this->database->readall($getRooms);
                return json_encode(["result" => $readRooms[1]]);
            } else {
                return json_encode(["result" => "fail"]);
            }
        }

    }

    public function closeRoom(string $roomId)
    {
        $room = json_decode($this->getRoomInfo($roomId), true);
        if($room['result']['status'] == "finished"){
            $this->setRoomStatus($room['result']['roomid'], "die");
            return json_encode(["result" => "ok"]);
        }
    }


    public function searchOpponent(string $roomId): string
    {
        $room = json_decode($this->getRoomInfo($roomId), true);

        if ($room['result']['p2id'] != 0) {
            $check = $this->getUser(["id" => $room['result']['p2id']]);
            return json_encode(["result" => $check['walletid']]);
        } else {
            return json_encode(["result" => "fail"]);
        }
    }

    public function startGame(string $roomId): string
    {
        $room = json_decode($this->getRoomInfo($roomId), true);

        if ($room['result']['status'] == "ready") {

            $this->setRoomStatus($roomId, "finished");
            $coin = array("heads", "tails");
            $randKey = array_rand($coin, 1);
            $result = $coin[$randKey];

            $winner = ($result == $room['result']['p1info']) ? "p1" : "p2";
            $p1Info = $this->getUser(["id" => $room['result']['p1id']]);
            $p2Info = $this->getUser(["id" => $room['result']['p2id']]);

            if ($winner == "p1") { // p1 winner, deposit p1, withdraw p2
                $winnerPlayerInfo = $p1Info;
                $this->depositAmount($p1Info['walletid'], $room['result']['amount']);
                $this->withdrawAmount($p2Info['walletid'], $room['result']['amount']);
            } elseif ($winner == "p2") { // p2 winner, deposit p2, withdraw p1
                $winnerPlayerInfo = $p2Info;
                $this->depositAmount($p2Info['walletid'], $room['result']['amount']);
                $this->withdrawAmount($p1Info['walletid'], $room['result']['amount']);
            }

            $winnerPlayerInfo['choose'] = $result;
            $winnerPlayerInfo['status'] = $room['result']['status'];

            $values = array("winnerid" => $winnerPlayerInfo['id']);
            $this->database->update("rooms", $values, " where roomid='$roomId' ");

            return json_encode(["result" => $winnerPlayerInfo]);


        } elseif ($room['result']['status'] == "finished") {
            $room = json_decode($this->getRoomInfo($roomId), true);
            $winnerPleyerId = $room['result']['winnerid'];
            $winner = $this->getUser(["id" => $winnerPleyerId]);

            if ($room['result']['winnerid'] == $room['result']['p1id']) {
                $winner['choose'] = $room['result']['p1info'];
            } elseif ($room['result']['winnerid'] == $room['result']['p2id']) {
                $winner['choose'] = $room['result']['p2info'];
            }
            $winner['status'] = $room['result']['status'];

            return json_encode(["result" => $winner]);
        } elseif ($room['result']['status'] == "open") {
            return json_encode(["result" => "fail"]);
        }
    }

    private function getUser($walletId)
    {
        $arrKey = key($walletId);
        $sql = $arrKey . "='" . $walletId[$arrKey] . "'";

        $getU = $this->database->select("users", "id, walletid, userkey, amount, nickname, avatar, datex", " where $sql ");
        if ($this->database->num_rowz($getU) == 0) {
            return false;
        } else {
            $readUser = $this->database->read($getU);
            return $readUser[1];
        }
    }

    public function checkUser($walletId): string
    {
        $walletId = htmlspecialchars($walletId, ENT_QUOTES);
        $check = $this->getUser(["walletid" => $walletId]);

        if ($check === false) {
            $userKey = $this->generateString(250);
            $values = array("userkey" => $userKey, "walletid" => $walletId, "datex" => time(), "amount" => "0");
            $this->database->insert("users", $values);
            return json_encode(["result" => "created"]);
        } else {
            return json_encode(["result" => $check]);
        }
    }


    public function userEvents($userKey): string
    {
        $userKey = htmlspecialchars($userKey, ENT_QUOTES);
        $check = $this->getUser(["userkey" => $userKey]);
        if ($check !== false) {

            $userId = $check['id'];
            $getEvents = $this->database->select("userevents", "*", " where userid='$userId' order by id desc ");

            if ($this->database->num_rowz($getEvents) == 0) {
                return json_encode(["result" => "fail"]);
            } else {
                $readEvents = $this->database->readall($getEvents);
                return json_encode(["result" => $readEvents[1]]);
            }
        } else {
            return json_encode(["result" => "fail"]);
        }
    }

    private function depositAmount(string $walletId, int $amount)
    {
        $check = $this->getUser(["walletid" => $walletId]);

        if ($check !== false) {
            $oldAmount = $check['amount'];
            $newAmount = $oldAmount + $amount;
            $values = array("amount" => $newAmount);
            $this->database->update("users", $values, " where walletid='$walletId' ");
            $this->createEvent($check, $amount, "deposit");
        }
    }


    private function withdrawAmount(string $walletId, int $amount)
    {

        $check = $this->getUser(["walletid" => $walletId]);

        if ($check !== false) {
            $oldAmount = $check['amount'];
            if ($oldAmount >= $amount) {
                $newAmount = $oldAmount - $amount;
                $values = array("amount" => $newAmount);
                $this->database->update("users", $values, " where walletid='$walletId' ");
                $this->createEvent($check, $amount, "withdraw");
            }
        }
    }

    public function depositManualAmount(string $userKey, int $amount): string
    {
        $check = $this->getUser(["userkey" => $userKey]);

        if ($check !== false) {
            $oldAmount = $check['amount'];
            $newAmount = $oldAmount + $amount;
            $values = array("amount" => $newAmount);
            $this->database->update("users", $values, " where userkey='$userKey' ");
            $this->createEvent($check, $amount, "deposit");
            return json_encode(["result" => "ok"]);
        } else {
            return json_encode(["result" => "fail"]);
        }
    }

    public function withdrawManualAmount(string $userKey, int $amount): string
    {
        $check = $this->getUser(["userkey" => $userKey]);

        if ($check !== false) {
            $oldAmount = $check['amount'];
            if ($oldAmount >= $amount) {
                $newAmount = $oldAmount - $amount;
                $values = array("amount" => $newAmount);
                $this->database->update("users", $values, " where userkey='$userKey' ");
                $this->createEvent($check, $amount, "withdraw");
                return json_encode(["result" => "ok"]);
            }
            return json_encode(["result" => "fail"]);
        } else {
            return json_encode(["result" => "fail"]);
        }
    }

    private function createEvent($user, $amount, $eventName)
    {
        $values = array("userid" => $user['id'], "amount" => $amount, "datex" => time(), "event" => $eventName);
        $this->database->insert("userevents", $values);
    }


    public function generateString($strength = 16): string
    {
        $chars = "0123456789abcdefghijklmnopqrstuvwxyz";
        $inputLength = strlen($chars);
        $randomString = "";
        for ($i = 0; $i < $strength; $i++) {
            $randomCharacter = $chars[mt_rand(0, $inputLength - 1)];
            $randomString .= $randomCharacter;
        }
        return $randomString;
    }


}