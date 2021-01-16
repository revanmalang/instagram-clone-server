<?php

declare(strict_types=1);

namespace App\Presentation\Errors\User;

use App\Domain\DomainException\DomainRecordNotPersistedException;

class UserCouldNotBeUpdatedException extends DomainRecordNotPersistedException
{
}
