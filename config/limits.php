<?php

return [
    // OTP request limits: prevents spamming email inboxes and brute-force request storms.
    // Increase the "max" values to allow more OTP requests; decrease to tighten abuse prevention.
    'otp_request' => [
        'ip' => [
            'max' => env('LIMIT_OTP_REQ_IP_MAX', 10),
            'window_minutes' => env('LIMIT_OTP_REQ_IP_WINDOW', 10),
        ],
        'email' => [
            'max' => env('LIMIT_OTP_REQ_EMAIL_MAX', 3),
            'window_minutes' => env('LIMIT_OTP_REQ_EMAIL_WINDOW', 10),
        ],
        'ip_email' => [
            'max' => env('LIMIT_OTP_REQ_IP_EMAIL_MAX', 3),
            'window_minutes' => env('LIMIT_OTP_REQ_IP_EMAIL_WINDOW', 10),
        ],
    ],

    // OTP verification limits: reduces brute-force attempts on the OTP itself.
    // Tightening these limits increases security but may block legitimate retries.
    'otp_verify' => [
        'ip' => [
            'max' => env('LIMIT_OTP_VERIFY_IP_MAX', 20),
            'window_minutes' => env('LIMIT_OTP_VERIFY_IP_WINDOW', 10),
        ],
        'email' => [
            'max' => env('LIMIT_OTP_VERIFY_EMAIL_MAX', 10),
            'window_minutes' => env('LIMIT_OTP_VERIFY_EMAIL_WINDOW', 10),
        ],
    ],

    // OTP failure lockout: blocks an email after too many incorrect OTP submissions.
    // Lower threshold = stricter lock; longer window = longer lockout duration.
    'otp_fail_lock' => [
        'threshold' => env('LIMIT_OTP_FAIL_LOCK_THRESHOLD', 5),
        'window_minutes' => env('LIMIT_OTP_FAIL_LOCK_WINDOW', 10),
    ],

    // Checkout limits: protects booking endpoints from abuse per user and per IP.
    // Increase for higher booking throughput; decrease to tighten abuse protection.
    'checkout' => [
        'user' => [
            'max' => env('LIMIT_CHECKOUT_USER_MAX', 10),
            'window_minutes' => env('LIMIT_CHECKOUT_USER_WINDOW', 60),
        ],
        'ip' => [
            'max' => env('LIMIT_CHECKOUT_IP_MAX', 30),
            'window_minutes' => env('LIMIT_CHECKOUT_IP_WINDOW', 60),
        ],
    ],

    // bKash creation limits: prevents payment initiation abuse and duplicate calls.
    // Adjust upward for busier payment flows; downward for stricter protection.
    'bkash_create' => [
        'user' => [
            'max' => env('LIMIT_BKASH_USER_MAX', 5),
            'window_minutes' => env('LIMIT_BKASH_USER_WINDOW', 60),
        ],
        'ip' => [
            'max' => env('LIMIT_BKASH_IP_MAX', 10),
            'window_minutes' => env('LIMIT_BKASH_IP_WINDOW', 60),
        ],
        'active_initiated_window_minutes' => env('LIMIT_BKASH_ACTIVE_INITIATED_WINDOW', 15),
    ],

    // Admin area limits: keeps admin surfaces safe from rapid polling.
    // Increase if admin screens are heavily used; decrease for stricter throttling.
    'admin' => [
        'user' => [
            'max' => env('LIMIT_ADMIN_USER_MAX', 60),
            'window_minutes' => env('LIMIT_ADMIN_USER_WINDOW', 1),
        ],
    ],
];

