<?php
namespace Riste;
class Repository extends AbstractRepository
{
    protected string $key = "_users";
    public function getKey(): string
    {
        return $this->key;
    }
    public function setKey(string $key): void
    {
        $this->key = $key;
    }
}