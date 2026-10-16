<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;

class ApiDocs extends Page
{
    protected static string|\BackedEnum|null $navigationIcon = Heroicon::OutlinedCodeBracket;

    protected static ?string $navigationLabel = 'API Docs';

    protected static ?string $title = 'API Docs';

    protected static ?int $navigationSort = 30;

    protected string $view = 'filament.pages.api-docs';

    /**
     * @return array{base: string, total: int, groups: array<int, array{title: string, id: string, note: string, endpoints: array<int, array<string, mixed>>}>}
     */
    public function catalog(): array
    {
        $path = resource_path('api/catalog.md');
        $markdown = is_file($path) ? (string) file_get_contents($path) : '';
        $chunks = preg_split('/```/', $markdown) ?: [];
        $groups = [];
        $groupIndex = -1;
        $endpoint = null;
        $groupNote = '';

        $flush = function () use (&$endpoint, &$groups, &$groupIndex): void {
            if ($endpoint !== null && $groupIndex >= 0) {
                $groups[$groupIndex]['endpoints'][] = $endpoint;
            }
            $endpoint = null;
        };

        foreach ($chunks as $index => $chunk) {
            if ($index % 2 === 1) {
                $code = trim((string) (preg_replace('/^[a-zA-Z0-9]+\r?\n/', '', $chunk) ?? $chunk));
                if ($endpoint !== null && $code !== '') {
                    $endpoint['blocks'][] = [
                        'label' => str_contains($code, '"success"') ? 'Response' : (str_starts_with($code, '{') ? 'Request body' : 'Example'),
                        'code' => $code,
                    ];
                }
                continue;
            }

            foreach (preg_split("/\r\n|\n|\r/", $chunk) ?: [] as $line) {
                if (str_starts_with($line, '## ')) {
                    $flush();
                    $title = trim(substr($line, 3));
                    $groups[] = [
                        'title' => $title,
                        'id' => str($title)->slug()->toString(),
                        'note' => '',
                        'endpoints' => [],
                    ];
                    $groupIndex = count($groups) - 1;
                    $groupNote = '';
                    continue;
                }

                if (str_starts_with($line, '### ') && preg_match('/^(GET|POST|PUT|PATCH|DELETE)\s+(\S+)/', trim(substr($line, 4)), $match) === 1) {
                    $flush();
                    if ($groupIndex < 0) {
                        $groups[] = ['title' => 'API', 'id' => 'api', 'note' => '', 'endpoints' => []];
                        $groupIndex = 0;
                    }
                    $endpoint = [
                        'method' => $match[1],
                        'path' => $match[2],
                        'lines' => [],
                        'blocks' => [],
                        'group_note' => $groupNote,
                    ];
                    continue;
                }

                if (str_starts_with($line, '# ') || trim($line) === '') {
                    continue;
                }

                if ($endpoint !== null) {
                    $endpoint['lines'][] = $line;
                } elseif ($groupIndex >= 0) {
                    $groupNote = trim($groupNote.' '.$line);
                    $groups[$groupIndex]['note'] = $groupNote;
                }
            }
        }
        $flush();

        $total = 0;
        foreach ($groups as &$group) {
            foreach ($group['endpoints'] as &$item) {
                $text = strtolower(implode(' ', $item['lines']).' '.$item['group_note']);
                $public = str_contains($text, 'no auth')
                    || str_contains($text, 'no token')
                    || str_contains($text, 'no bearer')
                    || str_contains($text, 'public');
                if (str_contains($text, 'auth required')) {
                    $public = false;
                }
                $item['auth'] = ! $public;
                $item['summary'] = trim(str_replace('`', '', $item['lines'][0] ?? ($item['group_note'] !== '' ? $item['group_note'] : $item['path'])));
                $total++;
            }
        }
        unset($group, $item);

        return [
            'base' => rtrim((string) config('app.url'), '/').'/api/v1',
            'total' => $total,
            'groups' => array_values(array_filter($groups, fn (array $group): bool => $group['endpoints'] !== [])),
        ];
    }
}
