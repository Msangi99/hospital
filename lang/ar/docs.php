<?php

return [
    'side_intro' => 'مقدمة',
    'side_welcome' => 'مرحباً',
    'side_auth' => 'المصادقة',
    'side_endpoints' => 'النقاط الأساسية',
    'side_tele' => 'الطب عن بُعد',
    'side_emerge' => 'تنبيهات الطوارئ',
    'side_triage' => 'فرز الأعراض بالذكاء',
    'side_interop' => 'قابلية التكامل',
    'side_emr' => 'تكامل EMR/HMS',
    'side_errors' => 'رموز الأخطاء',

    'doc_title' => 'واجهة API لنظام SemaNami',
    'doc_desc' => 'تتيح واجهة SemaNami API لأنظمة المستشفيات (HMS/EMR) والحكومات والشركاء الاتصال بالبنية التحتية للصحة الرقمية. عبرها يمكنك إطلاق الطوارئ وطلب الاستشارات الطبية ودمج بيانات المرضى بشكل أكثر أماناً (AES-256).',
    'doc_note_html' => 'يعتمد معيارنا على مبادئ <strong>HL7® FHIR®</strong> لتحقيق تكامل عالمي.',

    'auth_title' => 'المصادقة',
    'auth_desc_html' => 'تتطلب جميع طلبات API وجود <code>X-API-KEY</code> في الـ header. اطلب مفتاحك عبر لوحة <strong>UNIDA TECH LIMITED</strong>.',
    'auth_curl' => "curl -X GET \"https://api.semanamimi.com/v1/status\" \\\n     -H \"X-API-KEY: your_api_key_here\" \\\n     -H \"Content-Type: application/json\"",

    'tele_title' => 'طلب استشارة عن بُعد',
    'tele_desc' => 'ابدأ طلب استشارة فيديو بين المريض والطبيب.',
    'tele_payload' => "{\n  \"patient_id\": \"SN-9982\",\n  \"department\": \"Cardiology\",\n  \"urgency\": \"high\",\n  \"callback_url\": \"https://your-hms.com/api/webhooks\"\n}",

    'emerge_title' => 'إطلاق تنبيه طوارئ',
    'emerge_desc' => 'أرسل تنبيهات الطوارئ إلى أقرب سيارة إسعاف ومركز صحي باستخدام تتبع GPRS.',
    'emerge_payload' => "{\n  \"location\": {\n    \"lat\": -6.7924,\n    \"lng\": 39.2083\n  },\n  \"type\": \"Ambulance\",\n  \"patient_info\": \"Trauma - Severe Bleeding\"\n}",

    'footer_help' => 'هل تحتاج مساعدة تقنية؟',
    'footer_partnership_help' => 'مساعدة الشراكة',
    'footer_small' => 'UNIDA TECH LIMITED © 2026. البيانات محمية بـ AES-256. دار السلام، تنزانيا.',
];

