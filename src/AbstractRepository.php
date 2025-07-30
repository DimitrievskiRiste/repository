<?php
namespace Riste;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

/**
 * Class for model cache using build in laravel cache mechanism.
 * Since laravel eloquent automatically serializes and deserializes when setting them in cache,
 * there are no needs of serializing here.
 */
abstract class AbstractRepository implements IRepository
{
    public abstract function getKey(): string;
    public abstract function setKey(string $key): void;
    public function itemExist(string $column, string|int $value): bool
    {
        $items = $this->get();
        if(empty($items)){
            return false;
        }
        $modelColumn = array_column($items, $column);
        $key = array_search($value, $modelColumn);
        return array_key_exists($key, $items);
    }
    public function get()
    {
        return Cache::get($this->getKey(), []);
    }

    public function addOrUpdate(Model $model, int $ttl = 3600): void
    {
        $items = Cache::get($this->getKey(), []);
        $value = $model->getKey() ?? "test_";
        $primaryKeyName = $model->getKeyName();
        $keys = $this->findAllKeys([[$primaryKeyName => $value]]);
        foreach($keys as $key) {
            $items[$key] = $model;
        }
        Cache::set($this->getKey(), $items, NOW()->addDays(30));
    }
    public function findWhere(string $column, string $needle) :Model|null
    {
        $items = $this->get();
        if(!empty($items)){
            $modelColumn = array_column($items, $column);
            $key = array_search($needle, $modelColumn);
            if(array_key_exists($key, $items)){
                return $items[$key];
            }
            return null;
        }
        return null;
    }
    public function findMany(array $criteries): array
    {
        $items = [];
        $keys = $this->findAllKeys($criteries);
        foreach($keys as $key)
        {
            $items[] = $this->get()[$key];
        }
        return $items;
    }


    public function paginate(int $offset, int $limit, array $data =[]) :array {
        $items = [];
        if(empty($data)) {
            $collections = $this->get();
        } else {
            $collections = $data;
        }
        if(!empty($collections)){
            $calc = $limit + $offset;
            for($offset; $offset < $calc; $offset++)
            {
                if(array_key_exists($offset, $collections)){
                    $items[] = $collections[$offset];
                }
            }
            return $items;
        }
        return $items;
    }
    public function removeItem(string $column, string $needle): void
    {
        $items = $this->get();
        if(!empty($items)){
            $column = array_column($items, $column);
            $key = array_search($needle, $column);
            if(array_key_exists($key, $items)){
                unset($items[$key]);
                Cache::set($this->getKey(),$items, now()->addDays(30));
            }
        }
    }
    public function findAllKeys(array $criteria): array
    {
        $items = $this->get();
        $data = [];
        foreach($criteria as $keys) {
            foreach($keys as $column => $value) {
                $itemKey = array_find_key($items, function($item) use($column, $value) {
                   if($item instanceof Model){
                       return (stripos($item->$column, $value) !== false);
                   }
                   return null;
                });
                if(!is_null($itemKey)){
                    array_push($data);
                }
            }
        }
        return $data;
    }
}