<?php


namespace QueryBus;


abstract class Query
{
    /**
     * @return string
     */
    abstract public function getClass();
}