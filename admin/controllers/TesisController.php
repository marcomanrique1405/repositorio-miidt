<?php
declare(strict_types=1);

require_once __DIR__ . '/../models/Tesis.php';

final class TesisController
{
    public function buscar(): void
    {
        header('Content-Type: application/json; charset=utf-8');

        $query = $_GET['q'] ?? '';

        $model = new Tesis();
        $tesis = $model->buscar($query);

        echo json_encode($tesis, JSON_UNESCAPED_UNICODE);
        exit;
    }
}