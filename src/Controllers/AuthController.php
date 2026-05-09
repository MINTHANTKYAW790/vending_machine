<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Request;
use App\Middleware\AuthMiddleware;
use App\Repositories\UserRepository;
use PDOException;

final class AuthController extends Controller
{
    public function __construct(private readonly UserRepository $users)
    {
    }

    public function showRegister(): void
    {
        if (auth_user_id() !== null) {
            redirect('/products');
        }

        $this->view('auth.register', [
            'error' => flash('error'),
            'success' => flash('success'),
        ]);
    }

    public function showLogin(): void
    {
        if (auth_user_id() !== null) {
            redirect('/products');
        }

        $this->view('auth.login', [
            'error' => flash('error'),
        ]);
    }

    public function login(Request $request): void
    {
        $username = trim((string) $request->input('username'));
        $password = (string) $request->input('password');

        $user = $this->users->findByUsername($username);
        if (!$user || !password_verify($password, $user['password_hash'])) {
            flash('error', 'Invalid credentials.');
            redirect('/login');
        }

        session_regenerate_id(true);
        $_SESSION['user_id'] = (int) $user['id'];
        $_SESSION['role'] = $user['role'];
        $_SESSION['username'] = $user['username'];

        redirect('/products');
    }

    public function register(Request $request): void
    {
        if (auth_user_id() !== null) {
            redirect('/products');
        }

        $username = trim((string) $request->input('username'));
        $email = trim((string) $request->input('email'));
        $password = (string) $request->input('password');
        $passwordConfirm = (string) $request->input('password_confirm');

        if ($username === '' || $email === '' || $password === '') {
            flash('error', 'Username, email, and password are required.');
            redirect('/register');
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            flash('error', 'Please provide a valid email address.');
            redirect('/register');
        }

        if ($password !== $passwordConfirm) {
            flash('error', 'Password confirmation does not match.');
            redirect('/register');
        }

        if (strlen($password) < 6) {
            flash('error', 'Password must be at least 6 characters.');
            redirect('/register');
        }

        if ($this->users->findByUsername($username)) {
            flash('error', 'Username is already taken.');
            redirect('/register');
        }

        if ($this->users->findByEmail($email)) {
            flash('error', 'Email is already registered.');
            redirect('/register');
        }

        try {
            $this->users->create([
                'username' => $username,
                'email' => $email,
                'password_hash' => password_hash($password, PASSWORD_DEFAULT),
                'role' => 'User',
            ]);
        } catch (PDOException $exception) {
            if ($this->users->isUniqueConstraintViolation($exception)) {
                flash('error', 'Username or email is already registered.');
                redirect('/register');
            }
            throw $exception;
        }

        flash('success', 'Registration successful. Please log in.');
        redirect('/login');
    }

    public function logout(): void
    {
        AuthMiddleware::ensureAuthenticated();
        $_SESSION = [];
        if (ini_get('session.use_cookies')) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 3600, $params['path'], $params['domain'] ?? '', (bool) $params['secure'], (bool) $params['httponly']);
        }
        session_destroy();
        redirect('/login');
    }
}
