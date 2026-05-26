<?php

namespace App\Services;

use App\Models\DeploymentLog;
use App\Models\User;
use Illuminate\Support\Facades\Log;
use phpseclib3\Net\SSH2;

class DeployService
{
    private string $host;
    private int    $port;
    private string $username;
    private string $password;
    private string $path;
    private int    $timeout;
    /** @var string[] */
    private array $commands;

    public function __construct()
    {
        $this->host     = config('deploy.host');
        $this->port     = config('deploy.port');
        $this->username = config('deploy.username');
        $this->password = config('deploy.password');
        $this->path     = config('deploy.path');
        $this->timeout  = config('deploy.timeout');
        $this->commands = config('deploy.commands');
    }

    /**
     * Returns true when a deployment started in the last 10 minutes is still
     * in "running" state — preventing concurrent runs.
     */
    public function isDeploymentRunning(): bool
    {
        return DeploymentLog::where('status', 'running')
            ->where('started_at', '>=', now()->subMinutes(10))
            ->exists();
    }

    /**
     * Validate that all required SSH config values are present.
     *
     * @throws \RuntimeException
     */
    public function validateConfig(): void
    {
        $missing = [];

        foreach (['host', 'username', 'password', 'path'] as $key) {
            if (empty($this->$key)) {
                $missing[] = strtoupper("DEPLOY_{$key}");
            }
        }

        if (! empty($missing)) {
            throw new \RuntimeException(
                'Missing deployment configuration: ' . implode(', ', $missing)
            );
        }
    }

    /**
     * Create a DeploymentLog, connect via SSH, run commands, capture output.
     */
    public function deploy(User $user): DeploymentLog
    {
        $this->validateConfig();

        $log = DeploymentLog::create([
            'user_id'    => $user->id,
            'status'     => 'running',
            'output'     => '',
            'started_at' => now(),
        ]);

        Log::info("Deployment #{$log->id} started by user #{$user->id} ({$user->email})");

        try {
            $this->runSsh($log);

            $log->update([
                'status'      => 'success',
                'finished_at' => now(),
            ]);

            Log::info("Deployment #{$log->id} completed successfully in {$log->durationSeconds()}s");
        } catch (\Throwable $e) {
            $log->appendOutput("\n\n[FATAL ERROR] {$e->getMessage()}\n");
            $log->update([
                'status'      => 'failed',
                'finished_at' => now(),
            ]);

            Log::error("Deployment #{$log->id} failed: {$e->getMessage()}");
        }

        return $log->fresh();
    }

    /**
     * Open the SSH connection and execute each deployment command sequentially.
     * Appends partial output to the log so the status-polling endpoint can
     * return live progress to the browser.
     *
     * @throws \RuntimeException on SSH auth failure or non-zero exit code.
     */
    private function runSsh(DeploymentLog $log): void
    {
        $ssh = new SSH2($this->host, $this->port, $this->timeout);

        if (! $ssh->login($this->username, $this->password)) {
            throw new \RuntimeException(
                "SSH authentication failed for {$this->username}@{$this->host}:{$this->port}"
            );
        }

        $log->appendOutput("[OK] Connected to {$this->host}:{$this->port}\n");
        $log->appendOutput("[OK] Authenticated as {$this->username}\n");
        $log->appendOutput("[OK] Working directory: {$this->path}\n\n");

        foreach ($this->commands as $command) {
            $log->appendOutput("$ {$command}\n");

            // Each SSH2::exec() call runs in a fresh shell; prepend cd so the
            // working directory is always the project root.
            $output = $ssh->exec(
                "cd " . escapeshellarg($this->path) . " && {$command} 2>&1"
            );

            $exitCode = $ssh->getExitStatus();

            $log->appendOutput($output === '' ? "(no output)\n" : $output);

            if ($exitCode !== 0 && $exitCode !== false) {
                throw new \RuntimeException(
                    "Command exited with code {$exitCode}: {$command}"
                );
            }

            $log->appendOutput("\n");
        }

        $log->appendOutput("[DONE] All commands executed successfully.\n");

        // Gracefully close the SSH session (equivalent to running `exit`)
        $log->appendOutput("\n$ exit\n");
        $ssh->disconnect();
        $log->appendOutput("[OK] SSH session closed. Logged out from server.\n");
    }
}
