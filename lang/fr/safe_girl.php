<?php

return [
    'badge' => 'Safe-Girl Node Active',
    'title_html' => 'Suivi <br> <span class="text-pink-600 italic underline decoration-pink-200">hormonal & symptômes.</span>',
    'subtitle' => 'Un système reliant la santé de la jeune fille à un spécialiste.',

    'chart_title' => 'Hormonal Growth Chart (7-25 Years)',
    'chart_left' => 'Âge 7',
    'chart_mid' => 'Âge 16 (Puberté)',
    'chart_right' => 'Âge 25',

    'chat_header' => 'Chat SemaNami',
    'chat_status' => 'Système IA triage : en ligne',
    'chat_hint' => 'Décrivez les symptômes. L’IA peut poser des questions de suivi puis proposer une orientation et des conseils.',
    'chat_first_message' => 'Bonjour. Je suis l’assistante IA Safe-Girl. Décrivez vos symptômes et je poserai quelques questions.',

    'input_placeholder' => 'Écrivez les symptômes (ex : douleur bas ventre...)',
    'login_required' => 'Veuillez vous connecter pour envoyer un message',
    'login_now' => 'Se connecter',

    'e2e' => 'Chiffrement de bout en bout',
    'protected' => 'Protégé',
    'sent_to_moderator' => 'Envoyé au modérateur',
    'received_reply' => 'Reçu. L’assistant analyse votre message.',
    'safe_girl_symptom_received' => 'Votre message a été reçu.',

    'possible_condition' => 'Condition possible',
    'urgency' => 'Urgence',
    'advice' => 'Conseils',
    'red_flags' => 'Signaux d’alerte',
    'ai_error_reply' => 'Le service IA est indisponible pour le moment. Réessayez bientôt.',
    'ai_disabled_reply' => 'Le triage IA est désactivé actuellement. Partagez plus de détails pour revue clinique.',
    'ai_key_invalid_reply' => 'La clé IA est invalide. Contactez l’administrateur.',
    'ai_parse_fallback' => 'Merci. Depuis quand avez-vous ces symptômes ? Y a-t-il fièvre, saignement, ou douleur sévère ?',
    'ai_task_prompt' => 'Analyze the conversation and return ONLY JSON with keys: type (question|conclusion), assistant_message, possible_condition, urgency, advice (array), red_flags (array). Ask follow-up questions if details are insufficient.',
    'ai_default_system_prompt' => 'You are Safe-Girl triage assistant for girls/women. Ask follow-up questions first, then provide safe possible-condition guidance, urgency, advice, and emergency red flags. Never give definitive diagnosis.',
];