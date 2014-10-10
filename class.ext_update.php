<?php
	/**
	 * This file is part of the TYPO3 CMS project.
	 *
	 * It is free software; you can redistribute it and/or modify it under
	 * the terms of the GNU General Public License, either version 2
	 * of the License, or any later version.
	 *
	 * For the full copyright and license information, please read the
	 * LICENSE.txt file that was distributed with this source code.
	 *
	 * The TYPO3 project - inspiring people to share!
	 */

	use TYPO3\CMS\Core\Messaging\FlashMessage;
	use TYPO3\CMS\Core\Utility\GeneralUtility;

	/**
	 * Update class for the extension manager.
	 *
	 * @package TYPO3
	 * @subpackage tx_news
	 */
	class ext_update {

		/**
		 * Array of flash messages (params) array[][status,title,message]
		 *
		 * @var array
		 */
		protected $messageArray = array();

		/**
		 * @var \TYPO3\CMS\Core\Database\DatabaseConnection
		 */
		protected $databaseConnection;

		/**
		 * @var \TYPO3\CMS\Core\Resource\ResourceFactory
		 */
		protected $resourceFactory;

		/**
		 * Constructor
		 */
		public function __construct() {
			$this->databaseConnection = $GLOBALS['TYPO3_DB'];

			$this->resourceFactory = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance('TYPO3\\CMS\\Core\\Resource\\ResourceFactory');

		}

		/**
		 * Main update function called by the extension manager.
		 *
		 * @return string
		 */
		public function main() {
			$this->processUpdates();
			return $this->generateOutput();
		}

		/**
		 * Called by the extension manager to determine if the update menu entry
		 * should by showed.
		 *
		 * @return bool
		 * @todo find a better way to determine if update is needed or not.
		 */
		public function access() {
			return TRUE;
		}

		/**
		 * The actual update function. Add your update task in here.
		 *
		 * @return void
		 */
		protected function processUpdates() {
			// TODO Update list_type depending on tx_ecompc_type
			$this->updateDatabaseFieldLabels();
		}

		/**
		 * @return void
		 */
		protected function updateDatabaseFieldLabels() {
			$title = 'Update tt_content extension field names: ';

			$ttContentOldFieldNames = array(
				'tx_ecompc_type', 'tx_ecompc_pckg', 'tx_ecompc_conf', 'tx_ecompc_bpdc', 'tx_ecompc_bpfc'
			);
			$ttContentNewFieldNames = array(
				NULL, 'tx_ecompc_packages', 'tx_ecompc_configurations', 'tx_ecompc_base_price_default', 'tx_ecompc_pricing'
			);

			$ttContentTableFields = $this->databaseConnection->admin_get_fields('tt_content');
			foreach ($ttContentOldFieldNames as $arrayIndex => $fieldName) {
				if (array_key_exists($fieldName, $ttContentTableFields)) {
					if (is_string($ttContentNewFieldNames[$arrayIndex])) {
						$this->renameDatabaseTableField('tt_content', $fieldName, $ttContentNewFieldNames[$arrayIndex]);
					} else {
						$this->removeDatabaseTableField('tt_content', $fieldName);
					}
				}
			}
			$this->renameDatabaseTableField('tx_ecompc_domain_model_option', 'price_list', 'pricing');
		}

		protected function removeDatabaseTableField($table, $fieldName) {
			$title = 'Removing "' . $table . ':' . $fieldName . ': ';

			$currentTableFields = $this->databaseConnection->admin_get_fields($table);

			if ($currentTableFields[$fieldName]) {
				$sql = 'ALTER TABLE ' . $table . ' DROP COLUMN ' . $fieldName;

				if ($this->databaseConnection->admin_query($sql) === FALSE) {
					$message = ' SQL ERROR: ' . $this->databaseConnection->sql_error();
					$status = FlashMessage::ERROR;
				} else {
					$message = 'OK!';
					$status = FlashMessage::OK;
				}
			} else {
				$message = 'Field ' . $table . ':' . $fieldName . ' already removed.';
				$status = FlashMessage::OK;
			}

			$this->messageArray[] = array($status, $title, $message);
			return $status;
		}

		/**
		 * Renames a tabled field and does some plausibility checks.
		 *
		 * @param  string $table
		 * @param  string $oldFieldName
		 * @param  string $newFieldName
		 * @return int
		 */
		protected function renameDatabaseTableField($table, $oldFieldName, $newFieldName) {
			$title = 'Renaming "' . $table . ':' . $oldFieldName . '" to "' . $table . ':' . $newFieldName . '": ';

			$currentTableFields = $this->databaseConnection->admin_get_fields($table);

			if ($currentTableFields[$newFieldName]) {
				$message = 'Field ' . $table . ':' . $newFieldName . ' already existing.';
				$status = FlashMessage::OK;
			} else {
				if (!$currentTableFields[$oldFieldName]) {
					$message = 'Field ' . $table . ':' . $oldFieldName . ' not existing';
					$status = FlashMessage::ERROR;
				} else {
					$sql = 'ALTER TABLE ' . $table . ' CHANGE COLUMN ' . $oldFieldName . ' ' . $newFieldName . ' ' .
						$currentTableFields[$oldFieldName]['Type'];

					if ($this->databaseConnection->admin_query($sql) === FALSE) {
						$message = ' SQL ERROR: ' . $this->databaseConnection->sql_error();
						$status = FlashMessage::ERROR;
					} else {
						$message = 'OK!';
						$status = FlashMessage::OK;
					}

				}
			}

			$this->messageArray[] = array($status, $title, $message);
			return $status;
		}

		/**
		 * Rename a DB  table
		 *
		 * @param string $oldTableName old table name
		 * @param string $newTableName new table name
		 * @return boolean
		 */
		protected function renameDatabaseTable($oldTableName, $newTableName) {
			$title = 'Renaming "' . $oldTableName . '" to "' . $newTableName . '" ';

			$tables = $this->databaseConnection->admin_get_tables();
			if (isset($tables[$newTableName])) {
				$message = 'Table ' . $newTableName . ' already exists';
				$status = FlashMessage::OK;
			} elseif (!isset($tables[$oldTableName])) {
				$message = 'Table ' . $oldTableName . ' does not exist';
				$status = FlashMessage::ERROR;
			} else {
				$sql = 'RENAME TABLE ' . $oldTableName . ' TO ' . $newTableName . ';';

				if ($this->databaseConnection->admin_query($sql) === FALSE) {
					$message = ' SQL ERROR: ' . $this->databaseConnection->sql_error();
					$status = FlashMessage::ERROR;
				} else {
					$message = 'OK!';
					$status = FlashMessage::OK;
				}
			}

			$this->messageArray[] = array($status, $title, $message);
			return $status;
		}

		/**
		 * Renames a flex form field
		 *
		 * @param  string $pluginName The pluginName used in list_type
		 * @param  array $oldFieldPointer Pointer array the old field. E.g. array('sheetName', 'fieldName');
		 * @param  array $newFieldPointer Pointer array the new field. E.g. array('sheetName', 'fieldName');
		 * @return void
		 */
		protected function renameFlexformField($pluginName, array $oldFieldPointer, array $newFieldPointer) {
			$title = 'Renaming flexform field for "' . $pluginName . '" - ' .
				' sheet: ' . $oldFieldPointer[0] . ', field: ' . $oldFieldPointer[1] . ' to ' .
				' sheet: ' . $newFieldPointer[0] . ', field: ' . $newFieldPointer[1];

			$res = $this->databaseConnection->exec_SELECTquery('uid, pi_flexform',
				'tt_content',
				'CType=\'list\' AND list_type=\'' . $pluginName . '\'');

			/** @var \TYPO3\CMS\Core\Configuration\FlexForm\FlexFormTools $flexformTools */
			$flexformTools = GeneralUtility::makeInstance('TYPO3\\CMS\\Core\\Configuration\\FlexForm\\FlexFormTools');

			while ($row = $this->databaseConnection->sql_fetch_assoc($res)) {

				$xmlArray = GeneralUtility::xml2array($row['pi_flexform']);

				if (!is_array($xmlArray) || !isset($xmlArray['data'])) {
					$status = FlashMessage::ERROR;
					// @todo: This will happen when trying to update news2 > news but pluginName is already set to news
					// proposal for future: check for news2 somehow?
					$message = 'Flexform data of plugin "' . $pluginName . '" not found.';
				} elseif (!$xmlArray['data'][$oldFieldPointer[0]]) {
					$status = FlashMessage::WARNING;
					$message = 'Flexform data of record tt_content:' . $row['uid'] . ' did not contain ' .
						'sheet: ' . $oldFieldPointer[0];
				} else {
					$updated = FALSE;

					foreach ($xmlArray['data'][$oldFieldPointer[0]] as $language => $fields) {
						if ($fields[$oldFieldPointer[1]]) {

							$xmlArray['data'][$newFieldPointer[0]][$language][$newFieldPointer[1]] = $fields[$oldFieldPointer[1]];
							unset($xmlArray['data'][$oldFieldPointer[0]][$language][$oldFieldPointer[1]]);

							$updated = TRUE;
						}
					}

					if ($updated === TRUE) {
						$this->databaseConnection->exec_UPDATEquery('tt_content', 'uid=' . $row['uid'], array(
							'pi_flexform' => $flexformTools->flexArray2Xml($xmlArray)
						));
						$message = 'OK!';
						$status = FlashMessage::OK;
					} else {
						$status = FlashMessage::NOTICE;
						$message = 'Flexform data of record tt_content:' . $row['uid'] . ' did not contain ' .
							'sheet: ' . $oldFieldPointer[0] . ', field: ' . $oldFieldPointer[1] . '. This can
						also be because field has been updated already...';
					}
				}

				$this->messageArray[] = array($status, $title, $message);
			}
		}

		/**
		 * Generates output by using flash messages
		 *
		 * @return string
		 */
		protected function generateOutput() {
			$output = '';
			foreach ($this->messageArray as $messageItem) {
				/** @var \TYPO3\CMS\Core\Messaging\FlashMessage $flashMessage */
				$flashMessage = GeneralUtility::makeInstance(
					'TYPO3\\CMS\\Core\\Messaging\\FlashMessage',
					$messageItem[2],
					$messageItem[1],
					$messageItem[0]);
				$output .= $flashMessage->render();
			}
			return $output;
		}

	}
