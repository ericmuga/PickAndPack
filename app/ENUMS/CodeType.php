<?php

namespace App\Enums;

use Spatie\Enum\Enum;

/**
 * @method static self HORECA()
 * @method static self RETAIL()
 * @method static self MSA()
 * @method static self UPC()
 * @method static self F_FOOD()
 */
class CodeType extends Enum
{
    const MAP = [
        'HORECA' => ['052', '053', '054', '055', '056', '057', '061', '001', '002', 'B010', 'B016', 'B025', 'B026'],
        'RETAIL' => ['012', '013', '023', '024', '031', '032', '034', '062', '129', '611'],
        'MSA' => ['041', '042', '043', '044', 'B285'],
        'UPC' => ['085', '067', '180', '134', '035', '608', '575', '590', '037', '143'],
        'F_FOOD' => ['145', '151', '140', '138', '141', '128', '164', '147', '104', '124', '154'],
    ];
}


