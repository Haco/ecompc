<?php
/**
 * Created by PhpStorm.
 * User: sebo
 * Date: 27.10.14
 * Time: 14:38
 */

namespace S3b0\Ecompc\Utility;

/**
 * Class ObjectStorageSortingUtility
 *
 * @package S3b0\Ecompc\Utility
 */
class ObjectStorageSortingUtility {

	/**
	 * Sort ObjectStorage objects by any property
	 *
	 * @param string                                       $property
	 * @param \TYPO3\CMS\Extbase\Persistence\ObjectStorage $objectStorage
	 * @param boolean                                      $returnArray
	 * @param boolean                                      $reverseOrderings
	 *
	 * @return array|\TYPO3\CMS\Extbase\Persistence\ObjectStorage
	 */
	public static function sortByProperty($property, \TYPO3\CMS\Extbase\Persistence\ObjectStorage $objectStorage, $returnArray = FALSE, $reverseOrderings = FALSE) {
		/**
		 * Transform ObjectStorage to array
		 *
		 * @var array $objectStorageToArray
		 */
		$objectStorageToArray = $objectStorage->toArray();

		/**
		 * Check if there are more than one objects in storage to sort
		 * If so, check if they provide the property 'sorting'
		 * If check fails immediately return given ObjectStorage
		 */
		if ( !$objectStorage->count() > 1 || !method_exists(new \S3b0\Ecompc\Utility\ObjectStorageSortingUtility(), 'usortBy' . \TYPO3\CMS\Core\Utility\GeneralUtility::underscoredToUpperCamelCase($property)) ) {
			return $objectStorage;
		} elseif ( !$objectStorageToArray[0] instanceof \TYPO3\CMS\Extbase\DomainObject\AbstractDomainObject ) {
			return $objectStorage;
		}

		/**
		 * Sort the array
		 */
		usort($objectStorageToArray, 'S3b0\Ecompc\Utility\ObjectStorageSortingUtility::usortBy' . \TYPO3\CMS\Core\Utility\GeneralUtility::underscoredToUpperCamelCase($property));
		/**
		 * Reverse array before return if reverseOrderings-flag is set
		 */
		if ( $reverseOrderings ) {
			$objectStorageToArray = array_reverse($objectStorageToArray);
		}

		/**
		 * Return either an array or newly created StorageObject, depending on returnArray-flag
		 */
		if ( $returnArray ) {
			return $objectStorageToArray;
		} else {
			$newObjectStorage = new \TYPO3\CMS\Extbase\Persistence\ObjectStorage();
			/** @var \TYPO3\CMS\Extbase\DomainObject\AbstractDomainObject $item */
			foreach ( $objectStorageToArray as $item ) {
				$newObjectStorage->attach($item);
			}
			return $newObjectStorage;
		}
	}

	/**
	 * @param $a
	 * @param $b
	 *
	 * @return boolean
	 */
	public static function usortBySorting($a, $b) {
		return $a->getSorting() > $b->getSorting();
	}

}