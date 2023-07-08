<?php

class Dbh
{
    private ?PDO $connection = null;

    public function __construct()
    {
        throw new RuntimeException("Don't use this!!!");
    }

    public function connect()
    {
        if ($this->connection === null) {
            $this->buildConnection();
        }

        return $this->connection;
    }

    private function buildConnection()
    {
        $username = "root";
        $password = ""; //For default Xampp
        $this->connection = new PDO('mysql:host=localhost;dbname=pintzy_users', $username, $password);
        
    }

    private function prepareAndExecute(string $query, array $params): Results
    {
        $stmt = $this->connect()->prepare($query);

        return new Results($stmt, $params);
    }

    public function fetchOne(string $query, array $params)
    {
        $this->connect();

        return $this
            ->prepareAndExecute($query, $params)
            ->fetchOne();
    }

    public function fetchAssociative(string $query, array $params): ?array
    {
        $this->connect();

        return $this
            ->prepareAndExecute($query, $params)
            ->fetchAssociative();
    }

    
    public function fetchAll(string $query, array $params): ?array
    {
        return $this->prepareAndExecute($query, $params)
            ->fetchAll();
    }

}

class Results {

    private ?PDOStatement $statement = null;

    public function statement(): PDOStatement
    {
        return $this->statement;
    }

    public function __construct(PDOStatement $statement, array $params)
    {
        $this->statement = $statement;

        if (!$this->statement) {
            throw new RuntimeException("Results came back as false.");
        }

        $this->statement->execute($params);
    }

    public function __destruct() 
    {
        $this->closeCursor();
    }

    /**
     * Fetches a single record from the database
     * 
     * @var string $columnName
     */
    public function fetchOne(string $columName = null)
    {
         $results = $this->statement->fetch(PDO::FETCH_ASSOC) ?? [];

         if (isset($columName)) {
            if (!array_key_exists($columName, $results)) {
                throw new RuntimeException("Column $columName does not eixst in results");
            }

            return $results[$columName] ?? null;
         }

        return $results[array_keys($results)[0]] ?? null;
    }

    public function fetchAssociative(): ?array
    {
        return $this->statement->fetch(PDO::FETCH_ASSOC);
    }


    public function fetchAll(): ?array
    {
        return $this->statement()->fetchAll(PDO::FETCH_ASSOC);
    }

    public function closeCursor(): void
    {
        if (isset($this->statement)) {
            @$this->statement->closeCursor();
        }
    }
}
