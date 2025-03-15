<?php

require_once __DIR__ . '/ArticlesDatabase.php';

class ArticlesRepository extends ArticlesDatabase {

    public static function getArticleById(int $id): array {
        $stmt = parent::connection()->prepare("
            SELECT articles.*, authors.name AS author_name, providers.name AS provider_name
            FROM articles
            LEFT JOIN authors ON articles.author_id = authors.id
            LEFT JOIN providers ON articles.provider_id = providers.id
            WHERE articles.id = ?
        ");
    
        CustomThrow::exceptionWithCondition(
            !$stmt, 
            "Error preparing statement: " . parent::connection()->error
        );
    
        $stmt->bind_param("i", $id);
        $stmt->execute();
    
        $result = $stmt->get_result();
    
        CustomThrow::exceptionWithCondition(
            !$result,
            "Error executing query: " . parent::connection()->error
        );
    
        $articles = [];
        while ($row = $result->fetch_assoc()) {
            $articles[] = $row;
        }
    
        $stmt->close();
        return $articles;
    }

    public static function getAuthorById(int $id): array {
        $stmt = parent::connection()->prepare("SELECT * FROM authors WHERE id = ?");
        
        CustomThrow::exceptionWithCondition(
            !$stmt, 
            "Error preparing statement: " . parent::connection()->error
        );
    
        $stmt->bind_param("i", $id);
        $stmt->execute();
    
        $result = $stmt->get_result();
    
        CustomThrow::exceptionWithCondition(
            !$result,
            "Error executing query: " . parent::connection()->error
        );
    
        $articles = [];
        while ($row = $result->fetch_assoc()) {
            $articles[] = $row;
        }
    
        $stmt->close();
        return $articles;
    }

    public static function getProviderById(int $id): array {
        $stmt = parent::connection()->prepare("SELECT * FROM providers WHERE id = ?");
        
        CustomThrow::exceptionWithCondition(
            !$stmt, 
            "Error preparing statement: " . parent::connection()->error
        );
    
        $stmt->bind_param("i", $id);
        $stmt->execute();
    
        $result = $stmt->get_result();
    
        CustomThrow::exceptionWithCondition(
            !$result,
            "Error executing query: " . parent::connection()->error
        );
    
        $articles = [];
        while ($row = $result->fetch_assoc()) {
            $articles[] = $row;
        }
    
        $stmt->close();
        return $articles;
    }

    public static function getArticles(): array {
        $result = parent::connection()->query("
            SELECT articles.*, authors.name AS author_name, providers.name AS provider_name
            FROM articles
            LEFT JOIN authors ON articles.author_id = authors.id
            LEFT JOIN providers ON articles.provider_id = providers.id
        ");

        CustomThrow::exceptionWithCondition(
            !$result,
            "Error executing query: " . parent::connection()->error
        );

        $articles = [];
        while ($row = $result->fetch_assoc()) {
            $articles[] = $row;
        }

        return $articles;
    }

    public static function getAuthors(): array {
        $result = parent::connection()->query("SELECT * FROM authors");

        CustomThrow::exceptionWithCondition(
            !$result,
            "Error executing query: " . parent::connection()->error
        );

        $authors = [];
        while ($row = $result->fetch_assoc()) {
            $authors[] = $row;
        }

        return $authors;
    }

    public static function getProviders(): array {
        $result = parent::connection()->query("SELECT * FROM providers");

        CustomThrow::exceptionWithCondition(
            !$result,
            "Error executing query: " . parent::connection()->error
        );

        $providers = [];
        while ($row = $result->fetch_assoc()) {
            $providers[] = $row;
        }

        return $providers;
    }

    public static function addArticle(
        string $title,
        string $link,
        string $year,
        ?int $author_id = null,
        ?int $provider_id = null
    ): void {
        $stmt = parent::connection()->prepare(
            "INSERT INTO articles (title, link, year, author_id, provider_id) VALUES (?, ?, ?, ?, ?)"
        );
    
        CustomThrow::exceptionWithCondition(
            !$stmt, 
            "Error preparing statement: " . parent::connection()->error
        );
    
        $stmt->bind_param("ssiii", $title, $link, $year, $author_id, $provider_id);
        
        CustomThrow::exceptionWithCondition(
            !$stmt->execute(),
            "Error executing statement: " . $stmt->error
        );
    
        $stmt->close();
    }

    public static function addAuthor(
        string $name,
        string $gender,
        int $age,
        string $country,
        string $email
    ): void {
        CustomThrow::exceptionWithCondition(
            strcasecmp($gender, "Male") !== 0 && 
            strcasecmp($gender, "Female") !== 0 && 
            strcasecmp($gender, "Other") !== 0,
            "Gender must be Male, Female, or Other"
        );

        $stmt = parent::connection()->prepare(
            "INSERT INTO authors (name, gender, age, country, email) VALUES (?, ?, ?, ?, ?)"
        );

        CustomThrow::exceptionWithCondition(
            !$stmt, 
            "Error preparing statement: " . parent::connection()->error
        );

        $stmt->bind_param("ssiss", $name, $gender, $age, $country, $email);
        
        CustomThrow::exceptionWithCondition(
            !$stmt->execute(),
            "Error executing statement: " . $stmt->error
        );

        $stmt->close();
    }

    public static function addProvider(
        string $name,
        string $link
    ): void {
        $stmt = parent::connection()->prepare(
            "INSERT INTO providers (name, link) VALUES (?, ?)"
        );
    
        CustomThrow::exceptionWithCondition(
            !$stmt, 
            "Error preparing statement: " . parent::connection()->error
        );
    
        $stmt->bind_param("ss", $name, $link);
        
        CustomThrow::exceptionWithCondition(
            !$stmt->execute(),
            "Error executing statement: " . $stmt->error
        );
    
        $stmt->close();
    }

    public static function updateArticleById(
        int $id,
        ?string $title = null,
        ?string $link = null,
        ?string $year = null,
        ?int $author_id = null,
        ?int $provider_id = null
    ): void {       
        $updateFields = [];
        $types = "";
        $values = [];
        
        if ($title !== null) {
            $updateFields[] = "title = ?";
            $types .= "s";
            $values[] = $title;
        }
        
        if ($link !== null) {
            $updateFields[] = "link = ?";
            $types .= "s";
            $values[] = $link;
        }
        
        if ($year !== null) {
            $updateFields[] = "year = ?";
            $types .= "s";
            $values[] = $year;
        }
        
        if ($author_id !== null) {
            $updateFields[] = "author_id = ?";
            $types .= "i";
            $values[] = $author_id;
        }
        
        if ($provider_id !== null) {
            $updateFields[] = "provider_id = ?";
            $types .= "i";
            $values[] = $provider_id;
        }

        $types .= "i";
        $values[] = $id;
        
        CustomThrow::exceptionWithCondition(
            empty($updateFields),
            "No fields to update"
        );

        $sql = "UPDATE articles SET " . implode(", ", $updateFields) . " WHERE id = ?";


        $stmt = parent::connection()->prepare($sql);

        CustomThrow::exceptionWithCondition(
            !$stmt, 
            "Error preparing statement: " . parent::connection()->error
        );

        $stmt->bind_param($types, ...$values);
    
        CustomThrow::exceptionWithCondition(
            !$stmt->execute(),
            "Error executing statement: " . $stmt->error
        );
        
        $stmt->close();
    }

    public static function updateAuthorById(
        int $id,
        ?string $name = null,
        ?string $gender = null,
        ?int $age = null,
        ?string $country = null,
        ?string $email = null
    ): void {       
        $updateFields = [];
        $types = "";
        $values = [];
        
        if ($name !== null) {
            $updateFields[] = "name = ?";
            $types .= "s";
            $values[] = $name;
        }
        
        if ($gender !== null) {
            $updateFields[] = "gender = ?";
            $types .= "s";
            $values[] = $gender;
        }
        
        if ($age !== null) {
            $updateFields[] = "age = ?";
            $types .= "i";
            $values[] = $age;
        }
        
        if ($country !== null) {
            $updateFields[] = "country = ?";
            $types .= "s";
            $values[] = $country;
        }
        
        if ($email !== null) {
            $updateFields[] = "email = ?";
            $types .= "s";
            $values[] = $email;
        }

        $types .= "i";
        $values[] = $id;
        
        CustomThrow::exceptionWithCondition(
            empty($updateFields),
            "No fields to update"
        );

        $sql = "UPDATE authors SET " . implode(", ", $updateFields) . " WHERE id = ?";


        $stmt = parent::connection()->prepare($sql);

        CustomThrow::exceptionWithCondition(
            !$stmt, 
            "Error preparing statement: " . parent::connection()->error
        );

        $stmt->bind_param($types, ...$values);
    
        CustomThrow::exceptionWithCondition(
            !$stmt->execute(),
            "Error executing statement: " . $stmt->error
        );
        
        $stmt->close();
    }

    public static function updateProviderById(
        int $id,
        ?string $name = null,
        ?string $link = null,
    ): void {       
        $updateFields = [];
        $types = "";
        $values = [];
        
        if ($name !== null) {
            $updateFields[] = "name = ?";
            $types .= "s";
            $values[] = $name;
        }
        
        if ($link !== null) {
            $updateFields[] = "link = ?";
            $types .= "s";
            $values[] = $link;
        }
    

        $types .= "i";
        $values[] = $id;
        
        CustomThrow::exceptionWithCondition(
            empty($updateFields),
            "No fields to update"
        );

        $sql = "UPDATE providers SET " . implode(", ", $updateFields) . " WHERE id = ?";

        $stmt = parent::connection()->prepare($sql);

        CustomThrow::exceptionWithCondition(
            !$stmt, 
            "Error preparing statement: " . parent::connection()->error
        );

        $stmt->bind_param($types, ...$values);
    
        CustomThrow::exceptionWithCondition(
            !$stmt->execute(),
            "Error executing statement: " . $stmt->error
        );
        
        $stmt->close();
    }

    public static function deleteArticleById(
        int $id,
    ): void {       

        $sql = "DELETE FROM articles WHERE id = ?";
        $stmt = parent::connection()->prepare($sql);

        CustomThrow::exceptionWithCondition(
            !$stmt, 
            "Error preparing statement: " . parent::connection()->error
        );

        $stmt->bind_param("i", $id);
    
        CustomThrow::exceptionWithCondition(
            !$stmt->execute(),
            "Error executing statement: " . $stmt->error
        );
        
        $stmt->close();
    }

    public static function deleteAuthorById(
        int $id,
    ): void {       

        $sql = "DELETE FROM authors WHERE id = ?";
        $stmt = parent::connection()->prepare($sql);

        CustomThrow::exceptionWithCondition(
            !$stmt, 
            "Error preparing statement: " . parent::connection()->error
        );

        $stmt->bind_param("i", $id);
    
        CustomThrow::exceptionWithCondition(
            !$stmt->execute(),
            "Error executing statement: " . $stmt->error
        );
        
        $stmt->close();
    }

    public static function deleteProviderById(
        int $id,
    ): void {       

        $sql = "DELETE FROM providers WHERE id = ?";
        $stmt = parent::connection()->prepare($sql);

        CustomThrow::exceptionWithCondition(
            !$stmt, 
            "Error preparing statement: " . parent::connection()->error
        );

        $stmt->bind_param("i", $id);
    
        CustomThrow::exceptionWithCondition(
            !$stmt->execute(),
            "Error executing statement: " . $stmt->error
        );
        
        $stmt->close();
    }
}