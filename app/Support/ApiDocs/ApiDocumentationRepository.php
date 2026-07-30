<?php

namespace App\Support\ApiDocs;

use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use RuntimeException;

class ApiDocumentationRepository
{
    private array $cache = [];

    public function all(): array
    {
        $documentations = config('l5-swagger.documentations', []);

        return collect(array_keys($documentations))
            ->map(fn (string $key) => $this->find($key))
            ->filter()
            ->values()
            ->all();
    }

    public function find(string $key): ?array
    {
        if (array_key_exists($key, $this->cache)) {
            return $this->cache[$key];
        }

        $documentation = config("l5-swagger.documentations.$key");

        if (! $documentation) {
            return $this->cache[$key] = null;
        }

        $docsJson = $documentation['paths']['docs_json'] ?? null;

        if (! $docsJson) {
            return $this->cache[$key] = null;
        }

        $specPath = storage_path('api-docs/' . $docsJson);

        if (! file_exists($specPath)) {
            return $this->cache[$key] = null;
        }

        try {
            $spec = json_decode(file_get_contents($specPath), true, 512, JSON_THROW_ON_ERROR);
        } catch (\Throwable $throwable) {
            throw new RuntimeException("Unable to read API documentation spec [$key].", previous: $throwable);
        }

        return $this->cache[$key] = $this->transformDocumentation($key, $documentation, $spec);
    }

    private function transformDocumentation(string $key, array $documentation, array $spec): array
    {
        $components = $spec['components'] ?? [];
        $tagDescriptions = collect($spec['tags'] ?? [])
            ->mapWithKeys(fn (array $tag) => [$tag['name'] => $tag['description'] ?? ''])
            ->all();

        $endpoints = [];

        foreach ($spec['paths'] ?? [] as $path => $operations) {
            foreach ($this->sortOperations($operations) as $method => $operation) {
                $endpoints[] = $this->transformOperation($path, $method, $operation, $components);
            }
        }

        usort($endpoints, function (array $left, array $right): int {
            return [$left['path'], $left['method_order']] <=> [$right['path'], $right['method_order']];
        });

        $grouped = collect($endpoints)
            ->groupBy(fn (array $endpoint) => $endpoint['primary_tag'])
            ->map(fn ($group, string $tag) => [
                'name' => $tag,
                'description' => $tagDescriptions[$tag] ?? '',
                'endpoints' => $group->values()->all(),
            ])
            ->sortBy(fn (array $section) => $this->tagPriority($section['name']))
            ->values()
            ->all();

        return [
            'key' => $key,
            'title' => $documentation['api']['title'] ?? Str::headline($key),
            'route' => route('docs.show', $key),
            'base_url' => url('/api'),
            'version' => Arr::get($spec, 'info.version'),
            'description' => Arr::get($spec, 'info.description', ''),
            'tags' => $grouped,
            'endpoints' => $endpoints,
        ];
    }

    private function sortOperations(array $operations): array
    {
        $order = ['get', 'post', 'put', 'patch', 'delete', 'options', 'head'];
        $sorted = [];

        foreach ($order as $method) {
            if (isset($operations[$method])) {
                $sorted[$method] = $operations[$method];
            }
        }

        return $sorted;
    }

    private function transformOperation(string $path, string $method, array $operation, array $components): array
    {
        $requestBody = $this->extractRequestBody($operation['requestBody'] ?? null, $components);
        $responses = [];

        foreach (($operation['responses'] ?? []) as $status => $response) {
            $responses[] = $this->transformResponse((string) $status, $response, $components);
        }

        return [
            'id' => Str::slug($method . ' ' . $path),
            'path' => $path,
            'method' => strtoupper($method),
            'method_order' => $this->methodOrder($method),
            'summary' => $operation['summary'] ?? $path,
            'description' => $operation['description'] ?? '',
            'operation_id' => $operation['operationId'] ?? null,
            'tags' => $operation['tags'] ?? ['Untagged'],
            'primary_tag' => $operation['tags'][0] ?? 'Untagged',
            'secured' => ! empty($operation['security']),
            'parameters' => $this->transformParameters($operation['parameters'] ?? [], $components),
            'request' => $requestBody,
            'responses' => $responses,
        ];
    }

    private function transformParameters(array $parameters, array $components): array
    {
        return collect($parameters)
            ->map(function (array $parameter) use ($components) {
                $schema = $this->resolveSchema($parameter['schema'] ?? [], $components);

                return [
                    'name' => $parameter['name'] ?? '',
                    'in' => $parameter['in'] ?? '',
                    'required' => (bool) ($parameter['required'] ?? false),
                    'description' => $parameter['description'] ?? '',
                    'schema' => $schema,
                    'example' => $parameter['example'] ?? $this->exampleFromSchema($schema),
                ];
            })
            ->all();
    }

