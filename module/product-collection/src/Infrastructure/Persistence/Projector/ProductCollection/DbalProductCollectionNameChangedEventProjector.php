<?php

/**
 * Copyright © Bold Brand Commerce Sp. z o.o. All rights reserved.
 * See LICENSE.txt for license details.
 */

declare(strict_types = 1);

namespace Ergonode\ProductCollection\Infrastructure\Persistence\Projector\ProductCollection;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\DBALException;
use Doctrine\DBAL\Types\Types;
use Ergonode\ProductCollection\Domain\Event\ProductCollectionNameChangedEvent;
use JMS\Serializer\SerializerInterface;

/**
 */
class DbalProductCollectionNameChangedEventProjector
{
    private const TABLE = 'product_collection';

    /**
     * @var Connection
     */
    private Connection $connection;

    /**
     * @var SerializerInterface
     */
    private SerializerInterface $serializer;

    /**
     * @param Connection          $connection
     * @param SerializerInterface $serializer
     */
    public function __construct(Connection $connection, SerializerInterface $serializer)
    {
        $this->connection = $connection;
        $this->serializer = $serializer;
    }

    /**
     * @param ProductCollectionNameChangedEvent $event
     *
     * @throws DBALException
     */
    public function __invoke(ProductCollectionNameChangedEvent $event): void
    {
        $this->connection->update(
            self::TABLE,
            [
                'name' => $this->serializer->serialize($event->getTo()->getTranslations(), 'json'),
                'edited_at' => $event->getEditedAt(),
            ],
            [
                'id' => $event->getAggregateId()->getValue(),
            ],
            [
                'edited_at' => Types::DATETIMETZ_MUTABLE,
            ],
        );
    }
}