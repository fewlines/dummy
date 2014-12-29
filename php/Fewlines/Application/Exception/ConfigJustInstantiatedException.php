<?php
/**
 * fewlines CMS
 *
 * Description: This exception is thrown
 * if s.o. is trying to instaniate the
 * config object but it's already instantiated.
 * Here the user has to use the static
 * function "getInstance"
 *
 * @copyright Copyright (c) fewlines
 * @author Davide Perozzi
 */

namespace Fewlines\Application\Exception;

class ConfigJustInstantiatedException extends \Exception
{

}

?>