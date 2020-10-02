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

            $dbRoles = Role::find();
            $dbResources = Resource::find();
            $dbACLItems = Accesscontrollist::find();

            // Register roles
            foreach($dbRoles as $dbRole) {
                $acl->addRole($dbRole->getRole());
            }

            foreach($dbResources as $dbResource) {
                $dbActions = $dbResource->getAction();
                $actions[] = null;
                foreach($dbActions as $dbAction) {
                    $actions[] = $dbAction->getAction();
                }
                $acl->addComponent($dbAction->getResource(),$actions);
            }

            foreach ($dbACLItems as $ACLItem){
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
            $role = $auth->getAuth()->getTextAsArray()['role'];
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