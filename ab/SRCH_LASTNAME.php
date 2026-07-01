<?php
/**
 * @author Luis Carrillo Gutiérrez
 * @version v1.1 - 2026 (v1.0 - 2015)
 */

function
getAssociatePerLastname
(
    object $conn,
    string $queryString_
)
{
    try {
        $conn->beginTransaction();
            $stmt = $conn->prepare("
                SELECT [i] AS id, [a] AS dc, [b] AS nm, [c] AS ap, [d] AS am
                FROM [ab] WHERE [c] LIKE CONCAT('%',?,'%') ORDER BY [b]
            ");
            $stmt->bindParam(1, $queryString_, PDO::PARAM_STR);
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
    $rs_ = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $sizeResultSet = count($rs_);
    $stmt->closeCursor();
    return $sizeResultSet > 0 ? $rs_ : [];
}
