<?php

namespace parabase;

final class Session
{
    public function __construct(
        public readonly string $key,
        public readonly int $userId,
        public readonly string $csrf,
        public readonly bool $revoked,
        public readonly int $expires,
    ) {}

    public static function fromRow(object $row): self {
        return new self(
            key:     $row->key,
            userId:  (int)$row->user_id,
            csrf:    $row->csrf,
            revoked: (bool)$row->revoked,
            expires: (int)$row->expires,
        );
    }

    public function isValid(): bool {
        return !$this->revoked && $this->expires > time();
    }

    public function isExpired(): bool {
        return $this->expires <= time();
    }

    public function revoke(): void {
        Sessions::revoke($this->key);
    }
}
