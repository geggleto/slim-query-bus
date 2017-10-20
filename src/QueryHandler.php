<?php


namespace QueryBus;


interface QueryHandler
{
    /**
     * A Query Handler must return false when a query is not fulfilled
     *
     * @param Query $query
     *
     * @return bool|mixed
     */
    public function handle(Query $query);
}