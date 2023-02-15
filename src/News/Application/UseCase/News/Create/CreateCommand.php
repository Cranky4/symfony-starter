<?php

declare(strict_types=1);

namespace App\News\Application\UseCase\News\Create;

use DateTimeImmutable;
use Symfony\Component\Validator\Constraints as Assert;

final readonly class CreateCommand
{
    public function __construct(
        public string $id,
        public string $source,
        public string $sourceId,
        public string $title,
        #[Assert\Length(max: 255)]
        public string $short,
        public string $content,
        public DateTimeImmutable $dateTime,
        public ?string $image = null,
    ) {
    }
}
