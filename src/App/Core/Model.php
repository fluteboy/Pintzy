<?php

namespace App\Core;
use App\Foundation;
use InvalidArgumentException;

use App\Exceptions\ModelDestroyedException;
use RuntimeException;

/**
 * This extremely rudamentary model class provides a wrapper around database tables and allows
 * for business logic to be placed in a logical location (the model or a service class) rather than
 * copied and pasted throughout the code.
 * 
 * It also provides wrappers for adding, updating and deleting instances of models as needed without
 * neediing to write actual database queries and parse data.
 * 
 * This does not currently support specific data types from the database, nor does it support relationships
 * or collections of models. This is simply an example of a very simple model class that can be extended as
 * neeeded.
 * 
 */
abstract class Model {

    /**
     * The name of the table that the model connects to in the database
     */
    protected string $table;

    /**
     * The name of the primary key column. Override this in the model class if the column name is not 'ID'
     */
    protected string $primaryKeyName = "id";

    /**
     * The representation of each database column. The ID is not stored here and is stored below in
     * 'primaryKey'
     */
    private array $elements = [];

    /**
     * The primary key for the object. This will be NULL if the model is new and has not been saved
     */
    private int|null $primaryKey;

    /**
     * When model elements are updated, they are stored here. This is then flushed once the data is written to the
     * database
     */
    private array $updatedElements = [];

    /**
     * Indicates that the model needs saving
     */
    private bool $requiresSaving = true;

    /**
     * If the model is brand new (isn't being loaded from the database), this will be set to true
     */
    private bool $isNew = true;

    /**
     * Used to indicate that the model is being fetched from the database
     */
    private bool $initialLoad = false;

    /**
     * When the model is destroyed, this element will prevent any changes from being made to i
     */
    private bool $isDestroyed = false;


    /**
     * Sets a model element value.
     * 
     * @warning - This does not verify that the element (column name) in the database is valid, and will only
     * be known when 'save' is called and the record is written to the database.
     */
    public function __set(string $key, $value)
    {
        $originalValue = $this->elements[$key] ?? null;

        $this->elements[$key] = $value;

        $this->verifyNotDestroyed();

        // if we are loading the model from the database, we don't need to populate th changed elements
        if ($this->initialLoad) {
            return;
        }

        if ($value !== $originalValue) {
            $this->requiresSaving = true;
        }

        $this->updatedElements[$key] = $value;
    }

    /**
     * Retrieves a model element from the model.
     */
    public function __get(string $key)
    {
        $this->verifyNotDestroyed();

        if (!array_key_exists($key, $this->elements)) {
            throw new InvalidArgumentException("Invalid Model Element $key in model " . get_called_class() );
        }

        return $this->elements[$key];
    }

    public function __construct()
    {
        // you can add more methods here to the constructor
    }

    public function __destruct()
    {
        // add methods to your destructor here (ie what happens when the variable referencing the model goes out of scope)

        if ($this->requiresSaving()) {
            throw new RuntimeException("Model is now going out of scope, but requires saving");
        }
    }

    /**
     * Fetches the priamry key if it is set. Return return NULL if it is a new model
     */
    public function primaryKey(): ?int
    {
        return $this->isNew
            ? null
            : $this->primaryKey;
    }

    /**
     * The name of the primary column
     */
    public function primaryKeyName(): string
    {
        return $this->primaryKeyName;
    }

    /**
     * Returns true if the model has been changed and requires saving
     */
    public function requiresSaving(): bool
    {
        return $this->requiresSaving;
    }

    /**
     * Instantiates an instance of the model based on the called class. This allows for
     * concrete implementations of this class to use methods such as 'find' or 'findById'
     */
    private static function newModel(): ?static
    {
        $class = get_called_class();

        /** @var Model $model */
        return new $class;
    }
    
    /**
     * Attempts to load a model based on simple database parameters
     */
    public static function find(array $params): ?static
    {
        return self::newModel()->load($params);
    }

    /**
     * Attempts to load a model based on its primary key
     */
    public static function findById(int $id): ?static
    {
        $model = self::newModel();

        return $model->load([$model->primaryKeyName() => $id]);
    }

    /**
     * Loads the model from the database based on the parameters and assigns the database results
     * to model elements
     */
    private function load(array $params): ?static
    {
        $databaseResults = $this->fetchFromDatabase($params);

        if (empty($databaseResults)) {
            return null;
        }

        $this->requiresSaving = false;
        $this->isNew = false;
        $this->initialLoad = true;

        foreach ($databaseResults as $elementName => $value)
        {
            if ($elementName === $this->primaryKeyName()) {
                $this->primaryKey = (int) $value;
                continue;
            }

            $this->$elementName = $value;
        }

        $this->initialLoad = false;

        return $this;
    }

    /**
     * Searches for the model in the database. This will only return a single model instance
     */
    private function fetchFromDatabase(array $parameters): ?array
    {
        $query = $this->buildQuery($parameters);

        return Foundation::db()->fetchAssociative($query, $parameters);
    }

    /**
     * Builds the basic query to fetch the model data from the database
     */
    private function buildQuery(array $parameters): string
    {
        if (count($parameters) === 0) {
            throw new InvalidArgumentException("Must have at least one parameter");
        }

        $query = "select * from " . $this->table;

        
        $columns = [];

        foreach ($parameters as $key => $value)
        {
            $columns[] = "$key = :$key";
        }

        $query .= " where " . join(" and ", $columns);

        return $query;
    }

    /**
     * Ensures that the model is not destroyed.
     * 
     * Destroyed models cannot be updated.
     */
    private function verifyNotDestroyed(): void
    {
        if ($this->isDestroyed) {
            throw new ModelDestroyedException();
        }
    }

    /**
     * Saves the model to the database
     */
    public function save(bool $autoCommit = true): void
    {
        $this->verifyNotDestroyed();

        if (empty($this->updatedElements)) {
            return;
        }

        if ($this->isNew) {
            $this->createNewModel();
        } else {
            $this->updateModel();
        }

        if ($autoCommit) {
            $this->commit();
        }

        $this->requiresSaving = false;

        $this->updatedElements = [];
    }

    /**
     * Updates the data in an existing model
     */
    private function updateModel(): void
    {
        Foundation::db()->update($this->table, $this->updatedElements, [$this->primaryKeyName() => $this->primaryKey]);
    }

    /**
     * Inserts a new instance of a model into the database
     */
    private function createNewModel(): void
    {
        $this->primaryKey = Foundation::db()->insert($this->table, $this->elements);

        $this->isNew = false;
    }

    /**
     * Destroys a model, removing it from the database.
     * The model will still exist as a local variable
     */
    public function destroy(bool $autoCommit = true): void
    {
        if (!isset($this->primaryKey)) {
            throw new InvalidArgumentException("Model has not been saved or has already been deleted");
        }

        // delete from the database
        Foundation::db()->delete($this->table, [$this->primaryKeyName() => $this->primaryKey]);

        if ($autoCommit) {
            $this->commit();
        }

        // remove the model values
        foreach ($this->elements as $element => $value) {
            unset($this->elements[$element]);
        }

        unset($this->primaryKey);

        // set is Destroyed so that no further changes can be made to the model
        $this->isDestroyed = true;
    }

    public function commit(): void
    {
        Foundation::db()->commit();
    }

}