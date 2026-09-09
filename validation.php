<?php
// ── INPUT VALIDATION FUNCTIONS ──────────────────────────────────────────────

// Sanitize a plain text string (trim + strip tags)
function sanitize(string $value): string {
    return htmlspecialchars(strip_tags(trim($value)));
}

// Validate that a field is not empty
function validateRequired(string $value, string $fieldName = 'This field'): string {
    if (trim($value) === '') {
        return $fieldName . ' is required.';
    }
    return '';
}

// Validate minimum string length
function validateMinLength(string $value, int $min, string $fieldName = 'This field'): string {
    if (strlen(trim($value)) < $min) {
        return $fieldName . ' must be at least ' . $min . ' characters.';
    }
    return '';
}

// Validate maximum string length
function validateMaxLength(string $value, int $max, string $fieldName = 'This field'): string {
    if (strlen(trim($value)) > $max) {
        return $fieldName . ' must not exceed ' . $max . ' characters.';
    }
    return '';
}

// Validate email format
function validateEmail(string $email): string {
    if (trim($email) === '') {
        return 'Email is required.';
    }
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        return 'Please enter a valid email address.';
    }
    return '';
}

// Validate password strength:
// – At least 8 characters
// – At least one uppercase letter
// – At least one lowercase letter
// – At least one number
// – At least one special character
function validatePassword(string $password): string {
    if (trim($password) === '') {
        return 'Password is required.';
    }
    if (strlen($password) < 8) {
        return 'Password must be at least 8 characters.';
    }
    if (!preg_match('/[A-Z]/', $password)) {
        return 'Password must contain at least one uppercase letter.';
    }
    if (!preg_match('/[a-z]/', $password)) {
        return 'Password must contain at least one lowercase letter.';
    }
    if (!preg_match('/[0-9]/', $password)) {
        return 'Password must contain at least one number.';
    }
    if (!preg_match('/[\W_]/', $password)) {
        return 'Password must contain at least one special character (e.g. @, #, !).';
    }
    return '';
}

// Validate that two password fields match
function validatePasswordMatch(string $password, string $confirm): string {
    if ($password !== $confirm) {
        return 'Passwords do not match.';
    }
    return '';
}

// Validate a name (letters, spaces, hyphens only)
function validateName(string $name): string {
    if (trim($name) === '') {
        return 'Name is required.';
    }
    if (!preg_match('/^[\pL\s\-]+$/u', $name)) {
        return 'Name may only contain letters, spaces, and hyphens.';
    }
    if (strlen(trim($name)) < 2) {
        return 'Name must be at least 2 characters.';
    }
    if (strlen(trim($name)) > 100) {
        return 'Name must not exceed 100 characters.';
    }
    return '';
}

// Validate login form fields and return an array of error messages
function validateLoginForm(array $data): array {
    $errors = [];

    $emailError = validateEmail($data['email'] ?? '');
    if ($emailError) $errors['email'] = $emailError;

    $passwordError = validateRequired($data['password'] ?? '', 'Password');
    if ($passwordError) $errors['password'] = $passwordError;

    return $errors;
}

// Check whether a validation errors array has any entries
function hasErrors(array $errors): bool {
    return !empty($errors);
}

// Validate phone number (Philippine format)
function validatePhone(string $phone): string {
    if (trim($phone) === '') {
        return ''; // Phone is optional
    }
    // Remove spaces, dashes, and parentheses
    $clean = preg_replace('/[\s\-()]/', '', $phone);
    if (!preg_match('/^[0-9]{10,13}$/', $clean)) {
        return 'Please enter a valid phone number (e.g., 09123456789).';
    }
    return '';
}
?>