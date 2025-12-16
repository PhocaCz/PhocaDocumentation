<?php

/**
 * @package     Joomla.Site
 * @subpackage  com_content
 *
 * @copyright   (C) 2010 Open Source Matters, Inc. <https://www.joomla.org>
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

use Joomla\CMS\Router\Route;
use Joomla\Component\Content\Site\Helper\RouteHelper;

if ($this->maxLevelcat != 0 && count($this->items[$this->parent->id]) > 0) {

    echo '<ul class="list-group list-group-flush pdoc-categories-category-list">';
    foreach ($this->items[$this->parent->id] as $id => $item) {

        // Don't display hidden categories set in default.php override file
        if (in_array($item->id, $this->pdoc->tmpl_hide_categories)) {
            continue;
        }

        if ($this->params->get('show_empty_categories_cat') || $item->numitems || count($item->getChildren())) {

            echo '<li class="list-group-item">';

            echo '<svg class="pdoc-si pdoc-si-category"><use xlink:href="'.$this->pdoc->svg_path.'#pdoc-si-category"></use></svg>';

            echo '<a href="'. Route::_(RouteHelper::getCategoryRoute($item->id, $item->language)).'">'. $this->escape($item->title).'</a>';
            echo '</li>';

            if (count($item->getChildren()) > 0 && $this->maxLevelcat > 1) {

                $this->items[$item->id] = $item->getChildren();
                $this->parent = $item;
                $this->maxLevelcat--;
                echo $this->loadTemplate('subitems');
                $this->parent = $item->getParent();
                $this->maxLevelcat++;

            }
        }
    }
    echo '</ul>';
}
