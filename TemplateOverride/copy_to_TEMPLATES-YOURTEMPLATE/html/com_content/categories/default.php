<?php

/**
 * @package     Joomla.Site
 * @subpackage  com_content
 *
 * @copyright   (C) 2009 Open Source Matters, Inc. <https://www.joomla.org>
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

use Joomla\CMS\Helper\ModuleHelper;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\Registry\Registry;

/*
// Add strings for translations in Javascript.
Text::script('JGLOBAL_EXPAND_CATEGORIES');
Text::script('JGLOBAL_COLLAPSE_CATEGORIES');

/** @var Joomla\CMS\WebAsset\WebAssetManager $wa *//*
$wa = $this->document->getWebAssetManager();
$wa->getRegistry()->addExtensionRegistryFile('com_categories');
$wa->usePreset('com_categories.shared-categories-accordion');
*/

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

$displayData = $this;
?>
<div class="com-content-categories com-phocadocumentation categories-list">
    <?php //echo LayoutHelper::render('joomla.content.categories_default', $this); ?>
<?php if ($displayData->params->get('show_page_heading')) : ?>
<h1>
    <?php echo $displayData->escape($displayData->params->get('page_heading')); ?>
</h1>
<?php endif; ?>

<?php if ($displayData->params->get('show_base_description')) : ?>
    <?php // If there is a description in the menu parameters use that; ?>
    <?php if ($displayData->params->get('categories_description')) : ?>
        <div class="category-desc base-desc">
            <?php echo HTMLHelper::_('content.prepare', $displayData->params->get('categories_description'), '', $displayData->get('extension') . '.categories'); ?>
        </div>
    <?php else : ?>
        <?php // Otherwise get one from the database if it exists. ?>
        <?php if ($displayData->parent->description) : ?>
            <div class="category-desc base-desc">
                <?php echo HTMLHelper::_('content.prepare', $displayData->parent->description, '', $displayData->parent->extension . '.categories'); ?>
            </div>
        <?php endif; ?>
    <?php endif; ?>
<?php endif;
echo $this->loadTemplate('items');
?>
</div>
