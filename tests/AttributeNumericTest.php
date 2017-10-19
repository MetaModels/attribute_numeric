<?php

/**
 * This file is part of MetaModels/attribute_numeric.
 *
 * (c) 2012-2015 The MetaModels team.
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
 * @copyright  2012-2016 The MetaModels team.
 * @license    https://github.com/MetaModels/attribute_numeric/blob/master/LICENSE LGPL-3.0
 * @filesource
 */

namespace MetaModels\Test\Attribute\Numeric;

use Doctrine\DBAL\Driver\Connection;
use Doctrine\DBAL\Driver\Statement;
use MetaModels\Attribute\Numeric\AttributeNumeric;
use MetaModels\MetaModel;
use PHPUnit\Framework\TestCase;

/**
 * Unit tests to test class Numeric.
 */
class AttributeNumericTest extends TestCase
{
    /**
     * Mock the Contao database.
     *
     * @param string|null   expectedQuery The query to expect.
     *
     * @param callable|null $callback     Callback which gets mocked statement passed.
     *
     * @return Connection|\PHPUnit_Framework_MockObject_MockObject
     */
    private function mockDatabase(callable $callback = null, $expectedQuery = null, $queryMethod = 'prepare')
    {
        $mockDb = $this
            ->getMockBuilder(Connection::class)
            ->getMock();

        $statement = $this
            ->getMockBuilder(Statement::class)
            ->getMock();

        $mockDb->method('prepare')->willReturn($statement);
        $mockDb->method('query')->willReturn($statement);

        if ($callback) {
            call_user_func($callback, $statement);
        }

        if (!$expectedQuery || $expectedQuery === 'prepare') {
            $mockDb->expects($this->never())->method('query');
        }

        if (!$expectedQuery || $expectedQuery === 'query') {
            $mockDb->expects($this->never())->method('prepare');
        }

        if (!$expectedQuery) {
            return $mockDb;
        }

        $mockDb
            ->expects($this->once())
            ->method($queryMethod)
            ->with($expectedQuery);

        if ($queryMethod === 'prepare') {
            $statement
                ->expects($this->once())
                ->method('execute')
                ->willReturn(true);
        }

        return $mockDb;
    }

    /**
     * Mock a MetaModel.
     *
     * @param string     $language         The language.
     *
     * @param string     $fallbackLanguage The fallback language.
     *
     * @return \MetaModels\IMetaModel
     */
    protected function mockMetaModel($language, $fallbackLanguage)
    {
        $metaModel = $this->getMockBuilder(MetaModel::class)
            ->setConstructorArgs([[]])
            ->getMock();

        $metaModel
            ->expects($this->any())
            ->method('getTableName')
            ->will($this->returnValue('mm_unittest'));

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
     * Test that the attribute can be instantiated.
     *
     * @return void
     */
    public function testInstantiation()
    {
        $connection = $this->mockDatabase();
        $text       = new AttributeNumeric($this->mockMetaModel('en', 'en'), $connection);
        $this->assertInstanceOf(AttributeNumeric::class, $text);
    }

    /**
     * Test provider for testSearchFor().
     *
     * @return array
     */
    public function searchForProvider()
    {
        return array(
            array('10'),
            array(10),
        );
    }

    /**
     * Test the searchFor() method.
     *
     * @param string|int|float $value The value to search.
     *
     * @return void
     *
     * @dataProvider searchForProvider
     */
    public function testSearchFor($value)
    {
        $connection = $this->mockDatabase(
            function ($statement) use ($value) {
                $statement
                    ->expects($this->once())
                    ->method('bindValue')
                    ->with('pattern', $value);

                $statement
                    ->expects($this->once())
                    ->method('fetchAll')
                    ->with(\PDO::FETCH_COLUMN, 'id')
                    ->willReturn([1, 2]);
            },
            'SELECT id FROM mm_unittest WHERE test=:pattern'
        );

        $decimal = new AttributeNumeric(
            $this->mockMetaModel('en', 'en'),
            $connection,
            array('colname' => 'test')
        );

        $this->assertEquals(array(1, 2), $decimal->searchFor($value));
    }

    /**
     * Test the searchFor() method with a wildcard.
     *
     * @return void
     */
    public function testSearchForWithWildcard()
    {
        // TODO: Wait until BaseSimple attribute got rewritten to finish the test.
        $connection = $this->mockDatabase(
            function ($statement) {
                $statement
                    ->expects($this->once())
                    ->method('bindValue')
                    ->with('pattern', '10*');

                $statement
                    ->expects($this->once())
                    ->method('fetchAll')
                    ->with(\PDO::FETCH_COLUMN, 'id')
                    ->willReturn([1, 2]);
            },
            'SELECT id FROM mm_unittest WHERE test LIKE :pattern'
        );

        $decimal = new AttributeNumeric(
            $this->mockMetaModel('en', 'en'),
            $connection,
            array('colname' => 'test')
        );

        $this->assertEquals(array(1, 2), $decimal->searchFor('10*'));
    }

    /**
     * Test the searchFor() method with a non numeric value.
     *
     * @return void
     */
    public function testSearchForWithNonNumeric()
    {
        $decimal = new AttributeNumeric(
            $this->mockMetaModel('en', 'en'),
            $this->mockDatabase(),
            array('colname' => 'test')
        );

        $this->assertEquals(array(), $decimal->searchFor('abc'));
    }
}
