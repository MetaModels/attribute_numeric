<?php

/**
 * This file is part of MetaModels/attribute_numeric.
 *
 * (c) 2012-2017 The MetaModels team.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * This project is provided in good faith and hope to be usable by anyone.
 *
 * @package    MetaModels
 * @subpackage Tests
 * @author     Christian Schiffler <c.schiffler@cyberspectrum.de>
 * @author     David Greminger <david.greminger@1up.io>
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @copyright  2012-2017 The MetaModels team.
 * @license    https://github.com/MetaModels/attribute_numeric/blob/master/LICENSE LGPL-3.0
 * @filesource
 */

namespace MetaModels\Test\Attribute\Numeric;

use Doctrine\DBAL\Driver\Connection;
use MetaModels\Attribute\IAttributeTypeFactory;
use MetaModels\Attribute\Numeric\AttributeNumeric;
use MetaModels\Attribute\Numeric\AttributeTypeFactory;
use MetaModels\IMetaModel;
use PHPUnit\Framework\TestCase;

/**
 * Test the attribute factory.
 */
class NumericAttributeTypeFactoryTest extends TestCase
{
    /**
     * Mock a MetaModel.
     *
     * @param string $tableName        The table name.
     *
     * @param string $language         The language.
     *
     * @param string $fallbackLanguage The fallback language.
     *
     * @return IMetaModel
     */
    protected function mockMetaModel($tableName, $language, $fallbackLanguage)
    {
        $metaModel = $this
            ->getMockBuilder('MetaModels\MetaModel')
            ->setConstructorArgs([[]])
            ->getMock();

        $metaModel
            ->expects($this->any())
            ->method('getTableName')
            ->will($this->returnValue($tableName));

        $metaModel
            ->expects($this->any())
            ->method('getActiveLanguage')
            ->will($this->returnValue($language));

        $metaModel
            ->expects($this->any())
            ->method('getFallbackLanguage')
            ->will($this->returnValue($fallbackLanguage));

        return $metaModel;
    }

    /**
     * Override the method to run the tests on the attribute factories to be tested.
     *
     * @return IAttributeTypeFactory[]
     */
    protected function getAttributeFactories()
    {
        $connection = $this->getMockBuilder(Connection::class)->getMock();

        return array(new AttributeTypeFactory($connection));
    }

    /**
     * Test creation of a numeric attribute.
     *
     * @return void
     */
    public function testCreateNumeric()
    {
        $connection = $this->getMockBuilder(Connection::class)->getMock();
        $factory    = new AttributeTypeFactory($connection);
        $values     = array(
        );
        $attribute = $factory->createInstance(
            $values,
            $this->mockMetaModel('mm_test', 'de', 'en')
        );

        $this->assertInstanceOf(AttributeNumeric::class, $attribute);

        foreach ($values as $key => $value) {
            $this->assertEquals($value, $attribute->get($key), $key);
        }
    }
}
