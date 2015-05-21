<?php

/*
 * This file is part of Sulu.
 *
 * (c) MASSIVE ART WebServices GmbH
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Sulu\Component\DocumentManager\Collection;

use PHPCR\Query\QueryResultInterface;
use Sulu\Component\DocumentManager\Event\HydrateEvent;
use Sulu\Component\DocumentManager\Events;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * Lazily hydrate query results.
 */
class QueryResultCollection extends AbstractLazyCollection
{
    /**
     * @var EventDispatcherInterface
     */
    private $eventDispatcher;

    /**
     * @var QueryResultInterface
     */
    private $result;

    /**
     * @var string
     */
    private $locale;

    /**
     * @var bool
     */
    private $initialized = false;

    /**
     * @var null|string
     */
    private $primarySelector = null;

    /**
     * @param QueryResultInterface $result
     * @param EventDispatcherInterface $eventDispatcher
     * @param string $locale
     * @param null|string $primarySelector
     */
    public function __construct(
        QueryResultInterface $result,
        EventDispatcherInterface $eventDispatcher,
        $locale,
        $primarySelector = null
    ) {
        $this->result = $result;
        $this->eventDispatcher = $eventDispatcher;
        $this->locale = $locale;
        $this->primarySelector = $primarySelector;
    }

    /**
     * {@inheritDoc}
     */
    public function current()
    {
        $this->initialize();
        $row = $this->documents->current();
        $node = $row->getNode($this->primarySelector);

        $hydrateEvent = new HydrateEvent($node, $this->locale);
        $this->eventDispatcher->dispatch(Events::HYDRATE, $hydrateEvent);

        return $hydrateEvent->getDocument();
    }

    /**
     * {@inheritDoc}
     */
    protected function initialize()
    {
        if (true === $this->initialized) {
            return;
        }

        $this->documents = $this->result->getRows();
        $this->initialized = true;
    }
}
