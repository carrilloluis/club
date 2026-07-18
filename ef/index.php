<?php
/**
 * @author Luis Carrillo Gutiérrez
 * @version v1.1 - 2026 (v1.0 - 2015)
 */

declare(strict_types=1);

define('OPTION_ON_QUERYSTRING', 'taskID');
define('OPTION_ON_BODY', 'taskID');
define('KEY_ON_QUERYSTRING', 'ID_');
define('PAGE_REFERENCE_ON_PAGINATION', 'page_');
define('ITEMS_QUANTITY_ON_PAGINATION', 'nitems_');
define('STANDARD_HEADER', 'Content-Type: application/json; charset="UTF-8"');
define('DSN', 'sqlite:/tmp/data.db');
define('SOURCE_DATA', 'php://input');

global $HTTP_STATUS;
global $rs;

require_once 'ClassEF.php';

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
        $action2perform_ = @intval($_GET[OPTION_ON_QUERYSTRING]) < 1 ? 0 : @intval($_GET[OPTION_ON_QUERYSTRING]);
        switch ( $action2perform_ ) {
            case 1 : {
                $page_ = @intval($_GET[PAGE_REFERENCE_ON_PAGINATION]) < 1 ? 1 : @intval($_GET[PAGE_REFERENCE_ON_PAGINATION]);
                $numberOf_ = @intval($_GET[ITEMS_QUANTITY_ON_PAGINATION]) < 1 ? 10 : @intval($_GET[ITEMS_QUANTITY_ON_PAGINATION]);
				$id_ = @intval($_GET[KEY_ON_QUERYSTRING]);
				if (!is_numeric($id_) || $id_ <= 0) {
					http_response_code(403); # FORBIDDEN
					header(STANDARD_HEADER);
					die( json_encode([ 'error' => 'parameters error on ID requested [ERROR]' ]));
				}
                $rs = (new ClassEF($connection2db_))->list($id_, $page_, $numberOf_);
                $HTTP_STATUS = 200; # OK
                break;
            }
            default : {
				http_response_code(405);
				header(STANDARD_HEADER);
				die(json_encode([ 'error' => '[Error] no operation requested' ]));
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
		$action4post_ = !array_key_exists(OPTION_ON_BODY, $receivedData) ? 0 : (@intval($receivedData[OPTION_ON_BODY])  < 1 ? 0 : @intval($receivedData[OPTION_ON_BODY]));

		switch ( $action4post_ ) {
			case 1 : {
				if (!(
					count($receivedData) >= 10 &&
					isset($receivedData['id_']) &&
					isset($receivedData['cd_']) &&
					isset($receivedData['p1_']) &&
					isset($receivedData['p2_']) &&
					isset($receivedData['p3_']) &&
					isset($receivedData['p4_']) &&
					isset($receivedData['p5_']) &&
					isset($receivedData['p6_']) &&
					isset($receivedData['p7_']) &&
					isset($receivedData['p8_']) 

					isset($receivedData['cd_']) 
				))
				{
					http_response_code(403);
					header(STANDARD_HEADER);
					die( json_encode([ 'error' => '[ERROR] on parameters onto NEW ITEM' ]));
				}
				$rs = (new ClassEF($connection2db_))->add($receivedData);
				$HTTP_STATUS = 201; # CREATED
				break;
			}
			default : {
				http_response_code(405);
				header(STANDARD_HEADER);
				die(json_encode([ 'error' => '[Error] no operation requested' ]));
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

