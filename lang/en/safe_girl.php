<?php

return [
    'badge' => 'Safe-Girl Node Active',
    'title_html' => 'Hormone <br> <span class="text-pink-600 italic underline decoration-pink-200">tracking & symptoms.</span>',
    'subtitle' => 'A system connecting a girl\'s health patterns with a specialist through moderation.',

    'chart_title' => 'Hormonal Growth Chart (7-25 Years)',
    'chart_left' => 'Age 7',
    'chart_mid' => 'Age 16 (Puberty)',
    'chart_right' => 'Age 25',

    'chat_header' => 'SemaNami Chat',
    'chat_status' => 'AI Triage System: Online',
    'chat_hint' => 'Describe symptoms. AI may ask follow-up questions and then provide a possible condition and advice.',
    'chat_first_message' => 'Hello. I am Safe-Girl AI assistant. Please describe your symptoms and I will ask clarifying questions first.',

    'input_placeholder' => 'Write symptoms here (e.g., lower abdominal pain...)',
    'login_required' => 'Please log in to send a message',
    'login_now' => 'Log in now',

    'e2e' => 'End-to-End Encryption',
    'protected' => 'Protected',
    'sent_to_moderator' => 'Sent to Moderator',
    'received_reply' => 'Received. The assistant is reviewing your message.',
    'safe_girl_symptom_received' => 'Your message was received.',

    'possible_condition' => 'Possible condition',
    'urgency' => 'Urgency',
    'advice' => 'Advice',
    'red_flags' => 'Red flags',
    'ai_error_reply' => 'Sorry, the AI service is currently unavailable. Please try again shortly.',
    'ai_disabled_reply' => 'AI triage is currently disabled. Please share more details and a clinician will review.',
    'ai_key_invalid_reply' => 'AI key is invalid. Please contact the administrator.',
    'ai_parse_fallback' => 'Thank you. Could you share when the symptoms started and whether there is fever, bleeding, or severe pain?',
    'ai_task_prompt' => 'Analyze the conversation and return ONLY JSON with keys: type (question|conclusion), assistant_message, possible_condition, urgency, advice (array), red_flags (array). Ask a follow-up question if details are insufficient. If emergency red flags exist, mark urgency as emergency and advise immediate care.',
    'ai_default_system_prompt' => 'You are Safe-Girl triage assistant for girls/women. Use clear, compassionate language. Never provide definitive diagnosis. First ask targeted follow-up questions (duration, severity, menstrual context, pregnancy possibility, fever, bleeding, discharge, urinary symptoms, pain location). Once enough details are available, provide likely condition category, urgency level, practical advice, and red flags that require immediate hospital care. Keep answers concise and safe.',
];