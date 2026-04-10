# Super-Admin Requirements Roadmap (SemaNami)

## Purpose

This document translates product intent into implementable system requirements for the Super-Admin area, with a phased rollout where core dashboards ship first and advanced admin modules are finalized later.

## Interpreted Product Intent

1. The system needs a strict but clean clinician/facility verification flow.
2. Operations must be monitored in near real time (SOS, gateway health, active sessions, nearest service matching).
3. B2B ecosystem management (facilities, SaaS tiers, partner APIs) must be first-class.
4. Security/compliance must be enforceable and auditable.
5. Revenue and payouts need reliable tracking/reporting.
6. UI should remain minimalist, fast, searchable, and suitable for 24/7 monitoring.
7. Super-Admin routes must be hardened with MFA and optional IP allowlist.
8. Super-Admin completion can be sequenced after other dashboards for easier delivery.

## Current Baseline (Observed)

- Role-based route separation already exists (`SUPERADMIN`, `HOSPITAL_OWNER`, `MEDICAL_TEAM`, etc.).
- Admin pages/routes exist for users, facilities, analytics, audit logs, emergencies, AI settings, and console.
- Doctor profile onboarding currently captures only one license file and basic fields.
- Basic SOS and facility data models exist.
- Two-factor columns exist on users and Fortify 2FA trait is enabled.
- Current audit logs are aggregated from recent records, not immutable append-only compliance logs.
- No complete module yet for API key lifecycle, SaaS tier controls, payout engine, or grant/donor impact reports.

## Required Modules To Add

### 1) Vetting Engine (Practitioner Verification)

- Multi-document KYC intake for clinician IDs, diplomas, licenses, specialization proof.
- Verification queue with side-by-side previews and one-click approve/reject/suspend.
- Enforce minimum experience rule (>=2 years) at submission and moderation levels.
- License expiration date tracking and proactive alerts before expiry.
- Verification status transitions with reason codes and moderator attribution.

### 2) Ecosystem Health Monitor (Live Operations)

- Real-time SOS heatmap (Tanzania focus) from SOS/USSD events.
- Connectivity monitors for USSD gateway (`*150*40#`) and GPRS/server health.
- Active telemedicine session counter and trend graph.
- Nearby service matcher (patient -> nearest eligible hospital/ambulance/pharmacy).
- Alerting thresholds for spikes, outages, and degraded performance.

### 3) Stakeholder & Facility Management

- Facility hierarchy support (hospitals, pharmacies, ambulance fleets, parent-child relations).
- SaaS tier assignment per facility, with feature flags for Pro capabilities.
- Partner API key management portal (create, rotate, revoke, scope, audit).
- Facility compliance profile (license status, verification history, service readiness).

### 4) Security & Compliance

- Immutable audit ledger for PHI/data access events (who/what/when/where).
- Granular RBAC policy editor (e.g., Junior Admin vs Financial Admin).
- Data residency controls for Tanzanian compliance requirements.
- Super-Admin hardening: mandatory MFA + optional IP allowlist middleware.
- Security monitoring and alerting for suspicious access patterns.

### 5) Financial & Transactional Engine

- Revenue dashboard (subscriptions + transaction fees).
- Doctor payout orchestration with status lifecycle and reconciliation records.
- Grant/donor reporting exports with social impact metrics (e.g., SOS-assisted outcomes).
- Financial audit trail and settlement reports.

### 6) Clean Admin UX

- Minimalist information architecture with grouped sidebar:
  - Operations
  - Users
  - Facilities
  - System
- Global command search (Cmd/Ctrl + K) across users, patients, SOS events, facilities.
- Dark mode for 24/7 operations teams.
- Prioritize clarity and response speed over dense visual complexity.

## Suggested Delivery Phases

### Phase A (Now): Foundation + Non-Admin Dashboards

- Complete patient/doctor/facility/owner dashboard flows first.
- Stabilize SOS and telemedicine data capture reliability.
- Add shared observability primitives needed by admin later.

### Phase B: Super-Admin Security Baseline

- Enforce mandatory MFA for `SUPERADMIN`.
- Add optional IP allowlist gate for `/admin/*`.
- Add permission model foundation for granular RBAC.

### Phase C: Admin Core Operations

- Build Vetting Engine workflow and license expiration alerts.
- Implement real-time health monitor widgets and alarms.
- Expand facility hierarchy and SaaS tier controls.

### Phase D: Compliance + Financials

- Implement immutable audit log pipeline.
- Add payout workflows, revenue analytics, and donor reporting exports.
- Final hardening, load testing, and runbook documentation.

## Non-Functional Requirements

- Security: encryption at rest for sensitive documents and keys; least-privilege access.
- Performance: real-time cards should degrade gracefully (fallback polling).
- Reliability: critical monitors must have alert retry and outage-safe behavior.
- Traceability: every moderation/financial/compliance action must be attributable.
- Localization readiness: retain bilingual-friendly labels/content strategy.

## Acceptance Criteria (High-Level)

- Admin cannot access privileged routes without MFA confirmation.
- IP allowlist can be enabled/disabled via secure config and is enforced correctly.
- Clinician/facility verification supports full document set and decision audit trail.
- Expiring licenses trigger alerts before expiry windows.
- SOS and system health panels update near real-time and raise alerts on incidents.
- RBAC can restrict page, action, and data scope per admin role.
- Financial reports and donor exports are reproducible and auditable.

