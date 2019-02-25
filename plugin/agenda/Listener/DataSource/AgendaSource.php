<?php

/*
 * This file is part of the Claroline Connect package.
 *
 * (c) Claroline Consortium <consortium@claroline.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Claroline\AgendaBundle\Listener\DataSource;

use Claroline\AgendaBundle\Entity\Event;
use Claroline\AppBundle\API\FinderProvider;
use Claroline\CoreBundle\Entity\DataSource;
use Claroline\CoreBundle\Event\DataSource\GetDataEvent;
use JMS\DiExtraBundle\Annotation as DI;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

/**
 * @DI\Service
 */
class AgendaSource
{
    /** @var FinderProvider */
    private $finder;

    /**
     * AgendaSource constructor.
     *
     * @DI\InjectParams({
     *     "finder"       = @DI\Inject("claroline.api.finder"),
     *     "tokenStorage" = @DI\Inject("security.token_storage")
     * })
     *
     * @param FinderProvider $finder
     */
    public function __construct(
      FinderProvider $finder,
      TokenStorageInterface $tokenStorage
    ) {
        $this->finder = $finder;
        $this->tokenStorage = $tokenStorage;
    }

    /**
     * @DI\Observe("data_source.events.load")
     *
     * @param GetDataEvent $event
     */
    public function getEventsData(GetDataEvent $event)
    {
        $options = $event->getOptions() ? $event->getOptions() : [];
        $options['hiddenFilters']['types'] = ['event'];

        if (DataSource::CONTEXT_WORKSPACE === $event->getContext()) {
            $options['hiddenFilters']['workspaces'] = [$event->getWorkspace()->getUuid()];
        } elseif (DataSource::CONTEXT_HOME === $event->getContext()) {
            $options['hiddenFilters']['anonymous'] = true;
        } else {
            $options['hiddenFilters']['user'] = $this->tokenStorage->getToken()->getUser()->getUuid();
        }

        $event->setData(
            $this->finder->search(Event::class, $options)
        );

        $event->stopPropagation();
    }

    /**
     * @DI\Observe("data_source.tasks.load")
     *
     * @param GetDataEvent $event
     */
    public function getTasksData(GetDataEvent $event)
    {
        $options = $event->getOptions() ? $event->getOptions() : [];
        $options['hiddenFilters']['types'] = ['task'];

        if (DataSource::CONTEXT_WORKSPACE === $event->getContext()) {
            $options['hiddenFilters']['workspaces'] = [$event->getWorkspace()->getUuid()];
        } elseif (DataSource::CONTEXT_HOME === $event->getContext()) {
            $options['hiddenFilters']['anonymous'] = true;
        }
        $event->setData(
            $this->finder->search(Event::class, $options)
        );

        $event->stopPropagation();
    }
}