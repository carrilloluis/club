<?php
/**
 * @author Luis Carrillo Gutiérrez
 * @version v1.1 - 2026 (v1.0 - 2015)
 */
 
declare(strict_types=1);

define('QUERYSTRING_KEYWORD', 'taskID');
define('STANDARD_HEADER', 'Content-Type: application/json; charset="UTF-8"');
define('DSN', 'sqlite:/tmp/data.db');
define('SOURCE_DATA', 'php://input');

global $HTTP_STATUS;
global $rs;

require_once 'ClassBC.php';

	$startTime = microtime(true);
	$startMemory = memory_get_usage();
	$request_ = strtoupper(filter_input(INPUT_SERVER, 'REQUEST_METHOD', FILTER_SANITIZE_ENCODED));

	try { 
		$connection2db_ = new PDO(DSN);
		$connection2db_->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	} catch(PDOException $error) {
		http_response_code(500);
		die(json_encode( array('error' => $error->getMessage() )));
	} catch(Exception $errorConnection) {
		http_response_code(500);
		die(json_encode( array('error' => $errorConnection->getMessage() )));
	}

	if ($request_ === 'GET')
	{
		$action2perform_ = @intval($_GET[QUERYSTRING_KEYWORD]) < 1 ? 1 : @intval($_GET[QUERYSTRING_KEYWORD]);
		switch ( $action2perform_ ) {
			case 1 : {
				$page_ = @intval($_GET['page_']) < 1 ? 1 : @intval($_GET['page_']);
				$numberOf_ = @intval($_GET['nitems_']) < 1 ? 100 : @intval($_GET['nitems_']);
				$id_ = @intval($_GET['requestedID']);
				if (!is_numeric($id_) || $id_ <= 0) {
					http_response_code(403);
					header(STANDARD_HEADER);
					die( json_encode([ 'error' => 'parameters error on ID requested [ERROR]' ]));
				}
				$rs = (new ClassBC($connection2db_))->getAdditionalPersonalData($id_, $page_, $numberOf_);
				$HTTP_STATUS = 200;
				break;
			}
			case 2 : {
				$id_ = @intval($_GET['requestedID']);
				if (!is_numeric($id_) || $id_ <= 0) {
					http_response_code(403);
					header(STANDARD_HEADER);
					die( json_encode([ 'error' => 'parameters erro on UNIQUE ID requested [ERROR]' ]));
				}
				$rs = (new ClassBC($connection2db_))->getUniquePersonalDataById($id_);
				$HTTP_STATUS = 200; 
				break;
			}
			case 3 : {
				$querystring2search_ = @strval(explode(' ', $_GET['querySTR'])[0]);
				if (strlen($querystring2search_) < 3) {
					http_response_code(403);
					header(STANDARD_HEADER);
					die( json_encode([ 'error' => 'ERROR on parameters to SEARCH' ]));
				}
				$rs = (new ClassBC($connection2db_))->getPersonalDataByValue($querystring2search_);
				$HTTP_STATUS = 200;
				break;
			}
			default : {
				break;
			}
		}
	}

	if ($request_ === 'POST')
	{
		$receivedData = json_decode(file_get_contents(SOURCE_DATA), true);
		if (json_last_error() != JSON_ERROR_NONE)
		{
			http_response_code(403);
			header(STANDARD_HEADER);
			die(json_encode([ 'error' => '[Error] on REGISTER ITEM' ]));
		}
		$action4post_ = !array_key_exists(QUERYSTRING_KEYWORD, $receivedData) ? 1 : @intval($receivedData[QUERYSTRING_KEYWORD]);

		switch ( $action4post_ ) {
			case 1 : {
				if (!(
					count($receivedData) >= 3 &&
					isset($receivedData['id_']) &&
					isset($receivedData['code_']) &&
					isset($receivedData['value_'])
				))
				{
					http_response_code(403);
					header(STANDARD_HEADER);
					die( json_encode([ 'error' => '[ERROR] on parameters onto NEW ITEM' ]));
				}
				$rs = (new ClassBC($connection2db_))->add($receivedData['id_'], $receivedData['code_'], $receivedData['value_']);
				$HTTP_STATUS = 201;
				break;
			}
			case 2 : {
				if (!(
					count($receivedData) >= 3 &&
					isset($receivedData['id_']) &&
					isset($receivedData['code_']) &&
					isset($receivedData['value_'])
				))
				{
					http_response_code(403);
					header(STANDARD_HEADER);
					die( json_encode([ 'error' => '[ERROR] on parameters updated ITEM' ]));
				}

				$rs = (new ClassBC($connection2db_))->update($receivedData['id_'], $receivedData['code_'], $receivedData['value_']);
				$HTTP_STATUS = 200;
				break;
			}
			case 3 : {
				if (!(
					count($receivedData) >= 1 &&
					isset($receivedData['id_'])
				))
				{
					http_response_code(403);
					header(STANDARD_HEADER);
					die( json_encode([ 'error' => '[ERROR] on disable ITEM' ]));
				}
				$rs = (new ClassBC($connection2db_))->disable($receivedData['id_']);
				$HTTP_STATUS = 202;
				break;
			}
			default : {
				break;
			}
		}
	}

	$connection2db_ = null;
	unset($connection2db_);

	http_response_code(isset($HTTP_STATUS) ? $HTTP_STATUS : 500);
	header('Access-Control-Allow-Origin: *');
	header('Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With, Origin');
	header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
	header('Access-Control-Allow-Credentials: true');
	header(STANDARD_HEADER);
	echo json_encode([
		'delay' => number_format(microtime(true) - $startTime, 4) . ' ms',
		'memory' => round((memory_get_usage() - $startMemory) / (1024 * 1024), 3) . ' MB',
		'data' => isset($rs) ? $rs : [],
	]);

