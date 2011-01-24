<?php

/**
 * NavigationItem has a name, a link and optional children
 *
 * @author Linus Norton <linusnorton@gmail.com>
 * @package navigation
 *
 * @Entity
 * @InheritanceType("SINGLE_TABLE")
 * @DiscriminatorColumn(name="discr", type="string")
 * @DiscriminatorMap({"NavigationItem" = "NavigationItem"})
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
     * @param array $children
     */
    public function __construct($name, 
                                $link,
                                NavigationItem $parent,
                                array $children = array()) {
        $this->name = $name;
        $this->link = $link;
        $this->parent = $parent;
        $this->children = $children;
    }  
}
