<?php
/**
 * @copyright Copyright (c) 2015 Factor Energia
 * @license https://github.com/promocat/yii2-adminlte/blob/master/LICENSE
 * @link http://adminlte.yiister.ru
 */

namespace promocat\adminlte\widgets;

use rmrevin\yii\fontawesome\component\Icon;
use Yii;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;

/**
 * Class Menu
 * @package promocat\adminlte\widgets
 */
class Menu extends \yii\widgets\Menu
{
    /**
     * @inheritdoc
     */
    public $labelTemplate = '{label}';

    /**
     * @inheritdoc
     */
    public $linkTemplate = '<a href="{url}"><span>{label}</span>{badge}</a>';

    /**
     * @inheritdoc
     */
    public $submenuTemplate = "\n<ul class=\"treeview-menu\">\n{items}\n</ul>\n";

    /**
     * @inheritdoc
     */
    public $activateParents = true;

    /**
     * @inheritdoc
     */
    public $activeCssClass = 'active';

    /**
     * @var bool whether to add menu searching or not (Searching in the menu elements).
     */
    public $menuSearching = false;

    /**
     * @var string The name of the menu. Used for dynamically registering menu items.
     */
    public $name = 'main';

    public function init()
    {
        Yii::beginProfile("Menu - register - event", __CLASS__);
        $this->trigger(static::getEventName($this->name));
        Yii::endProfile("Menu - register - event", __CLASS__);


        if ($this->menuSearching) {
            echo Html::tag('div',
                    Html::tag('div',
                        Html::textInput('sideSearch', '', [
                            'placeholder' => 'Search...',
                            'class' => 'form-control sideSearch'
                        ]) .
                        Html::tag('span', new  Icon('search'),
                            ['class' => 'form-control-feedback kv-feedback-default']), [
                            'class' => 'has-feedback'
                        ]), [
                        'class' => 'sidebar-form'
                    ]) . Html::tag('span', '',
                    ['class' => 'menu-separator', 'style' => 'display: block; border-bottom: solid 1px #D2D6DE;']);
        }

        Html::addCssClass($this->options, 'sidebar-menu');
        $this->options['data']['widget'] = 'tree';
        parent::init();
    }

    public static function getEventName($menuName)
    {
        return 'menu.' . $menuName . '.register';
    }

    public function addItem($item)
    {
        if (!isset($item['sortOrder'])) {
            $item['sortOrder'] = 1000;
        }
        $this->items[] = $item;
    }

    /**
     * @inheritdoc
     */
    protected function renderItem($item)
    {
        $renderedItem = parent::renderItem($item);
        if (isset($item['badge'])) {
            $badgeOptions = ArrayHelper::getValue($item, 'badgeOptions', []);
            Html::addCssClass($badgeOptions, 'label pull-right');
        } else {
            $badgeOptions = null;
        }
        return strtr(
            $renderedItem,
            [
                '{icon}' => isset($item['icon'])
                    ? new Icon($item['icon'], ArrayHelper::getValue($item, 'iconOptions', []))
                    : '',
                '{badge}' => (
                    isset($item['badge'])
                        ? Html::tag('small', $item['badge'], $badgeOptions)
                        : ''
                    ) . (
                    isset($item['items']) && count($item['items']) > 0
                        ? new Icon('chevron-left', ['class' => 'pull-right'])
                        : ''
                    ),
            ]
        );
    }

    /**
     * @inheritdoc
     */
    protected function normalizeItems($items, &$active)
    {
        $items = $this->sortItems($items);
        foreach ($items as $i => $item) {
            if (isset($item['visible']) && !$item['visible']) {
                unset($items[$i]);
                continue;
            }
            if (!isset($item['label'])) {
                $item['label'] = '';
            }
            $encodeLabel = isset($item['encode']) ? $item['encode'] : $this->encodeLabels;
            $items[$i]['label'] = $encodeLabel ? Html::encode($item['label']) : $item['label'];
            $hasActiveChild = false;
            if (isset($item['items'])) {
                if (isset($items[$i]['options']['class'])) {
                    $items[$i]['options']['class'] .= ' treeview';
                } else {
                    $items[$i]['options']['class'] = 'treeview';
                }
                $items[$i]['items'] = $this->normalizeItems($item['items'], $hasActiveChild);
                if (empty($items[$i]['items']) && $this->hideEmptyItems) {
                    unset($items[$i]['items']);
                    if (!isset($item['url'])) {
                        unset($items[$i]);
                        continue;
                    }
                }
            }
            if (!isset($item['active'])) {
                $activeItem = $this->isItemActive($item);
                if ($this->activateParents && $hasActiveChild || $this->activateItems && $activeItem) {
                    $active = $items[$i]['active'] = true;
                    if ($activeItem) {
                        if (isset($items[$i]['options']['class'])) {
                            $items[$i]['options']['class'] .= ' current';
                        } else {
                            $items[$i]['options']['class'] = 'current';
                        }
                    }
                } else {
                    $items[$i]['active'] = false;
                }
            } elseif ($item['active'] instanceof Closure) {
                $active = $items[$i]['active'] = $item['active']($item, $hasActiveChild, $this->isItemActive($item),
                    $this);
            } elseif ($item['active']) {
                $active = true;
            }
        }

        return array_values($items);
    }

    /**
     * Sorts the item attribute by sortOrder
     * @param array $items
     * @return
     */
    private function sortItems($items)
    {
        usort($items, function ($a, $b) {
            if (!isset($a['sortOrder']) || !isset($b['sortOrder']) || $a['sortOrder'] == $b['sortOrder']) {
                return 0;
            } else {
                if ($a['sortOrder'] < $b['sortOrder']) {
                    return -1;
                } else {
                    return 1;
                }
            }
        });
        return $items;
    }

    /**
     * @inheritdoc
     */
    protected function isItemActive($item)
    {
        if (isset($item['url']) && is_array($item['url']) && isset($item['url'][0])) {
            $route = Yii::getAlias($item['url'][0]);
            if ($route[0] !== '/' && Yii::$app->controller) {
                $route = Yii::$app->controller->module->getUniqueId() . '/' . $route;
            }

            if (
                ($route == '/' && $this->route == 'site/index')
                || (substr($route, -1) == '/' && $this->route == ltrim($route, '/') . 'index')
                || ltrim($route, '/') . '/index' == $this->route
                || ($this->route == ltrim($route, '/'))
            ) {
                unset($item['url']['#']);
                if (count($item['url']) > 1) {
                    $params = $item['url'];
                    unset($params[0]);
                    foreach ($params as $name => $value) {
                        if ($value !== null && (!isset($this->params[$name]) || $this->params[$name] != $value)) {
                            return false;
                        }
                    }
                }

                return true;
            } else {

                return false;
            }
        }

        return false;
    }
}