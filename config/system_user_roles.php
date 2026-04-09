<?php

return [
    'SUPERADMIN' => [
        'title' => 'Superadmin',
        'summary' => 'Platform-level administrator responsible for approvals, governance, and security.',
        'responsibilities' => [
            'Approve, reject, or suspend hospital registrations.',
            'Manage platform-level users and policies.',
            'Review global analytics, incidents, and audit logs.',
            'Manage integrations and emergency governance.',
        ],
        'dashboard_focus' => [
            'platform-governance',
            'global-analytics',
            'security-and-audit',
            'verification-and-compliance',
        ],
    ],

    'HOSPITAL_OWNER' => [
        'title' => 'Hospital Owner',
        'summary' => 'Hospital account owner who registers and manages a hospital after superadmin verification.',
        'responsibilities' => [
            'Register hospital profile and keep details accurate.',
            'Manage workers under the owned hospital.',
            'Assign and manage hospital worker roles.',
            'Coordinate operations after approval.',
        ],
        'dashboard_focus' => [
            'hospital-profile',
            'worker-management',
            'operations-overview',
            'verification-status',
        ],
    ],

    'MEDICAL_TEAM' => [
        'title' => 'Medical Team',
        'summary' => 'Healthcare professionals directly involved in patient care.',
        'responsibilities' => [
            'View and update patient medical records and history.',
            'Write prescriptions, diagnoses, and treatment plans.',
            'Order lab tests and review results.',
            'Communicate with patients and care team members.',
            'Manage appointments and shift schedules.',
        ],
        'dashboard_focus' => [
            'clinical-workflows',
            'patient-care-tasks',
            'appointments-and-shifts',
            'team-communication',
        ],
    ],

    'PATIENT' => [
        'title' => 'Patient',
        'summary' => 'End-user receiving care and managing personal health journey.',
        'responsibilities' => [
            'Register and manage personal health profile.',
            'Book, reschedule, or cancel appointments.',
            'View own medical records, prescriptions, and test results.',
            'Communicate with assigned medical team.',
            'Track billing and insurance information.',
        ],
        'dashboard_focus' => [
            'care-journey',
            'appointments',
            'medical-records',
            'billing-and-insurance',
        ],
    ],

    'FACILITY' => [
        'title' => 'Facility',
        'summary' => 'Hospital, clinic, or healthcare center as an operational entity.',
        'responsibilities' => [
            'Manage facility profile, departments, and resources.',
            'Onboard and manage local staff accounts.',
            'Configure facility-specific workflows and schedules.',
            'View facility-level reports and analytics.',
            'Manage bed availability, rooms, and equipment.',
        ],
        'dashboard_focus' => [
            'operations',
            'staff-management',
            'resource-allocation',
            'facility-analytics',
        ],
    ],

    'AMBULANCE' => [
        'title' => 'Ambulance',
        'summary' => 'Emergency response unit role handling dispatch and transport workflows.',
        'responsibilities' => [
            'Receive and respond to emergency dispatch requests.',
            'Update real-time location and status during missions.',
            'Log patient condition and vitals during transport.',
            'Coordinate handoff with receiving facility.',
            'View assigned hospital destinations and routes.',
        ],
        'dashboard_focus' => [
            'dispatch-queue',
            'live-status',
            'transport-logs',
            'route-and-destination',
        ],
    ],
];

