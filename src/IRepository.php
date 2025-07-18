<?php
namespace Riste;
use Illuminate\Database\Eloquent\Model;

interface IRepository
{
    /**
     * Get the key from repository cache
     * @return string
     */
    public function getKey():string;
    /**
     * Set the key for repository cache
     * @param string $key
     * @return void
     */
    public function setKey(string $key) :void;
    /**
     * Add item to the repository's cache
     * @param \Illuminate\Database\Eloquent\Model $model
     * @param int $ttl
     * @return void
     */
    public function addOrUpdate(\Illuminate\Database\Eloquent\Model $model, int $ttl = 3600) :void;

    /**
     * Retrieve item data from cache by specified criteria
     * @param string $column
     * @param string $needle
     * @return Model|null
     *
     */
    public function findWhere(string $column, string $needle);

    /**
     * Get all items from the repository's cache
     *
     */
    public function get();

    /**
     * Return boolean true if item exists in cache, false otherwise
     * @param string $column
     * @param string|int $value
     * @return bool
     */
    public function itemExist(string $column, string|int $value) :bool;

    /**
     * Find many items from cache by given criteria.
     * Example: ["username" => "test", "name" => "Riste"]
     * @param array $criteries
     * @return array
     */
    public function findMany(array $criteries) :array;
    /**
     * Paginate items from stored cache
     * @param int $offset
     * @param int $limit
     * @return array
     */
    public function paginate(int $offset, int $limit) :array;

    /**
     * Find item in cache by column and needed value and removes it from the cache!
     * @param string $column
     * @param string $needle
     * @return void
     */
    public function removeItem(string $column, string $needle) :void;

    /**
     * Find all keys by the given array with column and values
     * Example would be  ["username" => "test"] column will be username and value test.
     * @param array $criteria
     * @return array
     */
    public function findAllKeys(array $criteria) :array;
}