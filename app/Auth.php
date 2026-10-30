<?php

declare(strict_types=1);

namespace App;

use App\Models\User;
use App\Repositories\UserRepository;

/**
 * Auth — authentication and session management.
 */
class Auth
{
    public function __construct(
        private UserRepository $users,
    ) {
    }

    public function login(string $email, string $password): array
    {
        $user = $this->users->findByEmail($email);
        if ($user === null) {
            return ['ok' => false, 'error' => 'Invalid email or password.'];
        }

        if (!password_verify($password, $user->passwordHash)) {
            return ['ok' => false, 'error' => 'Invalid email or password.'];
        }

        if (!$user->isActive()) {
            return ['ok' => false, 'error' => 'Your account has been suspended. Contact support.'];
        }

        $_SESSION['user_id'] = $user->id;
        $_SESSION['user_role'] = $user->role;
        $_SESSION['user_name'] = $user->name;
        $_SESSION['user_email'] = $user->email;

        return ['ok' => true, 'user' => $user];
    }

    public function register(string $name, string $email, string $password): array
    {
        if ($this->users->findByEmail($email) !== null) {
            return ['ok' => false, 'error' => 'Email already registered.'];
        }

        $passwordHash = password_hash($password, PASSWORD_DEFAULT, ['cost' => 12]);
        $token = bin2hex(random_bytes(32));
        $expires = date('Y-m-d H:i:s', strtotime('+24 hours'));

        $userId = $this->users->create([
            'name' => $name,
            'email' => $email,
            'password_hash' => $passwordHash,
            'role' => 'customer',
            'status' => 'active',
            'verification_token' => $token,
            'verification_expires' => $expires,
        ]);

        // Send verification email
        // Mail::send($email, 'Verify your email', ...);

        return ['ok' => true, 'user_id' => $userId, 'verification_token' => $token];
    }

    public function logout(): void
    {
        $_SESSION = [];
        if (isset($_COOKIE[session_name()])) {
            setcookie(session_name(), '', time() - 3600, '/');
        }
        session_destroy();
    }

    public function getCurrentUser(): ?User
    {
        $userId = $_SESSION['user_id'] ?? null;
        if ($userId === null) {
            return null;
        }
        return $this->users->findById((int) $userId);
    }

    public function isLoggedIn(): bool
    {
        return isset($_SESSION['user_id']);
    }

    public function isAdmin(): bool
    {
        return ($_SESSION['user_role'] ?? '') === 'admin';
    }

    public function isOwner(): bool
    {
        return in_array($_SESSION['user_role'] ?? '', ['owner', 'admin'], true);
    }

    public function requireLogin(): void
    {
        if (!$this->isLoggedIn()) {
            $_SESSION['flash'] = ['type' => 'warning', 'message' => 'Please sign in to continue.'];
            header('Location: /login');
            exit;
        }
    }

    public function requireRole(string $role): void
    {
        $this->requireLogin();
        if ($_SESSION['user_role'] !== $role) {
            $_SESSION['flash'] = ['type' => 'danger', 'message' => 'You do not have permission to access that page.'];
            header('Location: /');
            exit;
        }
    }

    public function requestPasswordReset(string $email): array
    {
        $user = $this->users->findByEmail($email);
        if ($user === null) {
            // Don't reveal whether email exists
            return ['ok' => true, 'message' => 'If an account exists, a reset link has been sent.'];
        }

        $token = bin2hex(random_bytes(32));
        $expires = date('Y-m-d H:i:s', strtotime('+1 hour'));

        $this->users->update($user->id, [
            'password_reset_token' => $token,
            'password_reset_expires_at' => $expires,
        ]);

        // Mail::send($email, 'Password reset', ...);

        return ['ok' => true, 'message' => 'If an account exists, a reset link has been sent.'];
    }

    public function resetPassword(string $token, string $newPassword): array
    {
        $user = $this->users->findByResetToken($token);
        if ($user === null) {
            return ['ok' => false, 'error' => 'Invalid or expired reset token.'];
        }

        $this->users->update($user->id, [
            'password_hash' => password_hash($newPassword, PASSWORD_DEFAULT, ['cost' => 12]),
            'password_reset_token' => null,
            'password_reset_expires_at' => null,
        ]);

        return ['ok' => true, 'message' => 'Password reset successfully.'];
    }

    public function verifyEmail(string $token): array
    {
        $user = $this->users->findByVerificationToken($token);
        if ($user === null) {
            return ['ok' => false, 'error' => 'Invalid or expired verification token.'];
        }

        $this->users->update($user->id, [
            'email_verified_at' => date('Y-m-d H:i:s'),
            'email_verification_token' => null,
            'email_verification_expires_at' => null,
        ]);

        return ['ok' => true, 'message' => 'Email verified successfully.'];
    }
}