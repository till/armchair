<?php
require_once 'ArmChair.php';

// example
$armchair = new ArmChair('http://localhost:5984/guestbook');

// save new document
$entry   = array('entry' => 'Great guestbook!', 'author' => 'Till');
$docData = $armchair->addDocument($entry);

// retrieve document
$document = $armchair->get($docData['id']);

// delete document
$armchair->deleteDocument($document['_id']);
