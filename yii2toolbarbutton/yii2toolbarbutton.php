<?php

 /**
 * This class is merely used to publish a TOC based upon the headings within a defined container
 * @copyright Frenzel GmbH - www.frenzel.net
 * @link http://www.frenzel.net
 * @author Philipp Frenzel <philipp@frenzel.net>
 *
 */

namespace yii2toolbarbutton;

use Yii;

use yii\base\Model;
use yii\base\View;
use yii\base\InvalidConfigException;

use yii\helpers\Html;
use yii\helpers\Json;
use yii\helpers\ArrayHelper;

use yii\base\Widget as Widget;

class yii2toolbarbutton extends Widget
{

    /**
     * @var array list of slides in the imageslider. Each array element represents a single
     * slide with the following structure:
     *
     * ```php
     * array(
     *     // required, slide content (HTML), such as an image tag
     *     'action' => 'site/view',
     *     'content' => '<i class="icon-signal"></i>'
     * )
     * ```
     */
    public $items = array();

    /**
    * @var array the HTML attributes (name-value pairs) for the field container tag.
    * The values will be HTML-encoded using [[Html::encode()]].
    * If a value is null, the corresponding attribute will not be rendered.
    */
    public $options = array(
        'class' => 'btToolbarWrap',
    );


    /**
     * can contain all configuration options
    * @var array all attributes that be accepted by the plugin, check docs!
    * visible_items
    */
    public $clientOptions = array(
        'position' => 'top',
        'hideOnClick' => true
    );

    /**
     * Initializes the widget.
     * If you override this method, make sure you call the parent implementation first.
     */
    public function init()
    {
        //checks for the element id
        if (!isset($this->options['id'])) {
            $this->options['id'] = $this->getId();
        }

        parent::init();
    }

    /**
     * Renders the widget.
     */
    public function run()
    {
        Html::addCssClass($this->options,'icon icon-wrench');
        echo Html::tag('i',' ',array_merge($this->options,array('style'=>'cursor: pointer'))) . "\n";
        echo Html::beginTag('div', array('id'=>$this->options['id'].'-options','style'=>'display: none;')) . "\n";            
            echo $this->renderItems() . "\n";                    
        echo Html::endTag('div') . "\n";
        $this->registerPlugin();
    }

    /**
    * Registers a specific dhtmlx widget and the related events
    * @param string $name the name of the dhtmlx plugin
    */
    protected function registerPlugin()
    {
        $id = $this->options['id'];

        //get the displayed view and register the needed assets
        $view = $this->getView();
        yii2toolbarbuttonAsset::register($view);

        $js = array();
        
        $className = $this->options['class'];

        //merge the content option into clientoptions
        $this->clientOptions = array_merge(array(
            'content' => '#'.$this->options['id'].'-options',
        ), $this->options);
        $options = empty($this->clientOptions) ? '' : Json::encode($this->clientOptions);

        $js[] = "jQuery('#$id').toolbar($options);";
        
        $view->registerJs(implode("\n", $js),View::POS_READY);
    }

    /**
     * Renders carousel items as specified on [[items]].
     * @return string the rendering result
     */
    public function renderItems()
    {
        $items = array();
        for ($i = 0, $count = count($this->items); $i < $count; $i++) {
            $items[] = $this->renderItem($this->items[$i], $i);
        }
        return implode("\n", $items);
    }

    /**
     * Renders a single carousel item
     * @param string|array $item a single item from [[items]]
     * @param integer $index the item index as the first item should be set to `active`
     * @return string the rendering result
     * @throws InvalidConfigException if the item is invalid
     */
    public function renderItem($item, $index)
    {
        $options = array();
        if(isset($item['action'])) {
            $content = $item['content'];
            $action = $item['action'];
        } else {
            throw new InvalidConfigException('The "action" option is required.');
        }

        return Html::a($content, $action, $options);
    }

}
