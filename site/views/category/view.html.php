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
jimport( 'joomla.application.component.view');

class PhocaDocumentationViewCategory extends JViewLegacy
{
	protected $category;
	protected $subcategories;
	protected $articles;
	protected $tmpl;
	protected $params;
	protected $uri;
	
	function display($tpl = null) {		
		
		$document			= JFactory::getDocument();
		$app				= JFactory::getApplication();
		$uri 				= JFactory::getURI();
		$this->params 		= $app->getParams();
		$this->uri 			= JFactory::getURI()->toString();
		$this->uri 			= str_replace('&amp;', '&', $this->uri);
		$this->uri 			= str_replace('&', '&amp;', $this->uri);
		$model				= $this->getModel();
		$this->categoryId	= JRequest::getVar( 'id', 0, '', 'int' );
		$limitStart			= JRequest::getVar( 'limitstart', 0, '', 'int' );
		
		$this->category				= $model->getCategory($this->categoryId);
		$this->subcategories		= $model->getSubcategories($this->categoryId);
		$this->articles				= $model->getArticleList($this->categoryId);
		$this->tmpl['pagination']	= $model->getPagination($this->categoryId);

		// Limit start
		if ($limitStart > 0 ) {
			$this->tmpl['limitstarturl'] =  '&start='.$limitStart;
		} else {
			$this->tmpl['limitstarturl'] = '';
		}

		$this->tmpl['display_new']			= $this->params->get( 'display_new', 0 );
		$this->tmpl['display_hot']			= $this->params->get( 'display_hot', 0 );
		$this->tmpl['display_up_icon'] 		= $this->params->get( 'display_up_icon', 1 );
		$this->tmpl['display_num_doc_cats']	= $this->params->get( 'display_num_doc_cats', 1 );
		$this->tmpl['display_hot_new_icon']	= $this->params->get( 'display_hot_new_icon', 1 );
		$this->tmpl['display_tags']			= $this->params->get( 'display_tags', 0 );
		$this->tmpl['tag_color']			= $this->params->get( 'tag_color', array() );
		$this->tmpl['tag_color_array']		= array();
		if(!empty($this->tmpl['tag_color'])) {
			$this->tmpl['tag_color_array']	= json_decode($this->tmpl['tag_color'], true);
		}
		
		$this->tmpl['pddc']					= PhocaDocumentationHelper::getPhocaId($this->params->get( 'display_id', 1 ));
		$this->tmpl['action']				= $uri->toString();
		
		
		// THEME, CSS, BOOTSTRAP
		$css								= $this->params->get( 'theme', 'phocadocumentation-grey' );
		$this->tmpl['load_bootstrap']		= $this->params->get( 'load_bootstrap', 0 );
		$this->tmpl['equal_height']			= $this->params->get( 'equal_height', 0 );
		
		if ($this->tmpl['load_bootstrap'] == 1) {
			JHTML::stylesheet('media/com_phocadocumentation/bootstrap/css/bootstrap.min.css' );
			$document->addScript(JURI::root(true).'/media/com_phocadocumentation/bootstrap/js/bootstrap.min.js');
		}
		if ($this->tmpl['equal_height'] == 1) {
			JHtml::_('jquery.framework', false);
			$document->addScript(JURI::root(true).'/media/com_phocadocumentation/js/jquery.equalheights.min.js');
			$document->addScriptDeclaration(
			'jQuery(document).ready(function(){
				jQuery(\'.ph-thumbnail\').equalHeights();
			});');
		}
		JHTML::stylesheet('media/com_phocadocumentation/css/'.$css.'.css' );
		

		$this->_prepareDocument();
		if ($css == 'phocadocumentation-bootstrap') {
			parent::display('bootstrap');	
		} else {
			parent::display($tpl);	
		}
		
	}
	
	protected function _prepareDocument() {
		
		$app		= JFactory::getApplication();
		$menus		= $app->getMenu();
		$pathway 	= $app->getPathway();
		$title 		= null;
		
		$menu = $menus->getActive();
		if ($menu) {
			$this->params->def('page_heading', $this->params->get('page_title', $menu->title));
		} else {
			$this->params->def('page_heading', JText::_('JGLOBAL_ARTICLES'));
		}

		$title = $this->params->get('page_title', '');
		if (empty($title)) {
			$title = htmlspecialchars_decode($app->getCfg('sitename'));
		} else if ($app->getCfg('sitename_pagetitles', 0)) {
			$title = JText::sprintf('JPAGETITLE', htmlspecialchars_decode($app->getCfg('sitename')), $title);
		}
		$this->document->setTitle($title);

		if (empty($title)) {
			$title = $this->item->title;
		}
		$this->document->setTitle($title);
		if ($this->params->get('menu-meta_description', '')) {
			$this->document->setDescription($this->params->get('menu-meta_description', ''));
		}

		if ($this->params->get('menu-meta_keywords', '')) {
			$this->document->setMetadata('keywords', $this->params->get('menu-meta_keywords', ''));
		}

		if ($app->getCfg('MetaTitle') == '1' && $this->params->get('menupage_title', '')) {
			$this->document->setMetaData('title', $this->params->get('page_title', ''));
		}

		/*if ($app->getCfg('MetaAuthor') == '1') {
			$this->document->setMetaData('author', $this->item->author);
		}

		/*$mdata = $this->item->metadata->toArray();
		foreach ($mdata as $k => $v) {
			if ($v) {
				$this->document->setMetadata($k, $v);
			}
		}*/
		
		// Breadcrumbs TODO (Add the whole tree)
		if (isset($this->category[0]->parentid)) {
			if ($this->category[0]->parentid == 1) {
			} else if ($this->category[0]->parentid > 0) {
				$pathway->addItem($this->category[0]->parenttitle, JRoute::_(PhocaDocumentationHelperRoute::getCategoryRoute($this->category[0]->parentid, $this->category[0]->parentalias)));
			}
		}

		if (!empty($this->category[0]->title)) {
			$pathway->addItem($this->category[0]->title);
		}
	}
}
?>