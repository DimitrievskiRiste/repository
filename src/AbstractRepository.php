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
        $modelColumn = array_column($column, $items);
        $key = array_search($value, $modelColumn);
        return array_key_exists($key, $items);
    }
    public function get()
    {
        return Cache::get($this->getKey(), []);
    }

    public function addOrUpdate(\Illuminate\Database\Eloquent\Model $model, int $ttl = 3600): void
    {
        $items = Cache::get($this->getKey(), []);
        $value = $model->getKey() ?? "test_";
        if($this->itemExist($model->getKeyName(),$value)){
            array_walk($items, function(&$value, $key) use($model){
                if($value instanceof Model){
                    if($value->getKey() == $model->getKey()){
                        $value = $model;
                    }
                }
            });
            Cache::set($this->getKey(),$items,NOW()->addHours($ttl));
        } else {
            array_push($items,$model);
            Cache::set($this->getKey(),$items,NOW()->addHours($ttl));
        }

    }
    public function findWhere(string $column, string $needle) :Model|null
    {
        $items = $this->get();
        if(!empty($items)){
            $modelColumn = array_column($items, $column);
            $key = array_search($needle, $modelColumn);
            if(array_key_exists($key)){
                return $items[$key];
            }
            return null;
        }
        return null;
    }
    public function findMany(array $criteries): array
    {
        $items = [];
        $collections = $this->get();
        if(!empty($collections)){
            foreach ($criteries as $key => $value) {
                $column = array_column($key, $collections);
                $key = array_search($value,$column);
                if(array_key_exists($collections)){
                    array_push($items,$collections[$key]);
                }
            }
            return $items;
        }
        return $items;
    }


    public function paginate(int $offset, int $limit) :array {
        $items = [];
        $collections = $this->get();
        if(!empty($collections)){
            $calc = $limit + $offset;
            for($offset; $offset < $calc; $offset++)
            {
                if(array_key_exists($offset, $collections)){
                    array_push($items, $collections[$offset]);
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
}