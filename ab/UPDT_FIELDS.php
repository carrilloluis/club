<?php
/**
 * @author Luis Carrillo Gutiérrez
 * @version v1.1 - 2026 (v1.0 - 2015)
 */

function
updtAssociate
(
    object $conn_,
    string $pId_,
    string $pDoc_,
    string $pFirstname_,
    string $pLastname_,
    string $pMLastname_
)
{
    try {
        $conn_->beginTransaction();
            $stmt = $conn_->prepare("
                UPDATE [ab] SET [a]=?, [b]=?, [c]=?, [d]=? WHERE [i]=?
            ");
            $stmt->bindParam(1, $pDoc_, PDO::PARAM_STR);
            $stmt->bindParam(2, $pFirstname_, PDO::PARAM_STR);
            $stmt->bindParam(3, $pLastname_, PDO::PARAM_STR);
            $stmt->bindParam(4, $pMLastname_, PDO::PARAM_STR);
            $stmt->bindParam(5, $pId_, PDO::PARAM_INT);
            $stmt->execute();
        $conn_->commit();
    } catch(PDOException $errorQuery1) {
        $conn_->rollBack();
        http_response_code(500);
        die(json_encode([ 'error' => $errorQuery1->getMessage() ]));
    } catch(Exception $errorQuery2) {
        $conn_->rollBack();
        http_response_code(500);
        die(json_encode([ 'error' => $errorQuery2->getMessage() ]));
    }
    $stmt->closeCursor();
    return [];
}

