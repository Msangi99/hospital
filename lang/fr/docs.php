<?php

return [
    'side_intro' => 'Introduction',
    'side_welcome' => 'Bienvenue',
    'side_auth' => 'Authentification',
    'side_endpoints' => 'Endpoints principaux',
    'side_tele' => 'Télémedecine',
    'side_emerge' => 'Alertes d’urgence',
    'side_triage' => 'Triage IA',
    'side_interop' => 'Interopérabilité',
    'side_emr' => 'Intégration EMR/HMS',
    'side_errors' => 'Codes d’erreur',

    'doc_title' => 'API de l’écosystème SemaNami',
    'doc_desc' => 'L’API SemaNami permet aux systèmes hospitaliers (HMS/EMR), aux gouvernements et aux partenaires de se connecter à l’infrastructure de santé numérique. Elle permet de déclencher des urgences, de demander un avis médical et d’intégrer des données patients de façon plus sécurisée (AES-256).',
    'doc_note_html' => 'Notre standard suit les principes <strong>HL7® FHIR®</strong> pour une interopérabilité mondiale.',

    'auth_title' => 'Authentification',
    'auth_desc_html' => 'Toutes les requêtes API nécessitent <code>X-API-KEY</code> dans le header. Demandez votre clé via le tableau de bord <strong>UNIDA TECH LIMITED</strong>.',
    'auth_curl' => "curl -X GET \"https://api.semanamimi.com/v1/status\" \\\n     -H \"X-API-KEY: your_api_key_here\" \\\n     -H \"Content-Type: application/json\"",

    'tele_title' => 'Demander une télé-consultation',
    'tele_desc' => 'Initiez une demande de consultation vidéo entre un patient et un médecin.',
    'tele_payload' => "{\n  \"patient_id\": \"SN-9982\",\n  \"department\": \"Cardiology\",\n  \"urgency\": \"high\",\n  \"callback_url\": \"https://your-hms.com/api/webhooks\"\n}",

    'emerge_title' => 'Déclencher une alerte d’urgence',
    'emerge_desc' => 'Envoyez des alertes d’urgence à l’ambulance et au centre de santé le plus proche via le suivi GPRS.',
    'emerge_payload' => "{\n  \"location\": {\n    \"lat\": -6.7924,\n    \"lng\": 39.2083\n  },\n  \"type\": \"Ambulance\",\n  \"patient_info\": \"Trauma - Severe Bleeding\"\n}",

    'footer_help' => 'Besoin d’aide technique ?',
    'footer_partnership_help' => 'Aide partenariat',
    'footer_small' => 'UNIDA TECH LIMITED © 2026. DONNÉES SÉCURISÉES PAR AES-256. DAR ES SALAAM, TZ.',
];

