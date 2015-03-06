<?php
/*
 * @package		Joomla.Framework
 * @copyright	Copyright (C) 2005 - 2010 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 *
 * @component Phoca Component
 * @copyright Copyright (C) Jan Pavelka www.phoca.cz
 * @license http://www.gnu.org/copyleft/gpl.html GNU General Public License version 2 or later;
 */
defined('_JEXEC') or die();
jimport('joomla.application.component.model');


class PhocaDocumentationModelCategories extends JModelLegacy
{
	var $_categories 			= null;
	var $_most_viewed_docs 		= null;
	var $_categories_ordering	= null;
	var $_category_ordering		= null;

	function __construct() {
		parent::__construct();
	}

	function getCategoriesList() {
		if (empty($this->_categories)) {				
			$query			= $this->_getCategoriesListQuery();
			
			//$this->_categories 	= $this->_getList( $query );
			$categories 	= $this->_getList( $query );
			if (!empty($categories)) {
				
				// Parent Only
				foreach ($categories as $k => $v) {
					if ($v->parent_id == 1) {
						$this->_categories[$v->id] = $categories[$k];
					}	
				}
			
				// Subcategories
				foreach ($categories as $k => $v) {
					if (isset($this->_categories[$v->parent_id])) {
						$this->_categories[$v->parent_id]->subcategories[] = $categories[$k];
						$this->_categories[$v->parent_id]->numsubcat++;
					}	
				}
			}
				
			/*foreach ($this->_categories as $key => $value) {
			
				$query	= $this->_getCategoryListQuery( $value->id );
				$this->_categories[$key]->subcategories = $this->_getList( $query );
			}*/
		}

		return $this->_categories;
	}
	
	
	
	/* 
	 * Get only parent categories
	 */
	function _getCategoriesListQuery(  ) {
		
		$wheres		= array();
		$app		= JFactory::getApplication();
		$params 	= $app->getParams();
		$user 		= JFactory::getUser();
		$userLevels	= implode (',', $user->getAuthorisedViewLevels());
		
		$display_categories 		= $params->get('display_categories', array());
		$hide_categories 			= $params->get('hide_categories', array());
		$display_empty_categories 	= $params->get('display_empty_categories', 0);

		if ( !empty($display_categories )) {
			$wheres[] = " cc.id IN (".implode(',', $display_categories).")";
		}
		
		if ( !empty($hide_categories ) ) {
			$wheres[] = " cc.id NOT IN (".implode(',',$hide_categories).")";
		}
		//$wheres[] = " cc.parent_id = 1";
		$wheres[] = " cc.published = 1";
		$wheres[] = " cc.extension = 'com_content'";
		
		$cState = '';
		if ($display_empty_categories == 2) {
			$wheres[] = " cc.title <> 'Uncategorised'";
		} else if ($display_empty_categories == 1) {
			
		} else {
			$cState = " AND c.state = 1";
		}
		$wheres[] = " cc.access IN (".$userLevels.")";
		
		
		$categoriesOrdering = $this->_getCategoryOrdering();
		
	
		
		$query =  " SELECT cc.id, cc.parent_id, cc.id AS catidnm, cc.title, cc.description, cc.alias, cc.access, COUNT(c.id) AS numdoc, 0 AS numsubcat "
				/*. " ("
				. " SELECT COUNT(sc.id)"
				. " FROM #__categories AS cc"
				. " LEFT JOIN #__categories AS sc ON sc.parent_id = cc.id"
				. " WHERE cc.id = catidnm"
				. ") AS numsubcat"*/
				. " FROM #__categories AS cc"
				. " LEFT JOIN #__content AS c ON c.catid = cc.id". $cState
				//" LEFT JOIN #__content AS c ON c.catid = cc.id AND c.state = 1" // condition on left join
				. " WHERE " . implode( " AND ", $wheres )
				. " GROUP BY cc.id"
				. " ORDER BY cc.".$categoriesOrdering;
		
		
		return $query;
	}
	
	
	/* 
	 * Get only first level under parent categories
	 */
	 
