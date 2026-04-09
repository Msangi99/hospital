<?php

return [
    'side_intro' => 'Introduction',
    'side_welcome' => 'Welcome',
    'side_auth' => 'Authentication',
    'side_endpoints' => 'Core Endpoints',
    'side_tele' => 'Telemedicine',
    'side_emerge' => 'Emergency Alerts',
    'side_triage' => 'AI Symptom Triage',
    'side_interop' => 'Interoperability',
    'side_emr' => 'EMR/HMS Integration',
    'side_errors' => 'Error Codes',

    'doc_title' => 'SemaNami Ecosystem API',
    'doc_desc' => 'The SemaNami API allows hospital systems (HMS/EMR), governments, and partners to connect with digital health infrastructure. Through this API you can trigger emergencies, request medical advice, and integrate patient data more securely (AES-256).',
    'doc_note_html' => 'Our standard follows <strong>HL7® FHIR®</strong> principles for global interoperability.',

    'auth_title' => 'Authentication',
    'auth_desc_html' => 'All API requests require <code>X-API-KEY</code> in the header. Request your key via the <strong>UNIDA TECH LIMITED</strong> dashboard.',
    'auth_curl' => "curl -X GET \"https://api.semanamimi.com/v1/status\" \\\n     -H \"X-API-KEY: your_api_key_here\" \\\n     -H \"Content-Type: application/json\"",

    'tele_title' => 'Request Tele-Consult',
    'tele_desc' => 'Initiate a video consultation request between a patient and a doctor.',
    'tele_payload' => "{\n  \"patient_id\": \"SN-9982\",\n  \"department\": \"Cardiology\",\n  \"urgency\": \"high\",\n  \"callback_url\": \"https://your-hms.com/api/webhooks\"\n}",

    'emerge_title' => 'Trigger Emergency Alert',
    'emerge_desc' => 'Send emergency alerts to the nearest ambulance and health center using GPRS tracking.',
    'emerge_payload' => "{\n  \"location\": {\n    \"lat\": -6.7924,\n    \"lng\": 39.2083\n  },\n  \"type\": \"Ambulance\",\n  \"patient_info\": \"Trauma - Severe Bleeding\"\n}",

    'footer_help' => 'Need technical support?',
    'footer_partnership_help' => 'Partnership Help',
    'footer_small' => 'UNIDA TECH LIMITED © 2026. DATA SECURED BY AES-256. DAR ES SALAAM, TZ.',
];

