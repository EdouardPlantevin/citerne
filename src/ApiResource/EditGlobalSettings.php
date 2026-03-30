<?php

declare(strict_types=1);

namespace App\ApiResource;

use Symfony\Component\Serializer\Attribute\Groups;
use Symfony\Component\Validator\Constraints as Assert;

final class EditGlobalSettings
{

    public function __construct(
        #[Assert\PositiveOrZero]
        #[Groups(['global_settings:write'])]
        public ?int $averageSpeed,

        #[Assert\PositiveOrZero]
        #[Groups(['global_settings:write'])]
        public ?int $loadingTime,

        #[Assert\PositiveOrZero]
        #[Groups(['global_settings:write'])]
        public ?int $unloadingTime,
    )
    {
    }


}
