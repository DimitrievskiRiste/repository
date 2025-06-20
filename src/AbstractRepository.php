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
        foreach($items as $key => $item) {
            if($item instanceof Model){
                if($item->isFillable($column)){
                    if($item->$column == $value){
                        return true;
                    }
                }
            }
        }
        return false;
    }
    public function get()
    {
        return Cache::get($this->getKey(), []);
    }

    public function addOrUpdate(\Illuminate\Database\Eloquent\Model $model, int $ttl = 3600): void
    {
        $items = Cache::get($this->getKey(), []);
        if($this->itemExist($model->getKeyName(),$model->getKey())){
            array_walk($items, function(&$value, $key) use($model){
                if($value instanceof Model){
                    if($value->getKey() == $model->getKey()){
                        $value = $model;
                    }
                }
            });
            Cache::set($this->getKey(),$items,NOW()->addHours($ttl));
        } else {
            $key = sizeof($items)+1;
            array_push($items,[$key => $model]);
            Cache::set($this->getKey(),$items,NOW()->addHours($ttl));
        }

    }
    public function findWhere(string $column, string $needle)
    {
        $items = $this->get();
        if(sizeof($items) > 0 ){
            foreach($items as $key => $item){
                if($item instanceof Model) {
                    if(property_exists($item, $column) && $item->$column == $needle) {
                        return $item;
                    }
                }
            }
            return null;
        } else {
            return null;
        }
    }
}