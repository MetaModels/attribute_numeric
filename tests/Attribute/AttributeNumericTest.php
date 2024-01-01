<?php

/**
 * This file is part of MetaModels/attribute_numeric.
 *
 * (c) 2012-2022 The MetaModels team.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * This project is provided in good faith and hope to be usable by anyone.
 *
 * @package    MetaModels/attribute_numeric
 * @author     Christian Schiffler <c.schiffler@cyberspectrum.de>
 * @author     David Greminger <david.greminger@1up.io>
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @author     Sven Baumann <baumann.sv@gmail.com>
 * @author     Ingolf Steinhardt <info@e-spin.de>
 * @copyright  2012-2022 The MetaModels team.
 * @license    https://github.com/MetaModels/attribute_numeric/blob/master/LICENSE LGPL-3.0-or-later
 * @filesource
 */

namespace MetaModels\AttributeNumericBundle\Test\Attribute;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Query\QueryBuilder;
use Doctrine\DBAL\Result;
use MetaModels\AttributeNumericBundle\Attribute\Numeric;
use MetaModels\Helper\TableManipulator;
use MetaModels\IMetaModel;
use PHPUnit\Framework\TestCase;

/**
 * Unit tests to test class Numeric.
 *
 * @covers \MetaModels\AttributeNumericBundle\Attribute\LangCode
 */
class AttributeNumericTest extends TestCase
{
    /**
     * System columns.
     *
     * @var array
     */
    private $systemColumns = [
        'id',
        'pid',
        'sorting',
        'tstamp',
        'vargroup',
        'varbase ',
    ];

    /**
     * Mock a MetaModel.
     *
     * @param string $language         The language.
     * @param string $fallbackLanguage The fallback language.
     *
     * @return \MetaModels\IMetaModel
     */
    protected function mockMetaModel($language, $fallbackLanguage)
    {
        $metaModel = $this->getMockForAbstractClass(IMetaModel::class);

        $metaModel
            ->expects(self::any())
            ->method('getTableName')
            ->willReturn('mm_unittest');

        $metaModel
            ->expects(self::any())
            ->method('getActiveLanguage')
            ->willReturn($language);

        $metaModel
            ->expects(self::any())
            ->method('getFallbackLanguage')
            ->willReturn($fallbackLanguage);

        return $metaModel;
    }

    /**
     * Mock the table manipulator.
     *
     * @param Connection $connection The database connection mock.
     *
     * @return TableManipulator&MockObject
     */
    private function mockTableManipulator(Connection $connection)
    {
        return $this->getMockBuilder(TableManipulator::class)
            ->setConstructorArgs([$connection, $this->systemColumns])
            ->getMock();
    }

    /**
     * Test that the attribute can be instantiated.
     *
     * @return void
     */
    public function testInstantiation()
    {
        $connection       = $this->getMockBuilder(Connection::class)->disableOriginalConstructor()->getMock();
        $tableManipulator = $this->mockTableManipulator($connection);
        $text             = new Numeric($this->mockMetaModel('en', 'en'), [], $connection, $tableManipulator);
        $this->assertInstanceOf(Numeric::class, $text);
    }

    /**
     * Test provider for testSearchFor().
     *
     * @return array
     */
    public function searchForProvider()
    {
        return [
            ['10'],
            [10],
        ];
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
    public function testSearchFor($value): void
    {
        $connection    = $this->getMockBuilder(Connection::class)->disableOriginalConstructor()->getMock();
        $selectBuilder = $this->getMockBuilder(QueryBuilder::class)->disableOriginalConstructor()->getMock();
        $connection->expects($this->once())->method('createQueryBuilder')->willReturn($selectBuilder);

        $selectBuilder
            ->expects($this->once())
            ->method('select')
            ->with('t.id')
            ->willReturn($selectBuilder);
        $selectBuilder
            ->expects($this->once())
            ->method('from')
            ->with('mm_unittest', 't')
            ->willReturn($selectBuilder);
        $selectBuilder
            ->expects($this->once())
            ->method('where')
            ->with('t.test=:pattern')
            ->willReturn($selectBuilder);
        $selectBuilder
            ->expects($this->once())
            ->method('setParameter')
            ->with('pattern', $value)
            ->willReturn($selectBuilder);

        $selectResult = $this->getMockBuilder(Result::class)->disableOriginalConstructor()->getMock();
        $selectBuilder
            ->expects($this->once())
            ->method('executeQuery')
            ->willReturn($selectResult);

        $selectResult
            ->expects($this->once())
            ->method('fetchFirstColumn')
            ->willReturn([1, 2]);

        $manipulator = $this->mockTableManipulator($connection);

        $decimal = new Numeric(
            $this->mockMetaModel('en', 'en'),
            ['colname' => 'test'],
            $connection,
            $manipulator
        );

        $this->assertEquals([1, 2], $decimal->searchFor($value));
    }

    /**
     * Test the searchFor() method with a wildcard.
     *
     * @return void
     */
    public function testSearchForWithWildcard(): void
    {
        $connection    = $this->getMockBuilder(Connection::class)->disableOriginalConstructor()->getMock();
        $selectBuilder = $this->getMockBuilder(QueryBuilder::class)->disableOriginalConstructor()->getMock();
        $connection->expects($this->once())->method('createQueryBuilder')->willReturn($selectBuilder);

        $selectBuilder
            ->expects($this->once())
            ->method('select')
            ->with('t.id')
            ->willReturn($selectBuilder);
        $selectBuilder
            ->expects($this->once())
            ->method('from')
            ->with('mm_unittest', 't')
            ->willReturn($selectBuilder);
        $selectBuilder
            ->expects($this->once())
            ->method('where')
            ->with('t.test LIKE :pattern')
            ->willReturn($selectBuilder);
        $selectBuilder
            ->expects($this->once())
            ->method('setParameter')
            ->with('pattern', '_10%')
            ->willReturn($selectBuilder);

        $selectResult = $this->getMockBuilder(Result::class)->disableOriginalConstructor()->getMock();
        $selectBuilder
            ->expects($this->once())
            ->method('executeQuery')
            ->willReturn($selectResult);

        $selectResult
            ->expects($this->once())
            ->method('fetchFirstColumn')
            ->willReturn([1, 2]);

        $decimal = new Numeric(
            $this->mockMetaModel('en', 'en'),
            ['colname' => 'test'],
            $connection,
            $this->mockTableManipulator($connection)
        );

        $this->assertEquals([1, 2], $decimal->searchFor('?10*'));
    }

    /**
     * Test the searchFor() method with a non-numeric value.
     *
     * @return void
     */
    public function testSearchForWithNonNumeric(): void
    {
        $connection = $this
            ->getMockBuilder(Connection::class)
            ->disableOriginalConstructor()
            ->getMock();
        $connection->expects($this->never())->method('createQueryBuilder');

        $decimal = new Numeric(
            $this->mockMetaModel('en', 'en'),
            ['colname' => 'test'],
            $connection,
            $this->mockTableManipulator($connection)
        );

        $this->assertEquals([], $decimal->searchFor('abc'));
    }
}
