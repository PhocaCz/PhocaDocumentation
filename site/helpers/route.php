<?php
/**
 * @version		$Id: route.php 11190 2008-10-20 00:49:55Z ian $
 * @package		Joomla
 * @subpackage	Content
 * @copyright	Copyright (C) 2005 - 2008 Open Source Matters. All rights reserved.
 * @license		GNU/GPL, see LICENSE.php
 * Joomla! is free software. This version may have been modified pursuant to the
 * GNU General Public License, and as distributed it includes or is derivative
 * of works licensed under the GNU General Public License or other free or open
 * source software licenses. See COPYRIGHT.php for copyright notices and
 * details.
 */

// no direct access
defined('_JEXEC') or die('Restricted access');

// Component Helper
jimport('joomla.application.component.helper');

/**
 * Content Component Route Helper
 *
 * @static
 * @package		Joomla
 * @subpackage	Content
 * @since 1.5
 */
class PhocaDocumentationHelperRoute
{
	
	public static function getArticleRoute($id, $alias, $catid, $catalias, $itemid = 0) {
	
		$app			= JFactory::getApplication();
		$params 		= $app->getParams();
		$articleItemid	= $params->get( 'article_itemid', 0 );
	
		$link = 'index.php?option=com_content&view=article&catid='.$catid.':'.$catalias.'&id='.$id.':'.$alias;

		
		if ((int)$itemid > 0) {
			$link .= '&Itemid='.(int)$itemid;
		} else if ((int)$articleItemid > 0 ) {
			$link .= '&Itemid='.(int)$articleItemid;
		} else {
			//$link .= '&Itemid='.JRequest::getVar('Itemid', 0, '', 'int');
		}
		
		return $link;
	}
	
	public static function getCategoryRoute($catid, $catidAlias = '')
	{
		$needles = array(
			'category' => (int) $catid,
			'categories' => ''
		);
		
		if ($catidAlias != '') {
			$catid = $catid . ':' . $catidAlias;
		}

		//Create the link
		$link = 'index.php?option=com_phocadocumentation&view=category&id='.$catid;

		if($item = PhocaDocumentationHelperRoute::_findItem($needles)) {
			if(isset($item->query['layout'])) {
				$link .= '&layout='.$item->query['layout'];
			}
			$link .= '&Itemid='.$item->id;
		} else {
			// Maybe root category was found
			$needles = array(
				'categories' => ''
			);
			$link = 'index.php?option=com_phocadocumentation&view=categories';
			if($item = PhocaDocumentationHelperRoute::_findItem($needles)) {
				if(isset($item->query['layout'])) {
					$link .= '&layout='.$item->query['layout'];
				}
				$link .= '&Itemid='.$item->id;
			}
		}

		return $link;
	}
	/*
	function getSectionRoute($sectionid, $sectionidAlias = '')
	{
		$needles = array(
			'section' => (int) $sectionid,
			'sections' => ''
		);
		
		if ($sectionidAlias != '') {
			$sectionid = $sectionid . ':' . $sectionidAlias;
		}

		//Create the link
		$link = 'index.php?option=com_phocadocumentation&view=section&id='.$sectionid;

		if($item = PhocaDocumentationHelperRoute::_findItem($needles)) {
			if(isset($item->query['layout'])) {
				$link .= '&layout='.$item->query['layout'];
			}
			$link .= '&Itemid='.$item->id;
		};

		return $link;
	}
	
	function getSectionsRoute()
	{
		$needles = array(
			'sections' => ''
		);
		
		//Create the link
		$link = 'index.php?option=com_phocadocumentation&view=sections';

		if($item = PhocaDocumentationHelperRoute::_findItem($needles)) {
			if(isset($item->query['layout'])) {
				$link .= '&layout='.$item->query['layout'];
			}
			$link .= '&Itemid='.$item->id;
		};

		return $link;
	}*/

	public static function _findItem($needles, $notCheckId = 0)
	{
		$app	= JFactory::getApplication();
		$menus	= $app->getMenu('site', array());
		$items	= $menus->getItems('component', 'com_phocadocumentation');

		if(!$items) {
			return JRequest::getVar('Itemid', 0, '', 'int');
			//return null;
		}
		
		$match = null;
		

		foreach($needles as $needle => $id)
		{
			if ($needle == 'category' && $id == 1) {
				// if root category - ignore it and make the link to all categories
				return false;
			}
			
			if ($notCheckId == 0) {
				foreach($items as $item) {
					
					if ((@$item->query['view'] == $needle) && (@$item->query['id'] == $id)) {
						$match = $item;
						break;
					}
				}
			} else {
				foreach($items as $item) {
					
					if (@$item->query['view'] == $needle) {
						$match = $item;
						break;
					}
				}
			}

			if(isset($match)) {
				break;
			}
		}

		return $match;
	}
}
?>
