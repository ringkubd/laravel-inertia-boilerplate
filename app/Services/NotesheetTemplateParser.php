<?php

namespace App\Services;

class NotesheetTemplateParser
{
    public function parse(string $templateContent, array $replacements = []): string
    {
        $normalized = [];

        foreach ($replacements as $key => $value) {
            $normalized['{{ ' . $key . ' }}'] = (string) $value;
            $normalized['{{' . $key . '}}'] = (string) $value;
        }

        return strtr($templateContent, $normalized);
    }
}
