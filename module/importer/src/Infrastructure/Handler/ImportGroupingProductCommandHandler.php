<?php
/**
 * Copyright © Ergonode Sp. z o.o. All rights reserved.
 * See LICENSE.txt for license details.
 */

declare(strict_types=1);

namespace Ergonode\Importer\Infrastructure\Handler;

use Ergonode\Importer\Infrastructure\Exception\ImportException;
use Ergonode\Importer\Domain\Repository\ImportRepositoryInterface;
use Ergonode\Importer\Domain\Command\Import\ImportGroupingProductCommand;
use Ergonode\Importer\Infrastructure\Action\GroupingProductImportAction;
use Psr\Log\LoggerInterface;
use Ergonode\Product\Domain\ValueObject\Sku;
use Ergonode\Category\Domain\ValueObject\CategoryCode;

class ImportGroupingProductCommandHandler
{
    private GroupingProductImportAction $action;

    private ImportRepositoryInterface $repository;

    private LoggerInterface $logger;

    public function __construct(
        GroupingProductImportAction $action,
        ImportRepositoryInterface $repository,
        LoggerInterface $logger
    ) {
        $this->action = $action;
        $this->repository = $repository;
        $this->logger = $logger;
    }

    public function __invoke(ImportGroupingProductCommand $command): void
    {
        try {
            if (!Sku::isValid($command->getSku())) {
                throw new ImportException('Sku {sku} is not valid', ['{sku}' => $command->getSku()]);
            }

            $categories = [];
            foreach ($command->getCategories() as $category) {
                if (!CategoryCode::isValid($category)) {
                    throw new ImportException('Category code {code} is not valid', ['{code}' => $category]);
                }
                $categories[] = new CategoryCode($category);
            }

            $children = [];
            foreach ($command->getChildren() as $child) {
                if (!Sku::isValid($child)) {
                    throw new ImportException('Child sku {code} is not valid', ['{code}' => $child]);
                }
                $children[] = new Sku($child);
            }

            $product = $this->action->action(
                new Sku($command->getSku()),
                $command->getTemplate(),
                $categories,
                $children,
                $command->getAttributes()
            );
            $this->repository->markLineAsSuccess($command->getId(), $product->getId());
        } catch (ImportException $exception) {
            $this->repository->markLineAsFailure($command->getId());
            $this->repository->addError($command->getImportId(), $exception->getMessage(), $exception->getParameters());
        } catch (\Exception $exception) {
            $message = 'Can\'t import grouping product {sku}';
            $this->repository->markLineAsFailure($command->getId());
            $this->repository->addError($command->getImportId(), $message, ['{sku}' => $command->getSku()]);
            $this->logger->error($exception);
        }
    }
}
