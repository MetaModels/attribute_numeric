<?php

/**
 * This file is part of MetaModels/attribute_numeric.
 *
 * (c) 2012-2024 The MetaModels team.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * This project is provided in good faith and hope to be usable by anyone.
 *
 * @package    MetaModels/attribute_numeric
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @author     Ingolf Steinhardt <info@e-spin.de>
 * @copyright  2012-2024 The MetaModels team.
 * @license    https://github.com/MetaModels/attribute_numeric/blob/master/LICENSE LGPL-3.0-or-later
 * @filesource
 */

namespace MetaModels\AttributeNumericBundle\Test\DependencyInjection;

use MetaModels\AttributeNumericBundle\Attribute\AttributeTypeFactory;
use MetaModels\AttributeNumericBundle\DependencyInjection\MetaModelsAttributeNumericExtension;
use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\ExtensionInterface;

/**
 * This test case test the extension.
 *
 * @covers \MetaModels\AttributeNumericBundle\DependencyInjection\MetaModelsAttributeNumericExtension
 */
class MetaModelsAttributeNumericExtensionTest extends TestCase
{
    /**
     * Test that extension can be instantiated.
     *
     * @return void
     */
    public function testInstantiation()
    {
        $extension = new MetaModelsAttributeNumericExtension();

        $this->assertInstanceOf(MetaModelsAttributeNumericExtension::class, $extension);
        $this->assertInstanceOf(ExtensionInterface::class, $extension);
    }

    /**
     * Test that the services are loaded.
     *
     * @return void
     */
    public function testFactoryIsRegistered()
    {
        $container = new ContainerBuilder();

        $extension = new MetaModelsAttributeNumericExtension();
        $extension->load([], $container);

        self::assertTrue($container->hasDefinition('metamodels.attribute_numeric.factory'));
        $definition = $container->getDefinition('metamodels.attribute_numeric.factory');
        self::assertCount(1, $definition->getTag('metamodels.attribute_factory'));
    }
}
