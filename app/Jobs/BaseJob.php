<?php

namespace App\Jobs;

use App\Models\BugReport;
use App\Models\JobMonitor;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;

abstract class BaseJob {

    /** @var User */
    protected $user;
    /** @var BaseJob */
    protected $parent_job;
    /** @var JobMonitor */
    protected $job_monitor;

    public function __construct(User $user) {
        $this->user = $user;
    }

    public function getUser() {
        return $this->user;
    }

    public function getCacheKey() {
        $job = $this->parent_job ?: $this;
        return "cron.{$this->user->id}." . get_class($job);
    }

    public function setJobActive() {
        Cache::put($this->getCacheKey(), getmypid(), 3600 * 3);
    }

    public function isJobActive() {
        return Cache::has($this->getCacheKey());
    }

    public function setJobDone() {
        Cache::forget($this->getCacheKey());
    }

    private function startMonitor() {
        $this->job_monitor = JobMonitor::query()->create([
            "user_id"    => $this->getUser()->id,
            "job_class"  => get_class_name($this),
            "server_id"  => config('app.instance'),
            "process_id" => getmypid(),
            "cache_key"  => $this->getCacheKey(),
            "started_at" => Carbon::now()
        ]);
    }

    private function ignoreJob() {
        $this->job_monitor = JobMonitor::query()->create([
            "user_id"    => $this->getUser()->id,
            "job_class"  => get_class_name($this),
            "server_id"  => config('app.instance'),
            "process_id" => getmypid(),
            "started_at" => Carbon::now(),
        ]);

        $this->job_monitor->setCompleted("Already Running");
    }

    public function handle() {
        if (!is_local() && $this->isJobActive()) {
            console_log("Job already running");
            $this->ignoreJob();
            return;
        }

        $this->startMonitor();

        try {
            console_log("Execution started for job: " . get_class($this));

            $this->setJobActive();
            $this->processJob();

            console_log("Job completed successfully");
            $this->job_monitor->setCompleted("Job Processed Successfully");

        } catch (\Exception $ex) {
            console_log($ex->getMessage(), $ex->getTrace());
            $this->job_monitor->logException($ex);
            BugReport::logException($ex, $this->getUser());

        } finally {
            $this->setJobDone();
        }

    }

    public function processJob() {
        // every job must implement this method.
    }
}
