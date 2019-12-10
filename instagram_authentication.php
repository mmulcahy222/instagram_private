<?php 
session_start();
require_once("vendor/autoload.php");
use SuperClosure\Serializer;
$user_name = '';
$password = '';
$session_name = '';
$debug = (isset($_GET['debug'])) ? $_GET['debug'] : 0;
$login = (isset($_GET['login'])) ? $_GET['login'] : 0;
$serializer = new Serializer();
if($login)
{
	var_export("Resetting Session");
	unset($_SESSION);
	session_destroy();
	session_start();
}
//IF THE SESSION IS IN PLACE, GO HERE
if(isset($_SESSION[$session_name]) || $login == false)
{
	if($debug)
	{
		var_export("SESSION REUSED");
		var_export($_SESSION);
	}
	$i = unserialize($_SESSION[$session_name]);
	//You cannot serialize closures. If updating this whole folder, make sure to make certain variables public so they could be made NULL to serialize the main Instagram Client Object
	//Make public in C:\makeshift\files\instagram_private\vendor\guzzlehttp\guzzle\src\HandlerStack.php
	$i->client->_guzzleClient->config['handler']->handler = $serializer->unserialize($_SESSION['handler']);
	$i->client->_guzzleClient->config['handler']->stack[0][0] = $serializer->unserialize($_SESSION['stack0']);
	$i->client->_guzzleClient->config['handler']->stack[1][0] = $serializer->unserialize($_SESSION['stack1']);
	$i->client->_guzzleClient->config['handler']->stack[2][0] = $serializer->unserialize($_SESSION['stack2']);
	$i->client->_guzzleClient->config['handler']->stack[3][0] = $serializer->unserialize($_SESSION['stack3']);
	// $i->client->_guzzleClient->config['handler']->cached = $serializer->unserialize($_SESSION['cached']);
	$rank_token = $_SESSION['rank_token'];
}
//IF NO SESSION, MAKE IT, AND SERIALIZE REQUISITE VARIABLES WITH SUPER SERIALIZER
else
{
	if($debug)
	{
		var_export("RE-DOING LOGIN");
	}
	$i = new \InstagramAPI\Instagram();
	try {
		$i->login($user_name,$password);
	} catch (Exception $e) {
		$e->getMessage();
		echo $e;
		unset($_SESSION[$session_name]);
		exit();
	}
	$rank_token = \InstagramAPI\Signatures::generateUUID();
	$i->settings->_callbacks = NULL;
	//Handler will show nothing in the var_export (just a closure with array), but Super Serializer shows a lot more
	$_SESSION['handler'] = $serializer->serialize($i->client->_guzzleClient->config['handler']->handler);
	$_SESSION['stack0'] = $serializer->serialize($i->client->_guzzleClient->config['handler']->stack[0][0]);
	$_SESSION['stack1'] = $serializer->serialize($i->client->_guzzleClient->config['handler']->stack[1][0]);
	$_SESSION['stack2'] = $serializer->serialize($i->client->_guzzleClient->config['handler']->stack[2][0]);
	$_SESSION['stack3'] = $serializer->serialize($i->client->_guzzleClient->config['handler']->stack[3][0]);
	// $_SESSION['cached'] = $serializer->serialize($i->client->_guzzleClient->config['handler']->cached);
	if($debug)
	{
		echo "<pre>";
		var_export($i);
		echo "</pre>";
	}
	//Making these closures NULL is necessary to serialize them. Be sure to put the original back in place
	$i->client->_guzzleClient->config['handler']->handler = NULL;
	$i->client->_guzzleClient->config['handler']->stack[0][0] = NULL;
	$i->client->_guzzleClient->config['handler']->stack[1][0] = NULL;
	$i->client->_guzzleClient->config['handler']->stack[2][0] = NULL;
	$i->client->_guzzleClient->config['handler']->stack[3][0] = NULL;
	$i->client->_guzzleClient->config['handler']->cached = NULL;
	$serialized_i = serialize($i);
	$_SESSION[$session_name] = $serialized_i;
	$_SESSION['rank_token'] = $rank_token;
	$i->client->_guzzleClient->config['handler']->handler = $serializer->unserialize($_SESSION['handler']);
	$i->client->_guzzleClient->config['handler']->stack[0][0] = $serializer->unserialize($_SESSION['stack0']);
	$i->client->_guzzleClient->config['handler']->stack[1][0] = $serializer->unserialize($_SESSION['stack1']);
	$i->client->_guzzleClient->config['handler']->stack[2][0] = $serializer->unserialize($_SESSION['stack2']);
	$i->client->_guzzleClient->config['handler']->stack[3][0] = $serializer->unserialize($_SESSION['stack3']);
	// $i->client->_guzzleClient->config['handler']->cached= $serializer->unserialize($_SESSION['cached']);
}
if($debug)
{
	echo "<pre>";
	var_export($i);
	var_export($_SESSION);
	echo "</pre>";
	exit();
}