<?php
/**
 * This file is part of Vegas Exporter package.
 *
 * @author Radosław Fąfara <radek@amsterdam-standard.pl>
 * @copyright Amsterdam Standard Sp. Z o.o.
 * @homepage https://github.com/vegas-cmf/exporter
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code. *
 */

namespace Vegas\Exporter;

trait ConfigurableTrait
{
    /**
     * @param array $config
     * @return $this
     */
    public function bind(array $config = [])
    {
        $reflection = new \ReflectionObject($this);
        foreach ($reflection->getProperties(\ReflectionProperty::IS_PROTECTED) as $property) {
            $name = $property->getName();
            if (!array_key_exists($name, $config)) {
                continue;
            }
            $this->__set($name, $config[$name]);
        }
        return $this;
    }

    /**
     * @param string $name
     * @param mixed $value
     * @return mixed
     */
    public function __set($name, $value)
    {
        $setter = 'set' . \Phalcon\Text::camelize($name);
        if (method_exists($this, $setter)) {
            return $this->{$setter}($value);
        }
        return null;
    }

    /**
     * @param string $name
     * @return mixed
     */
    public function __get($name)
    {
        $getter = 'get' . \Phalcon\Text::camelize($name);
        if (method_exists($this, $getter)) {
            return $this->{$getter}();
        }
        return null;
    }
}