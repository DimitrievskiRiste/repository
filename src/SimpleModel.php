<?php
namespace Riste;
use Illuminate\Database\Eloquent\Model;

/**
 * Class for testing purposes only
 */
class SimpleModel extends Model
{
    protected $fillable = ["name","surname","email"];
}