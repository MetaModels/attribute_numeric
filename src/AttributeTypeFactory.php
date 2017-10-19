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
 * @subpackage AttributeNumeric
 * @author     Christian Schiffler <c.schiffler@cyberspectrum.de>
 * @author     Stefan Heimes <stefan_heimes@hotmail.com>
 * @author     Andreas Isaak <info@andreas-isaak.de>
 * @author     David Greminger <david.greminger@1up.io>
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @copyright  2012-2017 The MetaModels team.
 * @license    https://github.com/MetaModels/attribute_numeric/blob/master/LICENSE LGPL-3.0
 * @filesource
 */

namespace MetaModels\Attribute\Numeric;

use Doctrine\DBAL\Driver\Connection;
use MetaModels\Attribute\AbstractAttributeTypeFactory;

/**
 * Attribute type factory for numeric attributes.
 */
class AttributeTypeFactory extends AbstractAttributeTypeFactory
{
    /**
     * @var Connection
     */
    private $connection;

    /**
     * Create a new instance.
     *
     * @param Connection $connection Database connection;
     *
     */
    public function __construct(Connection $connection)
    {
        parent::__construct();

        $this->typeName   = 'numeric';
        $this->typeIcon   = 'bundles/metamodelsattributenumericbundle/numeric.png';
        $this->typeClass  = 'MetaModels\Attribute\Numeric\AttributeNumeric';
        $this->connection = $connection;
    }

    /**
     * {@inheritdoc}
     */
    public function createInstance($information, $metaModel)
    {
        return new $this->typeClass($metaModel, $this->connection, $information);
    }
}