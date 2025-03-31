<?php

/**
 * This file is part of MetaModels/attribute_numeric.
 *
 * (c) 2012-2023 The MetaModels team.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * This project is provided in good faith and hope to be usable by anyone.
 *
 * @package    MetaModels/attribute_numeric
 * @author     Christian Schiffler <c.schiffler@cyberspectrum.de>
 * @author     Stefan Heimes <stefan_heimes@hotmail.com>
 * @author     Andreas Isaak <info@andreas-isaak.de>
 * @author     David Greminger <david.greminger@1up.io>
 * @author     David Molineus <david.molineus@netzmacht.de>
 * @author     Sven Baumann <baumann.sv@gmail.com>
 * @author     Ingolf Steinhardt <info@e-spin.de>
 * @copyright  2012-2023 The MetaModels team.
 * @license    https://github.com/MetaModels/attribute_numeric/blob/master/LICENSE LGPL-3.0-or-later
 * @filesource
 */

use Contao\System;
use MetaModels\ContaoFrontendEditingBundle\MetaModelsContaoFrontendEditingBundle;

$GLOBALS['TL_DCA']['tl_metamodel_dcasetting']['metasubselectpalettes']['attr_id']['numeric'] = [
    'presentation' => [
        'tl_class',
        'be_template',
    ],
    'functions'    => [
        'mandatory',
    ],
    'overview'     => [
        'filterable',
        'searchable',
    ],
];

// Load configuration for the frontend editing.
if (\in_array(
    MetaModelsContaoFrontendEditingBundle::class,
    System::getContainer()->getParameter('kernel.bundles'),
    true
)) {
    $GLOBALS['TL_DCA']['tl_metamodel_dcasetting']['metasubselectpalettes']['attr_id']['numeric']['presentation'][] =
        'fe_template';
}
