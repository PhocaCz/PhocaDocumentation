<?php

/**
 * @package     Joomla.Site
 * @subpackage  com_content
 *
 * @copyright   (C) 2010 Open Source Matters, Inc. <https://www.joomla.org>
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Router\Route;
use Joomla\Component\Content\Site\Helper\RouteHelper;

// Subsubcategories
if ($this->maxLevel != 0 && count($this->children[$this->category->id]) > 0) {

    echo '<ul class="list-group list-group-flush">';
    foreach ($this->children[$this->category->id] as $id => $child) {

        // Don't display hidden categories set in default.php override file
        if (in_array($child->id, $this->pdoc->tmpl_hide_categories)) {
            continue;
        }

        if ($this->params->get('show_empty_categories') || $child->numitems || count($child->getChildren())) {

            echo '<li class="list-group-item">';

            echo '<svg class="pdoc-si pdoc-si-category"><use xlink:href="'.$this->pdoc->svg_path.'#pdoc-si-category"></use></svg>';

            echo '<a href="'. Route::_(RouteHelper::getCategoryRoute($child->id, $child->language)).'">'. $this->escape($child->title).'</a>';
            echo '</li>';

            if ($this->maxLevel > 1 && count($child->getChildren()) > 0){

                $this->children[$child->id] = $child->getChildren();
                $this->category = $child;
                $this->maxLevel--;
                echo $this->loadTemplate('subitems');
                $this->category = $child->getParent();
                $this->maxLevel++;

            }
        }
    }
    echo '</ul>';
}

