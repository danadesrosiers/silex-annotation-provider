<?php
/**
 * This file is part of the silex-annotation-provider package.
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @license       MIT License
 * @copyright (c) 2014, Dana Desrosiers <dana.desrosiers@gmail.com>
 */

namespace DDesrosiers\SilexAnnotations;

/**
 * Interface for annotations that depend on a context.
 *
 * @author Martinr Rademacher <mano@radebatz.com>
 */
interface AnnotationContextAwareInterface
{

    /**
     * @param mixed $context
     */
    public function setContext($context);

}
