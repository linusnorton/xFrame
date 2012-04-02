<?php
namespace xframe\authorisation;

/**
 * Class to manage and query an Access Control List
 */
class Acl {

    protected $role = array();
    protected $resource = array();
    protected $rule = array();

    /**
     * Add a role
     * @param \xframe\authorisation\Role | string $role
     * @param \xframe\authorisation\Role | string $parent
     * @return \xframe\authorisation\Acl
     */
    public function addRole($role, $parent = null) {
        if (is_string($role)) {
            $role = new Role($role);
        }
        if (is_string($parent)) {
            $parent = new Role($parent);
        }

        if ($parent) {
            $this->role[(string)$parent]->addChild($role);
        }
        $this->role[(string)$role] = $role;
        return $this;
    }

    /**
     * Add a resource
     * @param \xframe\authorisation\Resource | string $role
     * @param \xframe\authorisation\Resource | string $parent
     * @throws \xframe\authorisation\AclEx
     * @return \xframe\authorisation\Acl
     */
    public function addResource($resource, $parent = null) {
        if (is_string($resource)) {
            $resource = new Resource($resource);
        }
        if (is_string($parent)) {
            $parent = new Resource($parent);
        }

        if ($parent) {
            $this->resource[(string)$parent]->addChild($resource);
        }

        $this->resource[(string)$resource] = $resource;
        return $this;
    }

    /**
     * Allow a role access to a resource
     * @param string $role
     * @param string $resource
     * @return \xframe\authorisation\Acl
     */
    public function allow($role, $resource) {

        $this->rule[(string)$resource][(string)$role] = true;
        foreach ($this->role[(string)$role]->getChildren() as $cRole) {
            $this->allow($cRole, $resource);
        }
        foreach ($this->resource[(string)$resource]->getChildren() as $cResource) {
            $this->allow($role, $cResource);
        }
        
        return $this;
    }

    /**
     * Remove access to a resource for a role
     * @param string $role
     * @param string $resource
     * @param int $privileges
     * @return \xframe\authorisation\Acl
     */
    public function deny($role, $resource) {

        unset($this->rule[(string)$resource][(string)$role]);
        foreach ($this->role[(string)$role]->getChildren() as $cRole) {
            $this->deny($cRole, $resource);
        }

        return $this;
    }

    /**
     * Check if a role is allowed access to a resource
     * @param string $role
     * @param string $resource
     */
    public function isAllowed($role, $resource) {
        return isset($this->rule[(string)$resource][(string)$role]);
    }

    /**
     * Allow access to all roles for a resource
     * @param string $resource
     * @return \xframe\authorisation\Acl
     */
    public function allowResource($resource) {
        foreach ($this->role as $role) {
            $this->rule[(string)$resource][(string)$role] = true;
        }
        foreach ($this->resource[(string)$resource]->getChildren() as $cResource) {
            $this->allowResource($cResource);
        }
        return $this;
    }

    /**
     * Allow role to access all resources
     * @param string $role
     * @return \xframe\authorisation\Acl
     */
    public function allowRole($role) {
        foreach ($this->resource as $resource) {
            $this->rule[(string)$resource][(string)$role] = true;
        }
        foreach ($this->role[(string)$role]->getChildren() as $cRole) {
            $this->allowRole($cRole);
        }
        return $this;
    }

    /**
     * Allows access to al resources for all roles
     * @return \xframe\authorisation\Acl
     */
    public function allowAll() {
        foreach ($this->role as $role) {
            foreach ($this->resource as $resource) {
                $this->rule[(string)$resource][(string)$role] = true;
            }
        }
        return $this;
    }

    /**
     * Denies access to all resources for all roles
     * @return \xframe\authorisation\Acl
     */
    public function denyAll() {
        $this->rule = array();
        return $this;
    }

    /**
     * Denys all access to a resource
     * @param string $resource
     * @return \xframe\authorisation\Acl
     */
    public function denyResource($resource) {
        unset($this->rule[(string)$resource]);
        foreach ($this->resource[(string)$resource]->getChildren() as $cResource) {
            $this->denyResource($cResource);
        }
        return $this;
    }

    /**
     * Denys to all resources for a role
     * @param type $role
     * @return \xframe\authorisation\Acl
     */
    public function denyRole($role) {
        foreach ($this->rule as &$res) {
            unset($res[(string)$role]);
        }
        foreach ($this->role[(string)$role]->getChildren() as $cRole) {
            $this->denyRole($cRole);
        }
        return $this;
    }

}
