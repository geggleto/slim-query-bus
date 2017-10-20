# Slim Query Bus

A scaffold for finding data in your application.

A query bus is an architectural design pattern where you prioritize querying different persistence layers of your application.

For example, if you wish to find data on a user of the system. You would first query hot-cache, then cold-cache and finally persistence storage.

In code you might have a repository with nested if's and finally a command to pull the data then insert.

This adds complexity to your application and makes testing much more difficult.

With a QueryBus you simply mock your Handler to return false and move on.

It also makes it very easy to change and maintain different Cache mechanisms without worrying about Gotchas.

### Objects

`Query` - A Query Object is a simple Data Transfer Object(DTO) which will encapsulate the information needed to complete your query.

`QueryBus` - A Bus object which holds the mappings of your Query and Handlers

`QueryHandler` - An object responsible for the lookup. It accepts a query and returns false upon failure to find OR the result

`QueryPipeline` - An object which processes the different handlers for a given Query

### Example

```php
/**
Get's a User by Id
*/
class UserQuery extends Query
{
   private $id;
   
   public function __construct($id)
   {
       $this->id = $id;
   }
   
   public function getId()
   {
       return $this->id;
   }
   
   public function getClass()
   {
       return self::class;
   }
}

class UserQueryRedisHandler implements QueryHandler
{
    public function __construct(Predis $predis)
    {
        $this->predis = $predis;
    }
    
    public function handle(UserQuery $query)
    {
        $object = $this->predis->get("user:{$query->getId()}");
        if (empty($object)) {
            return false;
        }
        
        return User::hydrateFromRedis($object);
    }
}

class UserQueryDoctrineHandler implements QueryHandler
{
    public function __construct(
        EntityManager $em,
        Predis $predis
    )
    {
        $this->predis = $predis;
        $this->em = $em;
    }
    
    public function handle(UserQuery $query)
    {
        $object = $this->em->find(User::class, $query->getId);
        if (is_null($object)) {
            return false;
        }
        
        //Insert it into Redis
        $this->predis->set("user:{$query->getId()}", json_encode($object->toArray()));
        
        return $object;
    }
}

$bus = new QueryBus();
$bus->addQuery(UserQuery::class, [
    UserQueryRedisHandler, //check Redis first
    UserQueryDoctrineHandler::class //Then pull from the DB
]);

//then somewhere in your code

$user = $bus->fetch(new UserQuery($id));

```
