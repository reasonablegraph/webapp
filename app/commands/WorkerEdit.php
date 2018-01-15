<?php

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;

class WorkerEdit extends Command {

	protected $name = 'worker:edit';
	protected $description = 'Run edit worker.';
	protected $edit_step3;

	public function __construct() {
		parent::__construct();

		$this->edit_step3 = function ($job) {
			$this->info("Received job: " . $job->handle() . ", with function: " . WorkerPrefixes::$edit_step3 . ", and workload size: " . $job->workloadSize());

			try {
				// load workload
				$wl = json_decode($job->workload());

				$locale = $wl->locale;
				App::setLocale($locale);
				$this->info('Worker job locale: ' . App::getLocale());
				$userName = $wl->userName;
				$submit_id = (int) $wl->submit_id;

				// proceed with edit step 3
				$this->info(">>>>>>>>>>>>>>>>>> EDIT_ITEM_STEP3: ");

				//activity_log
				if (empty($userName)) {
					$userName = 'Unknown';
				}
				PDao::activity_log('admin', $userName, 'worker:sample/edit_step3');

				if (!empty($submit_id)) {
					$submit_data = PDao::loadSubmit($submit_id);
					if (empty($submit_data)) {
						$this->info("error (submit_data)");
					}

					$status = $submit_data['status'];

					$item_id = null;
					if ($status == SubmitsStatus::$finished) {

						$this->info("submit is already in finished status, doing nothing.");
						return;

					} else {
						$lock_msg = isset($submit_data['item_id']) ? $submit_data['item_id'] : null;
						$rlock = new GRuleEngineLock();
						$rlock->lock($lock_msg);
						PUtil::log("#wdebug# #L#: LOCKING FROM WORKER with pid: " . getmypid());

						$idata = $submit_data['idata'];
						$is = new ItemSave();
						$is->setIdata($idata);
						$is->setEdoc($submit_data['edoc']);
						$is->setSubmitId($submit_data['submit_id']);
						$is->setWfdata($submit_data['wfdata']);
						$is->setUserName($userName);
						$is->setItemId($submit_data['item_id']);
						$is->setRlock($rlock);

						$idata->validate();
						$errors = $idata->getErrors();
						$err_counter = count($errors);

						if ($err_counter > 0) {
							$this->info("errors found, doing nothing.");
							$rlock->release();
							return;
						}

						$item_id = $is->save_item();
						$rlock->release();
					}
				}

				$this->info("<<<<<<<<<<<<<<<<<< EDIT_ITEM_STEP3: " . $item_id);
			} catch (Exception $e) {
				$this->info("Caught exception: " . $e->getMessage());

				try {
					if (!empty($submit_id)) {
						$this->info("trying to set error status for submit id: " . $submit_id);
						PDao::update_submits_status_and_error($submit_id, SubmitsStatus::$error, $e->getMessage());
						$this->info("status for submit id: " . $submit_id . " was set to error (" . SubmitsStatus::$error . ") successfully");
					}

					if (!empty($rlock)) {
						$this->info("trying to release active lock");
						$rlock->release();
						$this->info("active lock was released successfully");
					}
				} catch (Exception $e2) {
					$this->info("Caught 2nd exception: " . $e2->getMessage() . ", while handling the previous exception, doing nothing ...");
				}
			}

			return;
		};
	}

	public function fire() {
		$this->info('Starting edit worker pid: ' . getmypid() . ', locale: ' . App::getLocale());

		$gmworker= new GearmanWorker();
		$gmworker->addServer();
		$gmworker->addFunction(WorkerPrefixes::$edit_step3, $this->edit_step3);

		$this->info("Waiting for job...");
		while($gmworker->work()) {
			if ($gmworker->returnCode() != GEARMAN_SUCCESS) {
				$this->info("return_code: " . $gmworker->returnCode());
				break;
			}
		}
	}

}