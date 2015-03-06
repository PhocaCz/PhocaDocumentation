<?php
/* @package Joomla
 * @copyright Copyright (C) Open Source Matters. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
 * @extension Phoca Extension
 * @copyright Copyright (C) Jan Pavelka www.phoca.cz
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 */
defined('_JEXEC') or die('Restricted access'); 
echo '<div id="phoca-doc-category-box" class="pdoc-category-view'.$this->params->get( 'pageclass_sfx' ).'">';

if ( $this->params->get( 'show_page_heading' ) ) { 
	echo '<h1>'. $this->escape($this->params->get('page_heading')) . '</h1>';
}
// TODO
if (!empty($this->category[0])) {
	echo '<div class="pdoc-category">';
	if ($this->tmpl['display_up_icon'] == 1) {
		
		if (isset($this->category[0]->parentid)) {
			if ($this->category[0]->parentid == 0) {
				
				$linkUp = JRoute::_(PhocaDocumentationHelperRoute::getCategoriesRoute());
				$linkUpText = JText::_('COM_PHOCADOCUMENTATION_CATEGORIES');
			} else if ($this->category[0]->parentid > 0) {
				$linkUp = JRoute::_(PhocaDocumentationHelperRoute::getCategoryRoute($this->category[0]->parentid, $this->category[0]->parentalias));
				$linkUpText = $this->category[0]->parenttitle;
				
				if (strtolower($linkUpText) == 'root') {
					$linkUpText = JText::_('COM_PHOCADOCUMENTATION_CATEGORIES');
				}
			} else {
				$linkUp 	= '#';
				$linkUpText = ''; 
			}
			echo '<div class="ph-top">'
				.'<a class="btn btn-success" title="'.$linkUpText.'" href="'. $linkUp.'" ><span class="glyphicon glyphicon-arrow-left"></span> '
				. $linkUpText
				.'</a></div>';
		}
	}
} else {
	echo '<div class="pdoc-category"><div class="ph-top"></div>';
}


if (!empty($this->category[0])) {
	
		echo '<h3>'.$this->category[0]->title. '</h3>';

		// Description
		if ( (isset($this->category[0]->description) && $this->category[0]->description != '' && $this->category[0]->description != '<p>&#160;</p>')) {
			echo '<div class="ph-desc">'.$this->category[0]->description.'</div>';
		}
		

		//echo '<form action="'.$this->uri.'" method="post" name="adminForm">';
	
		
		// - - - - -
		// CATEGORIES
		// - - - - -
		
		if (!empty($this->subcategories)) {	
			foreach ($this->subcategories as $valueSubCat) {
				
				echo '<div class="ph-category">';
				echo '<span class="glyphicon glyphicon-folder-close"></span> <a href="'. JRoute::_(PhocaDocumentationHelperRoute::getCategoryRoute($valueSubCat->id, $valueSubCat->alias))
					 .'">'. $valueSubCat->title.'</a>';
				
				if ($this->tmpl['display_num_doc_cats'] == 1) {
					echo ' <small>('.$valueSubCat->numsubcat .'/'.$valueSubCat->numdoc .')</small>';
				}
				echo '</div>'. "\n";
				$subcategory = 1;
			}
			
			echo '<div class="ph-hr-cb">&nbsp;</div>';
		}

		
		// - - - - -
		// ARTICLES
		// - - - - -
		
		if (!empty($this->articles)) {
		
			// Tags
			$color 	= 'auto';
			foreach ($this->articles as $value) {
				
				echo '<div class="ph-document">';
				echo '<span class="glyphicon glyphicon-book"></span>  <a href="'. JRoute::_(PhocaDocumentationHelperRoute::getArticleRoute($value->id, $value->alias, $value->categoryid,$value->categoryalias))
				.'">'. $value->title.'</a>';
				
				if ($this->tmpl['display_tags'] == 1) {
					$tags = new JHelperTags;
					$tags->getItemTags('com_content.article', $value->id);
					if (isset($tags->itemTags) && !empty($tags->itemTags)) {
						foreach ($tags->itemTags as $k => $v) {
							//$tag			= new StdClass();
							//$tag->tagLayout = new JLayoutFile('joomla.content.tags'); 
							//echo $tag->tagLayout->render($tags->itemTags);
							if (!empty($this->tmpl['tag_color_array'])) {
								$found 	= array_search($v->title, $this->tmpl['tag_color_array']);
								if ($found !== false) {
									$color = $found;
								}
							}
							echo ' <span class="label label-default" style="background: '.$color.'">'.$v->title.'</span> ';
						}
					
					}
				}
			
				echo PhocaDocumentationHelper::displayNewIcon($value->created, $this->tmpl['display_new'], $this->tmpl['display_hot_new_icon']);
				echo PhocaDocumentationHelper::displayHotIcon($value->hits, $this->tmpl['display_hot'], $this->tmpl['display_hot_new_icon']);		
				echo '</div>' . "\n";
				
				
			}
			
			echo $this->loadTemplate('pagination');
			
		}

	}
echo $this->tmpl['pddc'];
echo '</div></div>';
?>
