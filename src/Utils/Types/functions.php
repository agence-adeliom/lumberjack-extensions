<?php

namespace Adeliom\WP\Extensions\Utils\Types;

/**
 * Recursively add ancestor class and properties to menu items.
 *
 * @param array $menu_items All menu items.
 * @param bool $with_parent Whether to add the direct parent property and class.
 * @return array Updated menu items.
 */
function menu_items_ancestors(object $child, array $menu_items, bool $with_parent = true): array
{
    // Bailout if menu item has no parent.
    if ((int)$child->menu_item_parent === 0) {
        return $menu_items;
    }

    foreach ($menu_items as $item) {
        if ((int)$item->ID === (int)$child->menu_item_parent) {
            if ($with_parent) {
                $item->current_item_parent = true;
                $item->classes[]           = 'current-menu-parent';
            }

            $item->current_item_ancestor = true;
            $item->classes[]             = 'current-menu-ancestor';

            if ((int)$item->menu_item_parent !== 0) {
                $menu_items = menu_items_ancestors($item, $menu_items, false);
            }

            break;
        }
    }

    return $menu_items;
}
