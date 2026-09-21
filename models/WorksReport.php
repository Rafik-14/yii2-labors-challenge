<?php

declare(strict_types=1);

namespace app\models;

/**
 * Builds the `POST /api/works` payload from labor rows.
 *
 * Output shape (required by the specification):
 *
 * ```json
 * {"2021-05-19": {"John Doe": {"name": "John Doe", "working_minutes": 480}}}
 * ```
 *
 * Rows are expected in chronological order; `aggregate()` does not re-sort, so dates and
 * workers appear in the order of their first shift.
 */
final class WorksReport
{
    /**
     * @param iterable<array{first_name?: ?string, last_name?: ?string, need_work?: mixed, working_minutes?: mixed, working_date?: ?string}> $rows
     * @return array<string, array<string, array{name: string, working_minutes: int}>>
     */
    public static function aggregate(iterable $rows): array
    {
        $result = [];

        foreach ($rows as $row) {
            if (empty($row['need_work'])) {
                continue;
            }

            $dateKey = self::dateKey($row['working_date'] ?? null);
            if ($dateKey === null) {
                continue;
            }

            $fullName = Labors::buildFullName($row['first_name'] ?? null, $row['last_name'] ?? null);
            if ($fullName === '') {
                continue;
            }

            // Missing / null minutes count as 0; negative values are treated as corrupt and ignored.
            $minutes = max(0, (int) ($row['working_minutes'] ?? 0));

            if (!isset($result[$dateKey][$fullName])) {
                $result[$dateKey][$fullName] = ['name' => $fullName, 'working_minutes' => 0];
            }
            $result[$dateKey][$fullName]['working_minutes'] += $minutes;
        }

        return $result;
    }

    /**
     * Sorts rows by `working_date` ascending (rows without a date go last).
     *
     * @param array<int, array<string, mixed>> $rows
     * @return array<int, array<string, mixed>>
     */
    public static function sortChronologically(array $rows): array
    {
        usort($rows, static function (array $a, array $b): int {
            return [empty($a['working_date']), (string) ($a['working_date'] ?? '')]
                <=> [empty($b['working_date']), (string) ($b['working_date'] ?? '')];
        });

        return $rows;
    }

    /**
     * Extracts the calendar day from a `Y-m-d H:i:s` value as stored, without any timezone
     * conversion (avoids shifting shifts near midnight to another day), and rejects invalid
     * values instead of silently grouping them under 1970-01-01.
     */
    private static function dateKey(mixed $workingDate): ?string
    {
        if (!is_string($workingDate) || !preg_match('/^(\d{4})-(\d{2})-(\d{2})/', $workingDate, $m)) {
            return null;
        }

        return checkdate((int) $m[2], (int) $m[3], (int) $m[1]) ? "{$m[1]}-{$m[2]}-{$m[3]}" : null;
    }
}
