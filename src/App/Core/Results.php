<?php

namespace App\Core;
use Exception;
use PDO;
use PDOStatement;
use RuntimeException;


/**
 * The Results class is part of the Database class, and provides an easy way of fetching results from a query.
 * 
 * This class is also responsible for closing the database cursor as needed.
 */
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

    /**
     * Returns the raw row count from the database based on the query executed
     */
    public function rowCount(): ?int
    {
        $rowCount = $this->statement()->rowCount();
        
        if ($rowCount === false)
        {
            return null;
        }

        return (int) $rowCount;
    }

    /**
     * Ensures that, once the individual instance of the Results class goes out of scope, that the
     * cursor to the database is closed. This prevents resources on the database being tied up
     * unnecessarily
     */
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
         $results = $this->fetchAssociative();
         if ($results === false) {
            $results = [];
         }

         if (isset($columName)) {
            if (!array_key_exists($columName, $results)) {
                throw new RuntimeException("Column $columName does not eixst in results");
            }

            return $results[$columName] ?? null;
         }

         return count($results) ? $results[array_keys($results)[0]] : null;
    }

    /**
     * Fetches a single row out of the database as an associative array
     */
    public function fetchAssociative(): ?array
    {
        return $this->runQuery("fetch");
    }


    /**
     * Fetches the entire resultset (all rows) based on a query as an array of associative arrays
     */
    public function fetchAll(): ?array
    {
        return $this->runQuery("fetchAll");
    }

    /**
     * Performs the basic fetch.
     */
    private function runQuery(string $functionName): ?array
    {
    
        $value = $this->statement()->$functionName(PDO::FETCH_ASSOC);

        return is_array($value)
        ? $value
        : null;
    }

    public function closeCursor(): void
    {
        if (isset($this->statement)) {
            @$this->statement->closeCursor();
        }
    }
}
