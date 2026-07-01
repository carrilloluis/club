<?php
/**
 * @author Luis Carrillo Gutiérrez
 * @version v1.1 - 2026 (v1.0 - 2015)
 */

function
getAssociateById
(
    object $conn,
    int $pId_
)
{
    try {
      $conn->beginTransaction();
        $stmt = $conn->prepare("
            SELECT [i] AS id, [a] AS dc, [b] AS nm, [c] AS ap, [d] AS am
            FROM [ab] WHERE [i]=?
        ");
        $stmt->bindParam(1, $pId_, PDO::PARAM_INT);
        $stmt->execute();
      $conn->commit();
    } catch(PDOException $errByID1) {
        $conn->rollBack();
        http_response_code(500);
        die(json_encode([ 'error' => $errByID1->getMessage() ]));
    } catch(Exception $errByID2) {
        $conn->rollBack();
        http_response_code(500);
        die(json_encode([ 'error' => $errByID2->getMessage() ]));
    }
    $rs = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $sizeResultSet = count($rs);
    $stmt->closeCursor();
    return $sizeResultSet > 0 ? $rs : [];
}
