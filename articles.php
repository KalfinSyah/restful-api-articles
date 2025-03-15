<?php

require_once __DIR__ . '/data/ArticlesRepository.php';

header('Content-Type: application/json');

try {
    if ($_SERVER['REQUEST_METHOD'] === 'GET') {
        if (isset($_GET['id']) && count($_GET) === 1) {
            $jsonResponse = \json_encode(ArticlesRepository::getArticleById($_GET['id']));
            echo $jsonResponse;
        } elseif (empty($_GET)) {
            $jsonResponse = \json_encode(ArticlesRepository::getArticles());
            echo $jsonResponse;
        } else {
            CustomThrow::exception('Invalid parameter');
        }
    }
    
    
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        ArticlesRepository::addArticle(
            $_POST['title'],
            $_POST['link'],
            $_POST['year'],
            $_POST['author_id'] ?? null,
            $_POST['provider_id'] ?? null
        );

        $jsonResponse = \json_encode([
            'status'=> [
                'code'=> 200,
                'message'=> 'Success add new article'
            ],

            'data'=> $_POST
        ]);

        echo $jsonResponse;
    }

    if ($_SERVER['REQUEST_METHOD'] === 'PUT') {
        parse_str(file_get_contents("php://input"), $putData);

        ArticlesRepository::updateArticleById(
            $_GET['id'],
            $putData['title'] ?? null,
            $putData['link'] ?? null,
            $putData['year'] ?? null,
            $putData['author_id'] ?? null,
            $putData['provider_id'] ?? null
        );

        $jsonResponse = \json_encode([
            'status'=> [
                'code'=> 200,
                'message'=> 'Success update article'
            ],

            'data'=> $putData
        ]);

        echo $jsonResponse;
    }

    if ($_SERVER['REQUEST_METHOD'] === 'DELETE') {
        ArticlesRepository::deleteArticleById(
            $_GET['id']
        );

        $jsonResponse = \json_encode([
            'status'=> [
                'code'=> 200,
                'message'=> 'Success delete article with id ' . $_GET['id']
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
