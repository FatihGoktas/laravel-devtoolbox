<?php

declare(strict_types=1);

namespace Grazulex\LaravelDevtoolbox\Console\Commands;

use Illuminate\Console\Command;
use Symfony\Component\Process\Process;

final class DevLogTailCommand extends Command
{
    protected $signature = 'dev:log:tail 
                            {--file= : Specific log file to tail (default: laravel.log)}
                            {--lines=50 : Number of lines to show initially}
                            {--pattern= : Filter logs by pattern (regex supported)}
                            {--level= : Filter by log level (emergency, alert, critical, error, warning, notice, info, debug)}
                            {--follow : Follow log in real-time (like tail -f)}
                            {--format=table : Output format (table, json)}';

    protected $description = 'Monitor Laravel logs with real-time filtering and pattern matching';

    private array $logLevels = [
        'emergency', 'alert', 'critical', 'error', 
        'warning', 'notice', 'info', 'debug'
    ];

    public function handle(): int
    {
        $file = $this->option('file') ?: 'laravel.log';
        $lines = (int) $this->option('lines');
        $pattern = $this->option('pattern');
        $level = $this->option('level');
        $follow = $this->option('follow');
        $format = $this->option('format');

        // Validate log level
        if ($level && !in_array(strtolower($level), $this->logLevels)) {
            $this->error("Invalid log level: {$level}");
            $this->line('Valid levels: ' . implode(', ', $this->logLevels));
            return self::FAILURE;
        }

        $logPath = $this->getLogPath($file);
        
        if (!file_exists($logPath)) {
            $this->error("Log file not found: {$logPath}");
            return self::FAILURE;
        }

        if ($follow) {
            return $this->followLog($logPath, $pattern, $level, $format);
        } else {
            return $this->showLogTail($logPath, $lines, $pattern, $level, $format);
        }
    }

    private function getLogPath(string $file): string
    {
        $logsPath = storage_path('logs');
        
        // If file contains path separator, treat as relative to logs directory
        if (str_contains($file, '/') || str_contains($file, '\\')) {
            return $logsPath . DIRECTORY_SEPARATOR . $file;
        }
        
        // If file doesn't have extension, add .log
        if (!pathinfo($file, PATHINFO_EXTENSION)) {
            $file .= '.log';
        }
        
        return $logsPath . DIRECTORY_SEPARATOR . $file;
    }

    private function followLog(string $logPath, ?string $pattern, ?string $level, string $format): int
    {
        $this->info("📡 Following log: {$logPath}");
        if ($pattern) {
            $this->line("🔍 Pattern: {$pattern}");
        }
        if ($level) {
            $this->line("📊 Level: " . strtoupper($level));
        }
        $this->line("Press Ctrl+C to stop");
        $this->newLine();

        // Use tail -f command
        $command = ['tail', '-f', $logPath];
        
        $process = new Process($command);
        $process->setTimeout(null); // No timeout for following
        
        $process->start();
        
        foreach ($process as $type => $data) {
            if ($process::OUT === $type) {
                $lines = explode("\n", rtrim($data));
                foreach ($lines as $line) {
                    if (!empty($line)) {
                        $this->processLogLine($line, $pattern, $level, $format);
                    }
                }
            }
        }
        
        return self::SUCCESS;
    }

    private function showLogTail(string $logPath, int $lines, ?string $pattern, ?string $level, string $format): int
    {
        $this->info("📋 Last {$lines} lines from: {$logPath}");
        if ($pattern) {
            $this->line("🔍 Pattern: {$pattern}");
        }
        if ($level) {
            $this->line("📊 Level: " . strtoupper($level));
        }
        $this->newLine();

        // Read last N lines
        $command = ['tail', '-n', (string) $lines, $logPath];
        
        $process = new Process($command);
        $process->run();
        
        if (!$process->isSuccessful()) {
            $this->error('Failed to read log file: ' . $process->getErrorOutput());
            return self::FAILURE;
        }
        
        $output = $process->getOutput();
        $logLines = explode("\n", rtrim($output));
        
        $filteredLines = [];
        foreach ($logLines as $line) {
            if (!empty($line)) {
                $processed = $this->processLogLine($line, $pattern, $level, $format, false);
                if ($processed) {
                    $filteredLines[] = $processed;
                }
            }
        }
        
        if ($format === 'json') {
            $this->line(json_encode($filteredLines, JSON_PRETTY_PRINT));
        } else {
            foreach ($filteredLines as $line) {
                $this->displayFormattedLogLine($line);
            }
        }
        
        return self::SUCCESS;
    }

    private function processLogLine(string $line, ?string $pattern, ?string $level, string $format, bool $display = true): ?array
    {
        // Parse Laravel log format
        $parsed = $this->parseLogLine($line);
        
        // Apply filters
        if ($level && isset($parsed['level']) && strtolower($parsed['level']) !== strtolower($level)) {
            return null;
        }
        
        if ($pattern && !$this->matchesPattern($line, $pattern)) {
            return null;
        }
        
        if ($display && $format !== 'json') {
            $this->displayFormattedLogLine($parsed);
        }
        
        return $parsed;
    }

    private function parseLogLine(string $line): array
    {
        // Laravel log format: [2024-07-26 10:30:45] local.ERROR: Message
        $pattern = '/^\[(\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2})\] (\w+)\.(\w+): (.*)$/';
        
        if (preg_match($pattern, $line, $matches)) {
            return [
                'timestamp' => $matches[1],
                'environment' => $matches[2],
                'level' => $matches[3],
                'message' => $matches[4],
                'raw' => $line,
            ];
        }
        
        // If doesn't match Laravel format, treat as continuation or raw log
        return [
            'timestamp' => null,
            'environment' => null,
            'level' => 'unknown',
            'message' => $line,
            'raw' => $line,
        ];
    }

    private function matchesPattern(string $line, string $pattern): bool
    {
        // Try as regex first (if wrapped in delimiters)
        if (preg_match('/^\/.*\/[gimxsu]*$/', $pattern)) {
            try {
                return (bool) preg_match($pattern, $line);
            } catch (\Exception $e) {
                // Fall back to string search if regex is invalid
            }
        }
        
        // Simple string search (case-insensitive)
        return stripos($line, $pattern) !== false;
    }

    private function displayFormattedLogLine(array $parsed): void
    {
        if (!$parsed['timestamp']) {
            // Continuation line
            $this->line("    " . $parsed['message']);
            return;
        }
        
        $level = strtoupper($parsed['level']);
        $timestamp = $parsed['timestamp'];
        $message = $parsed['message'];
        
        // Color code by level
        $color = match ($parsed['level']) {
            'emergency', 'alert', 'critical' => 'error',
            'error' => 'error',
            'warning' => 'warn',
            'notice', 'info' => 'info',
            'debug' => 'comment',
            default => 'line',
        };
        
        $levelColor = match ($parsed['level']) {
            'emergency', 'alert', 'critical' => '<fg=red>',
            'error' => '<fg=red>',
            'warning' => '<fg=yellow>',
            'notice', 'info' => '<fg=blue>',
            'debug' => '<fg=gray>',
            default => '<fg=white>',
        };
        
        $formatted = "<fg=gray>[{$timestamp}]</> {$levelColor}[{$level}]</> {$message}";
        $this->line($formatted);
    }
}
