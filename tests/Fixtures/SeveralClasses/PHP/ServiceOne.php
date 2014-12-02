<?php
/**
 * This file is part of the Global Trading Technologies Ltd ThriftGenerator package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * (c) fduch <alex.medwedew@gmail.com>
 *
 * Date: 11/21/14
 */

namespace Gtt\ThriftGenerator\Tests\Fixtures\SeveralClasses\PHP;

use Gtt\ThriftGenerator\Tests\Fixtures\SeveralClasses\PHP\DTO\DTO1;
use Gtt\ThriftGenerator\Tests\Fixtures\SeveralClasses\PHP\AnotherDTO\DTO\DTO1 as AnotherDTO1;

/**
 * Service one
 *
 * @author fduch <alex.medwedew@gmail.com>
 */
class ServiceOne
{
    /**
     * worksWithDTOsFromSingleNamespaces description
     *
     * @param \Gtt\ThriftGenerator\Tests\Fixtures\SeveralClasses\PHP\DTO\DTO1 $dto1 DTO from one namespace
     */
    public function worksWithDTOsFromSingleNamespaces(DTO1 $dto1)
    {
        // logic
    }
}
