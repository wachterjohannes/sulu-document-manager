<?php

/*
 * This file is part of Sulu.
 *
 * (c) MASSIVE ART WebServices GmbH
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Sulu\Component\DocumentManager;

use PHPCR\SessionInterface;
use ProxyManager\Factory\LazyLoadingGhostFactory;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * The document manager context is a container for all the dependencies of a
 * given document manager.
 */
class DocumentManagerContext
{
    /**
     * @var EventDispatcherInterface
     */
    private $eventDispatcher;

    /**
     * @var DocumentInspectorFactory
     */
    private $inspectorFactory;

    /**
     * @var NodeManager
     */
    private $nodeManager;

    /**
     * @var DocumentRegistry
     */
    private $registry;

    /**
     * @var ProxyFactory
     */
    private $proxyFactory;

    /**
     * @var MetadataFactoryInterface
     */
    private $metadataFactory;

    /**
     * @var SessionInterface
     */
    private $session;

    /**
     * @var DocumentManagerInterface
     */
    private $manager;

    /**
     * @var string
     */
    private $name;

    public function __construct(
        $name,
        SessionInterface $session,
        EventDispatcherInterface $eventDispatcher,
        MetadataFactoryInterface $metadataFactory,
        LazyLoadingGhostFactory $lazyProxyFactory,
        DocumentInspectorFactoryInterface $inspectorFactory,
        $defaultLocale
    ) {
        $this->name = $name;

        // the event dispatcher should be unique to this document manager context
        // instance.
        $this->eventDispatcher = $eventDispatcher;

        // the inspector factory provides a way for users to instantiate
        // their own document inspectors.
        $this->inspectorFactory = $inspectorFactory;

        // the metadata factory is currently intended to be the same for all
        // document mnaager contexts instances.
        $this->metadataFactory = $metadataFactory;

        // the PHPCR session SHOULD be unique to all (end user) document
        // manager contexts.
        $this->session = $session;

        // instantiate other objects scoped to this document manager.
        $this->nodeManager = new NodeManager($session);
        $this->proxyFactory = new ProxyFactory($this, $lazyProxyFactory, $metadataFactory);
        $this->registry = new DocumentRegistry($defaultLocale);
    }

    /**
     * Return the context name.
     *
     * This should be a simple string which can be used to identify the
     * document context while debugging. It would typically be the name
     * used to reference the document context/manager within the host application.
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Attach a document manager to this context.
     *
     * @param DocumentManagerInterface $manager
     */
    public function attachManager(DocumentManagerInterface $manager)
    {
        if (isset($this->manager)) {
            throw new \RuntimeException(sprintf(
                'This context has already been attached to document manager.',
                $manager
            ));
        }

        $this->manager = $manager;
    }

    /**
     * Return the attached document manager.
     *
     * @retrun DocumentManagerInterface
     */
    public function getManager()
    {
        if (!$this->manager) {
            throw new \RuntimeException(
                'This document context has not been attached to a document manager.'
            );
        }

        return $this->manager;
    }

    /**
     * Return the node manager instance.
     *
     * @return NodeManager
     */
    public function getNodeManager()
    {
        return $this->nodeManager;
    }

    /**
     * Return the document registry instance.
     *
     * @return DocumentRegistry
     */
    public function getRegistry()
    {
        return $this->registry;
    }

    /**
     * Return the proxy factory instance.
     *
     * @return ProxyFactory
     */
    public function getProxyFactory()
    {
        return $this->proxyFactory;
    }

    /**
     * Return the PHPCR session instance.
     *
     * @return SessionInterface
     */
    public function getSession()
    {
        return $this->session;
    }

    /**
     * Return the event dispatcher instance.
     *
     * @return EventDispatcher
     */
    public function getEventDispatcher()
    {
        return $this->eventDispatcher;
    }

    /**
     * Return the metadata factory instance.
     *
     * @return MetadataFactoryInterface
     */
    public function getMetadataFactory()
    {
        return $this->metadataFactory;
    }

    /**
     * Retrieve the document inspector.
     *
     * NOTE: In the future this will be a factory for retrieving inspectors
     * for a given object/class.
     *
     * @return DocumentInspector
     */
    public function getInspector()
    {
        return $this->inspectorFactory->getInspector($this);
    }
}
