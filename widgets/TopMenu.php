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
class TopMenu extends \yii\widgets\Menu {
    /**
     * @inheritdoc
     */
    public $labelTemplate = '{label}';

    /**
     * @inheritdoc
     */
    public $linkTemplate = '<a href="{url}"> <span>{label}</span>{badge}</a>';

    public $dropdownLinkTemplate = '<a href="{url}" class="dropdown-toggle" data-toggle="dropdown"> <span>{label}</span>{badge} <span class="caret"></span></a>';

    /**
     * @inheritdoc
     */
    public $submenuTemplate = "\n<ul class=\"dropdown-menu\">\n{items}\n</ul>\n";

    /**
     * @inheritdoc
     */
    public $activateParents = true;

    /**
     * @inheritdoc
     */
    public $activeCssClass = 'active';

    /**
     * @inheritdoc
     */


    public function init() {
        Html::addCssClass($this->options, 'nav navbar-nav');
        $this->options['data']['widget'] = 'tree';
        parent::init();
    }

    /**
     * @inheritdoc
     */
    protected function renderItem($item) {

        if (isset($item['items'])) {
            $item['template'] = $this->dropdownLinkTemplate;
        }

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
                ),
            ]
        );
    }

    /**
     * @inheritdoc
     */
    protected function normalizeItems($items, &$active) {
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
                    $items[$i]['options']['class'] .= ' dropdown';
                } else {
                    $items[$i]['options']['class'] = 'dropdown';
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
                $active = $items[$i]['active'] = call_user_func($item['active'], $item, $hasActiveChild,
                    $this->isItemActive($item), $this);
            } elseif ($item['active']) {
                $active = true;
            }
        }

        return array_values($items);
    }

    /**
     * @inheritdoc
     */
    protected function isItemActive($item) {
        if (isset($item['url']) && is_array($item['url']) && isset($item['url'][0])) {
            $route = Yii::getAlias($item['url'][0]);
            if ($route[0] !== '/' && Yii::$app->controller) {
                $route = Yii::$app->controller->module->getUniqueId() . '/' . $route;
            }

            if (
                ($route == '/' && $this->route == 'site/index')
                || (substr($route, -1) == '/' && $this->route == ltrim($route, '/') . 'index')
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
