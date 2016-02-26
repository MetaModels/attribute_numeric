<?php

/**
 * This file is part of MetaModels/attribute_decimal.
 *
 * (c) 2012-2015 The MetaModels team.
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
 * @copyright  2012-2016 The MetaModels team.
 * @license    https://github.com/MetaModels/attribute_numeric/blob/master/LICENSE LGPL-3.0
 * @filesource
 */

namespace MetaModels\Attribute\Numeric;

use MetaModels\Attribute\BaseSimple;

/**
 * This is the MetaModelAttribute class for handling numeric fields.
 *
 * @package    MetaModels
 * @subpackage AttributeNumeric
 * @author     Christian Schiffler <c.schiffler@cyberspectrum.de>
 */
class Numeric extends BaseSimple
{
    /**
     * {@inheritdoc}
     */
    public function getSQLDataType()
    {
        return 'int(10) NULL default NULL';
    }

    /**
     * {@inheritdoc}
     */
    public function getAttributeSettingNames()
    {
        return array_merge(
            parent::getAttributeSettingNames(),
            array(
                'mandatory',
                'filterable',
                'searchable',
            )
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getFieldDefinition($arrOverrides = array())
    {
        $arrFieldDef                 = parent::getFieldDefinition($arrOverrides);
        $arrFieldDef['inputType']    = 'text';
        $arrFieldDef['eval']['rgxp'] = 'digit';

        return $arrFieldDef;
    }

    /**
     * {@inheritdoc}
     */
    public function filterGreaterThan($varValue, $blnInclusive = false)
    {
        return $this->getIdsFiltered($varValue, ($blnInclusive) ? '>=' : '>');
    }

    /**
     * {@inheritdoc}
     */
    public function filterLessThan($varValue, $blnInclusive = false)
    {
        return $this->getIdsFiltered($varValue, ($blnInclusive) ? '<=' : '<');
    }

    /**
     * {@inheritdoc}
     */
    public function filterNotEqual($varValue)
    {
        return $this->getIdsFiltered($varValue, '!=');
    }

    /**
     * Filter all values by specified operation.
     *
     * @param int    $varValue     The value to use as upper end.
     *
     * @param string $strOperation The specified operation like greater than, lower than etc.
     *
     * @return string[] The list of item ids of all items matching the condition.
     */
    protected function getIdsFiltered($varValue, $strOperation)
    {
        $strSql = sprintf(
            'SELECT id FROM %s WHERE %s %s %d',
            $this->getMetaModel()->getTableName(),
            $this->getColName(),
            $strOperation,
            intval($varValue)
        );

        $objIds = $this->getMetaModel()->getServiceContainer()->getDatabase()->execute($strSql);

        return $objIds->fetchEach('id');
    }
}
