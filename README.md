INSTALLATION:
 composer require riste/repository:dev-main

Caching models, example:

class UserRepository extends AbstractRepository
{
 private string $key = "_users";
 public function getKey():string {
  return $this->key;
 }
 public function setKey(string $key):void {
  $this->$key = $key;
 }
}

Finally, if i want to save/update my current user eloquent model i will call:

$userRepo = new UserRepository();
$userModel = User::find(1);
$userRepo->addOrUpdate($userModel, 1); // where second parameter is TTL cache in hours

// If i need to search trough cache i will call findMany method
$userRepo->findMany(["name" => "Riste"]); // Will look through cache where column is name and value Riste in case insensitive and will return array items of eloquent models that matches name Riste.
