<?php
/**
 * fewlines CMS
 *
 * Description: This exception is thrown
 * if s.o. is trying to set a property to
 * the Template controller, but the property
 * does already exists.
 *
 * @copyright Copyright (c) fewlines
 * @author Davide Perozzi
 */

namespace Fewlines\Controller\Exception;

class PropertyExistException extends \Exception
{

}

?>