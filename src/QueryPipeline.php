<?php


namespace QueryBus;


class QueryPipeline
{
    /** @var QueryHandler[] */
    private $handlers;

    public function __construct($handlers = [])
    {
        $this->handlers = $handlers;
    }

    /**
     * @param Query $query
     *
     * @return bool|mixed
     */
    public function execute(Query $query)
    {
        foreach ($this->handlers as $handler) {
            $result = $handler->handle($query);

            if ($result) {
                return $result;
            }
        }

        return false;
    }
}