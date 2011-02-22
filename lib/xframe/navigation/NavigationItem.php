<?php

namespace xframe\navigation;

/**
 * NavigationItem has a name, a link and optional children
 *
 * @author Linus Norton <linusnorton@gmail.com>
 * @package navigation
 *
 * @MappedSuperclass
 */
class NavigationItem {

    /**
     * @Id @Column(type="integer")
     * @GeneratedValue
     */
    private $id;
    /**
     * @Column(type="string")
     */
    private $name;
    /**
     * @Column(type="string")
     */
    private $link;
    /**
     * @ManyToOne(targetEntity="NavigationItem", inversedBy="children")
     * @JoinColumn(name="parent", referencedColumnName="id")
     */
    private $parent;
    /**
     * @OneToMany(targetEntity="NavigationItem", mappedBy="parent")
     */
    private $children;

    /**
     * Constructor
     * @param string $name
     * @param string $link
     * @param NavigationItem $parent
     * @param array $children
     */
    public function __construct($name, 
                                $link,
                                NavigationItem $parent = null,
                                array $children = array()) {
        $this->name = $name;
        $this->link = $link;
        $this->parent = $parent;
        $this->children = $children;
    }

    /**
     * @return string
     */
    public function getName() {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getLink() {
        return $this->link;
    }

    /**
     * @return NavigationItem
     */
    public function getParent() {
        return $this->parent;
    }

    /**
     * @return array of {@link NavigationItem}
     */
    public function getChildren() {
        return $this->children;
    }


}
