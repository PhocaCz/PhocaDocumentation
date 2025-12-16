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

if ($this->maxLevelcat != 0 && count($this->items[$this->parent->id]) > 0) {

    echo '<div class="row row-cols-1 row-cols-md-3 g-4">';
    foreach ($this->items[$this->parent->id] as $id => $item) {

        // Don't display hidden categories set in default.php override file
        if (in_array($item->id, $this->pdoc->tmpl_hide_categories)) {
            continue;
        }

        if ($this->params->get('show_empty_categories_cat') || $item->numitems || count($item->getChildren())) {

            echo '<div class="col">';
            echo '<div class="card h-100">';
            echo '<div class="card-body">';

            echo '<h3 class="card-title">';
            echo '<a href="'. Route::_(RouteHelper::getCategoryRoute($item->id, $item->language)).'">'. $this->escape($item->title).'</a>';
            echo '</h3>';

            if ($this->params->get('show_description_image') && $item->getParams()->get('image')) {
                echo HTMLHelper::_('image', $item->getParams()->get('image'), $item->getParams()->get('image_alt'));
            }

            if ($this->params->get('show_subcat_desc_cat') == 1) {
                if ($item->description) {
                    echo '<div class="pdoc-cat-desc">';
                    echo HTMLHelper::_('content.prepare', $item->description, '', 'com_content.categories');
                    echo '</div>';
                }
            }

            if (count($item->getChildren()) > 0 && $this->maxLevelcat > 1) {

                $this->items[$item->id] = $item->getChildren();
                $this->parent = $item;
                $this->maxLevelcat--;
                echo $this->loadTemplate('subitems');
                $this->parent = $item->getParent();
                $this->maxLevelcat++;

            }

            echo '</div>'; // end card-body

            if ($this->params->get('show_cat_num_articles_cat') == 1){
				echo '<div class="card-footer pdoc-categories-card">';
					echo '<small class="text-muted float-end">';

                    /*$subCategoriesCount = count($item->getChildren());
                    if ($subCategoriesCount > 0) {
                        echo '<span class="pd-categories-number">' . Text::_('COM_CONTENT_NUM_CATEGORIES') . ': ' . $subCategoriesCount . '</span>';
                        echo '<span class="pd-sep-number">&nbsp;/&nbsp;</span>';
                    }*/
                    echo '<span class="pdoc-articles-number">'.Text::_('COM_CONTENT_NUM_ITEMS').' '.$item->numitems.'</span>';

					echo '</small>';
				echo '</div>';
			}

            echo '</div>'; // end card
            echo '</div>'; // end col

        }
    }
    echo '</div>';
}
