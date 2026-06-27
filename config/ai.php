<?php

return [

    /*
    |--------------------------------------------------------------------------
    | مزوّد الذكاء الاصطناعي (مجاني)
    |--------------------------------------------------------------------------
    | groq  — مفتاح مجاني من https://console.groq.com (موصى به)
    | gemini — مفتاح مجاني من https://aistudio.google.com/apikey
    | ollama — محلي بالكامل http://localhost:11434 (بدون مفتاح)
    */
    'provider' => env('AI_PROVIDER', 'groq'),

    'groq' => [
        'api_key' => env('GROQ_API_KEY'),
        'base_url' => env('GROQ_BASE_URL', 'https://api.groq.com/openai/v1'),
        'model' => env('GROQ_MODEL', 'openai/gpt-oss-20b'),
    ],

    'gemini' => [
        'api_key' => env('GEMINI_API_KEY'),
        'model' => env('GEMINI_MODEL', 'gemini-1.5-flash'),
    ],

    'ollama' => [
        'base_url' => env('OLLAMA_BASE_URL', 'http://127.0.0.1:11434'),
        'model' => env('OLLAMA_MODEL', 'llama3.2'),
    ],

    'timeout' => (int) env('AI_TIMEOUT', 45),

    /** Timeout for full article generation (includes web search + long body). */
    'article_timeout' => (int) env('AI_ARTICLE_TIMEOUT', 120),

    /*
    |--------------------------------------------------------------------------
    | البحث في الإنترنت لتوليد المقالات
    |--------------------------------------------------------------------------
    | tavily — موصى به، 1000 بحث مجاني/شهر بدون بطاقة: https://tavily.com
    | brave  — مدفوع: ~$5 رصيد شهري (~1000 بحث) ثم يُخصم من البطاقة (ليس مجانياً بالكامل)
    | duckduckgo — بدون مفتاح (نتائج محدودة، احتياطي)
    */
    'web_search' => [
        'provider' => env('AI_WEB_SEARCH_PROVIDER', 'auto'),

        'max_results' => (int) env('AI_WEB_SEARCH_MAX_RESULTS', 6),

        'tavily' => [
            'api_key' => env('TAVILY_API_KEY'),
        ],

        'brave' => [
            'api_key' => env('BRAVE_SEARCH_API_KEY'),
        ],
    ],

];
