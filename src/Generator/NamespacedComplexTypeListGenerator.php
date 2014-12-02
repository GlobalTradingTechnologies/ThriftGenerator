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

namespace Gtt\ThriftGenerator\Generator;

use Gtt\ThriftGenerator\Exception\InvalidArgumentException;
use Gtt\ThriftGenerator\Exception\TargetNotSpecifiedException;
use Gtt\ThriftGenerator\Transformer\ClassNameTransformer;
use Gtt\ThriftGenerator\Transformer\NamespaceTransformer;
use Gtt\ThriftGenerator\Reflection\ComplexTypeReflection;

/**
 * Generates definition of the list of complex types with the common namespace
 *
 * @author fduch <alex.medwedew@gmail.com>
 */
class NamespacedComplexTypeListGenerator extends AbstractGenerator
{
    /**
     * Complex types common namespace
     *
     * @var string
     */
    protected $complexTypesNamespace;

    /**
     * List of complex type reflection classes
     *
     * @var ComplexTypeReflection[]
     */
    protected $complexTypeRefs;

    /**
     * Sets complex types common namespace
     *
     * @param string $namespace namespace
     *
     * @return $this
     */
    public function setComplexTypesNamespace($namespace)
    {
        $this->complexTypesNamespace = $namespace;

        return $this;
    }

    /**
     * Sets complex type reflections
     *
     * @param ComplexTypeReflection[] $complexTypeRefs complex type reflection classes
     *
     * @return $this
     */
    public function setComplexTypeRefs($complexTypeRefs)
    {
        $this->complexTypeRefs = array();

        foreach ($complexTypeRefs as $complexTypeRef) {
            if (!$complexTypeRef instanceof ComplexTypeReflection) {
                throw new InvalidArgumentException("All the complex types to be handled by ".__CLASS__." specified by ".
                __METHOD__. "must be instances of ComplexTypeReflection");
            }
            $this->complexTypeRefs[] = $complexTypeRef;
        }
    }

    /**
     * {@inheritdoc}
     */
    public function generate()
    {
        if (is_null($this->complexTypeRefs)) {
            throw new TargetNotSpecifiedException("complex type references", "complex type list", __CLASS__."::".__METHOD__);
        }

        $includes     = $this->generateIncludes();
        $namespace    = $this->generateNamespace();
        $complexTypes = $this->generateComplexTypes();

        $search          = array("<includes>", "<namespace>", "<complexTypes>");
        $replace         = array($includes, $namespace, $complexTypes);
        $complexTypeList = trim(str_replace($search, $replace, $this->getComplexTypeListTemplate()));

        return $complexTypeList;
    }

    /**
     * {@inheritdoc}
     */
    protected function getComplexTypeListTemplate()
    {
        $complexTypeListTemplate = <<<EOT
<includes>

<namespace>

<complexTypes>
EOT;
        return $complexTypeListTemplate;
    }

    /**
     * Creates and returns namespace generator
     *
     * @return NamespaceGenerator
     */
    protected function getNamespaceGenerator()
    {
        $namespaceGenerator = new NamespaceGenerator();
        $namespaceGenerator->setNamespaceTransformer(new NamespaceTransformer());
        $namespaceGenerator->setIndentation($this->getIndentation());
        $namespaceGenerator->setNamespace($this->complexTypesNamespace);

        return $namespaceGenerator;
    }

    /**
     * Creates and returns struct generator
     *
     * @return StructGenerator
     */
    protected function getStructGenerator()
    {
        $complexTypeNameTransformer = new ClassNameTransformer();
        $complexTypeNameTransformer->setCurrentNamespace($this->complexTypesNamespace);

        $generator = new StructGenerator();
        $generator
            ->setComplexTypeNameTransformer($complexTypeNameTransformer)
            ->setIndentation($this->getIndentation());

        return $generator;
    }

    /**
     * Creates and returns exception generator
     *
     * @return ExceptionGenerator
     */
    protected function getExceptionGenerator()
    {
        $complexTypeNameTransformer = new ClassNameTransformer();
        $complexTypeNameTransformer->setCurrentNamespace($this->complexTypesNamespace);

        $generator = new ExceptionGenerator();
        $generator
            ->setComplexTypeNameTransformer($complexTypeNameTransformer)
            ->setIndentation($this->getIndentation());

        return $generator;
    }

    /**
     * Creates and returns include list generator
     *
     * @return IncludeListGenerator
     */
    protected function getIncludeListGenerator()
    {
        $generator = new IncludeListGenerator();
        $generator->setIndentation($this->getIndentation());

        return $generator;
    }

    /**
     * Generates includes
     *
     * @return string
     */
    protected function generateIncludes()
    {
        $includeListGenerator = $this->getIncludeListGenerator();

        $includeListGenerator->setUsedNamespacesFromComplexTypes($this->complexTypeRefs);
        $includeListGenerator->setExcludedNamespaces(array($this->complexTypesNamespace));

        $includes = $includeListGenerator->generate();

        return $includes;
    }

    /**
     * Generates complex type list namespace
     *
     * @return string
     */
    protected function generateNamespace()
    {
        $namespaceGenerator = $this->getNamespaceGenerator();
        $namespace = $namespaceGenerator->generate();
        return $namespace;
    }

    /**
     * Generates complex type list
     *
     * @return string
     */
    protected function generateComplexTypes()
    {
        $complexTypes       = array();
        $exceptionGenerator = $this->getExceptionGenerator();
        $structGenerator    = $this->getStructGenerator();
        foreach ($this->complexTypeRefs as $complexTypeRef) {
            if ($complexTypeRef->isSubclassOf("\Exception")) {
                // exception
                $exceptionGenerator->setClass($complexTypeRef);
                $complexTypes[] = $exceptionGenerator->generate();
            } else {
                // struct
                $structGenerator->setClass($complexTypeRef);
                $complexTypes[] = $structGenerator->generate();
            }
        }
        $complexTypes = implode("\n", $complexTypes);
        return $complexTypes;
    }
}
