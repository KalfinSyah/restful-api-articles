<?php

require_once __DIR__ . '/data/ArticlesRepository.php';

header('Content-Type: application/json');

try {
    if ($_SERVER['REQUEST_METHOD'] === 'GET') {
        if (isset($_GET['id']) && count($_GET) === 1) {
            $jsonResponse = \json_encode(ArticlesRepository::getAuthorById($_GET['id']));
            echo $jsonResponse;
        } elseif (empty($_GET)) {
            $jsonResponse = \json_encode(ArticlesRepository::getAuthors());
            echo $jsonResponse;
        } else {
            CustomThrow::exception('Invalid parameter');
        }
    }
    
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        ArticlesRepository::addAuthor(
            $_POST['name'],
            $_POST['gender'],
            $_POST['age'],
            $_POST['country'],
            $_POST['email'],
        );

        $jsonResponse = \json_encode([
            'status'=> [
                'code'=> 200,
                'message'=> 'Success add new author'
            ],

            'data'=> $_POST
        ]);

        echo $jsonResponse;
    }

    if ($_SERVER['REQUEST_METHOD'] === 'PUT') {
        parse_str(file_get_contents("php://input"), $putData);

        ArticlesRepository::updateAuthorById(
            $_GET['id'],
            $putData['name'] ?? null,
            $putData['gender'] ?? null,
            $putData['age'] ?? null,
            $putData['country'] ?? null,
            $putData['email'] ?? null
        );

        $jsonResponse = \json_encode([
            'status'=> [
                'code'=> 200,
                'message'=> 'Success update author'
            ],

            'data'=> $putData
        ]);

        echo $jsonResponse;
    }

    if ($_SERVER['REQUEST_METHOD'] === 'DELETE') {
        ArticlesRepository::deleteAuthorById(
            $_GET['id']
        );

        $jsonResponse = \json_encode([
            'status'=> [
                'code'=> 200,
                'message'=> 'Success delete author with id ' . $_GET['id']
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
