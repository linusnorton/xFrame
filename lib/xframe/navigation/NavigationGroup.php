<?php

/**
 * NavigationGroup contains navigation items
 *
 * @author Linus Norton <linusnorton@gmail.com
 * @package navigation
 */
class NavigationGroup {
    /**
     * @var array of {@link NavigationItem}
     */
    private $items;

    public function __construct(array $items) {
        $this->items = $items;
    }
}