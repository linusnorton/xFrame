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
    public function addRole($role, $parent) {
        $this->role[$role] = $role;
        return $this;
    }

    /**
     * Add a resource
     * @param \xframe\authorisation\Resource | string $role
     * @param \xframe\authorisation\Resource | string $parent
     * @throws \xframe\authorisation\AclEx
     * @return \xframe\authorisation\Acl
     */
    public function addResource($resource, $parent) {
        if (is_string($resource)) {
            $resource = new Resource($resource);
        }

        if (!$resource instanceof Resource) {
            throw new AclException("Resource must be a type of \xframe\authorisation\Resource, " .  get_class($resource) . " found.");
        }

        if ($parent) {
            $this->resource[$parent]->addChild($resource);
        }

        $this->resource[$resource->getName()] = $resource;
        return $this;
    }

    /**
     * Allow a role access to a resource
     * @param string $role
     * @param string $resource
     * @return \xframe\authorisation\Acl
     */
    public function allow($role = null, $resource = null) {
        $this->rule[$resource][$role] = true;
        return $this;
    }

    /**
     * Remove access to a resource for a role
     * @param string $role
     * @param string $resource
     * @param int $privileges
     * @return \xframe\authorisation\Acl
     */
    public function deny($role = null, $resource = null) {
        unset($this->rule[$resource][$role]);
        return $this;
    }

    /**
     * Check if a role is allowed access to a resource
     * @param string $role
     * @param string $resource
     */
    public function isAllowed($role, $resource) {
        return isset($this->rule[$resource][$role]);
    }

}

