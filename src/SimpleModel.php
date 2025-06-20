<?php
namespace Riste;
use Illuminate\Database\Eloquent\Model;

/**
 * Class for testing purposes only
 */
class SimpleModel extends Model
{
    protected $primaryKey = 'id';
    protected $fillable = ["name","surname","email"];
}