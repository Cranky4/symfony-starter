<?php

declare(strict_types=1);

namespace App\Tests\News\Application\UseCase\News\Create;

use App\News\Application\Exception\CannotCreateNewsException;
use App\News\Application\UseCase\News\Create\CreateCommand;
use App\News\Application\UseCase\News\Create\CreateCommandHandler;
use App\News\Domain\Entity\News;
use App\News\Domain\Repository\NewsRepositoryInterface;
use App\News\Domain\ValueObject\NewsSource;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * @testdox Тесты обработчика ...
 */
class CreateCommandHandlerTest extends TestCase
{
    private NewsRepositoryInterface&MockObject $newsRepository;

    private CreateCommandHandler $handler;

    protected function setUp(): void
    {
        parent::setUp();

        $this->newsRepository = $this->createMock(NewsRepositoryInterface::class);

        $this->handler = new CreateCommandHandler(
            newsRepository: $this->newsRepository,
        );
    }

    public function testDuplicate(): void
    {
        // Arrange
        $source = new NewsSource(
            name: 'source',
            id: 'id',
        );

        $this->newsRepository
            ->expects($this->once())
            ->method('existBySource')
            ->with($source)
            ->willReturn(true);

        $this->newsRepository
            ->expects($this->never())
            ->method('save');

        // Act
        $this->expectException(CannotCreateNewsException::class);

        ($this->handler)(new CreateCommand(
            id: '14ec51f2-843d-4b92-ba7c-d8abacda61c8',
            source: $source->name,
            sourceId: $source->id,
            title: 'Хорошая новость',
            short: 'Все будет..',
            content: '<p>Привет</p>',
            dateTime: new \DateTimeImmutable(),
            image: 'http://image.com/1.png'
        ));

        // Asserts in arrange
    }

    public function testSuccess(): void
    {
        // Arrange
        $source = new NewsSource(
            name: 'source',
            id: 'id',
        );

        $this->newsRepository
            ->expects($this->once())
            ->method('existBySource')
            ->with($source)
            ->willReturn(false);

        $this->newsRepository
            ->expects($this->once())
            ->method('save')
            ->with(
                $this->callback(
                    static function (News $n): bool {
                        self::assertEquals(
                            '14ec51f2-843d-4b92-ba7c-d8abacda61c8',
                            $n->getId(),
                        );
                        // TODO: тут можно проверить другие поля
                        self::assertEquals(
                            'http://image.com/1.png',
                            $n->getImage(),
                        );

                        return true;
                    },
                )
            );

        // Act
        ($this->handler)(new CreateCommand(
            id: '14ec51f2-843d-4b92-ba7c-d8abacda61c8',
            source: $source->name,
            sourceId: $source->id,
            title: 'Хорошая новость',
            short: 'Все будет..',
            content: '<p>Привет</p>',
            dateTime: new \DateTimeImmutable(),
            image: 'http://image.com/1.png'
        ));

        // Asserts in arrange
    }
}
