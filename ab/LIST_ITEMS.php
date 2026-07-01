<?php
/**
 * @author Luis Carrillo Gutiérrez
 * @version v1.1 - 2026 (v1.0 - 2015)
 */

function
getAssociates
(
    object $conn,
    int $pPage_,
    int $pNumberOf_
)
{
    try {
        $conn->beginTransaction();
            $stmt = $conn->prepare("
                SELECT [i] AS id, [a] AS dc, [b] AS nm, [c] AS ap, [d] AS am, [e] AS cd, [f] AS st
                FROM [ab] ORDER BY [c] LIMIT ? OFFSET ?
            ");
            $stmt->bindParam(1, $pNumberOf_, PDO::PARAM_INT);
            $offset = @intval(($pPage_ - 1) * $pNumberOf_);
            $stmt->bindParam(2, $offset, PDO::PARAM_INT);
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
    $rs = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $sizeResultSet = count($rs);
    $stmt->closeCursor();
    return $sizeResultSet > 0 ? $rs : [];
}
