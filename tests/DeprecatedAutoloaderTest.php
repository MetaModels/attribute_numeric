<?php

/**
 * This file is part of MetaModels/attribute_numeric.
 *
 * (c) 2012-2019 The MetaModels team.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * This project is provided in good faith and hope to be usable by anyone.
 *
 * @package    MetaModels/attribute_numeric
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @copyright  2012-2019 The MetaModels team.
 * @license    https://github.com/MetaModels/attribute_numeric/blob/master/LICENSE LGPL-3.0-or-later
 * @filesource
 */

namespace MetaModels\AttributeNumericBundle\Test;

use MetaModels\AttributeNumericBundle\Attribute\Numeric;
use MetaModels\AttributeNumericBundle\Attribute\AttributeTypeFactory;
use PHPUnit\Framework\TestCase;

/**
 * This class tests if the deprecated autoloader works.
 *
 * @package MetaModels\AttributeNumericBundle\Test
 */
class DeprecatedAutoloaderTest extends TestCase
{
    /**
     * Numerices of old classes to the new one.
     *
     * @var array
     */
    private static $classes = [
        'MetaModels\Attribute\Numeric\Numeric' => Numeric::class,
        'MetaModels\Attribute\Numeric\AttributeTypeFactory' => AttributeTypeFactory::class
    ];

    /**
     * Provide the numeric class map.
     *
     * @return array
     */
    public function provideNumericClassMap()
    {
        $values = [];

        foreach (static::$classes as $numeric => $class) {
            $values[] = [$numeric, $class];
        }

        return $values;
    }

    /**
     * Test if the deprecated classes are numericed to the new one.
     *
     * @param string $oldClass Old class name.
     * @param string $newClass New class name.
     *
     * @dataProvider provideNumericClassMap
     */
    public function testDeprecatedClassesAreAliased($oldClass, $newClass)
    {
        $this->assertTrue(class_exists($oldClass), sprintf('Class numeric "%s" is not found.', $oldClass));

        $oldClassReflection = new \ReflectionClass($oldClass);
        $newClassReflection = new \ReflectionClass($newClass);

        $this->assertSame($newClassReflection->getFileName(), $oldClassReflection->getFileName());
    }
}
