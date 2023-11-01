<?php

namespace App\Utilities;

use App\Utilities\Contracts\ElasticsearchHelperInterface;
use Elasticsearch\ClientBuilder;

class ElasticsearchHelper implements ElasticsearchHelperInterface
{
    protected $client;
    protected $index = 'emails';

    public function __construct()
    {
        $this->client = ClientBuilder::create()->build();
    }

    public function storeEmail(string $messageBody, string $messageSubject, string $toEmailAddress): mixed
    {
        $params = [
            'index' => $this->index,
            'body' => [
                'body' => $messageBody,
                'subject' => $messageSubject,
                'to' => $toEmailAddress,
            ],

        ];

        $response = $this->client->index($params);
        return $response;
    }

    public function listEmails(int $page, int $perPage): array
    {
        $params = [
            'index' => $this->index,
            'body' => [
                'from' => ($page - 1) * $perPage,
                'size' => $perPage,
            ]
        ];

        $response = $this->client->search($params);
        $emails = [];
        foreach ($response['hits']['hits'] as $hit) {
            $emails[] = [
                'id' => $hit['_id'],
                'body' => $hit['_source']['body'],
                'subject' => $hit['_source']['subject'],
                'to' => $hit['_source']['to'],
            ];
        }

        return [
            'emails' => $emails,
            'total' => $response['hits']['total']['value'],
            'page' => $page,
            'per_page' => $perPage,
        ];
    }
}
