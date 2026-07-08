<?php
/**
 * @author Luis Carrillo Gutiérrez
 * @version v1.1 - 2026 (v1.0 - 2015)
 */

define('QUERYSTRING_KEYWORD', 'taskID');
define('STANDARD_HEADER', 'Content-Type: application/json; charset="UTF-8"');
define('DSN', 'sqlite:/tmp/data');
define('SOURCE_DATA', 'php://input');

global $HTTP_STATUS;
global $rs;

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

                # LIST_ITEMS
                require_once '49e45a3e-81e4-34bb-d28a-7c3f4e4d4c0f.php';
                $rs = getRelatives($connection2db_, $page_, $numberOf_);
                $HTTP_STATUS = 200;
                break;
            }
            case 2 : {
                # RETRIEVE_BY_ID == GET ASSOCIATE
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

