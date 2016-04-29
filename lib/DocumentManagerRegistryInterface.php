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

/**
 * Document manager registries provide access and information
 * to and about different document manager implementations.
 *
 * Typically this interface would be best implemented by via. integration
 * with a dependency injection container.
 */
interface DocumentManagerRegistryInterface
{
    /**
     * Return the name of the default document manager.
     *
     * @return string
     */
    public function getDefaultManagerName();

    /**
     * Return the names of all document managers.
     *
     * @return string[]
     */
    public function getManagerNames();

    /**
     * Return the named document manager, if no name
     * is given then return the default document manager.
     *
     * If no document manager is known by the given name, then
     * an \InvalidArgumentException should be thrown.
     *
     * @param string $name
     *
     * @throws \InvalidArgumentException
     */
    public function getManager($name = null);

    /**
     * Return the context for the named document manager.
     *
     * @return string $name
     */
    public function getContext($name = null);
}