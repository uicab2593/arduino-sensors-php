<?php
require 'vendor/autoload.php';
use Ratchet\ConnectionInterface;
use Ratchet\Wamp\WampServerInterface;

class app implements WampServerInterface {
    protected $topics = [];
    public function onSubscribe(ConnectionInterface $conn,$topic) {
    	$this->topics[$topic->getId()]=$topic;
    }
    public function onChangeConfig($entry) {
    	$aux = explode(':',$entry);
    	if(count($aux)==2){
        	$this->topics['all']->broadcast(json_encode(['sensor'=>$aux[0],'state'=>$aux[1]]));
    	}
    	return;
    }
    public function onUnSubscribe(ConnectionInterface $conn, $topic) {
    }
    public function onOpen(ConnectionInterface $conn) {
    }
    public function onClose(ConnectionInterface $conn) {
    }
    public function onCall(ConnectionInterface $conn, $id, $topic, array $params) {
        // In this application if clients send data it's because the user hacked around in console
        $conn->callError($id, $topic, 'You are not allowed to make calls')->close();
    }
    public function onPublish(ConnectionInterface $conn, $topic, $event, array $exclude, array $eligible) {
        // In this application if clients send data it's because the user hacked around in console
        $conn->close();
    }
    public function onError(ConnectionInterface $conn, \Exception $e) {
    }
}