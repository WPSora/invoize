<?php

namespace Invoize\Classes;

use Carbon\Carbon;
use Exception;
use Invoize\InvoizePlugin;

class Log
{
    const ERROR_LOG  = 'error';
    const ACTION_LOG = 'action';
    const EMAIL_LOG  = 'email';


    protected static function processLog(string $type, string $message)
    {
        $enableLog = filter_var(invoizeGetOption('other.log')['enableLog'], FILTER_VALIDATE_BOOL);
        if (!$enableLog) {
            return;
        }

        $user    = wp_get_current_user();
        $logDir  = InvoizePlugin::getInstance()->getPluginDir() . 'logs' . DIRECTORY_SEPARATOR;

        global $wp_filesystem;
        if (!$wp_filesystem->is_dir($logDir)) {
            if (!$wp_filesystem->mkdir($logDir)) {
                error_log('Failed to create Invoize log directory: ' . $logDir);
                return;
            }
        }

        $thisMonth = Carbon::now()->format('Y-m');
        $timestamp = Carbon::now()->toDateTimeString();
        $logFile   = "$logDir$thisMonth-$type.log";

        static::writeLog($logFile, $timestamp . ' ' . $user->display_name . ': ' . $message . PHP_EOL);
    }


    protected static function writeLog($fileName, $text)
    {
        try {
            global $wp_filesystem;
            if (!$wp_filesystem->exists($fileName)) {
                $wp_filesystem->put_contents($fileName, '', 0644);
            }
            $existing_content = $wp_filesystem->get_contents($fileName);
            $wp_filesystem->put_contents($fileName, $existing_content . $text, 0644);
        } catch (Exception $e) {
            error_log('Failed to write invoize log file. ' . $e->getMessage());
        }
    }


    public static function error(string $message)
    {
        static::processLog(static::ERROR_LOG, $message);
    }


    public static function action(string $message)
    {
        static::processLog(static::ACTION_LOG, $message);
    }

    public static function email(array $emails, $subject)
    {
        $sentContent    = 'Email sent to: ' . join(", ", $emails);
        $subjectContent = 'Subject: ' . $subject;
        static::processLog(static::EMAIL_LOG, "$sentContent. $subjectContent.");
    }

    public static function emailError($message)
    {
        static::processLog(static::EMAIL_LOG, "Failed to send email. " . $message);
    }


    public static function run_clear_log_action()
    {
        $startOfMonth = Carbon::now()->day;
        if ($startOfMonth !== 1) return;

        $option = invoizeGetOption('other.log')['keepLogsFor'] ?? false;
        $month  = null;

        switch ($option) {
            case 'forever':
                static::unschedule_clear_log();
                return;
            case '1-month':
                $month = Carbon::now()->subMonth()->format('Y-m');
                break;
            case '3-month':
                $month = Carbon::now()->subMonths(3)->format('Y-m');
                break;
            case '6-month':
                $month = Carbon::now()->subMonths(6)->format('Y-m');
                break;
            default:
                return;
        }

        if (!$month) return;

        $logDir        = InvoizePlugin::getInstance()->getPluginDir() . 'logs' . DIRECTORY_SEPARATOR;
        $errorLogFile  = $logDir . $month . '-' . static::ERROR_LOG . '.log';
        $actionLogFile = $logDir . $month . '-' . static::ACTION_LOG . '.log';
        $mailLogFile   = $logDir . $month . '-' . static::EMAIL_LOG . '.log';

        global $wp_filesystem;
        if ($wp_filesystem->exists($errorLogFile)) {
            wp_delete_file($errorLogFile);
        }
        if ($wp_filesystem->exists($actionLogFile)) {
            wp_delete_file($actionLogFile);
        }
        if ($wp_filesystem->exists($mailLogFile)) {
            wp_delete_file($mailLogFile);
        }
    }


    public static function schedule_clear_log()
    {
        try {
            $logHook = InvoizePlugin::getInstance()->getSlug() . '_clear_log_hook';
            if (!wp_next_scheduled($logHook)) {
                // set and run cron
                wp_schedule_event(time(), 'daily', $logHook);
                do_action($logHook);
            } else {
                // only run the cron
                do_action($logHook);
            }
        } catch (\Exception $e) {
            Log::error('Failed to schedule clear log. ' . $e->getMessage());
        }
    }


    public static function unschedule_clear_log()
    {
        $logHook = InvoizePlugin::getInstance()->getSlug() . "_clear_log_hook";
        $timestampRecurring = wp_next_scheduled($logHook);
        wp_unschedule_event($timestampRecurring, $logHook);
    }
}
