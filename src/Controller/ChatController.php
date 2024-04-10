<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;

class ChatController extends AbstractController
{
    #[Route('/chat', name: 'app_chat')]
    public function index(): Response
    {
        return $this->render('chat/chat.html.twig');
    }

    #[Route('/api/chat', name: 'api_chat', methods: ['POST'])]
    public function chat(Request $request): Response
    {
        $content = json_decode($request->getContent(), true);
        $userMessage = trim($content['message']);

        $greetings = ["hello", "hi", "hey"];
        $isGreeting = false;
        
        foreach ($greetings as $greeting) {
            if (stripos($userMessage, $greeting) !== false) {
                $isGreeting = true;
                break;
            }
        }
        if ($isGreeting) {
            $greetingResponses = [
                "Hello! How can I assist you today?",
                "Hi there! What can I do for you?",
                "Hey! Ready to talk about sports?"
            ];
            return $this->json(['reply' => $greetingResponses[array_rand($greetingResponses)]]);
        }
        $client = new Client();
        try {
            $response = $client->request('POST', 'https://api.openai.com/v1/completions', [
                'headers' => [
                    'Authorization' => 'Bearer ' . $_ENV['OPENAI_API_KEY'],
                    'Content-Type' => 'application/json',
                ],
                'json' => [
                    'model' => 'gpt-3.5-turbo-instruct',
                    'prompt' => "User asks: " . $userMessage . " \nProvide a detailed answer:",
                    'max_tokens' => 150,
                    'temperature' => 0.5,
                ],
            ]);

            $apiResponse = json_decode((string)$response->getBody(), true);
            $reply = $apiResponse['choices'][0]['text'] ?? 'Sorry, I could not generate a response. Please try asking something else.';

            return $this->json(['reply' => $reply]);
        } catch (\Exception $e) {
            return $this->json(['error' => 'An unexpected error occurred. Please try again.'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
