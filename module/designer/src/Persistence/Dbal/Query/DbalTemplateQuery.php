<?php

/**
 * Copyright © Bold Brand Commerce Sp. z o.o. All rights reserved.
 * See LICENSE.txt for license details.
 */

declare(strict_types = 1);

namespace Ergonode\Designer\Persistence\Dbal\Query;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Query\QueryBuilder;
use Ergonode\Attribute\Domain\Entity\AttributeId;
use Ergonode\Core\Domain\ValueObject\Language;
use Ergonode\Designer\Domain\Entity\TemplateId;
use Ergonode\Designer\Domain\Query\TemplateQueryInterface;
use Ergonode\Grid\DataSetInterface;
use Ergonode\Grid\DbalDataSet;

/**
 */
class DbalTemplateQuery implements TemplateQueryInterface
{
    private const TABLE = 'designer.template';
    private const FIELDS = [
        't.id',
        't.name',
        't.image_id',
        't.template_group_id AS group_id',
    ];

    /**
     * @var Connection
     */
    private $connection;

    /**
     * @param Connection $connection
     */
    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    /**
     * @return DataSetInterface
     */
    public function getDataSet(): DataSetInterface
    {
        $result = $this->connection->createQueryBuilder();
        $result->select('*');
        $result->from(sprintf('(%s)', $this->getQuery()->getSQL()), 't');

        return new DbalDataSet($result);
    }

    /**
     * @param Language $language
     *
     * @return array
     */
    public function getDictionary(Language $language): array
    {
        return $this->getQuery()
            ->select('id, name')
            ->orderBy('name', 'desc')
            ->execute()
            ->fetchAll(\PDO::FETCH_KEY_PAIR);
    }

    /**
     * {@inheritDoc}
     */
    public function findTemplateIdByAttributeId(AttributeId $attributeId): array
    {
        $queryBuilder = $this->connection->createQueryBuilder();
        $queryBuilder->select('template_id')
            ->from('designer.template_element')
            ->where('properties ->> \'variant\' = \'attribute\'')
            ->andWhere('properties ->> \'attribute_id\' = :attribute')
            ->setParameter('attribute', $attributeId->getValue());

        $result = $queryBuilder->execute()->fetchAll(\PDO::FETCH_COLUMN);

        if (false === $result) {
            $result = [];
        }

        foreach ($result as &$item) {
            $item = new TemplateId($item);
        }

        return $result;
    }

    /**
     * @return QueryBuilder
     */
    private function getQuery(): QueryBuilder
    {
        return $this->connection->createQueryBuilder()
            ->select(self::FIELDS)
            ->from(self::TABLE, 't');
    }
}