	function _getCategoryListQuery( $parentCatId ) {
		
		$wheres		= array();
		$app		= JFactory::getApplication();
		$params 	= $app->getParams();
		$user 		= JFactory::getUser();
		$userLevels	= implode (',', $user->getAuthorisedViewLevels());
			
		$display_categories = $params->get('display_categories', array());
		$hide_categories 	= $params->get('hide_categories', array());

		if ( !empty($display_categories )) {
			$wheres[] = " cc.id IN (".implode(',', $display_categories).")";
		}
		
		if ( !empty($hide_categories ) ) {
			$wheres[] = " cc.id NOT IN (".implode(',',$hide_categories).")";
		}
		$wheres[] = " cc.parent_id = ".(int)$parentCatId;
		$wheres[] = " cc.published = 1";
		$wheres[] = " cc.extension = 'com_content'";
		$wheres[] = " cc.access IN (".$userLevels.")";
		
		$categoryOrdering = $this->_getCategoryOrdering();
		
		
		$query = " SELECT  cc.id, cc.id as catidnm, cc.title, cc.alias, cc.access, COUNT(c.id) AS numdoc,"
				. " ("
				. " SELECT COUNT(sc.id)"
				. " FROM #__categories AS cc"
				. " LEFT JOIN #__categories AS sc ON sc.parent_id = cc.id"
				. " WHERE cc.id = catidnm"
				. ") AS numsubcat"
				. " FROM #__categories AS cc"
				. " LEFT JOIN #__content AS c ON c.catid = cc.id"
				. " WHERE " . implode( " AND ", $wheres )
				. " GROUP BY cc.id"
				. " ORDER BY cc.".$categoryOrdering;
		
		return $query;
		
		
	}
	
	function getMostViewedDocsList() {
		
		if (empty($this->_most_viewed_docs)) {			
			$query						= $this->_getMostViewedDocsListQuery();
			$this->_most_viewed_docs 	= $this->_getList( $query );
		}
		return $this->_most_viewed_docs;
	}
	
	function _getMostViewedDocsListQuery() {
		
		$wheres		= array();
		$app		= JFactory::getApplication();
		$params 	= $app->getParams();
		$user 		= JFactory::getUser();
		$userLevels	= implode (',', $user->getAuthorisedViewLevels());
		
		$most_viewed_docs_num 	= $params->get( 'most_viewed_docs_num', 5 );
		$display_categories = $params->get('display_categories', array());
		$hide_categories 	= $params->get('hide_categories', array());

		if ( !empty($display_categories )) {
			$wheres[] = " cc.id IN (".implode(',', $display_categories).")";
		}
		
		if ( !empty($hide_categories ) ) {
			$wheres[] = " cc.id NOT IN (".implode(',',$hide_categories).")";
		}
		

		$wheres[]	= " c.catid= cc.id";
		$wheres[]	= " c.state= 1";
		$wheres[] 	= " cc.access IN (".$userLevels.")";
		$wheres[] 	= " c.access IN (".$userLevels.")";
		
		
		// Active
		$jnow		= JFactory::getDate();
		$now		= $jnow->toSql();
		$nullDate	= $this->_db->getNullDate();
		$wheres[] = ' ( c.publish_up = '.$this->_db->Quote($nullDate).' OR c.publish_up <= '.$this->_db->Quote($now).' )';
		$wheres[] = ' ( c.publish_down = '.$this->_db->Quote($nullDate).' OR c.publish_down >= '.$this->_db->Quote($now).' )';
		
		
		$query = " SELECT c.*, cc.id AS categoryid, cc.access as cataccess, cc.title AS categorytitle, cc.alias AS categoryalias "
				." FROM #__categories AS cc"
				. " LEFT JOIN #__content AS c ON c.catid = cc.id"
				. " WHERE " . implode( " AND ", $wheres )
				. " ORDER BY c.hits DESC"
				. " LIMIT ".(int)$most_viewed_docs_num;
		return $query;
	}
	
	function _getCategoryOrdering() {
		if (empty($this->_category_ordering)) {
	
			$app						= JFactory::getApplication();
			$params 					= $app->getParams();
			$ordering					= $params->get( 'category_ordering', 1 );
			//$pC 					= JComponentHelper::getParams('com_content') ;
			//$p['orderby_pri']	= $pC->get( 'orderby_pri',  '');
			//$p['orderby_sec']	= $pC->get( 'orderby_sec',  '');
			//$this->_category_ordering =  $p['orderby_pri'];
			$this->_category_ordering 	= PhocaDocumentationHelperFront::getOrderingText($ordering, true);
			
			

		}
		return $this->_category_ordering;
	}
}
?>