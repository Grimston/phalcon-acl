<?php


namespace Grimston\Security\Plugins;

use Grimston\Security\Models\Accesscontrollist;
use Grimston\Security\Models\Resource;
use Grimston\Security\Models\Role;
use Grimston\Security\PhalconAuth\Auth;
use Grimston\Security\PhalconAuth\Exceptions\EntityNotFoundException;
use Phalcon\Acl\Enum;
use Phalcon\Di\Injectable;
use Phalcon\Events\Event;
use Phalcon\Mvc\Dispatcher;
use Phalcon\Acl\Adapter\Memory as AclList;


class AccessControlListPlugin extends Injectable
{
    /**
     * Returns an existing or new access control list
     *
     * @returns AclList
     */
    public function getAcl()
    {

        if (!isset($this->persistent->acl)) {

            $acl = new AclList();

            $acl->setDefaultAction(Enum::DENY);

            $roles = Role::find();
            $resources = Resource::find();
            $ACLItems = Accesscontrollist::find();

            // Register roles
            foreach($roles as $role) {
                $acl->addRole($role->getRole());
            }

            foreach($resources as $resource) {
                $actions = $resource->getAction();
                $actions[] = null;
                foreach($actions as $action) {
                    $actions[] = $action->getAction();
                }
                $acl->addComponent($resource->getResource(),$actions);
            }

            foreach ($ACLItems as $ACLItem){
                $acl->allow($ACLItem->getRole(), $ACLItem->getResource(), $ACLItem->getAction());
            }

            //The acl is stored in the session
            $this->persistent->acl = $acl;
        }

        return $this->persistent->acl;
    }

    /**
     * This action is executed before execute any action in the application
     *
     * @param Event      $event
     * @param Dispatcher $dispatcher
     *
     * @return bool
     * @throws EntityNotFoundException
     */
    public function beforeDispatch(Event $event, Dispatcher $dispatcher)
    {
        /** @var Auth $auth */
        $auth = $this->auth;
        if (!$auth->getAuth()){
            $role = 'Guest';
        } else {
            $role = $auth->getAuth()->getRole();
        }

        $controller = strtolower($dispatcher->getControllerName());
        $action = strtolower($dispatcher->getActionName());

        $acl = $this->getAcl();
        if (!$acl->isComponent($controller)) {
            $dispatcher->forward([
                'controller' => 'errors',
                'action'     => 'show404'
            ]);

            return false;
        }

        $allowed = $acl->isAllowed($role, $controller, $action);
        if (!$allowed) {
            $dispatcher->forward(array(
                'controller' => 'errors',
                'action'     => 'show401'
            ));
            return false;
        }
    }
}