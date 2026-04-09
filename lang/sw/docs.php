<?php

return [
    'side_intro' => 'Utangulizi',
    'side_welcome' => 'Karibu',
    'side_auth' => 'Authentication',
    'side_endpoints' => 'Endpoints Kuu',
    'side_tele' => 'Telemedicine',
    'side_emerge' => 'Dharura (Emergency)',
    'side_triage' => 'AI Triage',
    'side_interop' => 'Ushirikiano',
    'side_emr' => 'EMR/HMS Integration',
    'side_errors' => 'Error Codes',

    'doc_title' => 'SemaNami Ecosystem API',
    'doc_desc' => 'API ya SemaNami inaruhusu mifumo ya hospitali (HMS/EMR), serikali, na partners kuunganishwa na miundombinu ya afya ya kidigitali. Kupitia API hii, unaweza kuanzisha dharura, kupata ushauri wa kitabibu, na kuunganisha data za wagonjwa kwa usalama zaidi (AES-256).',
    'doc_note_html' => 'Standard yetu inafuata misingi ya <strong>HL7® FHIR®</strong> kwa interoperability ya kimataifa.',

    'auth_title' => 'Authentication',
    'auth_desc_html' => 'Maombi yote ya API yanahitaji <code>X-API-KEY</code> kwenye header. Omba key yako kupitia dashboard ya <strong>UNIDA TECH LIMITED</strong>.',
    'auth_curl' => "curl -X GET \"https://api.semanamimi.com/v1/status\" \\\n     -H \"X-API-KEY: your_api_key_here\" \\\n     -H \"Content-Type: application/json\"",

    'tele_title' => 'Request Tele-Consult',
    'tele_desc' => 'Anzisha ombi la video consultation kati ya mgonjwa na daktari.',
    'tele_payload' => "{\n  \"patient_id\": \"SN-9982\",\n  \"department\": \"Cardiology\",\n  \"urgency\": \"high\",\n  \"callback_url\": \"https://your-hms.com/api/webhooks\"\n}",

    'emerge_title' => 'Trigger Emergency Alert',
    'emerge_desc' => 'Tuma taarifa za dharura kwa ambulance na kituo cha afya kilicho karibu zaidi kwa kutumia GPRS.',
    'emerge_payload' => "{\n  \"location\": {\n    \"lat\": -6.7924,\n    \"lng\": 39.2083\n  },\n  \"type\": \"Ambulance\",\n  \"patient_info\": \"Trauma - Severe Bleeding\"\n}",

    'footer_help' => 'Je, unahitaji msaada wa kiufundi?',
    'footer_partnership_help' => 'Partnership Help',
    'footer_small' => 'UNIDA TECH LIMITED © 2026. DATA SECURED BY AES-256. DAR ES SALAAM, TZ.',
];

