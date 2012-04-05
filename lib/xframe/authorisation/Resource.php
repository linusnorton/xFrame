<?php
namespace xframe\authorisation;

/**
 * Represent a resource in the Acl. Children and parents can be attached to form a
 * hierarchy
 */
class Resource {

    /**
     *
     * @var string
     */
    protected $name;

    /**
     *
     * @var stdClass
     */
    protected $children;

    /**
     *
     * @param string $name
     * @param \stdClass $children
     */
    public function __construct($name, \stdClass $children = null) {
        $this->name = $name;
        $this->children = $children == null ? new \stdClass() : $children;
    }

    /**
     *
     * @return string
     */
    public function getName() {
        return $this->name;
    }

    /**
     *
     * @return \stdClass
     */
    public function getChildren() {
        return $this->children;
    }

    /**
     * Adds a child role to this role.
     * @param \xframe\authorisation\Resource $role
     * @return \xframe\authorisation\Resource
     */
    public function addChild(Resource $resource) {
        $this->children->{$resource->getName()} = $resource;
        return $this;
    }

    public function __toString() {
        return $this->name;
    }

}

