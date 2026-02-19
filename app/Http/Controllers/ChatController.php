<?php

namespace App\Http\Controllers;

use App\Services\ChatParserService;
use Illuminate\Http\Request;

class ChatController extends Controller
{
    protected ChatParserService $chatParser;

    public function __construct(ChatParserService $chatParser)
    {
        $this->chatParser = $chatParser;
    }

    /**
     * Parse natural language input and return parsed data.
     */
    public function parse(Request $request)
    {
        $request->validate([
            'message' => 'required|string|max:500',
        ]);

        $result = $this->chatParser->parse($request->message);

        if ($result['success']) {
            return response()->json([
                'success' => true,
                'data' => $result,
                'message' => 'Transaction parsed successfully!'
            ]);
        } else {
            return response()->json([
                'success' => false,
                'errors' => $result['errors'],
                'data' => $result
            ], 422);
        }
    }
}
