<?php

namespace App\Core;
use InvalidArgumentException;
use PDO;

/**
 * The Database class provides a wrapper around the raw PDO library and allows for quick and easy
 * execution of SELECT, INSERT, UPDATE and DELETE queries.
 * 
 * Unless using one of the 'fetch' methods, the system will return an instance of Results
 * 
 */
class Database {


    private ?PDO $connection = null;

    /**
     * Returns a single instance of the database connection. As much as possible, this will
     * attempt to only ever have a single connection to the database.
     */
    public function connect()
    {
        if ($this->connection === null) {
            $this->buildConnection();
        }

        return $this->connection;
    }

    /**
     * Builds the connection to the database
     * TODO - store credentials for the database in an ini file and not directly in code
     */
    private function buildConnection()
    {
        $username = "root";
        $password = "password"; //For default Xampp
        $this->connection = new PDO('mysql:host=localhost;dbname=pintzy_users', $username, $password);
    }

    /**
     * Commits any uncommitted changes to the database
     */
    public function commit(): void
    {
        $this->connect()->commit();
    }

    /**
     * Rolls back the active transaction. Any data not already committed but has changed in the
     * transaction is lost
     */
    public function rollback(): void
    {
        $this->connect()->rollBack();
    }


    /**
     * This is used when inserting data. This will return the last ID that was generated for the table
     * against which data was inserted.
     * 
     * @return int|null
     */
    public function lastId(): ?int
    {
        $id = $this->connect()->lastInsertId();

        if ($id === false) {
            return null;
        }

        return (int) $id;
    }

    /**
     * Check to see if there is an active database transaction. This will occur
     * when an insert query is performed and not committed
     * 
     * @return bool
     */
    public function hasActiveTransaction(): bool
    {
        return $this->connect()->inTransaction();
    }

    /**
     * Closes the database connection.
     */
    public function closeConnection(): void
    {
        if ($this->hasActiveTransaction()) {
            $this->rollback();
        }

        $this->connection = null;
    }

    /**
     * Builds a connection to the database and prepares to execute the query.
     * 
     * @param string - query to run
     * @param array query parameters
     * 
     * @return Results
     */
    private function prepareAndExecute(string $query, array $params): Results
    {
        $db = $this->connect();

        if (!$db->inTransaction()) {
            $db->beginTransaction();
        }

        $stmt = $db->prepare($query);

        return new Results($stmt, $params);
    }

    /**
     * Publically accessible shortcut method for prepareAndExecute.
     * Use this for if performing custom INSERT or UPDATE queries.
     * 
     * @param string - query to run
     * @param array query parameters
     * 
     * @return Results
     */
    public function execute(string $query, array $params = []): Results
    {
        return $this->prepareAndExecute($query, $params);
    }

    /**
     * Fetches a single row from the first column from the database
     * 
     * @param string - query to run
     * @param array query parameters
     * 
     * @return mixed|null
     */
    public function fetchOne(string $query, array $params = [])
    {
        $this->connect();

        return $this
            ->prepareAndExecute($query, $params)
            ->fetchOne();
    }

    /**
     * Fetches a single row as an associative array out of the database
     * 
     * @param string - query to run
     * @param array query parameters
     */
    public function fetchAssociative(string $query, array $params = []): ?array
    {
        $this->connect();

        return $this
            ->prepareAndExecute($query, $params)
            ->fetchAssociative();
    }

    /**
     * Fetches all as an array of associative arrays
     * 
     * @param string - query to run
     * @param array query parameters
     * 
     * @return array|null - null if no records are found.
     */
    public function fetchAll(string $query, array $params = []): ?array
    {
        return $this->prepareAndExecute($query, $params)
            ->fetchAll();
    }


    /**
     * Builds an insert into the database
     * 
     * @param string - query to run
     * @param array query parameters
     * 
     * @return int - priamry key for the inserted row
     */
    public function insert(string $tableName, array $params, bool $autoCommit = false): ?int
    {
        if (!count($params)) {
            throw new InvalidArgumentException("Params must not be emtpy");
        }


        $this->connect();

        $keysArray = [];
        foreach ($params as $key => $value)
        {
            $keysArray[] = ":" . $key;
        }

        $query = "insert into $tableName (" 
                . join(",", array_keys($params)) . ")" 
                . " values (" . join(", ", $keysArray) . ")";

        $this->execute($query, $params);

        if ($autoCommit) {
            $this->commit();
        }

        return $this->lastId();
    }


    /**
     * Updates one or more rows in the database
     * @param string $tableName
     * @param array $newColumnValues - the values to update TO
     * @param array $params - the records to search for to update
     * @param bool $autoCommit - commit once done or not
     * 
     * @return int|null - row count of rows affected.
     */
    public function update(string $tableName, array $newColumnValues, array $params, bool $autoCommit = false): ?int
    {
        $updateColumnString = $this->buildClauseFromParameters($newColumnValues, "u");
        $whereString = $this->buildClauseFromParameters($params);

        $allParamaters = $params;
        foreach ($newColumnValues as $updateColumn => $updateValue)
        {
            $allParamaters["u_" . $updateColumn] = $updateValue;
        }

        if (!count($allParamaters)) {
            throw new InvalidArgumentException("Params must not be emtpy");
        }

        $query = "update " . $tableName . " set " . $updateColumnString . " where " . $whereString;

        $results = $this->execute($query, $allParamaters);

        if ($autoCommit) {
            $this->commit();
        }

        return $results->rowCount();
    }

    /**
     * Deletes one or more rows in the database
     * 
     * @param string $tableName
     * @param array $params - the records to search for to delete
     * @param bool $autoCommit - commit once done or not
     */
    public function delete(string $tableName, array $params, bool $autoCommit = false): ?int
    {
        if (!count($params)) {
            throw new InvalidArgumentException("Params must not be emtpy");
        }

        $query = "delete from $tableName where " 
                . $this->buildClauseFromParameters($params);

        $this->connect();

        $results = $this->execute($query, $params);

        if ($autoCommit) {
            $this->commit();
        }

        return $results->rowCount();
    }


    /**
     * Builds a string of database parameters based on the provided params
     * 
     * ["param1" => "value1", "param2" => "value2"] becomes:
     * 
     * ":param1, :param2"
     * 
     * @return string
     * 
     */
    private function buildClauseFromParameters(array $params, ?string $column_prefix = null): string
    {
        $columnArray = [];

        foreach ($params as $key => $value)
        {
            $parameterName = (!empty($column_prefix) ? $column_prefix . "_" : "") . $key;

            $columnArray[] = "$key = :$parameterName";
        }

        return join(", ", $columnArray);
    }

}