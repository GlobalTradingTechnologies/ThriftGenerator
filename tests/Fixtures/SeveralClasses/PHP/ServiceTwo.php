<?php
/**
 * This file is part of the Global Trading Technologies Ltd ThriftGenerator package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * (c) fduch <alex.medwedew@gmail.com>
 *
 * Date: 11/24/14
 */

namespace Gtt\ThriftGenerator\Tests\Fixtures\SeveralClasses\PHP;

use Gtt\ThriftGenerator\Tests\Fixtures\SeveralClasses\PHP\DTO\DTO2;
use Gtt\ThriftGenerator\Tests\Fixtures\SeveralClasses\PHP\AnotherDTO\DTO\DTO2 as AnotherDTO2;

/**
 * Service two
 *
 * @author fduch <alex.medwedew@gmail.com>
 */
class ServiceTwo
{
    /**
     * worksWithDTOsFromSeveralNamespaces description
     *
     * @param \Gtt\ThriftGenerator\Tests\Fixtures\SeveralClasses\PHP\DTO\DTO2 $dto1 DTO from one namespace
     * @param \Gtt\ThriftGenerator\Tests\Fixtures\SeveralClasses\PHP\AnotherDTO\DTO\DTO2 $dto2 DTO from another namespace
     */
    public function worksWithDTOsFromSeveralNamespaces(DTO2 $dto1, AnotherDTO2 $dto2)
    {
        // logic
    }
}
 