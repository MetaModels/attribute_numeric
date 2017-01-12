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
 * @copyright  2012-2017 The MetaModels team.
 * @license    https://github.com/MetaModels/attribute_numeric/blob/master/LICENSE LGPL-3.0
 * @filesource
 */

namespace MetaModels\Attribute\Numeric;

use MetaModels\Attribute\AbstractAttributeTypeFactory;

/**
 * Attribute type factory for numeric attributes.
 */
class AttributeTypeFactory extends AbstractAttributeTypeFactory
{
    /**
     * {@inheritDoc}
     */
    public function __construct()
    {
        parent::__construct();

        $this->typeName  = 'numeric';
        $this->typeIcon  = 'system/modules/metamodelsattribute_numeric/html/numeric.png';
        $this->typeClass = 'MetaModels\Attribute\Numeric\Numeric';
    }
}
