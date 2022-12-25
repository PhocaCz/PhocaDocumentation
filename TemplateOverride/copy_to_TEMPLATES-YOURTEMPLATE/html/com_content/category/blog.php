<?php

/**
 * @package     Joomla.Site
 * @subpackage  com_content
 *
 * @copyright   (C) 2006 Open Source Matters, Inc. <https://www.joomla.org>
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Helper\ModuleHelper;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Language;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Layout\FileLayout;
use Joomla\CMS\Layout\LayoutHelper;
use Joomla\CMS\Router\Route;
use Joomla\Component\Content\Administrator\Extension\ContentComponent;
use Joomla\Component\Content\Site\Helper\RouteHelper;
use Joomla\Registry\Registry;


// Get path for the svg-definitions.svg - this can be path to media folder:
// com_phocadocumentation: media/com_phocadocumentation/images/svg-definitions.svg
// or override in template: media/templates/site/cassiopeia/images/com_phocadocumentation/svg-definitions.svg
$this->pdoc = new stdClass();
$this->pdoc->svg_path = HTMLHelper::image('com_phocadocumentation/svg-definitions.svg', '', [], true, 1);

// OPTIONS
$module = ModuleHelper::getModule('mod_phocadocumentation_category');
$this->pdoc->tmpl_hide_categories = [];
if (isset($module->id) && $module->id > 0 && !empty($module->params)) {

    $registry = new Registry;
    $registry->loadString($module->params);
    $params = $registry;
    $tmpl_hide_categories = $params->get('tmpl_hide_categories', '');
    $this->pdoc->tmpl_hide_categories = array_map('intval', explode(',', $tmpl_hide_categories));
}

$wa = $this->document->getWebAssetManager();
$wa->registerAndUseStyle('com_phocadocumentation.main', 'media/com_phocadocumentation/css/main.css', array('version' => 'auto'));

$app = Factory::getApplication();

$this->category->text = $this->category->description;
$app->triggerEvent('onContentPrepare', array($this->category->extension . '.categories', &$this->category, &$this->params, 0));
$this->category->description = $this->category->text;

$results = $app->triggerEvent('onContentAfterTitle', array($this->category->extension . '.categories', &$this->category, &$this->params, 0));
$afterDisplayTitle = trim(implode("\n", $results));

$results = $app->triggerEvent('onContentBeforeDisplay', array($this->category->extension . '.categories', &$this->category, &$this->params, 0));
$beforeDisplayContent = trim(implode("\n", $results));

$results = $app->triggerEvent('onContentAfterDisplay', array($this->category->extension . '.categories', &$this->category, &$this->params, 0));
$afterDisplayContent = trim(implode("\n", $results));

$htag    = $this->params->get('show_page_heading') ? 'h2' : 'h1';

?>
<div class="com-content-category-blog com-phocadocumentation blog " itemscope itemtype="https://schema.org/Blog">

    <?php

    if (isset($this->category->parent_id) && ((int)$this->category->parent_id > 0)) {

        $parent = $this->category->getParent();
        echo '<a class="btn btn-primary" href="'. Route::_(RouteHelper::getCategoryRoute($parent->id, $parent->language)).'">';
        echo '<svg class="pdoc-si pdoc-si-prev"><use xlink:href="'.$this->pdoc->svg_path.'#pdoc-si-prev"></use></svg>';
        echo $this->escape($parent->title);
        echo '</a>';

    } else {

        $parent = $this->category->getParent();
        $link = 'index.php?option=com_content&view=categories&id=0';
        if ($parent->language && $parent->language !== '*' && Multilanguage::isEnabled()) {
            $link .= '&lang=' . $parent->language;
        }
        echo '<a class="btn btn-primary" href="'. Route::_($link).'">';
        echo '<svg class="pdoc-si pdoc-si-prev"><use xlink:href="'.$this->pdoc->svg_path.'#pdoc-si-prev"></use></svg>';
        // echo Text::_('COM_CONTENT_CATEGORIES'); No such language file in frontend
        $newlanguage = new Language($parent->language, false);
        $newlanguage->load('joomla', JPATH_ADMINISTRATOR, $parent->language, true);
        $title = $newlanguage->_('JCATEGORIES');

        echo $title;
        echo '</a>';

    }





    if ($this->params->get('show_page_heading')) : ?>
        <div class="page-header">
            <h1> <?php echo $this->escape($this->params->get('page_heading')); ?> </h1>
        </div>
    <?php endif; ?>

    <?php if ($this->params->get('show_category_title', 1)) : ?>
    <<?php echo $htag; ?>>
        <?php echo $this->category->title; ?>
    </<?php echo $htag; ?>>
    <?php endif; ?>
    <?php echo $afterDisplayTitle; ?>

    <?php if ($this->params->get('show_cat_tags', 1) && !empty($this->category->tags->itemTags)) : ?>
        <?php $this->category->tagLayout = new FileLayout('joomla.content.tags'); ?>
        <?php echo $this->category->tagLayout->render($this->category->tags->itemTags); ?>
    <?php endif; ?>

    <?php if ($beforeDisplayContent || $afterDisplayContent || $this->params->get('show_description', 1) || $this->params->def('show_description_image', 1)) : ?>
        <div class="category-desc clearfix">
            <?php if ($this->params->get('show_description_image') && $this->category->getParams()->get('image')) : ?>
                <?php echo LayoutHelper::render(
                    'joomla.html.image',
                    [
                        'src' => $this->category->getParams()->get('image'),
                        'alt' => empty($this->category->getParams()->get('image_alt')) && empty($this->category->getParams()->get('image_alt_empty')) ? false : $this->category->getParams()->get('image_alt'),
                    ]
                ); ?>
            <?php endif; ?>
            <?php echo $beforeDisplayContent; ?>
            <?php if ($this->params->get('show_description') && $this->category->description) : ?>
                <?php echo HTMLHelper::_('content.prepare', $this->category->description, '', 'com_content.category'); ?>
            <?php endif; ?>
            <?php echo $afterDisplayContent; ?>
        </div>
    <?php endif;




    // Articles
    if (!empty($this->items)) {

        $currentDate   = Factory::getDate()->format('Y-m-d H:i:s');

        echo '<ul class="list-group list-group-flush pdoc-category-article-list">';

        foreach ($this->items as $item) {

            $isUnpublished = ($item->state == ContentComponent::CONDITION_UNPUBLISHED || $item->publish_up > $currentDate) || ($item->publish_down < $currentDate && $item->publish_down !== null);

            if ($isUnpublished) {
                continue;
            }

            $link = RouteHelper::getArticleRoute($item->slug, $item->catid, $item->language);

            echo '<li class="list-group-item">';

            echo '<svg class="pdoc-si pdoc-si-article"><use xlink:href="'.$this->pdoc->svg_path.'#pdoc-si-article"></use></svg>';

            if ($item->params->get('access-view') || $item->params->get('show_noauth', '0') == '1'){
                echo '<a href="'.Route::_($link).'" itemprop="url">';
                echo $this->escape($item->title);
                echo '</a>';
            } else {
                echo $this->escape($item->title);
            }

            echo '</li>';

        }

        echo '</ul>';
    }





    if ($this->maxLevel != 0 && !empty($this->children[$this->category->id])) : ?>
        <div class="com-content-category-blog__children cat-children">
            <?php if ($this->params->get('show_category_heading_title_text', 1) == 1) : ?>
                <h3> <?php echo Text::_('JGLOBAL_SUBCATEGORIES'); ?> </h3>
            <?php endif; ?>
            <?php echo $this->loadTemplate('children'); ?> </div>
    <?php endif; ?>
    <?php if (($this->params->def('show_pagination', 1) == 1 || ($this->params->get('show_pagination') == 2)) && ($this->pagination->pagesTotal > 1)) : ?>
        <div class="com-content-category-blog__navigation w-100">
            <?php if ($this->params->def('show_pagination_results', 1)) : ?>
                <p class="com-content-category-blog__counter counter float-end pt-3 pe-2">
                    <?php echo $this->pagination->getPagesCounter(); ?>
                </p>
            <?php endif; ?>
            <div class="com-content-category-blog__pagination">
                <?php echo $this->pagination->getPagesLinks(); ?>
            </div>
        </div>
    <?php endif; ?>
</div>
