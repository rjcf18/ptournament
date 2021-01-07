<?php declare(strict_types=1);
namespace PoolTournament\Domain\Module\Match\Entity;

use DateTimeImmutable;

class MatchEntity
{
    private int $id;
    private FriendEntity $winner;
    private FriendEntity $looser;
    private int $looserBallsLeft;
    private bool $absence;
    private DateTimeImmutable $date;
    private DateTimeImmutable $createdAt;
    private DateTimeImmutable $updatedAt;

    public function __construct(
        int $id,
        FriendEntity $winner,
        FriendEntity $looser,
        int $looserBallsLeft,
        bool $absence,
        DateTimeImmutable $date,
        DateTimeImmutable $createdAt,
        DateTimeImmutable $updatedAt
    ) {
        $this->id = $id;
        $this->winner = $winner;
        $this->looser = $looser;
        $this->looserBallsLeft = $looserBallsLeft;
        $this->absence = $absence;
        $this->date = $date;
        $this->createdAt = $createdAt;
        $this->updatedAt = $updatedAt;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function setId(int $id): self
    {
        $this->id = $id;

        return $this;
    }

    public function getWinner(): FriendEntity
    {
        return $this->winner;
    }

    public function setWinner(FriendEntity $winner): self
    {
        $this->winner = $winner;

        return $this;
    }

    public function getLooser(): FriendEntity
    {
        return $this->looser;
    }

    public function setLooser(FriendEntity $looser): self
    {
        $this->looser = $looser;

        return $this;
    }

    public function getLooserBallsLeft(): int
    {
        return $this->looserBallsLeft;
    }

    public function setLooserBallsLeft(int $looserBallsLeft): self
    {
        $this->looserBallsLeft = $looserBallsLeft;

        return $this;
    }

    public function isAbsence(): bool
    {
        return $this->absence;
    }

    public function setAbsence(bool $absence): self
    {
        $this->absence = $absence;

        return $this;
    }

    public function getDate(): DateTimeImmutable
    {
        return $this->date;
    }

    public function setDate(DateTimeImmutable $date): self
    {
        $this->date = $date;

        return $this;
    }

    public function getCreatedAt(): DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(DateTimeImmutable $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getUpdatedAt(): DateTimeImmutable
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(DateTimeImmutable $updatedAt): self
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }
}