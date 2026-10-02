<?php

namespace App\Security;

use Symfony\Component\Filesystem\Filesystem;

final class UserStore
{
    private const STORAGE_FILE = '/var/data/users.json';

    public function __construct(private readonly string $projectDir)
    {
    }

    /**
     * @return array<string, array{email: string, password: string, roles: list<string>, createdAt: string, birthdate?: string}>
     */
    public function all(): array
    {
        $path = $this->getPath();

        if (!is_file($path)) {
            return [];
        }

        $content = file_get_contents($path);
        $users = json_decode($content ?: '[]', true);

        return is_array($users) ? $users : [];
    }

    public function find(string $email): ?AppUser
    {
        $record = $this->all()[$this->normalizeEmail($email)] ?? null;

        if (!is_array($record)) {
            return null;
        }

        return new AppUser($record['email'], $record['password'], $record['roles'] ?? ['ROLE_USER']);
    }

    public function exists(string $email): bool
    {
        return isset($this->all()[$this->normalizeEmail($email)]);
    }

    /**
     * @param list<string> $roles
     */
    public function create(string $email, string $hashedPassword, array $roles = ['ROLE_USER'], string $birthdate = ''): void
    {
        $users = $this->all();
        $normalizedEmail = $this->normalizeEmail($email);

        $users[$normalizedEmail] = [
            'email' => $normalizedEmail,
            'password' => $hashedPassword,
            'roles' => array_values(array_unique($roles)),
            'birthdate' => $birthdate,
            'createdAt' => (new \DateTimeImmutable())->format('Y-m-d H:i:s'),
        ];

        $this->save($users);
    }

    private function normalizeEmail(string $email): string
    {
        return mb_strtolower(trim($email));
    }

    /**
     * @param array<string, array{email: string, password: string, roles: list<string>, createdAt: string, birthdate?: string}> $users
     */
    private function save(array $users): void
    {
        $filesystem = new Filesystem();
        $path = $this->getPath();
        $filesystem->mkdir(dirname($path));
        $filesystem->dumpFile($path, json_encode($users, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
    }

    private function getPath(): string
    {
        return $this->projectDir.self::STORAGE_FILE;
    }
}
