<?php

namespace Grimston\Security\Models;

use Phalcon\Mvc\Model;
use Phalcon\Mvc\Model\ResultInterface;
use Phalcon\Mvc\Model\ResultsetInterface;

class Action extends Model
{

    /**
     *
     * @var string
     */
    protected $resource;

    /**
     *
     * @var string
     */
    protected $action;

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
     * Method to set the value of field action
     *
     * @param string $action
     * @return $this
     */
    public function setAction($action)
    {
        $this->action = $action;

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
     * Returns the value of field action
     *
     * @return string
     */
    public function getAction()
    {
        return $this->action;
    }

    /**
     * Initialize method for model.
     */
    public function initialize()
    {
        $this->setSource("action");
        $this->hasMany('resource', Accesscontrollist::class, 'resource', ['alias' => 'Accesscontrollist']);
        $this->belongsTo('resource', Resource::class, 'resource', ['alias' => 'Resource']);
    }

    /**
     * Allows to query a set of records that match the specified conditions
     *
     * @param mixed $parameters
     *
     * @return Action[]|Action|ResultSetInterface
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
     * @return Action|ResultInterface
     */
    public static function findFirst($parameters = null)
    {
        return parent::findFirst($parameters);
    }

}
