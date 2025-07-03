<?php
declare(strict_types=1);

namespace diygw\exceptions;


class FailedException extends DiygwException
{
    protected $code = 500;
}
