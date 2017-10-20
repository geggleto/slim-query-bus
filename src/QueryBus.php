<?php


namespace QueryBus;


use Psr\Container\ContainerInterface;

class QueryBus
{
    /** @var array */
    private $mapping;

    /** @var ContainerInterface */
    private $container;

    /**
     * QueryBus constructor.
     *
     * @param ContainerInterface $container
     */
    public function __construct(
        ContainerInterface $container
    )
    {
        $this->mapping = [];
        $this->container = $container;
    }

    /**
     * @param string $queryClass
     * @param string[] $handlers
     */
    public function addQuery($queryClass, array $handlers)
    {
        $this->mapping[(string)$queryClass] = $handlers;
    }

    /**
     * @param Query $query
     *
     * @return bool|mixed
     */
    public function fetch(Query $query)
    {
        $handlerDefinitions = $this->mapping[$query->getClass()];

        $handlers = [];

        foreach ($handlerDefinitions as $definition) {
            $handlers[] = $this->container->get($definition);
        }

        return (new QueryPipeline($handlers))
                    ->execute($query);
    }
}