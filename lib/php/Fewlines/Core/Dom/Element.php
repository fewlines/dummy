<?php
namespace Fewlines\Core\Dom;

class Element extends Element\Renderer
{
    /**
     * @var string
     */
    const DIV_TAG = 'div';

    /**
     * @var string
     */
    const DIV_STR = '<div %s>%s</div>';

    /**
     * @var string
     */
    const INPUT_TAG = 'input';

    /**
     * @var string
     */
    const INPUT_STR = '<input %s/>';

    /**
     * @var string
     */
    const SPAN_TAG = 'span';

    /**
     * @var string
     */
    const SPAN_STR = '<span %s>%s</span>';

    /**
     * @var string
     */
    const META_TAG = 'meta';

    /**
     * @var string
     */
    const META_STR = '<meta %s/>';

    /**
     * @var string
     */
    const FORM_TAG = 'form';

    /**
     * @var string
     */
    const FORM_STR = '<form %s>%s</form>';

    /**
     * @var string
     */
    const SELECT_TAG = 'select';

    /**
     * @var string
     */
    const SELECT_STR = '<select %s>%s</select>';

    /**
     * @var string
     */
    const TEXTAREA_TAG = 'textarea';

    /**
     * @var string
     */
    const TEXTAREA_STR = '<textarea %s>%s</textarea>';

    /**
     * @var string
     */
    const OPTION_TAG = 'option';

    /**
     * @var string
     */
    const OPTION_STR = '<option %s>%s</option>';

    /**
     * @var string
     */
    protected $content = '';

    /**
     * @var array
     */
    protected $attributes = array();

    /**
     * @var string
     */
    protected $domTag;

    /**
     * @var string
     */
    protected $domStr;

    /**
     * @var array
     */
    protected $children = array();

    /**
     * @param string $content
     */
    public function setContent($content) {
        $this->content = $content;
    }

    /**
     * @return string
     */
    public function getContent() {
        return $this->content;
    }

    /**
     * @param array $children
     */
    public function setChildren($children) {
        $this->children = $children;
    }

    /**
     * @param \Fewlines\Core\Dom\Element $child
     */
    public function addChild(\Fewlines\Core\Dom\Element $child) {
        $this->children[] = $child;
    }

    /**
     * @param string $domStr
     */
    public function setDomStr($domStr) {
        $this->domStr = $domStr;
    }

    /**
     * @param string $domTag
     */
    public function setDomTag($domTag) {
        $this->domTag = $domTag;
    }

    /**
     * @return string
     */
    public function getDomTag() {
        return $this->domTag;
    }

    public function setAttributes($attributes) {
        if (false == is_array($attributes)) {
            throw new Exception\InvalidElementAttributesTypeException("
				The attributes given has an invlid type.
				Excepting array.
			");
        }

        $this->attributes = $attributes;
    }

    /**
     * @param  string $name
     * @return string
     */
    public function getAttribute($name) {
        if (true == array_key_exists($name, $this->attributes)) {
            return (string)$this->attributes[$name];
        }

        return '';
    }

    /**
     * @return array
     */
    public function getAttributes() {
        return $this->attributes;
    }

    /**
     * @param  array   $children
     * @param  integer $childrenPos
     * @return string
     */
    public function render($children = array(), $childrenPos = 0) {
        $content = '';

        if ($childrenPos === 0) {
            $this->appendChildrenContent($content, $children);
        }

        $content.= $this->content;

        if ($childrenPos === 1) {
            $this->appendChildrenContent($content, $children);
        }

        // Append default children
        $this->appendChildrenContent($content, $this->children);

        if ($childrenPos != 0 && $childrenPos != 1) {
            $this->appendChildrenContent($content, $children);
        }

        return $this->renderStr($this->domStr, $this->getAttributeString(), $content);
    }

    /**
     * @param  string $content
     * @param  array  $children
     * @return string
     */
    public function appendChildrenContent(&$content, $children) {
        for ($i = 0, $len = count($children); $i < $len; $i++) {
            if ($children[$i] instanceof \Fewlines\Core\Dom\Element) {
                $content.= $children[$i]->render();
            }
        }
    }

    /**
     * @param  array $attributes
     * @return string
     */
    public function getAttributeString() {
        $attrStr = '';

        foreach ($this->getAttributes() as $name => $content) {
            if (trim($content) == "false") {
                continue;
            }

            // Do not insert content for "boolean attributes"
            if (trim($content) == "true") {
                $attrStr.= $name . ' ';
                continue;
            }

            $attrStr.= $name . '="' . $content . '" ';
        }

        return $attrStr;
    }
}
