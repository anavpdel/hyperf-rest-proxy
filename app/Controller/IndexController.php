<?php

declare(strict_types=1);

namespace App\Controller;

use Hyperf\Contract\ConfigInterface;
use Hyperf\Kafka\Producer;
use Hyperf\Contract\StdoutLoggerInterface;

use Psr\Http\Message\ServerRequestInterface;
use Hyperf\HttpServer\Annotation\AutoController;
use Psr\Http\Message\ResponseInterface as PsrResponseInterface;

#[AutoController]
class IndexController extends AbstractController
{
    public function __construct(private readonly StdoutLoggerInterface $logger, private readonly ConfigInterface $config) {}

    public function sendJsonData(
        Producer $producer,
        string $topicName,
        ServerRequestInterface $request
    ) {
        $body = $request->getBody()->getContents();
        $data = $this->validateJson($body);

        if (!$data['isValid']) {
            $this->logger->error('Invalid JSON request: ' . $body);
            return $this->createJsonResponse(false, 'Invalid JSON', 400);
        }

        $result = $this->processMessage($producer, $topicName, $body);

        // Check if a custom response JSON is configured
        if (!$result['errorFlag'] && $this->config->get('custom_response_json')) {
            try {
                $customResponse = json_decode($this->config->get('custom_response_json'), true, 512, JSON_THROW_ON_ERROR);
                return $this->response->json($customResponse, 200);
            } catch (\JsonException $e) {
                return $this->createJsonResponse(
                    !$result['errorFlag'],
                    "Custom JSON parse error: {$e->getMessage()}",
                    $result['errorFlag'] ? 400 : 200,
                    [
                        'code' => $result['errorFlag'] ? 1 : 0,
                        'topic' => $topicName,
                    ]
                );
            }

            return $this->response->json($customResponse, 200);
        }

        return $this->createJsonResponse(
            !$result['errorFlag'],
            $result['message'],
            $result['errorFlag'] ? 400 : 200,
            [
                'code' => $result['errorFlag'] ? 1 : 0,
                'topic' => $topicName,
            ]
        );
    }

    private function validateJson(string $body): array
    {
        $decoded = json_decode($body, true);
        return [
            'isValid' => json_last_error() === JSON_ERROR_NONE,
            'data' => $decoded,
        ];
    }

    private function processMessage(Producer $producer, string $topicName, string $body): array
    {
        $errorFlag = false;
        $message = 'Message sent successfully';

        try {
            $producer->send($topicName, $body);
            $this->logger->info('Message sent successfully. Topic: ' . $topicName);
        } catch (\Exception $e) {
            $this->logger->error('Could not send message. Reason: ' . $e->getMessage());
            $errorFlag = true;
            $message = $e->getMessage();
        }

        return [
            'errorFlag' => $errorFlag,
            'message' => $message,
        ];
    }

    private function createJsonResponse(
        bool $success,
        string $message,
        int $status,
        array $additionalData = []
    ): PsrResponseInterface {
        $responseData = array_merge([
            'success' => $success,
            'message' => $message,
        ], $additionalData);

        // var_dump($status);

        return $this->response->json($responseData)->withStatus($status);
    }
}