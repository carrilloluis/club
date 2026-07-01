<?php
/**
 * @author Luis Carrillo Gutiérrez
 * @version v1.1 - 2026 (v1.0 - 2015)
 */

function
addAssociate
(
    object $conn,
    string $pDoc_,
    string $pFirstname_,
    string $pLastname_,
    string $pMLastname_
)
{
    try {
        $conn->beginTransaction();
            $stmt = $conn->prepare("
                INSERT INTO [ab]
                VALUES (NULL, ?, ?, ?, ?, 1, 1)
            ");
            $stmt->bindParam(1, $pDoc_, PDO::PARAM_STR);
            $stmt->bindParam(2, $pFirstname_, PDO::PARAM_STR);
            $stmt->bindParam(3, $pLastname_, PDO::PARAM_STR);
            $stmt->bindParam(4, $pMLastname_, PDO::PARAM_STR);

            $stmt->execute();
        $conn->commit();
    } catch(PDOException $errorQuery1) {
        $conn->rollBack();
        http_response_code(500);
        die(json_encode([ 'error' => $errorQuery1->getMessage() ]));
    } catch(Exception $errorQuery2) {
        $conn->rollBack();
        http_response_code(500);
        die(json_encode([ 'error' => $errorQuery2->getMessage() ]));
    }
    $stmt->closeCursor();
    return [];
}

