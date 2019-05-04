<?php
/* @package Joomla
 * @copyright Copyright (C) Open Source Matters. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
 * @extension Phoca Extension
 * @copyright Copyright (C) Jan Pavelka www.phoca.cz
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 */
defined('_JEXEC') or die('Restricted access');

$classSuffix = '';
if ($this->tmpl['equal_height'] == 1) {
	$classSuffix = ' equalHeight';
}

echo '<div id="phoca-doc-categories-box" class="pdoc-categories-view'.$this->params->get( 'pageclass_sfx' ).$classSuffix.'">';


if ( $this->params->get( 'show_page_heading' ) ) {
	echo '<h1>'. $this->escape($this->params->get('page_heading')) . '</h1>';
}

if ( $this->tmpl['cat_desc'] != '') {
	echo '<div class="ph-desc">'. $this->tmpl['cat_desc']. '</div>';
}


if (!empty($this->categories)) {
	//$i = 1;
	echo '<div class="row ph-dc-row">';
	foreach ($this->categories as $value) {
		// Categories
		$numDoc 	= 0;
		$numSubcat	= 0;
		$catOutput 	= '';
		if (!empty($value->subcategories)) {
			foreach ($value->subcategories as $valueCat) {

				$catOutput 	.= '<div class="ph-category">';
				$catOutput 	.= '<span class="glyphicon glyphicon-folder-close"></span> <a href="'. JRoute::_(PhocaDocumentationHelperRoute::getCategoryRoute($valueCat->id, $valueCat->alias))
							.'">'. $valueCat->title.'</a>';

				if ($this->tmpl['display_num_doc_cats'] == 1) {
					$catOutput  .=' <small>('.$valueCat->numsubcat .'/'.$valueCat->numdoc .')</small>';
				}
				$catOutput 	.= '</div>' . "\n";
				$numDoc = (int)$valueCat->numdoc + (int)$numDoc;
				$numSubcat++;

			}
		}
		echo '<div class="col-sm-4 col-md-4 ph-dc-col">';
		echo '<div class="thumbnail ph-thumbnail ph-dc-thumbnail">';
		echo '<h3>';
		echo '<a href="'. JRoute::_(PhocaDocumentationHelperRoute::getCategoryRoute($value->id, $value->alias)).'">'. $value->title.'</a>';


		if ($this->tmpl['display_num_doc_cats_header'] == 1) {
			echo ' <small>('.$numSubcat.'/' . $value->numdoc .')</small>';
		}
		echo '</h3>';


		if ($this->tmpl['displaymaincatdesc']	 == 1) {
			echo '<div class="ph-desc">'.$value->description.'</div>';
		} else {
			if ($catOutput != '') {
				echo '<div class="ph-subcategories">'.$catOutput. '</div>';
			} else {
				echo '<div class="ph-subcategories"> '.JText::_('COM_PHOCADOCUMENTATION_NO_SUBCATEGORIES').'</div>';
			}
		}

		echo '</div></div>';
		/*if ($i%3==0) {
			echo '<div style="clear:both"></div>';
		}
		$i++;*/

	}
	echo '</div>';// end row
}
echo '</div>';
echo '<div class="ph-cb"></div>';


// - - - - - - - - - -
// Most viewed docs
// - - - - - - - - - -
$outputFile		= '';

if (!empty($this->mostvieweddocs) && (int)$this->tmpl['most_viewed_docs_num'] > 0) {
	foreach ($this->mostvieweddocs as $value) {

			$outputFile .= '<div class="ph-document">';
			$outputFile .= '<span class="glyphicon glyphicon-book"></span> <a href="'
						. JRoute::_(PhocaDocumentationHelperRoute::getArticleRoute($value->id, $value->alias, $value->categoryid,$value->categoryalias))
						.'">'. $value->title.'</a>'
						.' <small>(' .$value->categorytitle.')</small>';

			$outputFile .= PhocaDocumentationHelper::displayNewIcon($value->created, $this->tmpl['display_new'], $this->tmpl['display_hot_new_icon']);
			$outputFile .= PhocaDocumentationHelper::displayHotIcon($value->hits, $this->tmpl['display_hot'], $this->tmpl['display_hot_new_icon']);

			$outputFile .= '</div>' . "\n";

	}

	if ($outputFile != '') {
		echo '<div class="ph-hr-cb">&nbsp;</div>';
		echo '<div id="phoca-doc-most-viewed-box">';
		echo '<div class="ph-documents"><h3>'. JText::_('COM_PHOCADOCUMENTATION_MOST_VIEWED_DOCS').'</h3>';
		echo $outputFile;
		echo '</div></div>';
	}
}
echo $this->tmpl['pddc'];
?>
