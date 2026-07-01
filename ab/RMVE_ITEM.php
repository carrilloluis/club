<?php
/**
 * @author Luis Carrillo Gutiérrez
 * @version v1.1 - 2026 (v1.0 - 2015)
 */
function
disableAssociate
(
    object $conn,
    string $pId_
)
{
    try {
        $conn->beginTransaction();
            $stmt = $conn->prepare("
                UPDATE [ab] SET [f] = 0
                WHERE [i] = ? AND [f]=1
            ");
            $stmt->bindParam(1, $pId_, PDO::PARAM_INT);
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

