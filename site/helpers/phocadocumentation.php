<?php
/*
 * @package Joomla 1.5
 * @copyright Copyright (C) 2005 Open Source Matters. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
 *
 * @component Phoca Component
 * @copyright Copyright (C) Jan Pavelka www.phoca.cz
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 */
defined('_JEXEC') or die();
jimport('joomla.application.component.controller');
jimport( 'joomla.filesystem.folder' ); 
jimport( 'joomla.filesystem.file' );

class PhocaDocumentationHelperFront
{	
	
	public static function getOrderingText ($ordering, $category = false) {
		switch ((int)$ordering) {
			case 2:
				if ($category) { $orderingOutput	= 'lft DESC';}
				else { $orderingOutput	= 'ordering DESC';}
			break;
			
			case 3:
				$orderingOutput	= 'title ASC';
			break;
			
			case 4:
				$orderingOutput	= 'title DESC';
			break;
			
			case 5:
				//$orderingOutput	= 'date ASC';
				$orderingOutput	= 'created ASC';
			break;
			
			case 6:
				//$orderingOutput	= 'date DESC';
				$orderingOutput	= 'created DESC';
			break;
			
			case 7:
				$orderingOutput	= 'id ASC';
			break;
			
			case 8:
				$orderingOutput	= 'id DESC';
			break;
		
			case 1:
			default:
				if ($category) { $orderingOutput	= 'lft ASC';}
				else { $orderingOutput	= 'ordering ASC';}
			break;
		}
		
		/*$order  = $params->get('items_order', 'ordering');	
		$dir    = $params->get('items_orderdir', 'ASC');
		//$subdir = $params->get('items_commentdir', 'ASC'); //comments
		if ($order == 'ordering'){
			if ($dir == 'DESC') {
				$order = 'rgt';
			} else {
				$order = 'lft';
			}
		}*/
		
		
		return $orderingOutput;
	}
	
}

jimport('joomla.html.pagination');
class PhocaPagination extends JPagination
{

	function getLimitBox()
	{
		
		$app				= JFactory::getApplication();
		$paramsC 			= JComponentHelper::getParams('com_phocadocumentation') ;
		$pagination 		= $paramsC->get( 'pagination', '5,10,15,20,50' );
		$paginationArray	= explode( ',', $pagination );
		
		// Initialize variables
		$limits = array ();

		foreach ($paginationArray as $paginationValue) {
			$limits[] = JHTML::_('select.option', $paginationValue);
		}
		$limits[] = JHTML::_('select.option', '0', JText::_('COM_PHOCADOCUMENTATION_ALL'));

		$selected = $this->viewall ? 0 : $this->limit;

		// Build the select list
		if ($app->isAdmin()) {
			$html = JHTML::_('select.genericlist',  $limits, 'limit', 'class="inputbox" size="1" onchange="submitform();"', 'value', 'text', $selected);
		} else {
			$html = JHTML::_('select.genericlist',  $limits, 'limit', 'class="inputbox" size="1" onchange="this.form.submit()"', 'value', 'text', $selected);
		}
		return $html;
	}
}
?>