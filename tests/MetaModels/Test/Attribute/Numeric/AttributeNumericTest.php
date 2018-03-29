<?php

/**
 * This file is part of MetaModels/attribute_numeric.
 *
 * (c) 2012-2018 The MetaModels team.
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
 * @author     Sven Baumann <baumann.sv@gmail.com>
 * @copyright  2012-2018 The MetaModels team.
 * @license    https://github.com/MetaModels/attribute_numeric/blob/master/LICENSE LGPL-3.0
 * @filesource
 */

namespace MetaModels\Test\Attribute\Numeric;

use Contao\Database;
use MetaModels\Attribute\Numeric\AttributeNumeric;
use MetaModels\MetaModelsServiceContainer;
use Contao\Database\Statement;
use Contao\Database\Result;
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
     * @param string     $expectedQuery The query to expect.
     *
     * @param null|array $result        The resulting datasets.
     *
     * @return Database|\PHPUnit_Framework_MockObject_MockObject
     */
    private function mockDatabase($expectedQuery = '', $result = null)
    {
        $mockDb = $this
            ->getMockBuilder(Database::class)
            ->disableOriginalConstructor()
            ->setMethods(['__destruct'])
            ->getMockForAbstractClass();

        $mockDb->method('createStatement')->willReturn(
            $statement = $this
                ->getMockBuilder(Statement::class)
                ->disableOriginalConstructor()
                ->setMethods(['debugQuery', 'createResult'])
                ->getMockForAbstractClass()
        );

        if (!$expectedQuery) {
            $statement->expects($this->never())->method('prepare_query');

            return $mockDb;
        }

        $statement
            ->expects($this->once())
            ->method('prepare_query')
            ->with($expectedQuery)
            ->willReturnArgument(0);

        if ($result === null) {
            $result = ['ignored'];
        } else {
            $result = (object) $result;
        }

        $statement->method('execute_query')->willReturn($result);
        $statement->method('createResult')->willReturnCallback(
            function ($resultData) {
                $index = 0;

                $resultData = (array) $resultData;

                $resultSet = $this
                    ->getMockBuilder(Result::class)
                    ->disableOriginalConstructor()
                    ->getMockForAbstractClass();

                $resultSet->method('fetch_row')->willReturnCallback(function () use (&$index, $resultData) {
                    return \array_values($resultData[$index++]);
                });
                $resultSet->method('fetch_assoc')->willReturnCallback(function () use (&$index, $resultData) {
                    if (!isset($resultData[$index])) {
                        return false;
                    }
                    return $resultData[$index++];
                });
                $resultSet->method('num_rows')->willReturnCallback(function () use ($index, $resultData) {
                    return \count($resultData);
                });
                $resultSet->method('num_fields')->willReturnCallback(function () use ($index, $resultData) {
                    return \count($resultData[$index]);
                });
                $resultSet->method('fetch_field')->willReturnCallback(function ($field) use ($index, $resultData) {
                    $data = \array_values($resultData[$index]);
                    return $data[$field];
                });
                $resultSet->method('data_seek')->willReturnCallback(function ($newIndex) use (&$index, $resultData) {
                    $index = $newIndex;
                });

                return $resultSet;
            }
        );

        return $mockDb;
    }

    /**
     * Mock a MetaModel.
     *
     * @param string   $language         The language.
     *
     * @param string   $fallbackLanguage The fallback language.
     *
     * @param Database $database         The database to use.
     *
     * @return \MetaModels\IMetaModel
     */
    protected function mockMetaModel($language, $fallbackLanguage, $database)
    {
        $metaModel = $this->getMockBuilder(MetaModel::class)->setMethods([])->setConstructorArgs([[]])->getMock();

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

        $serviceContainer = new MetaModelsServiceContainer();
        $serviceContainer->setDatabase($database);

        $metaModel
            ->method('getServiceContainer')
            ->willReturn($serviceContainer);

        return $metaModel;
    }

    /**
     * Test that the attribute can be instantiated.
     *
     * @return void
     */
    public function testInstantiation()
    {
        $text = new AttributeNumeric($this->mockMetaModel('en', 'en', $this->mockDatabase()));
        $this->assertInstanceOf(AttributeNumeric::class, $text);
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
    public function testSearchFor($value)
    {
        $decimal = new AttributeNumeric(
            $this->mockMetaModel(
                'en',
                'en',
                $this->mockDatabase(
                    'SELECT id FROM mm_unittest WHERE test=?',
                    [['id' => 1], ['id' => 2]]
                )
            ),
            ['colname' => 'test']
        );

        $this->assertEquals([1, 2], $decimal->searchFor($value));
    }

    /**
     * Test the searchFor() method with a wildcard.
     *
     * @return void
     */
    public function testSearchForWithWildcard()
    {
        $decimal = new AttributeNumeric(
            $this->mockMetaModel(
                'en',
                'en',
                $this->mockDatabase(
                    'SELECT id FROM mm_unittest WHERE test LIKE ?',
                    [['id' => 1], ['id' => 2]]
                )
            ),
            ['colname' => 'test']
        );

        $this->assertEquals([1, 2], $decimal->searchFor('10*'));
    }

    /**
     * Test the searchFor() method with a non numeric value.
     *
     * @return void
     */
    public function testSearchForWithNonNumeric()
    {
        $decimal = new AttributeNumeric(
            $this->mockMetaModel(
                'en',
                'en',
                $this->mockDatabase()
            ),
            ['colname' => 'test']
        );

        $this->assertEquals([], $decimal->searchFor('abc'));
    }
}
