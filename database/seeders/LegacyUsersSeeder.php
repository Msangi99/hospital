<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class LegacyUsersSeeder extends Seeder
{
    public function run(): void
    {
        $sqlPath = base_path('../old/eunicetz_semanami.sql');

        if (! is_file($sqlPath)) {
            $this->command?->warn("Legacy SQL dump not found at: {$sqlPath}");
            return;
        }

        $sql = file_get_contents($sqlPath);
        if ($sql === false) {
            $this->command?->warn("Unable to read legacy SQL dump at: {$sqlPath}");
            return;
        }

        if (! preg_match('/INSERT INTO `users`\\s*\\(([^)]*)\\)\\s*VALUES\\s*(.+?);/s', $sql, $m)) {
            $this->command?->warn('No `users` INSERT statement found in legacy SQL dump.');
            return;
        }

        $columns = array_map(
            fn ($c) => trim($c, " \t\n\r\0\x0B`"),
            explode(',', $m[1]),
        );

        $tuples = $this->parseTuples($m[2]);

        $count = 0;
        DB::transaction(function () use ($tuples, $columns, &$count) {
            foreach ($tuples as $tuple) {
                $row = $this->tupleToRow($columns, $tuple);

                $email = (string) ($row['email'] ?? '');
                if ($email === '') {
                    continue;
                }

                $password = (string) ($row['password'] ?? '');
                // Skip obviously placeholder / invalid hashes from the dump.
                if ($password === '' || (! str_starts_with($password, '$2y$') && ! str_starts_with($password, '$2a$') && ! str_starts_with($password, '$2b$'))) {
                    continue;
                }

                $fullName = (string) ($row['fullname'] ?? '');
                $role = (string) ($row['role'] ?? 'PATIENT');
                $status = (string) ($row['status'] ?? 'ACTIVE');

                DB::table('users')->updateOrInsert(
                    ['email' => $email],
                    [
                        // preserve legacy id when possible (not required)
                        'id' => is_numeric($row['id'] ?? null) ? (int) $row['id'] : null,
                        'name' => $this->makeShortName($fullName, $email),
                        'full_name' => $fullName !== '' ? $fullName : null,
                        'email' => $email,
                        'phone' => ($row['phone'] ?? null) ?: null,
                        'role' => $role !== '' ? $role : 'PATIENT',
                        'status' => $status !== '' ? $status : 'ACTIVE',
                        'password' => $password,
                        'created_at' => $row['created_at'] ?? now(),
                        'updated_at' => $row['updated_at'] ?? now(),
                    ],
                );

                $count++;
            }
        });

        $this->command?->info("Imported {$count} legacy users into `users` table.");
    }

    /**
     * @return array<int, array<int, mixed>>
     */
    private function parseTuples(string $valuesSql): array
    {
        $tuples = [];
        $len = strlen($valuesSql);
        $i = 0;

        while ($i < $len) {
            while ($i < $len && $valuesSql[$i] !== '(') {
                $i++;
            }
            if ($i >= $len) {
                break;
            }

            $i++; // skip '('
            $fields = [];
            $field = '';
            $inString = false;
            $escape = false;

            while ($i < $len) {
                $ch = $valuesSql[$i];

                if ($inString) {
                    if ($escape) {
                        $field .= $ch;
                        $escape = false;
                    } elseif ($ch === '\\\\') {
                        $escape = true;
                    } elseif ($ch === "'") {
                        $inString = false;
                    } else {
                        $field .= $ch;
                    }

                    $i++;
                    continue;
                }

                if ($ch === "'") {
                    $inString = true;
                    $i++;
                    continue;
                }

                if ($ch === ',') {
                    $fields[] = $this->normalizeSqlValue($field);
                    $field = '';
                    $i++;
                    continue;
                }

                if ($ch === ')') {
                    $fields[] = $this->normalizeSqlValue($field);
                    $i++; // skip ')'
                    break;
                }

                $field .= $ch;
                $i++;
            }

            if ($fields !== []) {
                $tuples[] = $fields;
            }
        }

        return $tuples;
    }

    private function normalizeSqlValue(string $raw): mixed
    {
        $v = trim($raw);

        if ($v === '' || strcasecmp($v, 'NULL') === 0) {
            return null;
        }

        if (is_numeric($v)) {
            // keep numeric strings as numeric
            return str_contains($v, '.') ? (float) $v : (int) $v;
        }

        return $v;
    }

    /**
     * @param  array<int, string>  $columns
     * @param  array<int, mixed>  $tuple
     * @return array<string, mixed>
     */
    private function tupleToRow(array $columns, array $tuple): array
    {
        $row = [];
        foreach ($columns as $idx => $col) {
            $row[$col] = $tuple[$idx] ?? null;
        }
        return $row;
    }

    private function makeShortName(string $fullName, string $email): string
    {
        $name = trim($fullName);
        if ($name === '') {
            return Str::before($email, '@') ?: 'User';
        }

        $name = preg_replace('/\\s+/', ' ', $name) ?? $name;

        // keep "name" reasonably short for UI.
        if (mb_strlen($name) > 60) {
            return mb_substr($name, 0, 60);
        }

        return $name;
    }
}

