<?php
/**
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE file
 * Redistributions of files must retain the above copyright notice.
 * You may obtain a copy of the License at
 *
 *     https://opensource.org/licenses/mit-license.php
 *
 *
 * @copyright Copyright (c) Mikaël Capelle (https://typename.fr)
 * @license https://opensource.org/licenses/mit-license.php MIT License
 */
namespace Bootstrap\View\Helper;

trait BootstrapTrait {

    /**
     * Set to false to disable easy icon processing.
     *
     * @var bool
     */
    public $easyIcon = true;

    /**
     * Adds the given class to the element options.
     *
     * @param array        $options Array of options/attributes to add a class to.
     * @param string|array $class   The class names to be added.
     * @param string       $key     The key to use for class (default to `'class'`).
     *
     * @return array Array of options with `$key` set or updated.
     */
    public function addClass(array $options = [], $class = null, $key = 'class') {
        if (!is_array($class)) {
            $class = explode(' ', trim($class));
        }
        $optClass = [];
        if (isset($options[$key])) {
            $optClass = $options[$key];
            if (!is_array($optClass)) {
                $optClass = explode(' ', trim($optClass));
            }
        }
        $class = array_merge($optClass, $class);
        $class = array_map('trim', $class);
        $class = array_unique($class);
        $class = array_filter($class);
        $options[$key] = implode(' ', $class);
        return $options;
    }

    /**
     * Add classes to options according to the default values of bootstrap-type
     * and bootstrap-size for button (see configuration).
     *
     * @param array $options The initial options with bootstrap-type and/or
     * bootstrat-size values.
     *
     * @return array The new options with class values (btn, and btn-* according to
     * initial options).
     */
    protected function _addButtonClasses($options) {
        $options += [
            'bootstrap-type' => $this->config('buttons.type'),
            'bootstrap-size' => false
        ];
        $type = $options['bootstrap-type'];
        $size = $options['bootstrap-size'];
        unset($options['bootstrap-type'], $options['bootstrap-size']);
        $options = $this->addClass($options, 'btn');
        if (!preg_match('#btn-[a-z]+#', $options['class'])) {
            $options = $this->addClass($options, 'btn-'.$type);
        }
        if ($size) {
            $options = $this->addClass($options, 'btn-'.$size);
        }
        return $options;
    }

    /**
     * Check weither the specified array is associative or not.
     *
     * @param array $array The array to check.
     *
     * @return bool `true` if the array is associative, `false` otherwize.
     */
    protected function _isAssociativeArray($array) {
        return array_keys($array) !== range(0, count($array) - 1);
    }

    /**
     * Try to convert the specified string to a bootstrap icon. The string is converted if
     * it matches a format `i:icon-name` (leading and trailing spaces or ignored) and if
     * easy-icon is activated.
     *
     * **Note:** This function will currently fail if the Html helper associated with the
     * view is not BootstrapHtmlHelper.
     *
     * @param string $text      The string to convert.
     * @param bool   $converted If specified, will contains `true` if the text was converted,
     * `false` otherwize.
     *
     * @return string The text after conversion.
     */
    protected function _makeIcon($text, &$converted = false) {
        $converted = false;
        if (!$this->easyIcon) {
            return $text;
        }
        $text = preg_replace_callback(
            '#(^|\s+)i:([a-zA-Z0-9\\-_]+)(\s+|$)#', function ($matches) {
                return $matches[1].$this->_View->Html->icon($matches[2]).$matches[3];
            }, $text, -1, $count);
        $converted = (bool)$count;
        return $text;
    }

    /**
     * This method calls the given callback with the specified argument (`$title` and
     * `$options`) after applying a filter on them.
     *
     * **Note:** Currently this method only works for function that take
     * two arguments ($title and $options).
     *
     * @param callable $callback The callback.
     * @param string   $title    The first argument for the callback.
     * @param array    $options  The second argument for the calback.
     *
     * @return mixed Whatever might be returned by $callback.
     */
    protected function _easyIcon($callback, $title, $options) {
        $title = $this->_makeIcon($title, $converted);
        if ($converted) {
            $options += [
                'escape' => false
            ];
        }
        return call_user_func($callback, $title, $options);
    }

}

?>