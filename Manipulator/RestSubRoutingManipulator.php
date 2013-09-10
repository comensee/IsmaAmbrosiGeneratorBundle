<?php


namespace IsmaAmbrosi\Bundle\GeneratorBundle\Manipulator;

use Symfony\Component\DependencyInjection\Container;
use Sensio\Bundle\GeneratorBundle\Manipulator\RoutingManipulator;
/**
 * Changes the PHP code of a YAML routing file.
 *
 * @author Fabien Potencier <fabien@symfony.com>
 */
class RestSubRoutingManipulator
{
    private $file;

    /**
     * Constructor.
     *
     * @param string $file The YAML routing file path
     */
    public function __construct($file)
    {
        $this->file = $file;
    }

    /**
     * Adds a routing resource at the top of the existing ones.
     *
     * @param string $bundle
     * @param string $format
     * @param string $prefix
     * @param string $path
     *
     * @return Boolean true if it worked, false otherwise
     *
     * @throws \RuntimeException If bundle is already imported
     */
    public function addResource($bundle, $document, $subdocument, $format, $prefix = '/', $path = 'routing', $base_file = false, $hasParent = false)
    {
        $current = '';
        if (file_exists($this->file)) {
            $current = file_get_contents($this->file);
            
            // Don't add same bundle twice
            if (false !== strpos($current, strtolower($document).'s_'.strtolower($subdocument))) {
                throw new \RuntimeException(sprintf('Route "%s" is already imported.', strtolower($document).'s_'.strtolower($subdocument)));
            }
        } elseif (!is_dir($dir = dirname($this->file))) {
            mkdir($dir, 0777, true);
        }

        $code = $current."\n";
        $code .= sprintf("%s:\n", strtolower($document).'s_'.strtolower($subdocument));
        if($base_file){
            if ('annotation' == $format) {
                $code .= sprintf("    resource: \"@%s/Controller/\"\n    type:     annotation\n", $bundle);
            } else {
                $code .= sprintf("    resource: %s\Controller\%s\%sController\n", $bundle, $document, $subdocument);
            }
        } else {
            $code .= sprintf("    resource: %s\Controller\%s\%s\n", $bundle, $document,$path);
        }

        $code .= "    type:    rest\n";
        $code .= "    parent:    ".strtolower($document)."s \n";
        $code .= "\n";

        if (false === file_put_contents($this->file, $code)) {
            return false;
        }

        return true;
    }
}
