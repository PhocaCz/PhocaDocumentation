<?php

/**
 * @package     Joomla.Site
 * @subpackage  com_content
 *
 * @copyright   (C) 2010 Open Source Matters, Inc. <https://www.joomla.org>
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Router\Route;
use Joomla\Component\Content\Site\Helper\RouteHelper;

$lang   = Factory::getLanguage();
$user   = Factory::getUser();
$groups = $user->getAuthorisedViewLevels();

// Subcategories
if ($this->maxLevel != 0 && count($this->children[$this->category->id]) > 0) {

 //   echo '<div class="row row-cols-1 row-cols-md-3 g-4">';
    echo '<div class="card">';
    echo '<ul class="list-group list-group-flush pdoc-categories-category-list">';


    foreach ($this->children[$this->category->id] as $id => $child) {

        // Don't display hidden categories set in default.php override file
        if (in_array($child->id, $this->pdoc->tmpl_hide_categories)) {
            continue;
        }

        if ($this->params->get('show_empty_categories') || $child->numitems || count($child->getChildren())) {

           // echo '<div class="col">';
          //  echo '<div class="card h-100">';
           // echo '<div class="card-body">';

           echo '<li class="list-group-item">';
            echo '<svg class="pdoc-si pdoc-si-category"><use xlink:href="'.$this->pdoc->svg_path.'#pdoc-si-category"></use></svg>';
          //  echo '<h3 class="card-title">';
            echo '<a href="'. Route::_(RouteHelper::getCategoryRoute($child->id, $child->language)).'">'. $this->escape($child->title).'</a>';
          //  echo '</h3>';

            if ($this->params->get('show_cat_num_articles', 1)){
                echo ' <small class="text-muted">';
                echo '<span class="pdoc-articles-number">('.Text::_('COM_CONTENT_NUM_ITEMS').' '.$child->getNumItems(true).')</span>';
                echo '</small>';
            }


            echo '</li>';

            if ($this->params->get('show_description_image') && $child->getParams()->get('image')) {
                echo HTMLHelper::_('image', $child->getParams()->get('image'), $child->getParams()->get('image_alt'));
            }

            if ($this->params->get('show_subcat_desc_cat') == 1) {
                if ($child->description && $child->description != '' && $child->description != '<p>&#160;</p>') {
                    echo '<div class="pdoc-cat-desc">';
                    echo HTMLHelper::_('content.prepare', $child->description, '', 'com_content.categories');
                    echo '</div>';
                }
            }

            if ($this->maxLevel > 1 && count($child->getChildren()) > 0){

                $this->children[$child->id] = $child->getChildren();
                $this->category = $child;
                $this->maxLevel--;
                echo $this->loadTemplate('subitems');
                $this->category = $child->getParent();
                $this->maxLevel++;

            }

           // echo '</div>'; // end card-body
           /* if ($this->params->get('show_cat_num_articles', 1)){
				echo '<div class="card-footer pdoc-categories-card">';
					echo '<small class="text-muted float-end">';

                    /*$subCategoriesCount = count($item->getChildren());
                    if ($subCategoriesCount > 0) {
                        echo '<span class="pd-categories-number">' . Text::_('COM_CONTENT_NUM_CATEGORIES') . ': ' . $subCategoriesCount . '</span>';
                        echo '<span class="pd-sep-number">&nbsp;/&nbsp;</span>';
                    }*//*
                    echo '<span class="pdoc-articles-number">'.Text::_('COM_CONTENT_NUM_ITEMS').' '.$child->getNumItems(true).'</span>';

					echo '</small>';
				echo '</div>';
			}*/

           // echo '</div>'; // end card
           // echo '</div>'; // end col

        }
    }
  //  echo '</div>';
  echo '</ul>';
  echo '</div>';
}
