<?php
namespace Riste\Tests;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use PHPUnit\Framework\TestCase;
use Riste\Repository;
use Riste\SimpleModel;

class RepositoryTest extends TestCase
{
    public function testAddCache():void{
        $repo = new Repository();
        $model = new User();
        $model->name = "Riste";
        $model->email = "test@dimitrievski.dev";
        var_dump($model->getKey());
        $repo->addOrUpdate($model,2);
        $items = $repo->get();
        var_dump($items);
        $this->assertSame(!empty($items), !empty($items));
    }
    public function testGettingData():void{
        $repo = new Repository();
        $items = $repo->get();
        var_dump($items);
        $this->assertSame(!empty($items), !empty($items));
    }
    public function testIfItemsAreModels():void{
        $repo = new Repository();
        $model = new User();
        $model->name = "Riste";
        $model->email = "test@dimitrievski.dev";
        $repo->addOrUpdate($model,2);
        $items = $repo->get();
        var_dump($items[0] instanceof Model);
        $this->assertSame(($items[0] instanceof Model), true, "Items are valid eloquent model");
    }
    public function testFindManyFunction():void
    {
        $repo = new Repository();
        $model = new User();
        $model->name = "Riste Dim";
        $model->email = "abcd";
        $model2 = new User();
        $model2->name = "Riste Iliev";
        $model2->email = "test";
        $repo->addOrUpdate($model, 2);
        $repo->addOrUpdate($model2,2);
        $items = $repo->findMany(["name" => "Riste"]);
        var_dump($items);
        $this->assertSame(sizeof($items), 2, "Repo has found 2 data from cache using findMany");
    }
}