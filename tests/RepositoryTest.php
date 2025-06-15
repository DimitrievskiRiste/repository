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
        $model = new SimpleModel();
        $model->name = "Riste";
        $model->surname = "Dimitrievski";
        $model->email = "test@dimitrievski.dev";
        $repo->addOrUpdate($model,2);
        var_dump($repo->get());
        $this->assertSame(count($repo->get()), count($repo->get()) > 0);
    }
    public function testGettingData():void{
        $repo = new Repository();
        $items = $repo->get();
        var_dump($items);
        $this->assertSame(count($items), count($items) >= 0);
}
}