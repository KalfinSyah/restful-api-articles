<?php

require_once __DIR__ . '/data/ArticlesRepository.php';

header('Content-Type: application/json');

try {
    if ($_SERVER['REQUEST_METHOD'] === 'GET') {
        if (isset($_GET['id']) && count($_GET) === 1) {
            $jsonResponse = \json_encode(ArticlesRepository::getProviderById($_GET['id']));
            echo $jsonResponse;
        } elseif (empty($_GET)) {
            $jsonResponse = \json_encode(ArticlesRepository::getProviders());
            echo $jsonResponse;
        } else {
            CustomThrow::exception('Invalid parameter');
        }
    }
    
    
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        ArticlesRepository::addProvider(
            $_POST['name'],
            $_POST['link'],
        );

        $jsonResponse = \json_encode([
            'status'=> [
                'code'=> 200,
                'message'=> 'Success add new provider'
            ],

            'data'=> $_POST
        ]);

        echo $jsonResponse;
    }

    if ($_SERVER['REQUEST_METHOD'] === 'PUT') {
        parse_str(file_get_contents("php://input"), $putData);

        ArticlesRepository::updateProviderById(
            $_GET['id'],
            $putData['name'] ?? null,
            $putData['link'] ?? null,
        );

        $jsonResponse = \json_encode([
            'status'=> [
                'code'=> 200,
                'message'=> 'Success update provider'
            ],

            'data'=> $putData
        ]);

        echo $jsonResponse;
    }

    if ($_SERVER['REQUEST_METHOD'] === 'DELETE') {
        ArticlesRepository::deleteProviderById(
            $_GET['id']
        );

        $jsonResponse = \json_encode([
            'status'=> [
                'code'=> 200,
                'message'=> 'Success delete provider with id ' . $_GET['id']
            ]
        ]);

        echo $jsonResponse;
    }
} catch (Exception $e) {
    echo json_encode([
        'error' => $e->getMessage()
    ]);
    
    exit;
}
