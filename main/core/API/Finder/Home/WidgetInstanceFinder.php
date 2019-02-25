<?php

/*
 * This file is part of the Claroline Connect package.
 *
 * (c) Claroline Consortium <consortium@claroline.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Claroline\CoreBundle\API\Finder\Home;

use Claroline\AppBundle\API\Finder\AbstractFinder;
use Claroline\CoreBundle\Entity\Widget\WidgetInstance;
use Doctrine\ORM\QueryBuilder;
use JMS\DiExtraBundle\Annotation as DI;

/**
 * @DI\Service("claroline.api.finder.widget_instance")
 * @DI\Tag("claroline.finder")
 */
class WidgetInstanceFinder extends AbstractFinder
{
    public function getClass()
    {
        return WidgetInstance::class;
    }

    public function configureQueryBuilder(QueryBuilder $qb, array $searches = [], array $sortBy = null, array $options = ['count' => false, 'page' => 0, 'limit' => -1])
    {
        foreach ($searches as $filterName => $filterValue) {
            switch ($filterName) {
                case 'container':
                  $qb->join('obj.container', 't');
                  $qb->andWhere('t.uuid = :container');
                  $qb->setParameter('container', $filterValue);
                  break;
                case 'homeTab':
                  $qb->join('obj.container', 'c');
                  $qb->join('c.homeTab', 't');
                  $qb->andWhere('t.uuid = :homeTab');
                  $qb->setParameter('homeTab', $filterValue);
                  break;
            }
        }

        return $qb;
    }
}