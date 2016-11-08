<?php
namespace Mia3\Mia3Location\Domain\Repository;
use TYPO3\CMS\Extbase\Persistence\QueryInterface;
use TYPO3\CMS\Extbase\Utility\DebuggerUtility;

/**
 *
 */
class LocationRepository extends \TYPO3\CMS\Extbase\Persistence\Repository
{
    /**
     * @var string
     */
    protected $tableName = 'tx_mia3location_domain_model_location';

    /**
     * @var array
     */
    protected $defaultOrderings = array ('sorting' => QueryInterface::ORDER_ASCENDING);

    public function findNearBy(
        $search,
        $latitude,
        $longitude,
        $distance = 30,
        $searchColumns = array(),
        $categories = array()
    ) {
        $pi = M_PI;

        $whereParts = array(
            'distance <= ' . intval($distance),
        );

        if (strlen($search) > 0) {
            $search = $GLOBALS['TYPO3_DB']->fullQuoteStr('%' . $search . '%', $this->tableName);
            foreach ($searchColumns as $column) {
                $whereParts[] = '`' . $column . '` LIKE ' . $search;
            }
        }

        $additionalWhere = array('1=1');

        if (count($categories) > 0) {
            $locationsInCategoryQuery = '
				SELECT uid_foreign
				FROM sys_category_record_mm
				WHERE sys_category_record_mm.tablenames = "tx_mia3location_domain_model_location"
				AND sys_category_record_mm.uid_local IN (' . implode(',', $categories) . ')
				';

            $result = $GLOBALS['TYPO3_DB']->sql_query($locationsInCategoryQuery);
            $locationsInCategoryUids = array();
            while ($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($result)) {
                $locationsInCategoryUids[] = $row['uid_foreign'];
            }
            $additionalWhere[] = 'uid in (' . implode(',', $locationsInCategoryUids) . ')';
        }

        $query = 'SELECT *, (
        	((acos(
				sin((' . number_format($latitude * $pi / 180, 10, '.',
                '') . ')) * sin((latitude * ' . number_format($pi, 10, '.', '') . ' / 180))
				+
				cos((' . number_format($latitude * $pi / 180, 10, '.',
                '') . ')) *  cos((latitude * ' . number_format($pi, 10, '.', '') . ' / 180))
				*
				cos(((' . number_format($longitude, 10, '.', '') . ' - longitude) * ' . number_format($pi, 10, '.',
                '') . ' / 180))
			)) * 180 / ' . number_format($pi, 10, '.', '') . ') * 60 * 1.423
		) as distance
		FROM ' . $this->tableName . '
		GROUP BY '. $this->tableName. '.external_id HAVING (' . implode(' OR ', $whereParts) . ')
			   AND (' . implode(' AND ', $additionalWhere) . ')
		' . $GLOBALS['TSFE']->sys_page->enableFields($this->tableName) . '
		ORDER BY distance ASC';
        $result = $GLOBALS['TYPO3_DB']->sql_query($query);
        // echo $query;
        // exit();

        $locations = array();
        while ($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($result)) {
            $locations[$row['uid']] = $this->findByUid($row['uid']);
        }

        return $locations;
    }

    public function findByColumns($search, $columns)
    {
        $search = $GLOBALS['TYPO3_DB']->fullQuoteStr('%' . $search . '%');

        $whereParts = array();
        foreach ($columns as $column) {
            $whereParts[] = $column . ' LIKE ' . $search;
        }

        $query = 'SELECT *
		FROM ' . $this->tableName . '
		WHERE (' . implode(' AND ', $whereParts) . ') ' . $GLOBALS['TSFE']->sys_page->enableFields($this->tableName) . '
		ORDER BY name
		';

        $result = $GLOBALS['TYPO3_DB']->sql_query($query);

        $locations = array();
        while ($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($result)) {
            $locations[$row['uid']] = $this->findByUid($row['uid']);
        }

        return $locations;
    }

    public function findAll($categories = array()) {
        $query = $this->createQuery();
        if (count($categories) > 0) {
            $locationsInCategoryQuery = '
                SELECT uid_foreign
                FROM sys_category_record_mm
                WHERE sys_category_record_mm.tablenames = "tx_mia3location_domain_model_location"
                AND sys_category_record_mm.uid_local IN (' . implode(',', $categories) . ')';

            $result = $GLOBALS['TYPO3_DB']->sql_query($locationsInCategoryQuery);
            $locationsInCategoryUids = array();
            while ($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($result)) {
                $locationsInCategoryUids[] = $row['uid_foreign'];
            }
            $query->matching($query->in('uid', $locationsInCategoryUids));
        };

        return $query->execute(true);
    }
}
