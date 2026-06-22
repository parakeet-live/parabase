<?php

namespace parabase;

/*
 * ExampleModel: copy this file and rename the class to build your own models.
 *
 * table schema:
 *   CREATE TABLE example (
 *     id         INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
 *     title      VARCHAR(255) NOT NULL,
 *     body       TEXT NOT NULL,
 *     created_at INT UNSIGNED NOT NULL
 *   );
 */
class ExampleModel
{
    public int    $id;
    public string $title;
    public string $body;
    public int    $createdAt;

    /*
    public static function fromID(int $id): ExampleModel|null
    {
        $data = Database::singleton()
            ->run("SELECT * FROM example WHERE id = :id", [":id" => $id])
            ->fetch(\PDO::FETCH_OBJ);

        if (!$data) return null;
        return new self($data);
    }

    // @return ExampleModel[]
    public static function listing(int $limit = 20, int $offset = 0): array
    {
        $rows = Database::singleton()
            ->run(
                "SELECT * FROM example ORDER BY id DESC LIMIT :lim OFFSET :off",
                [":lim" => $limit, ":off" => $offset]
            )
            ->fetchAll(\PDO::FETCH_OBJ);

        return array_map(fn($row) => new self($row), $rows);
    }

    public static function create(string $title, string $body): int|null
    {
        Database::singleton()->run(
            "INSERT INTO example (title, body, created_at) VALUES (:title, :body, UNIX_TIMESTAMP())",
            [":title" => $title, ":body" => $body]
        );
        $id = Database::singleton()->lastInsertId();
        return $id ?: null;
    }

    public function __construct(object $data)
    {
        $this->id        = $data->id;
        $this->title     = $data->title;
        $this->body      = $data->body;
        $this->createdAt = $data->created_at;
    }

    public function update(string $title, string $body): void
    {
        $this->title = $title;
        $this->body  = $body;

        Database::singleton()->run(
            "UPDATE example SET title = :title, body = :body WHERE id = :id",
            [":title" => $title, ":body" => $body, ":id" => $this->id]
        );
    }

    public function delete(): void
    {
        Database::singleton()->run(
            "DELETE FROM example WHERE id = :id",
            [":id" => $this->id]
        );
    }
    */

    public function toArray(): array
    {
        return [
            "id"        => $this->id,
            "title"     => $this->title,
            "body"      => $this->body,
            "createdAt" => $this->createdAt,
        ];
    }
}
