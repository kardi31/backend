<?php

/**
 * Copyright © Bold Brand Commerce Sp. z o.o. All rights reserved.
 * See LICENSE.txt for license details.
 */

declare(strict_types = 1);

namespace Ergonode\AttributeDate\Domain\Updater;

use Ergonode\Attribute\Domain\AttributeUpdaterInterface;
use Ergonode\Attribute\Domain\Command\UpdateAttributeCommand;
use Ergonode\Attribute\Domain\Entity\AbstractAttribute;
use Ergonode\Attribute\Domain\ValueObject\AttributeType;
use Ergonode\AttributeDate\Domain\Entity\DateAttribute;
use Ergonode\AttributeDate\Domain\ValueObject\DateFormat;

/**
 */
class DateAttributeUpdater implements AttributeUpdaterInterface
{
    /**
     * @param AttributeType $type
     *
     * @return bool
     */
    public function isSupported(AttributeType $type): bool
    {
        return DateAttribute::TYPE === $type->getValue();
    }

    /**
     * @param AbstractAttribute|DateAttribute $attribute
     * @param UpdateAttributeCommand          $command
     *
     * @return AbstractAttribute
     */
    public function update(AbstractAttribute $attribute, UpdateAttributeCommand $command): AbstractAttribute
    {
        if (!$command->hasParameter('format')) {
            throw new \InvalidArgumentException('No required format parameter');
        }

        $format = new DateFormat($command->getParameter('format'));

        $attribute->changeFormat($format);

        return $attribute;
    }
}
