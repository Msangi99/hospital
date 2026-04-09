<?php

return [
    'badge' => 'Safe-Girl Node Active',
    'title_html' => 'Ufuatiliaji wa <br> <span class="text-pink-600 italic underline decoration-pink-200">Homoni & Dalili.</span>',
    'subtitle' => 'Mfumo unaounganisha afya ya binti na mtaalamu.',

    'chart_title' => 'Hormonal Growth Chart (7-25 Years)',
    'chart_left' => 'Umri 7',
    'chart_mid' => 'Umri 16 (Puberty)',
    'chart_right' => 'Umri 25',

    'chat_header' => 'Sema Nami Chat',
    'chat_status' => 'Mfumo wa AI Triage: Online',
    'chat_hint' => 'Eleza dalili. AI inaweza kuuliza maswali ya kufuatilia kisha kutoa hitimisho la awali na ushauri.',
    'chat_first_message' => 'Habari. Mimi ni msaidizi wa AI wa Safe-Girl. Eleza dalili zako na nitauliza maswali ya ziada kwanza.',

    'input_placeholder' => 'Andika dalili hapa (mfano: maumivu ya chini ya tumbo...)',
    'login_required' => 'Tafadhali Login ili uweze kutuma ujumbe',
    'login_now' => 'Ingia Sasa',

    'e2e' => 'End-to-End Encryption',
    'protected' => 'Protected',
    'sent_to_moderator' => 'Imetumwa kwa Moderator',
    'received_reply' => 'Imepokelewa. Msaidizi anachambua ujumbe wako.',
    'safe_girl_symptom_received' => 'Ujumbe wako umepokelewa.',

    'possible_condition' => 'Hali inayowezekana',
    'urgency' => 'Kiwango cha haraka',
    'advice' => 'Ushauri',
    'red_flags' => 'Dalili za hatari',
    'ai_error_reply' => 'Huduma ya AI haipatikani kwa sasa. Tafadhali jaribu tena baadaye.',
    'ai_disabled_reply' => 'AI triage imezimwa kwa sasa. Tuma maelezo zaidi kwa ukaguzi wa daktari.',
    'ai_key_invalid_reply' => 'AI key si sahihi. Wasiliana na admin.',
    'ai_parse_fallback' => 'Asante. Dalili zilianza lini? Kuna homa, damu, au maumivu makali?',
    'ai_task_prompt' => 'Analyze the conversation and return ONLY JSON with keys: type (question|conclusion), assistant_message, possible_condition, urgency, advice (array), red_flags (array). Ask follow-up questions if details are insufficient.',
    'ai_default_system_prompt' => 'You are Safe-Girl triage assistant for girls/women. Ask follow-up questions first, then provide safe possible-condition guidance, urgency, advice, and emergency red flags. Never give definitive diagnosis.',
];