    private function extractRequestBody(mixed $requestBody, array $components): ?array
    {
        if (! is_array($requestBody)) {
            return null;
        }

        $content = $this->firstContent($requestBody['content'] ?? []);

        if (! $content) {
            return null;
        }

        $schema = $this->resolveSchema($content['schema'] ?? [], $components);

        return [
            'required' => (bool) ($requestBody['required'] ?? false),
            'content_type' => $content['contentType'] ?? 'application/json',
            'schema' => $schema,
            'example' => $this->exampleFromSchema($schema),
        ];
    }

    private function transformResponse(string $status, array $response, array $components): array
    {
        $content = $this->firstContent($response['content'] ?? []);
        $schema = null;
        $example = null;

        if ($content) {
            $schema = $this->resolveSchema($content['schema'] ?? [], $components);
            $example = $this->exampleFromSchema($schema);
        }

        return [
            'status' => $status,
            'description' => $response['description'] ?? '',
            'content_type' => $content['contentType'] ?? null,
            'schema' => $schema,
            'example' => $example,
        ];
    }

    private function firstContent(array $content): ?array
    {
        if (isset($content['application/json'])) {
            return ['contentType' => 'application/json'] + $content['application/json'];
        }

        $first = reset($content);

        if (! is_array($first)) {
            return null;
        }

        return ['contentType' => array_key_first($content)] + $first;
    }

    private function resolveSchema(array $schema, array $components): array
    {
        if (isset($schema['$ref'])) {
            $resolved = $this->resolveReference($schema['$ref'], $components);

            if (is_array($resolved)) {
                $schema = array_merge($resolved, Arr::except($schema, ['$ref']));
            }
        }

        if (isset($schema['allOf']) && is_array($schema['allOf'])) {
            $merged = [];

            foreach ($schema['allOf'] as $item) {
                if (! is_array($item)) {
                    continue;
                }

                $merged = array_replace_recursive($merged, $this->resolveSchema($item, $components));
            }

            $schema = array_replace_recursive($merged, Arr::except($schema, ['allOf']));
        }

        if (isset($schema['items']) && is_array($schema['items'])) {
            $schema['items'] = $this->resolveSchema($schema['items'], $components);
        }

        if (isset($schema['properties']) && is_array($schema['properties'])) {
            foreach ($schema['properties'] as $name => $property) {
                if (is_array($property)) {
                    $schema['properties'][$name] = $this->resolveSchema($property, $components);
                }
            }
        }

        if (isset($schema['additionalProperties']) && is_array($schema['additionalProperties'])) {
            $schema['additionalProperties'] = $this->resolveSchema($schema['additionalProperties'], $components);
        }

        return $schema;
    }

    private function resolveReference(string $reference, array $components): mixed
    {
        $reference = ltrim($reference, '#/');
        $segments = explode('/', $reference);
        $value = $components;

        foreach ($segments as $segment) {
            if (! is_array($value) || ! array_key_exists($segment, $value)) {
                return null;
            }

            $value = $value[$segment];
        }

        return $value;
    }

    private function exampleFromSchema(mixed $schema): mixed
    {
        if (! is_array($schema)) {
            return null;
        }

        if (array_key_exists('example', $schema)) {
            return $schema['example'];
        }

        if (isset($schema['enum']) && is_array($schema['enum']) && $schema['enum'] !== []) {
            return $schema['enum'][0];
        }

        if (isset($schema['type']) && $schema['type'] === 'object') {
            $example = [];

            foreach ($schema['properties'] ?? [] as $name => $property) {
                $example[$name] = $this->exampleFromSchema($property);
            }

            if ($example !== []) {
                return $example;
            }

            if (isset($schema['additionalProperties'])) {
                return [
                    'key' => $this->exampleFromSchema($schema['additionalProperties']),
                ];
            }

            return (object) [];
        }

        if (isset($schema['type']) && $schema['type'] === 'array') {
            return [$this->exampleFromSchema($schema['items'] ?? [])];
        }

        $type = $schema['type'] ?? 'string';
        $format = $schema['format'] ?? null;

        return match ($type) {
            'integer' => 1,
            'number' => 1.5,
            'boolean' => true,
            'array' => [],
            default => match ($format) {
                'email' => 'user@example.com',
                'uuid' => '00000000-0000-0000-0000-000000000000',
                'date' => now()->toDateString(),
                'date-time' => now()->toIso8601String(),
                'password' => 'password123',
                default => 'example',
            },
        };
    }

    private function methodOrder(string $method): int
    {
        return match ($method) {
            'get' => 10,
            'post' => 20,
            'put' => 30,
            'patch' => 40,
            'delete' => 50,
            'options' => 60,
            'head' => 70,
            default => 99,
        };
    }

    private function tagPriority(string $tag): array
    {
        $normalized = strtolower($tag);

        return [
            str_contains($normalized, 'auth') ? 0 : 1,
            $normalized,
        ];
    }
}
