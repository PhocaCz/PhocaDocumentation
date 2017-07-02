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

class PhocaDocumentationModelCategory extends JModelLegacy
{
	var $_document 			= null;
	var $_category 			= null;
	var $_subcategories 	= null;
	var $_article_ordering		= null;
	var $_category_ordering	= null;
	var $_pagination		= null;
	var $_total				= null;

	function __construct() {
		
		$app	= JFactory::getApplication();
		
		parent::__construct();
		
		$config = JFactory::getConfig();		
		
		$paramsC 			= JComponentHelper::getParams('com_phocadocumentation') ;
		$defaultPagination	= $paramsC->get( 'default_pagination', '20' );
		
		// Get the pagination request variables
		$this->setState('limit', $app->getUserStateFromRequest('com_phocadocumentation.limit', 'limit', $defaultPagination, 'int'));
		$this->setState('limitstart',  $app->input->get('limitstart', 0, 'int'));

		// In case limit has been changed, adjust limitstart accordingly
		$this->setState('limitstart', ($this->getState('limit') != 0 ? (floor($this->getState('limitstart') / $this->getState('limit')) * $this->getState('limit')) : 0));

		// Get the filter request variables
		$this->setState('filter_order', JFactory::getApplication()->input->get('filter_order', 'ordering'));
		$this->setState('filter_order_dir', JFactory::getApplication()->input->get('filter_order_Dir', 'ASC'));
		
	}
	
	function getPagination($categoryId) {
		if (empty($this->_pagination)) {
			jimport('joomla.html.pagination');
			$this->_pagination = new PhocaPagination( $this->getTotal($categoryId), $this->getState('limitstart'), $this->getState('limit') );
		}
		return $this->_pagination;
	}
	
	function getTotal($categoryId) {
		if (empty($this->_total)) {
			$query = $this->_getArticleListQuery($categoryId);
			$this->_total = $this->_getListCount($query);
		}
		return $this->_total;
	}

	function getArticleList($categoryId) {
		if (empty($this->_document)) {	
			$query			= $this->_getArticleListQuery( $categoryId);
			$this->_document= $this->_getList( $query ,$this->getState('limitstart'), $this->getState('limit'));
		}
		return $this->_document;
	}
	
	function getCategory($categoryId) {	
		if (empty($this->_category)) {			
			$query					= $this->_getCategoriesQuery( $categoryId, FALSE );
			$this->_category 		= $this->_getList( $query, 0, 1 );
		}
		return $this->_category;
	}
	
	function getSubcategories($categoryId) {	
		if (empty($this->_subcategories)) {			
			$query					= $this->_getCategoriesQuery( $categoryId, TRUE );
			$this->_subcategories 	= $this->_getList( $query );
		}
		return $this->_subcategories;
	}
	
	function _getArticleListQuery( $categoryId ) {
	
		$wheres		= array();
		$app		= JFactory::getApplication();
		$params 	= $app->getParams();
		$user 		= JFactory::getUser();
		$userLevels	= implode (',', $user->getAuthorisedViewLevels());
		
		$wheres[]	= ' c.catid= '.(int)$categoryId;

		
		$wheres[] = ' c.state = 1';
		$wheres[] = ' cc.published = 1';
		
		$wheres[] 	= " c.access IN (".$userLevels.")";
		
		// Active
		$jnow		= JFactory::getDate();
		$now		= $jnow->toSql();
		$nullDate	= $this->_db->getNullDate();
		$wheres[] = ' ( c.publish_up = '.$this->_db->Quote($nullDate).' OR c.publish_up <= '.$this->_db->Quote($now).' )';
		$wheres[] = ' ( c.publish_down = '.$this->_db->Quote($nullDate).' OR c.publish_down >= '.$this->_db->Quote($now).' )';
		
		
		$articleOrdering = $this->_getArticleOrdering();
		
		$query = ' SELECT c.*, cc.id AS categoryid, cc.title AS categorytitle, cc.alias AS categoryalias, cc.access as cataccess'
				.' FROM #__content AS c'
				.' LEFT JOIN #__categories AS cc ON cc.id = c.catid'
				. ' WHERE ' . implode( ' AND ', $wheres )
				. ' ORDER BY c.'.$articleOrdering;
				
		return $query;
	}
	
	
	
	function _getCategoriesQuery( $categoryId, $subcategories = FALSE ) {
		
		$wheres		= array();
		$app		= JFactory::getApplication();
		$params 	= $app->getParams();
		$user 		= JFactory::getUser();
		$userLevels	= implode (',', $user->getAuthorisedViewLevels());
		
		
		// Get the current category or get parent categories of the current category
		if ($subcategories) {
			$wheres[]			= " cc.parent_id = ".(int)$categoryId;
			$categoryOrdering 	= $this->_getCategoryOrdering();
		} else {
			$wheres[]	= " cc.id= ".(int)$categoryId;
		}
		
		$wheres[] = " cc.access IN (".$userLevels.")";
		$wheres[] = " cc.published = 1";
		$wheres[] = " cc.extension = 'com_content'";
		
		$display_categories = $params->get('display_categories', array());
		$hide_categories 	= $params->get('hide_categories', array());

		if ( !empty($display_categories )) {
			$wheres[] = " cc.id IN (".implode(',', $display_categories).")";
		}
		
		if ( !empty($hide_categories ) ) {
			$wheres[] = " cc.id NOT IN (".implode(',',$hide_categories).")";
		}
		
		
		if ($subcategories) {
			$query = " SELECT  cc.id, cc.id AS catidnm, cc.title, cc.alias, cc.access as cataccess, COUNT(c.id) AS numdoc,"
				. " ("
				. " SELECT COUNT(sc.id)"
				. " FROM #__categories AS cc"
				. " LEFT JOIN #__categories AS sc ON sc.parent_id = cc.id"
				. " WHERE cc.id = catidnm"
				. ") AS numsubcat"
				. " FROM #__categories AS cc"
				. " LEFT JOIN #__content AS c ON c.catid = cc.id AND c.state = 1"
				. " WHERE " . implode( " AND ", $wheres )
				. " GROUP BY cc.id"
				. " ORDER BY cc.".$categoryOrdering;
		} else {
			$query = " SELECT cc.id, cc.title, cc.alias, cc.access as cataccess, cc.description, cc.metakey, cc.metadesc, pc.title as parenttitle, cc.parent_id as parentid, pc.alias as parentalias"
				. " FROM #__categories AS cc"
				. " LEFT JOIN #__categories AS pc ON pc.id = cc.parent_id"
				. " WHERE " . implode( " AND ", $wheres )
				. " ORDER BY cc.lft";
		}
		
		return $query;
	}
	
	
	function _getArticleOrdering() {
		if (empty($this->_article_ordering)) {
			$app						= JFactory::getApplication();
			$params						= $app->getParams();
			$ordering					= $params->get( 'article_ordering', 1 );
			$this->_article_ordering 		= PhocaDocumentationHelperFront::getOrderingText($ordering);

		}
		return $this->_article_ordering;
	}
	
	function _getCategoryOrdering() {
		if (empty($this->_category_ordering)) {
	
			global $mainframe;
			$app						= JFactory::getApplication();
			$params						= $app->getParams();
			$ordering					= $params->get( 'category_ordering', 1 );
			$this->_category_ordering 	= PhocaDocumentationHelperFront::getOrderingText($ordering, true);

		}
		return $this->_category_ordering;
	}
}
?>