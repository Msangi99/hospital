<?php

return [
    'badge' => 'Safe-Girl Node Active',
    'title_html' => 'Suivi <br> <span class="text-pink-600 italic underline decoration-pink-200">hormonal & sympt�mes.</span>',
    'subtitle' => 'Un syst�me reliant la sant� de la jeune fille � un sp�cialiste.',

    'chart_title' => 'Hormonal Growth Chart (7-25 Years)',
    'chart_left' => '�ge 7',
    'chart_mid' => '�ge 16 (Pubert�)',
    'chart_right' => '�ge 25',

    'chat_header' => 'Chat SemaNami',
    'chat_status' => 'Syst�me IA triage : en ligne',
    'chat_hint' => 'D�crivez les sympt�mes. L�IA peut poser des questions de suivi puis proposer une orientation et des conseils.',
    'chat_first_message' => 'Bonjour. Je suis l�assistante IA Safe-Girl. D�crivez vos sympt�mes et je poserai quelques questions.',

    'input_placeholder' => '�crivez les sympt�mes (ex : douleur bas ventre...)',
    'login_required' => 'Veuillez vous connecter pour envoyer un message',
    'login_now' => 'Se connecter',

    'e2e' => 'Chiffrement de bout en bout',
    'protected' => 'Prot�g�',
    'sent_to_moderator' => 'Envoy� au mod�rateur',
    'received_reply' => 'Re�u. L�assistant analyse votre message.',
    'typing_indicator_a11y' => 'L�assistant r�dige une r�ponse',
    'safe_girl_symptom_received' => 'Votre message a �t� re�u.',

    'possible_condition' => 'Condition possible',
    'urgency' => 'Urgence',
    'advice' => 'Conseils',
    'red_flags' => 'Signaux d�alerte',
    'ai_error_reply' => 'Le service IA est indisponible pour le moment. R�essayez bient�t.',
    'ai_disabled_reply' => 'Le triage IA est d�sactiv� actuellement. Partagez plus de d�tails pour revue clinique.',
    'ai_key_invalid_reply' => 'La cl� IA est invalide. Contactez l�administrateur.',
    'ai_parse_fallback' => 'Merci. Depuis quand avez-vous ces sympt�mes ? Y a-t-il fi�vre, saignement, ou douleur s�v�re ?',
    'ai_task_prompt' => 'Analyze the conversation and return ONLY JSON with keys: type (question|conclusion), assistant_message, possible_condition, urgency, advice (array), red_flags (array). Ask follow-up questions if details are insufficient.',
    'ai_default_system_prompt' => 'You are Safe-Girl triage assistant for girls/women. Ask follow-up questions first, then provide safe possible-condition guidance, urgency, advice, and emergency red flags. Never give definitive diagnosis.',
];