<?php

use BB\Repo\EquipmentLogRepository;
use Illuminate\Console\Command;

class CheckFixEquipmentLog extends Command {

	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $name = 'bb:fix-equipment-log';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Fix equipment log entries';
    /**
     * @var EquipmentLogRepository
     */
    private $equipmentLogRepository;

    /**
     * Create a new command instance.
     *
     * @return \CheckFixEquipmentLog
     */
	public function __construct()
	{
		parent::__construct();
        $this->equipmentLogRepository = App::make('\BB\Repo\EquipmentLogRepository');
    }

	/**
	 * Execute the console command.
	 *
	 * @return mixed
	 */
	public function fire()
	{
        $records = $this->equipmentLogRepository->getActiveRecords();
        foreach ($records as $log)
        {
            if ($log->last_update && $log->last_update->lt(\Carbon\Carbon::now()->subHour())) {
                //Last update received over an hour ago

                //End the session with the end date being the last update date
                $this->equipmentLogRepository->endSession($log->id, $log->last_update);

            } elseif (!$log->last_update && $log->started->lt(\Carbon\Carbon::now()->subHour())) {
                //started over an hour ago, no updates

                //We don't know how long the user was active so record a minute
                $this->equipmentLogRepository->endSession($log->id, $log->started->addMinute());
            }

            //We should also be merging records and perhaps deleting very short records
        }
        $unProcessedRecords = $this->equipmentLogRepository->getUnprocessedRecords();
        foreach ($unProcessedRecords as $record)
        {
            if (!$record->active) {
                $secondsActive = $record->finished->diffInSeconds($record->started);

                //If the record is less than 60 seconds ignore it
                if ($secondsActive <= 60) {
                    $record->removed = true;
                }

                //Processing is finished
                $record->processed = true;
                $record->Save();
            }
        }
	}

}
