<?php
/*
 * This file is part of the Claroline Connect package.
 *
 * (c) Claroline Consortium <consortium@claroline.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Icap\BibliographyBundle\Controller\API;

use Claroline\AppBundle\Annotations\ApiMeta;
use Claroline\AppBundle\Controller\AbstractCrudController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

/**
 * @ApiMeta(
 *     class="Icap\BibliographyBundle\Entity\BookReference",
 *     ignore={"find", "deleteBulk", "doc", "list", "get", "exist", "create", "copyBulk"}
 * )
 * @Route("/book_reference")
 */
class BookReferenceController extends AbstractCrudController
{
    /**
     * Get the name of the managed entity.
     *
     * @return string
     */
    public function getName()
    {
        return 'bookReference';
    }
}