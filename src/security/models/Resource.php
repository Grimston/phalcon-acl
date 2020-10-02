<?php

namespace Grimston\Security\Models;

use Phalcon\Mvc\Model;
use Phalcon\Mvc\Model\ResultInterface;
use Phalcon\Mvc\Model\ResultsetInterface;

class Resource extends Model
{

    /**
     *
     * @var string
     */
    protected $resource;

    /**
     * Method to set the value of field resource
     *
     * @param string $resource
     * @return $this
     */
    public function setResource($resource)
    {
        $this->resource = $resource;

        return $this;
    }

    /**
     * Returns the value of field resource
     *
     * @return string
     */
    public function getResource()
    {
        return $this->resource;
    }

    /**
     * Initialize method for model.
     */
    public function initialize()
    {
        $this->setSchema("phacs");
        $this->setSource("resource");
        $this->hasMany('resource', Action::class, 'resource', ['alias' => 'Action']);
    }

    /**
     * Allows to query a set of records that match the specified conditions
     *
     * @param mixed $parameters
     *
     * @return Resource[]|Resource|ResultSetInterface
     */
    public static function find($parameters = null): ResultsetInterface
    {
        return parent::find($parameters);
    }

    /**
     * Allows to query the first record that match the specified conditions
     *
     * @param mixed $parameters
     *
     * @return Resource|ResultInterface
     */
    public static function findFirst($parameters = null)
    {
        return parent::findFirst($parameters);
    }

}
